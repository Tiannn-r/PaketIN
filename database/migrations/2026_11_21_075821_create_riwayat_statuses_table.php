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
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paket')->constrained('paket');
            $table->string('status_dari')->nullable();
            $table->string('status_ke');
            $table->string('tipe_pelaku', 10);
            $table->unsignedBigInteger('id_pelaku')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_statuses');
    }
};
