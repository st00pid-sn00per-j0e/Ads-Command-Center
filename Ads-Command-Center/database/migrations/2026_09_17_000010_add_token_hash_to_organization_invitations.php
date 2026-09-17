<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_invitations', function (Blueprint $table) {
            $table->string('token_hash')->nullable()->unique()->after('role');
        });

        // Backfill token_hash from existing token values if present
        try {
            $invitations = DB::table('organization_invitations')->select('id', 'token')->get();
            foreach ($invitations as $inv) {
                if (! empty($inv->token)) {
                    DB::table('organization_invitations')->where('id', $inv->id)
                        ->update(['token_hash' => hash('sha256', $inv->token)]);
                }
            }

            Schema::table('organization_invitations', function (Blueprint $table) {
                if (Schema::hasColumn('organization_invitations', 'token')) {
                    $table->dropColumn('token');
                }
            });
        } catch (\Throwable $e) {
            // In some environments (sqlite) dropping columns may not be supported; leave token if drop fails.
        }
    }

    public function down(): void
    {
        Schema::table('organization_invitations', function (Blueprint $table) {
            if (! Schema::hasColumn('organization_invitations', 'token')) {
                $table->string('token')->unique()->after('role');
            }
            if (Schema::hasColumn('organization_invitations', 'token_hash')) {
                $table->dropColumn('token_hash');
            }
        });
    }
};
