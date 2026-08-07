<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('campaign_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('campaign_document_types')->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->timestamps();

            $table->unique(['campaign_id', 'document_type_id']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('ends_at');
            $table->foreignId('reviewed_by')->nullable()->after('submitted_at')->constrained('admins')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('rejection_reason')->nullable()->after('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['submitted_at', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
        });

        Schema::dropIfExists('campaign_documents');
        Schema::dropIfExists('campaign_document_types');
    }
};
