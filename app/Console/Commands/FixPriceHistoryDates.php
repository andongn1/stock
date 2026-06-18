<?php

namespace App\Console\Commands;

use App\Models\PriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FixPriceHistoryDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price-history:fix-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix price history dates to ensure valid_from is not in the future and valid_to is properly set';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Fixing price history dates...');
        
        $now = Carbon::now();
        $fixedCount = 0;
        $alreadyValidCount = 0;
        
        // Get all price histories
        $priceHistories = PriceHistory::all();
        
        $this->line("Found {$priceHistories->count()} price history records.");
        
        foreach ($priceHistories as $priceHistory) {
            $needsUpdate = false;
            
            // Fix valid_from: ensure it's not in the future
            $validFrom = Carbon::parse($priceHistory->valid_from);
            if ($validFrom->isAfter($now)) {
                $priceHistory->valid_from = $now->format('Y-m-d');
                $needsUpdate = true;
                $this->line("Fixed valid_from for price ID {$priceHistory->id} (was: {$validFrom->format('Y-m-d')})");
            }
            
            // Fix valid_to: ensure it's not in the future if set
            if ($priceHistory->valid_to) {
                $validTo = Carbon::parse($priceHistory->valid_to);
                if ($validTo->isAfter($now)) {
                    $priceHistory->valid_to = $now->format('Y-m-d');
                    $needsUpdate = true;
                    $this->line("Fixed valid_to for price ID {$priceHistory->id} (was: {$validTo->format('Y-m-d')})");
                }
            }
            
            if ($needsUpdate) {
                $priceHistory->save();
                $fixedCount++;
            } else {
                $alreadyValidCount++;
            }
        }
        
        $this->info("Done! Fixed {$fixedCount} records, {$alreadyValidCount} were already valid.");
    }
}
