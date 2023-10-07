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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tagline')->nullable();
            $table->text('about')->nullable();
            $table->point('location')->nullable();
            $table->string('formatted_address')->nullable();
            $table->boolean('available_to_hire')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tagline');
            $table->dropColumn('about');
            $table->dropColumn('location');
            $table->dropColumn('formatted_address');
            $table->dropColumn('available_to_hire');
        });
    }
};
