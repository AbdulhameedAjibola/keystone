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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->integer('mediable_id')->unsigned();
            $table->string('mediable_type');
            $table->string('public_id')->unique();
            $table->string('url');
            $table->enum('type', ['image','video','document'])->nullable();     
            $table->string('format')->nullable();   
            $table->integer('size')->nullable(); 
            $table->string('collection')->nullable();   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
