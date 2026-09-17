<?php

namespace App\Http\Controllers;

use App\Models\OrganizationInvitation;
use App\Models\User;
use App\Mail\OrganizationInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrganizationInvitationController extends Controller
{
    public function store(\App\Http\Requests\InviteSpecialistRequest $request)
    {
        $data = $request->validated();

        // generate a secure raw token and store only its hash
        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        // invitation is always for the admin's current organization and specialist role
        $organizationId = $request->user()->organization_id ?? $request->user()->organization?->id;

        $invitation = OrganizationInvitation::create([
            'organization_id' => $organizationId,
            'email' => $data['email'],
            'role' => 'specialist',
            'invited_by' => $request->user()->id,
            'expires_at' => now()->addHours(48),
            // store a safe placeholder for the legacy `token` column (don't store raw token)
            'token' => $tokenHash,
            'token_hash' => $tokenHash,
        ]);

        // Send invitation email containing the raw token (fallback to logging on failure)
        try {
            Mail::to($invitation->email)->send(new OrganizationInvitationMail($invitation, $rawToken));
        } catch (\Throwable $e) {
            Log::error('Failed to send invitation email', ['error' => $e->getMessage(), 'invitation_id' => $invitation->id]);
        }

        // If this was an Inertia request, return an Inertia-friendly redirect
        if ($request->header('X-Inertia')) {
            return redirect()->route('admin.invitations.invite')->with('success', 'Invitation sent.');
        }

        return response()->json(['invitation' => $invitation], 201);
    }

    public function acceptForm($token)
    {
        $hash = hash('sha256', $token);
        $invitation = OrganizationInvitation::where('token_hash', $hash)->firstOrFail();

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            abort(410, 'Invitation expired');
        }

        return inertia('auth/invitation-accept', [
            'email' => $invitation->email,
            'token' => $token,
            'organization' => $invitation->organization->name,
        ]);
    }

    public function accept(Request $request, $token)
    {
        $hash = hash('sha256', $token);
        $invitation = OrganizationInvitation::where('token_hash', $hash)->firstOrFail();

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return redirect()->route('home')->withErrors('Invitation expired');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed'],
        ]);
        // If a user with this email already exists, show friendly error per Laravel docs
        if (User::where('email', $invitation->email)->exists()) {
            // Redirect to login with an explanatory message so the user can sign in or reset password
            return redirect()->route('login')->withErrors([
                'email' => 'An account with this email already exists. Please login or request a password reset.',
            ]);
        }

        return DB::transaction(function () use ($invitation, $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => $data['password'],
                'role' => $invitation->role,
                'status' => 'active',
            ]);

            // attach to organization
            DB::table('organization_user')->insert([
                'organization_id' => $invitation->organization_id,
                'user_id' => $user->id,
                'role' => $invitation->role,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $invitation->accepted_at = now();
            $invitation->save();

            return redirect()->route('login')->with('status', 'Invitation accepted. Please login.');
        });
    }
}
