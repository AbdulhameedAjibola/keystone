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
        Schema::create('careers', function (Blueprint $table) {
           $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');                
            $table->string('location')->nullable();  
            $table->enum('type', ['full-time','part-time','contract', 'internship'])->nullable();      
            $table->text('description');             
            $table->text('requirements')->nullable();
            $table->text('salary')->nullable();    
            $table->boolean('is_active')->default(true);
            $table->date('application_deadline')->nullable(); 
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
