<?php

namespace App\Jobs;

use App\Models\ApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class ExecuteApprovedGoogleAdsChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public function __construct(public int $approvalRequestId) {}

    public function handle(): void
    {
        $request = ApprovalRequest::query()->with('account.connection')->findOrFail($this->approvalRequestId);
        if ($request->status !== 'approved') {
            return;
        }

        $request->update(['status' => 'executing']);

        // Each Google Ads mutate must be implemented against its specific official resource/service contract.
        // Never infer a mutation from arbitrary payload data.
        throw new RuntimeException("No executor is registered for approved change type [{$request->type}].");
    }

    public function failed(\Throwable $exception): void
    {
        ApprovalRequest::query()->whereKey($this->approvalRequestId)->where('status', 'executing')->update(['status' => 'failed', 'execution_error' => $exception->getMessage()]);
    }
}
