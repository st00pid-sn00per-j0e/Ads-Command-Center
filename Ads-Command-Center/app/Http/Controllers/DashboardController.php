<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\CampaignMetric;
use App\Models\GoogleAdsAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $accounts = GoogleAdsAccount::query()->whereHas('connection', fn ($query) => $query->where('organization_id', $user->organization_id));
        if (! $user->isAdmin()) {
            $accounts->whereHas('specialists', fn ($query) => $query
                ->whereKey($user)
                ->where('google_ads_account_specialist.status', 'active'));
        }

        $accountIds = $accounts->pluck('id');
        $metrics = CampaignMetric::query()->whereHas('campaign', fn ($query) => $query->whereIn('google_ads_account_id', $accountIds));

        return Inertia::render('dashboard', [
            'scope' => $user->isAdmin() ? 'Organization' : 'My assigned accounts',
            'summary' => [
                'accounts' => $accountIds->count(),
                'spendMicros' => (int) $metrics->sum('cost_micros'),
                'impressions' => (int) $metrics->sum('impressions'),
                'clicks' => (int) $metrics->sum('clicks'),
                'conversions' => (float) $metrics->sum('conversions'),
                'pendingApprovals' => ApprovalRequest::query()->where('organization_id', $user->organization_id)->where('status', 'pending')->when(! $user->isAdmin(), fn ($query) => $query->where('requested_by', $user->id))->count(),
            ],
        ]);
    }
}
