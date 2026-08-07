<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('control_number')->unique();
            $table->decimal('amount_released', 12, 2);
            $table->date('released_at');
            $table->foreignId('released_by')->constrained('admins')->cascadeOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('donation_payment_release', function (Blueprint $table) {
            $table->foreignId('payment_release_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donation_id')->unique()->constrained()->cascadeOnDelete();
        });

        DB::table('donations')->where('status', 'paid')->update(['status' => 'confirmed_payment']);
        DB::table('donations')->whereIn('status', ['failed', 'refunded'])->update(['status' => 'cancelled']);
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_payment_release');
        Schema::dropIfExists('payment_releases');

        DB::table('donations')->where('status', 'confirmed_payment')->update(['status' => 'paid']);
    }
};
