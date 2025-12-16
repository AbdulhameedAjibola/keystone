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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade')->onUpdate('cascade');
             $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->enum('property_type', ['apartment', 'house', 'shortlet', 'penthouse', 'land', 'commercial']);
            $table->enum('listing_type', ['sale', 'rent']);
            $table->enum('status', ['available', 'sold', 'unavailable'])->default('available');
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('size')->nullable(); 
            $table->string('address');
            $table->string('area')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->timestamps();

            $table->index(['price', 'city', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
