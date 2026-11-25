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
    Schema::create('wishlists', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('cheapshark_game_id');
        $table->string('title');
        $table->text('thumb_url')->nullable();
        $table->string('store_name')->default('Steam');
        $table->decimal('normal_price_usd', 8, 2);
        $table->decimal('sale_price_usd', 8, 2);
        $table->decimal('cheapest_ever_usd', 8, 2);
        $table->decimal('target_price_idr', 15, 2);
        $table->enum('status', ['wait', 'buy_now', 'purchased'])->default('wait');
        $table->longText('specs_note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
