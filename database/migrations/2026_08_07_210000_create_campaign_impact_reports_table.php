<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_impact_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('campaign_impact_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_impact_report_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_impact_photos');
        Schema::dropIfExists('campaign_impact_reports');
    }
};
