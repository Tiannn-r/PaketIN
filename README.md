# PaketIN

## 1. Ringkasan
(tempel ringkasan yang sudah kita rapikan)

## 2. Teknologi
(tempel bagian teknologi yang sudah jadi)

## 3. Cara menjalankan server
Dibuat untuk Windows dengan XAMPP.
1. Salin repositori, lalu `composer install`
2. Salin `.env.example` menjadi `.env`, atur database dan alamat serta API key Pusat Wilayah
3. `php artisan key:generate`
4. Buat database `paketin` di phpMyAdmin
5. `php artisan migrate`
6. Impor data awal CSV (perintah menyusul)
7. Seeder: `php artisan db:seed`
8. `php artisan serve`

## 4. Cara memasang APK
(menyusul)

## 5. Akun uji
| Peran | Username | Password |
|---|---|---|
| Admin | admin | admin12345 |
| Manajer | manajer | manajer12345 |
| Kurir | (menyusul) | (menyusul) |

## 6. Letak berkas
(menyusul: migrasi, impor CSV, seeder demo)

## 7. Keputusan saya
Cara menyimpan harga: Tarif per kg dan ongkir disimpan di tabel paket saat paket di input. Laporan pendapatan menjumlahkan kolom ongkir itu, jadi kalau manager mengubah tarif, maka paket lama tidak akan ikut berubah

Aturan format telepon: Nomor hanya boleh di isi oleh angka, diawali 08, panjangnya 10 sampai 13 digit. Nomor yang berisi huruf, spasi,atau tanda lain, dan kurang dari 10 digit akan ditolak dengan pesan yang jelas.

## 8. Lembar self-check
(menyusul: tabel 14 butir)

## 9. Belum selesai / keterbatasan
(menyusul)

## 10. Referensi dan bantuan
(menyusul)  