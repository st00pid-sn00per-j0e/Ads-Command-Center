<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApprovalRequest;
use App\Jobs\ExecuteApprovedGoogleAdsChange;
use App\Models\ApprovalRequest;
use App\Models\GoogleAdsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovalRequestController extends Controller
{
    public function store(StoreApprovalRequest $request): RedirectResponse
    {
        $account = GoogleAdsAccount::query()->with('connection')->findOrFail($request->integer('google_ads_account_id'));
        Gate::authorize('view', $account);

        $approval = ApprovalRequest::create([...$request->validated(), 'organization_id' => $request->user()->organization_id, 'requested_by' => $request->user()->id, 'status' => 'pending', 'submitted_at' => now()]);

        return back()->with('success', "Change request #{$approval->id} submitted for approval.");
    }

    public function approve(Request $request, ApprovalRequest $approval): RedirectResponse
    {
        Gate::authorize('approve', $approval);
        $approval->update(['status' => 'approved', 'approved_by' => $request->user()->id, 'approved_at' => now(), 'admin_comment' => $request->string('admin_comment')->toString() ?: null]);
        ExecuteApprovedGoogleAdsChange::dispatch($approval->id);

        return back()->with('success', "Change request #{$approval->id} approved and queued.");
    }

    public function reject(Request $request, ApprovalRequest $approval): RedirectResponse
    {
        Gate::authorize('approve', $approval);
        $approval->update(['status' => 'rejected', 'approved_by' => $request->user()->id, 'rejected_at' => now(), 'admin_comment' => $request->validate(['admin_comment' => ['required', 'string', 'max:2000']])['admin_comment']]);

        return back()->with('success', "Change request #{$approval->id} rejected.");
    }
}
