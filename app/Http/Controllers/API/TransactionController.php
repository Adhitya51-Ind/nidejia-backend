<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Transaction\Store;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('listing')->whereUserId(auth()->id())->paginate();

        return response()->json([
            'success'=> true,
            'message'=> 'Get All My Transactions',
            'data'=> $transactions
        ]);
    }
    private function _fullyBookedChecker(Store $request)
    {
        $listing = Listing::find($request->listing_id);

        $runningTransactionCount = Transaction::whereListingId($listing->id)
            ->whereNot('status', 'canceled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [
                    $request->start_date,
                    $request->end_date,
                ])
                ->orWhereBetween('end_date', [
                    $request->start_date,
                    $request->end_date,
                ])
                ->orWhere(function ($subquery) use ($request) {
                    $subquery->where('start_date', '<', $request->start_date)
                             ->where('end_date', '>', $request->end_date);
                });
            })->count();

        if ($runningTransactionCount >= $listing->max_person) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Listing is fully booked',
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        return true;
    }

    public function isAvailable(Store $request)
    {
        $this->_fullyBookedChecker($request);

        return response()->json([
            'success'=> true,
            'message'=> 'Listing is ready to book',
        ]);
    }

    public function store(Store $request)
    {
        $this->_fullyBookedChecker($request);

        $transaction = Transaction::create([
            'start_date'=> $request->start_date,
            'end_date'=> $request->end_date,
            'listing_id'=> $request->listing_id,
            'user_id' => auth()->id(),
        ]);

        $transaction->Listing;

        return response()->json([
            'success'=> true,
            'message'=> 'New Transaction Created',
            'data' => $transaction,
            ]);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id != auth()->id()) {
            return response()->json([
                'success'=> false,
                'message'=> 'Unauthorized']
                , JsonResponse::HTTP_UNAUTHORIZED);
        }

        $transaction->listing;

        return response()->json([
            'success'=> true,
            'message'=> 'get detail transaction',
            'data'=> $transaction,
            ]);
    }
}
