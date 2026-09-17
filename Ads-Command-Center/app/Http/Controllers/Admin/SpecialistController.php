<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SpecialistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            abort(403);
        }

        $organizationId = $user->organization_id ?? $user->organization?->id;

        $specialists = User::query()
            ->whereHas('organizations', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId)
                  ->wherePivot('role', 'specialist');
            })
            ->with(['organizations' => function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            }])
            ->get(['id', 'name', 'email', 'status']);

        return Inertia::render('admin/specialists/index', [
            'specialists' => $specialists,
            'organization' => Organization::find($organizationId)?->only('id','name'),
        ]);
    }

    public function updateStatus(Request $request, User $user)
    {
        $actor = $request->user();
        if (! $actor->isAdmin()) {
            abort(403);
        }

        $organizationId = $actor->organization_id ?? $actor->organization?->id;

        $request->validate(['status' => 'required|in:active,inactive']);

        // ensure the target user is a member specialist of this organization
        $isMember = DB::table('organization_user')
            ->where('organization_id', $organizationId)
            ->where('user_id', $user->id)
            ->where('role', 'specialist')
            ->exists();

        if (! $isMember) {
            abort(404);
        }

        DB::transaction(function () use ($organizationId, $user, $request) {
            DB::table('organization_user')
                ->where('organization_id', $organizationId)
                ->where('user_id', $user->id)
                ->update(['status' => $request->input('status'), 'updated_at' => now()]);

            // keep users.status in sync
            $user->status = $request->input('status');
            $user->save();
        });

        if ($request->header('X-Inertia')) {
            return redirect()->route('admin.specialists.index')->with('success', 'Specialist updated.');
        }

        return response()->json(['ok' => true]);
    }
}
