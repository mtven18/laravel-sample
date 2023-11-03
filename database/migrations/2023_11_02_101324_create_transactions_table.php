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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('from_user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignUuid('to_user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('currency');
            $table->unsignedDecimal('amount', 1000, 2);
            $table->string('status');
            $table->dateTime('completed_at');
            $table->text('failed_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
