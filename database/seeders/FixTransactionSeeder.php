<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Listing;

class FixTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactions = Transaction::all();

        foreach ($transactions as $transaction) {
            $transaction->user_id = User::inRandomOrder()->first()->id;
            $transaction->listing_id = Listing::inRandomOrder()->first()->id;
            $transaction->save();
        }
    }
}
