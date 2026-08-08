<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_featured');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_featured']);
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'status']);
        });
    }
};
