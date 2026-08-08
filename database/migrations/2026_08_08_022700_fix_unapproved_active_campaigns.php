<?php

use App\Models\Campaign;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Campaign::query()
            ->where('status', Campaign::STATUS_ACTIVE)
            ->whereNull('reviewed_at')
            ->whereNotNull('submitted_at')
            ->update(['status' => Campaign::STATUS_PENDING]);

        Campaign::query()
            ->where('status', Campaign::STATUS_ACTIVE)
            ->whereNull('reviewed_at')
            ->whereNull('submitted_at')
            ->update(['status' => Campaign::STATUS_DRAFT]);
    }

    public function down(): void
    {
        // Irreversible data correction.
    }
};
