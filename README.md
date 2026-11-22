# PaketIN

## 1. Ringkasan
(tempel ringkasan yang sudah kita rapikan)

## 2. Teknologi
(tempel bagian teknologi yang sudah jadi)

## 3. Cara menjalankan server

Dibuat untuk Windows dengan XAMPP.

1. Salin repositori, lalu jalankan `composer install`.
2. Salin `.env.example` menjadi `.env`.
3. Atur koneksi database dan konfigurasi API Pusat Wilayah.
4. Jalankan `php artisan key:generate`.
5. Buat database `paketin` di phpMyAdmin.
6. Jalankan `php artisan migrate`.
7. Import data awal menggunakan `php artisan db:seed`.
8. Jalankan data demonstrasi menggunakan `php artisan db:seed --class=DemoSeeder`.
9. Jalankan `php artisan serve`.

Catatan: DemoSeeder dijalankan terpisah dari import data awal. Jangan menggunakan `migrate:fresh` pada database yang sudah berisi data.

## 4. Cara memasang APK
(menyusul)

## 5. Akun uji

| Peran | Username | Password awal |
|---|---|---|
| Admin | admin | admin123 |
| Manajer | manager | manager123 |
| Kurir | KUR-01 sampai KUR-08 | Kurir12345 |

Password kurir merupakan password sementara dan harus diganti saat login pertama.
Password akun yang sudah ada tidak otomatis diubah oleh StafSeeder.

## 6. Letak berkas

- Data CSV: `database/seeders/data/`
- Pembaca CSV: `app/Services/CsvimportService.php`
- Normalisasi telepon: `app/Support/Telepon.php`
- API wilayah: `app/Services/Wilayah.php`
- Import kurir: `database/seeders/KurirSeeder.php`
- Import pelanggan: `database/seeders/PelangganSeeder.php`
- Import paket historis: `database/seeders/PaketHistorisSeeder.php`
- Pengaturan urutan import: `database/seeders/DatabaseSeeder.php`
- Data demonstrasi: `database/seeders/DemoSeeder.php`

## 7. Keputusan saya
Cara menyimpan harga: Tarif per kg dan ongkir disimpan di tabel paket saat paket di input. Laporan pendapatan menjumlahkan kolom ongkir itu, jadi kalau manager mengubah tarif, maka paket lama tidak akan ikut berubah

Aturan format telepon: Nomor hanya boleh di isi oleh angka, diawali 08, panjangnya 10 sampai 13 digit. Nomor yang berisi huruf, spasi,atau tanda lain, dan kurang dari 10 digit akan ditolak dengan pesan yang jelas.

## 8. Lembar self-check
(menyusul: tabel 14 butir)

## 9. Belum selesai / keterbatasan
(menyusul)

## 10. Referensi dan bantuan
(menyusul)  