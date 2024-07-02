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
        Schema::table('saving_account_categories', function (Blueprint $table) {
            $table->text('icon')->nullable()->change();
            $table->text('color')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saving_account_categories', function (Blueprint $table) {
            $table->text('icon')->change();
            $table->text('color')->change();
        });
    }
};
