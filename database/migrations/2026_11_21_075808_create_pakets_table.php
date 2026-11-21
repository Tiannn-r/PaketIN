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
        Schema::create('paket', function (Blueprint $table) {
    $table->id();
    $table->string('no_resi', 20)->unique();
    $table->foreignId('id_pelanggan')->constrained('pelanggan');
    $table->foreignId('id_kurir')->nullable()->constrained('kurir');
    $table->foreignId('dibuat_oleh')->nullable()->constrained('users');
    $table->string('nama_penerima');
    $table->string('telepon_penerima', 20);
    $table->text('alamat_penerima')->nullable();
    $table->string('kota_tujuan');
    $table->unsignedTinyInteger('zona')->nullable();
    $table->boolean('status_zona')->default(false);
    $table->decimal('berat_asli', 8, 2)->nullable();
    $table->unsignedInteger('berat_bulat')->nullable();
    $table->enum('layanan', ['Reguler', 'Express']);
    $table->unsignedInteger('tarif_per_kg')->nullable();
    $table->unsignedInteger('ongkir')->nullable();
    $table->boolean('status_pembayaran')->default(false);
    $table->unsignedInteger('nilai_cod')->nullable();
    $table->unsignedInteger('biaya_cod')->default(0);
    $table->enum('status', ['Diterima', 'Transit', 'Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'])->default('Diterima');
    $table->unsignedTinyInteger('jumlah_gagal')->default(0);
    $table->boolean('perlu_verifikasi')->default(false);
    $table->date('tanggal_masuk');
    $table->timestamps();

    $table->index('status');
    $table->index('tanggal_masuk');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakets');
    }
};
