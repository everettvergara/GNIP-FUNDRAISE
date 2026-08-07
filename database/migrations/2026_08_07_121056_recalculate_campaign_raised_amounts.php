<?php

use App\Models\Campaign;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Campaign::query()->each(fn (Campaign $campaign) => $campaign->recalculateRaisedAmount());
    }

    public function down(): void
    {
        // Raised amounts are derived from donations and cannot be restored.
    }
};
