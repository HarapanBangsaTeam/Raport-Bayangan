# SISTEM RAPORT BAYANGAN - SMP HARAPAN BANGSA

## 1. Requirement
- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite / MySQL

## 2. Instalasi
Project ini sudah di-setup dengan Laravel 11 dan Laravel Breeze (Tailwind CSS).
Berikut cara instalasi jika di-clone di tempat baru:

## 3. Composer install
```bash
composer install
```

## 4. .env
Copy file `.env.example` ke `.env`, lalu generate key:
```bash
cp .env.example .env
php artisan key:generate
```

## 5. Database
Gunakan SQLite secara default atau ubah pengaturan koneksi di file `.env`.

## 6. php artisan migrate
Jalankan migrasi database:
```bash
php artisan migrate
```

## 7. php artisan db:seed
Jalankan seeder untuk mengisi data dummy admin, sekolah, siswa, pelajaran, dan nilai:
```bash
php artisan db:seed
```

## 8. Storage link
```bash
php artisan storage:link
```

## 9. Cara Menjalankan Server
Jika Anda menggunakan Laragon, akses project melalui:
`http://raport-bayangan.test`

Atau jalankan server bawaan Laravel:
```bash
php artisan serve
npm run dev
```

## 10. Cara Login
Gunakan akun yang telah di-seed:
- **Email:** `admin@harapanbangsa.test`
- **Password:** `password`

## 11. Cara Membuat Siswa
1. Login ke sistem.
2. Masuk ke menu **Siswa**.
3. Klik tombol **Tambah Siswa** (Fungsionalitas form penambahan dapat dikembangkan lebih lanjut di controller, struktur MVC sudah siap).

## 12. Cara Memasukkan Nilai
1. Klik menu **Input Nilai**.
2. Pilih siswa yang ingin diinput nilainya dengan klik **Input Nilai** pada baris siswa tersebut.
3. Masukkan nilai Formative (PR, UH) dan Sumatif (Tertulis, Praktek).
4. Masukkan Keterangan Kehadiran dan Catatan Wali Kelas.
5. Klik **Simpan Nilai** atau **Simpan & Lihat Raport**.

## 13. Cara Generate Raport
1. Klik menu **Raport**.
2. Pilih siswa, klik **Preview** untuk melihat tampilan web.
3. Klik **Excel** untuk mengunduh raport dalam format `.xlsx`. File akan terformat rapi sesuai layout A4 portrait.

## 14. Cara Mengganti Logo Sekolah
Fitur pengaturan sekolah ada di database `school_settings`. Anda dapat memperbarui data `logo` melalui database, dan gambar akan otomatis digunakan di preview dan excel export jika path gambarnya benar.
