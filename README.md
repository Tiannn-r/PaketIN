# PaketIN

## 1. Ringkasan

PaketIN adalah sistem pengelolaan paket yang dibuat untuk perusahaan ekspedisi milik Pak Dedi. Sistem ini nantinya akan digunakan oleh pemilik dari ekspedisi (manajer), admin, kurir, serta penerima yang ingin melacak barang miliknya. Masalah utama yang terjadi sekarang adalah banyaknya proses pengelolaan paket yang masih manual sehingga banyak proses yang tidak tercatat atau terekap dengan baik dan menyebabkan kerugian, serta penerima harus menunggu admin terlebih dahulu untuk mengetahui posisi paketnya, dan kurir yang membawa terlalu banyak paket sehingga menyebabkan keterlambatan pengantaran. Alur yang saya buat adalah ketika barang pertama kali datang, admin mencatatkan data barang tersebut ke dalam sistem. Setelah data barang masuk, sistem menentukan zona tujuan, membulatkan berat barang ke atas per 1kg, menghitung ongkos kirimnya, lalu membuat nomor resi dengan status diterima. Setelah itu admin mengubah statusnya menjadi transit dan menugaskan kurir untuk mengirim barang tersebut (setiap kurir dibatasi muatannya sebanyak 20 dan kurir yang tidak aktif tidak bisa mengirim barang), dan saat ditugaskan status barang otomatis menjadi dikirim. Kurir menyerahkan paketnya ke penerima dan mengubah statusnya menjadi terkirim. Jika kurir gagal bertemu dengan penerima maka kurir mengubah statusnya menjadi gagal kirim, sehingga admin harus menugaskan ulang. Jika sudah 2 kali gagal dalam pengiriman, maka admin mengubah statusnya menjadi retur dan barang dikembalikan kepada pengirim.

## 2. Teknologi

Framework: Laravel 13 
Bahasa Pemograman: PHP, JavaScript
Tampilan: HTML, CSS
Database: MySQL 
Android: Kotlin 

## 3. Cara menjalankan server

Dibuat untuk Windows dengan XAMPP. Pastikan MySQL pada XAMPP menyala.

1. Pasang PHP 8.3 atau lebih baru, Composer, MySQL, Node.js, dan npm.
2. Clone repositori, lalu masuk ke folder proyek:

   git clone <URL_REPOSITORY>
   cd PaketIN

3. Pasang dependency dan buat file `.env`:

   composer install
   copy .env.example .env

   Jika menggunakan PowerShell, jalankan `Copy-Item .env.example .env`.

4. Buat database bernama `paketin`, kemudian sesuaikan pengaturan database di `.env`:

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=paketin
   DB_USERNAME=root
   DB_PASSWORD=

   Ubah username dan password jika pengaturan MySQL di komputer berbeda.

5. Isi alamat layanan Pusat Wilayah dan API key yang dibagikan panitia. Contoh pengaturannya:

   env
   WILAYAH_URL=https://ukksmkn1katapang.next-it.co.id/ekspedisi
   WILAYAH_API_KEY=lVWMc6V2SXhkeXjRMioax61WUuRhy2VW
   KURIR_PASSWORD_SEMENTARA=Kurir12345

6. Buat application key dan jalankan migrasi:

   php artisan key:generate
   php artisan migrate

7. Impor data awal sekaligus membuat akun staf, tarif, akun kurir, pelanggan, dan paket historis:

   php artisan db:seed

8. Setelah data CSV masuk, jalankan seeder untuk menyiapkan data demo:

   php artisan db:seed --class=DemoSeeder

9. Jalankan server:

    php artisan serve --host=0.0.0.0 --port=8000

Jangan menjalankan `php artisan migrate:fresh` pada database yang datanya masih ingin dipakai.

## 4. Cara memasang APK

1. Ambil APK yang sudah dibuild dari folder /apk di repository.
2. Pindahkan file APK ke HP Android, lalu pasang.
3. Pastikan HP dan laptop server terhubung ke jaringan Wi-Fi yang sama.
4. Di laptop, jalankan ipconfig dan cari alamat IPv4 yang dipakai laptop.
5. Buka aplikasi PaketIN di HP. Pada halaman login, pilih Atur alamat server, lalu isi alamat server laptop, misalnya http://192.168.1.10:8000.
7. Jika HP belum bisa terhubung, periksa jaringan dan izin Windows Firewall untuk port 8000.

## 5. Akun uji

Admin = admin | admin123 
Manajer = manager | manager123 
Kurir = KUR-01 sampai KUR-08 | Kurir12345

Password kurir merupakan password sementara dan harus diganti saat login pertama.
Password akun yang sudah ada tidak otomatis diubah oleh StafSeeder.

## 6. Letak berkas

- Migrasi database | `database/migrations/` |
- Seeder utama | `database/seeders/DatabaseSeeder.php` |
- Seeder Admin dan Manajer | `database/seeders/StafSeeder.php` |
- Seeder tarif | `database/seeders/TarifSeeder.php` |
- Seeder Kurir | `database/seeders/KurirSeeder.php` |
- Seeder pelanggan | `database/seeders/PelangganSeeder.php` |
- Seeder paket historis | `database/seeders/PaketHistorisSeeder.php` |
- Seeder data demo | `database/seeders/DemoSeeder.php` |
- Data CSV | `database/seeders/data/` |
- Pembaca CSV | `app/Services/CsvimportService.php` |
- Normalisasi nomor telepon | `app/Support/Telepon.php` |
- Validasi nomor telepon | `app/Rules/TeleponValid.php` |
- Layanan Pusat Wilayah | `app/Services/Wilayah.php` |
- Pengaturan Pusat Wilayah | `config/services.php` dan `.env` |
- Route web | `routes/web.php` |
- Controller web | `app/Http/Controllers/` |
- Model | `app/Models/` |
- Service aturan bisnis | `app/Services/` |
- Route API Kurir | `routes/api.php` |
- Controller API Kurir | `app/Http/Controllers/Api/` |
- ERD | `docs/erd/` |
- UML (diagram status paket) | `docs/uml/` |
- APK untuk wawancara | `apk/` |
- Source Android Studio | Folder project Android yang disertakan di repositori |

## 7. Keputusan saya
Saat paket dibuat, nilai tarif per kg dan ongkir disimpan pada data paket. Jadi, kalau Manajer mengubah tarif, perubahan itu hanya dipakai untuk paket yang dibuat setelahnya. Ongkir paket lama tetap memakai nilai saat paket tersebut dicatat.

Aturan format telepon: Nomor hanya boleh di isi oleh angka, diawali 08, panjangnya 10 sampai 13 digit. Nomor yang berisi huruf, spasi,atau tanda lain, dan kurang dari 10 digit akan ditolak dengan pesan yang jelas.

## 8. Lembar self-check
(menyusul: tabel 14 butir)

## 9. Belum selesai / keterbatasan
(menyusul)

## 10. Referensi dan bantuan

ChatGPT: saya gunakan untuk bertanya tentang error, memahami beberapa bagian Laravel dan Kotlin, mencari ide pengujian, Saran kode saya cek lagi dengan source proyek dan pengujian, bukan langsung dipakai begitu saja.
PHP Artisan Tinker: saya gunakan untuk mengecek route, service, controller, data paket, riwayat, perhitungan COD, dan beberapa respons API.