<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Commande sans compte : le client est identifié par ses coordonnées, et suit sa commande
     * grâce à un lien secret. La commande n'est plus supprimée avec le compte client,
     * car les pièces de vente doivent être conservées 10 ans (droit comptable OHADA).
     */
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['id_ut']);
        });

        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ut')->nullable()->change();
            $table->foreign('id_ut')->references('id')->on('users')->nullOnDelete();

            $table->string('client_nom', 120)->nullable()->after('id_ut');
            $table->string('client_telephone', 30)->nullable()->after('client_nom');
            $table->string('client_email')->nullable()->after('client_telephone');
            $table->string('jeton_suivi', 64)->nullable()->unique()->after('client_email');
        });

        DB::table('commandes')->orderBy('id')->each(function (object $commande): void {
            $client = DB::table('users')->where('id', $commande->id_ut)->first();

            DB::table('commandes')->where('id', $commande->id)->update([
                'jeton_suivi' => Str::random(40),
                'client_nom' => $client ? trim($client->prenom.' '.$client->nom) : null,
                'client_telephone' => $client?->tel,
                'client_email' => $client?->email,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropUnique(['jeton_suivi']);
            $table->dropColumn(['client_nom', 'client_telephone', 'client_email', 'jeton_suivi']);
            $table->dropForeign(['id_ut']);
        });

        Schema::table('commandes', function (Blueprint $table) {
            $table->foreign('id_ut')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
