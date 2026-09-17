<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'organization_name' => ['required_without:organization', 'string', 'max:255'],
            'organization' => ['sometimes', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($input) {
            // create user first
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => 'admin',
                'status' => 'active',
            ]);

            // create organization and assign owner
            $orgName = $input['organization_name'] ?? $input['organization'];
            $organization = Organization::create([
                'name' => $orgName,
                'owner_id' => $user->id,
                'status' => 'active',
            ]);

            // link user to organization
            $user->organization_id = $organization->id;
            $user->save();

            // create membership pivot
            DB::table('organization_user')->insert([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $user;
        });
    }
}
