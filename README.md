**# PaketIN**

**## 1. Ringkasan**

PaketIN adalah sistem pengelolaan paket yang dibuat untuk perusahaan ekspedisi milik Pak Dedi. Sistem ini nantinya akan digunakan oleh pemilik dari ekspedisi (manajer), admin, kurir, serta penerima yang ingin melacak barang miliknya. Masalah utama yang terjadi sekarang adalah banyaknya proses pengelolaan paket yang masih manual sehingga banyak proses yang tidak tercatat atau terekap dengan baik dan menyebabkan kerugian, serta penerima harus menunggu admin terlebih dahulu untuk mengetahui posisi paketnya, dan kurir yang membawa terlalu banyak paket sehingga menyebabkan keterlambatan pengantaran. Alur yang saya buat adalah ketika barang pertama kali datang, admin mencatatkan data barang tersebut ke dalam sistem. Setelah data barang masuk, sistem menentukan zona tujuan, membulatkan berat barang ke atas per 1kg, menghitung ongkos kirimnya, lalu membuat nomor resi dengan status diterima. Setelah itu admin mengubah statusnya menjadi transit dan menugaskan kurir untuk mengirim barang tersebut (setiap kurir dibatasi muatannya sebanyak 20 dan kurir yang tidak aktif tidak bisa mengirim barang), dan saat ditugaskan status barang otomatis menjadi dikirim. Kurir menyerahkan paketnya ke penerima dan mengubah statusnya menjadi terkirim. Jika kurir gagal bertemu dengan penerima maka kurir mengubah statusnya menjadi gagal kirim, sehingga admin harus menugaskan ulang. Jika sudah 2 kali gagal dalam pengiriman, maka admin mengubah statusnya menjadi retur dan barang dikembalikan kepada pengirim.

**## 2. Teknologi**

Framework: Laravel 13&#x20;  
Bahasa Pemograman: PHP, JavaScript  
Tampilan: HTML, CSS  
Database: MySQL&#x20;  
Android: Kotlin&#x20;

**## 3. Cara menjalankan server**

Dibuat untuk Windows dengan XAMPP. Pastikan MySQL pada XAMPP menyala.

1\. Pasang PHP 8.3 atau lebih baru, Composer, MySQL, Node.js, dan npm.
2\. Clone repositori, lalu masuk ke folder proyek:

   git clone <URL_REPOSITORY>
   cd PaketIN

3\. Pasang dependency dan buat file `.env`:

   composer install
   copy .env.example .env

   Jika menggunakan PowerShell, jalankan `Copy-Item .env.example .env`.

4\. Buat database bernama `paketin`, kemudian sesuaikan pengaturan database di `.env`:

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=paketin
   DB_USERNAME=root
   DB_PASSWORD=

   Ubah username dan password jika pengaturan MySQL di komputer berbeda.

5\. Isi alamat layanan Pusat Wilayah dan API key yang dibagikan panitia. Contoh pengaturannya:

   ```env
   WILAYAH_URL=https://ukksmkn1katapang.next-it.co.id/ekspedisi
   WILAYAH_API_KEY=lVWMc6V2SXhkeXjRMioax61WUuRhy2VW
   KURIR_PASSWORD_SEMENTARA=Kurir12345
   ```

6\. Buat application key dan jalankan migrasi:

   php artisan key:generate
   php artisan migrate

7\. Impor data awal sekaligus membuat akun staf, tarif, akun kurir, pelanggan, dan paket historis:

   php artisan db:seed

8\. Setelah data CSV masuk, jalankan seeder untuk menyiapkan data demo:

   php artisan db:seed --class=DemoSeeder

9\. Jalankan server:

   php artisan serve --host=0.0.0.0 --port=8000

Jangan menjalankan `php artisan migrate:fresh` pada database yang datanya masih ingin dipakai.

**## 4. Cara memasang APK**

1\. Ambil APK yang sudah dibuild dari folder /apk di repository.
2\. Pindahkan file APK ke HP Android, lalu pasang.
3\. Pastikan HP dan laptop server terhubung ke jaringan Wi-Fi yang sama.
4\. Di laptop, jalankan ipconfig dan cari alamat IPv4 yang dipakai laptop.
5\. Buka aplikasi PaketIN di HP. Pada halaman login, pilih Atur alamat server, lalu isi alamat server laptop, misalnya http://192.168.1.10:8000.
7\. Jika HP belum bisa terhubung, periksa jaringan dan izin Windows Firewall untuk port 8000.

**## 5. Akun uji**

Admin = admin | admin123  
Manajer = manager | manager123  
Kurir = KUR-01 sampai KUR-08 | Kurir12345

Password kurir merupakan password sementara dan harus diganti saat login pertama.  
Password akun yang sudah ada tidak otomatis diubah oleh StafSeeder.

**## 6. Letak berkas**

- Migrasi database | `database/migrations/`
- Seeder utama | `database/seeders/DatabaseSeeder.php`
- Seeder Admin dan Manajer | `database/seeders/StafSeeder.php`
- Seeder tarif | `database/seeders/TarifSeeder.php`
- Seeder Kurir | `database/seeders/KurirSeeder.php`
- Seeder pelanggan | `database/seeders/PelangganSeeder.php`
- Seeder paket historis | `database/seeders/PaketHistorisSeeder.php`
- Seeder data demo | `database/seeders/DemoSeeder.php`
- Data CSV | `database/seeders/data/`
- Pembaca CSV | `app/Services/CsvimportService.php`
- Normalisasi nomor telepon | `app/Support/Telepon.php`
- Validasi nomor telepon | `app/Rules/TeleponValid.php`
- Layanan Pusat Wilayah | `app/Services/Wilayah.php`
- Pengaturan Pusat Wilayah | `config/services.php` dan `.env`
- Route web | `routes/web.php`
- Controller web | `app/Http/Controllers/`
- Model | `app/Models/`
- Service aturan bisnis | `app/Services/`
- Route API Kurir | `routes/api.php`
- Controller API Kurir | `app/Http/Controllers/Api/`
- ERD | `docs/erd/`
- UML (diagram status paket) | `docs/uml/`
- APK untuk wawancara | `apk/`
- Source Android Studio | Folder project Android yang disertakan di repositori

**## 7. Keputusan saya**
Saat paket dibuat, nilai tarif per kg dan ongkir disimpan pada data paket. Jadi, kalau Manajer mengubah tarif, perubahan itu hanya dipakai untuk paket yang dibuat setelahnya. Ongkir paket lama tetap memakai nilai saat paket tersebut dicatat.

Aturan format telepon: Nomor hanya boleh di isi oleh angka, diawali 08, panjangnya 10 sampai 13 digit. Nomor yang berisi huruf, spasi,atau tanda lain, dan kurang dari 10 digit akan ditolak dengan pesan yang jelas.

**## 8. Lembar self-check**

| No. | Butir penerimaan | Klaim (Ya/Tidak) | Langkah uji singkat |
|---:|---|:---:|---|
| 1 | Input paket (pengirim, penerima, kota tujuan, berat, layanan), nomor resi otomatis, unik, reset harian; nomor telepon divalidasi. | Tidak | Generator resi dan perhitungan berat/ongkir sudah diperiksa. Pengujian lengkap melalui form input untuk seluruh variasi nomor telepon serta pergantian tanggal tepat tengah malam belum dibuktikan ulang. |
| 2 | Ongkir otomatis dari zona dan berat (dibulatkan ke atas, minimum 1 kg), Express +50%; ongkir paket lama tidak berubah bila tarif per kg zona diubah. **(Butir inti)** | Ya | Uji Reguler 0,4 kg menjadi 1 kg dan ongkir Rp8.000; Express 2,3 kg menjadi 3 kg dan ongkir Rp36.000. Setelah tarif diubah dari Rp8.000 menjadi Rp9.000, paket lama tetap Rp8.000 dan paket baru memakai tarif baru. |
| 3 | Status berjenjang, loncat ditolak, Terkirim dan Retur final. **(Butir inti)** | Ya | Uji perubahan langsung Diterima ke Dikirim ditolak; alur Diterima → Transit → Dikirim dapat dilakukan. Status Terkirim tidak dapat diubah kembali. |
| 4 | Riwayat setiap perubahan status (waktu, pelaku: Admin atau Kurir, catatan). | Ya | Uji pembuatan paket, perubahan ke Transit, penugasan, dan Gagal Kirim. Riwayat menyimpan waktu, aktor Admin/Kurir, perubahan status, dan catatan yang diberikan. |
| 5 | Gagal kirim maksimal 2 kali; setelah gagal kedua, kirim ulang ditolak dan status berikutnya hanya Retur (diubah Admin). | Ya | Uji dua kali Gagal Kirim. Percobaan penugasan/pengiriman ulang setelah kegagalan kedua ditolak, sedangkan perubahan menjadi Retur diperbolehkan. |
| 6 | Penugasan ke kurir (status menjadi Dikirim), batas 20 paket berstatus Dikirim per kurir pada saat yang sama; kurir nonaktif tidak bisa ditugaskan. | Ya | Uji kurir dengan 20 paket Dikirim: penugasan paket ke-21 ditolak. Uji penugasan kepada kurir nonaktif juga ditolak. |
| 7 | Cek resi publik tanpa login, telepon disamarkan di server. | Ya | Uji Cek Resi untuk nomor resi yang tersedia. Hasil menampilkan nomor telepon tersamarkan dan tidak mengirimkan nomor lengkap pada data yang ditampilkan. |
| 8 | Biaya COD 2% (minimum Rp2.000) dibayar pengirim; setoran COD dicatat Admin per paket (hanya paket COD Terkirim, tidak dua kali); rekap COD per kurir belum/sudah disetor (di laporan Manajer). | Ya | Uji biaya COD Rp50.000 menghasilkan minimum Rp2.000 dan Rp150.000 menghasilkan Rp3.000. Setoran untuk paket COD Terkirim berhasil dicatat, duplikasi ditolak, dan jumlah sudah/belum disetor dicocokkan dengan data uji. |
| 9 | Laporan pengiriman dan pendapatan per zona per periode (menurut tanggal masuk); hak akses: tarif hanya bisa diubah Manajer, laporan hanya Manajer (peran lain ditolak, juga bila halaman dibuka lewat alamat langsung), data pelanggan dan kurir hanya diubah Admin. | Tidak | Uji middleware menunjukkan halaman laporan hanya dapat diakses Manajer dan perubahan data pelanggan/kurir hanya Admin. Namun route `tarif.update` masih mengizinkan Admin, sehingga ketentuan hak akses tarif belum terpenuhi sepenuhnya. |
| 10 | Data awal CSV diimpor: resi ganda menjadi satu, paket tanpa berat bertanda "perlu verifikasi", telepon dirapikan. | Ya | Uji hasil impor: jumlah resi unik diperiksa, paket tanpa berat ditemukan dan ditandai `perlu_verifikasi`, serta nomor telepon pelanggan, kurir, dan penerima diperiksa hasil normalisasinya. |
| 11 | Integrasi Pusat Wilayah (dengan API key), fallback zona manual bila layanan mati atau kota tidak dikenal. | Ya | Uji kondisi layanan/hasil zona tidak tersedia sehingga pencarian mengembalikan zona kosong. Input zona manual dapat digunakan untuk menyimpan paket dan paket diberi penanda zona manual. |
| 12 | Akun kurir lengkap (APK): dibuat Admin, login dengan kode kurir (KUR-01 dst.), password minimal 8 karakter, wajib ganti password saat login pertama, ganti password, lupa password direset Admin (wajib ganti saat login berikutnya), logout. | Tidak | Login kurir, kewajiban ganti password, penggantian password, dan reset password Admin telah diperiksa melalui pengujian backend. Verifikasi logout dengan token yang sama dan pengujian seluruh alur langsung dari APK belum selesai dipastikan. |
| 13 | Kurir (APK) melihat paket berstatus Dikirim yang ditugaskan kepadanya dan mengubahnya menjadi Terkirim atau Gagal Kirim dengan catatan; paket kurir lain tidak bisa dibuka atau diubah. | Tidak | Uji controller menunjukkan kurir lain mendapat 404 saat membuka atau mengubah paket, catatan Gagal Kirim wajib diisi, dan perubahan status oleh pemilik paket berhasil. Namun endpoint daftar paket tanpa filter masih mengembalikan status selain Dikirim; alur tampilan APK belum diverifikasi penuh. |
| 14 | Kurir (APK) melihat paket COD berstatus Terkirim yang diantarnya dan total nilai COD yang belum disetor. | Tidak | Uji respons backend menunjukkan daftar COD dan ringkasan jumlah/nilai belum disetor serta sudah disetor sesuai data database. Tampilan dan pengujian langsung pada APK belum diverifikasi penuh. |

### Fitur opsional

| No. | Fitur opsional | Dikerjakan (Ya/Tidak) |
|---|---|:---:|
| O1 | Berat volumetrik: panjang × lebar × tinggi (cm) ÷ 6000, dipakai bila lebih besar dari berat aktual. | Tidak |
| O2 | Asuransi opsional sebesar 0,5% dari nilai barang. | Tidak |
| O3 | Laporan performa kurir: persentase paket terkirim dan gagal. | Tidak |
| O4 | Penanda paket tertahan lebih dari 3 hari di status Transit. | Tidak |
| O5 | Reset password lewat email menggunakan server email tiruan lokal. | Tidak |

**## 9. Belum selesai / keterbatasan**

1. **Hak akses perubahan tarif:** route `tarif.update` masih menggunakan middleware yang mengizinkan Admin, padahal sesuai soal hanya Manajer yang boleh mengubah tarif. Route ini perlu diperbaiki dan diuji kembali.
2. **Daftar paket Kurir:** endpoint daftar paket API jika dipanggil tanpa filter masih mengembalikan paket dengan beberapa status. Ketentuan soal meminta Kurir hanya melihat paket berstatus `Dikirim`; filter perlu dipastikan berlaku di sisi server dan bukan hanya pada tampilan APK.
3. **Logout Kurir:** pengujian melalui HTTP Kernel belum memberikan hasil token yang konsisten antara token yang dibuat dan token yang dikenali request. Karena itu, logout belum dinyatakan terverifikasi sepenuhnya.
4. **Pengujian APK:** pengujian backend untuk sebagian fungsi Kurir sudah dilakukan, tetapi alur langsung pada perangkat Android—termasuk login, ganti password, daftar paket, perubahan status, daftar COD, total belum disetor, dan logout—masih perlu diverifikasi.
5. **Pengujian lengkap input paket:** mekanisme generator resi dan perhitungan telah diperiksa, tetapi pengujian form input untuk seluruh variasi validasi telepon dan simulasi pergantian tanggal pada saat reset nomor urut belum dibuktikan ulang.

**## 10. Referensi dan bantuan**

ChatGPT: saya gunakan untuk bertanya tentang error, memahami beberapa bagian Laravel dan Kotlin, mencari ide pengujian, Saran kode saya cek lagi dengan source proyek dan pengujian, bukan langsung dipakai begitu saja.  
PHP Artisan Tinker: saya gunakan untuk mengecek route, service, controller, data paket, riwayat, perhitungan COD, dan beberapa respons API.
