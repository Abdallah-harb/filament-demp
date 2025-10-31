<?php

use App\Enum\ProductStatusEnum;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('status')->default(ProductStatusEnum::IN_STOCK->value);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->json('attachment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
