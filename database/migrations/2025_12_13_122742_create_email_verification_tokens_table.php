<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\Expr\Cast;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_verification_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('guard');
            $table->string('email')->unique();
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['email', 'guard']);
        });
    }

    protected function casts(){
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_verification_tokens');
    }
};
