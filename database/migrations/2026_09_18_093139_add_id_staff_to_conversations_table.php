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
        Schema::table('conversations', function (Blueprint $table) {
            if (! Schema::hasColumn('conversations', 'id_staff')) {
                $table->unsignedBigInteger('id_staff')->nullable()->after('id_ut_client');
            }
            $table->foreign('id_staff')->references('id_staff')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['id_staff']);
            if (Schema::hasColumn('conversations', 'id_staff')) {
                $table->dropColumn('id_staff');
            }
        });
    }
};
