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
        Schema::create('metadata', function (Blueprint $table) {
            $table->id();
            $table->string('tenant')->nullable();
            $table->string('entity');
            $table->string('field');
            $table->string('name');
            $table->text('value');
            $table->string('type')->default('string');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('notes')->nullable();
            $table->jsonb('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadata');
    }
};
