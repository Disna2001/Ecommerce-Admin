<?php

namespace App\Console\Commands;

use App\Services\Promotions\DiscountOrchestrator;
use Illuminate\Console\Command;

class OrchestrateDailyDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotions:orchestrate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Orchestrate margin-protected daily discounts for the storefront';

    /**
     * Execute the console command.
     */
    public function handle(DiscountOrchestrator $orchestrator)
    {
        $this->info('Initializing Daily Discount Orchestration Protocol...');
        
        $count = $orchestrator->generateDailyDiscounts();
        
        if ($count > 0) {
            $this->info("Successfully synchronized {$count} daily discounts to the registry.");
        } else {
            $this->warn('Registry orchestration complete. No qualifying discounts were identified for today.');
        }

        return Command::SUCCESS;
    }
}
