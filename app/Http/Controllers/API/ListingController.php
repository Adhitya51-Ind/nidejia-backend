<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Listing;

class ListingController extends Controller
{
    public function index(): JsonResponse
    {
       $listing = Listing::withCount('transaction')->orderBy('transaction_count', 'desc')->paginate();

       return response()->json([
        'succes'=> true,
        'massage'=> 'Get All listing',
        'data'=> $listing
       ]);
    }

    public function show(Listing $listing): JsonResponse
    {
        return response()->json([
            'success'=> true,
            'massage'=> 'Get All Listings',
            'data'=> $listing
        ]);
    }
    
}

