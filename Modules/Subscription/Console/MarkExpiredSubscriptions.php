<?php

namespace Modules\Subscription\Console;

use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\Entities\Subscription;

class MarkExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'subscription:mark-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    public function __construct(private WalletService $walletService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('[Subscription] Mark expired subscriptions');

        $subscriptions = Subscription::where('status', 1)
            ->where('is_canceled', 0)
            ->where('expiry_date', '<', now())
            ->get();

        if ($subscriptions->isNotEmpty()) {
            DB::transaction(function () use ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    $user = $subscription->subscriber;

                    $subscription->update(['status' => 0]);

                    if ($subscription->remaining_credits > 0) {
                        $this->walletService->deductCredits(
                            user: $user,
                            amount: $subscription->remaining_credits,
                            transaction_type: 'deduction',
                            reference: 'sub:' . $subscription->id,
                            details: 'Expired credits',
                        );
                    }
                }
            });
        }


        $this->info('[Subscription] Mark expired subscriptions finished.');
    }
}
