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
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // id_msg
            $table->text('contenu');
            $table->dateTime('date_envoi');
            $table->boolean('lu')->default(false);

            // Clés étrangères
            $table->foreignId('id_conv')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('id_ut_expediteur')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
