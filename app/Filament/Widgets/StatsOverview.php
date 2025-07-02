<?php

namespace App\Filament\Widgets;

use App\Models\listing;
use App\Models\Transaction;
use Filament\Support\Formatting\Number;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    private function getPercentage(int $from , int $to)
    {
        return $to - $from / ( $to + $from *2) * 100;
    }

    protected function getStats(): array
    {
        $newListing = Listing::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at',Carbon::now()->year)->count();    
        $Transaction = Transaction::whereStatus('approved')->    whereMonth('created_at', Carbon::now()->month)->whereYear('created_at',Carbon::now()->year); 
        $prevTransaction =Transaction::whereStatus('approved')->    whereMonth('created_at', Carbon::now()->subMonth()->month)->whereYear('created_at',Carbon::now()->year);
        $TransactionPercentage = $this->getPercentage($prevTransaction->count(), $Transaction->count());
        $revenuePercentage = $this->getPercentage($prevTransaction->sum('total_price'), $Transaction->sum('total_price'));

        return [
            Stat::make('New Listing Of The Month', $newListing),
            Stat::make('Transaction Of the Month', $Transaction->count())
            ->description($TransactionPercentage > 0 ? "{$TransactionPercentage}% Increased" : "{$TransactionPercentage}% Decreased" )
            ->descriptionIcon($TransactionPercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down' )
            ->color($TransactionPercentage > 0 ? 'success' : 'danger' ),
            Stat::make('Revenue Of the Month', '$' . number_format($Transaction->sum('total_price'), 2))
    ->description($revenuePercentage > 0 ? "{$revenuePercentage}% Increased" : "{$revenuePercentage}% Decreased")
    ->descriptionIcon($revenuePercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
    ->color($revenuePercentage > 0 ? 'success' : 'danger'),
        ];
    }
}
