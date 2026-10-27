# Antigravity Agent Guidelines: Deployment & Environment Architecture

Dokumen ini adalah instruksi kerja dan batasan sistem untuk agen AI yang mengelola proyek ini di lingkungan hosting Antigravity/cPanel.

---

## 1. Spesifikasi Teknis Proyek

* **Framework**: Laravel 13
* **PHP Runtime**: PHP 8.3+ / 8.4 (sesuai dependensi Laravel 13)
* **Database Engine**: PostgreSQL 13 / MariaDB 10.13 / MySQL
* **User/Home Directory**: `/home/fivgoweb/`
* **Target Domain**: `harapanbangsa.my.id`
* **Target Public Webroot**: `/home/fivgoweb/harapanbangsa.my.id/`
* **Core Application Root**: `/home/fivgoweb/repositories/Raport-Bayangan/`

---

## 2. Struktur Arsitektur & Keamanan Direktori

> **PENTING**: Demi alasan keamanan, folder inti Laravel (`app/`, `config/`, `vendor/`, `.env`, dll.) **TIDAK BOLEH** ditempatkan langsung di dalam webroot publik (`harapanbangsa.my.id`).

Agen wajib mengikuti pola pemisahan direktori berikut:

1. **Backend / Core Repo**:
   Tersimpan pada direktori non-publik:
   `/home/fivgoweb/repositories/Raport-Bayangan`

2. **Webroot Publik**:
   Direktori `/home/fivgoweb/harapanbangsa.my.id/` hanya berisi aset dari folder `public/` milik Laravel (seperti `index.php`, `.htaccess`, `robots.txt`, file build CSS/JS).

---

## 3. Konfigurasi Deployment Otomatis (`.cpanel.yml`)

Setiap kali melakukan pembaruan skrip deployment di repository root, agen harus memastikan file `.cpanel.yml` menyalin konten folder `public` ke direktori webroot domain:

```yaml
---
deployment:
  tasks:
    - export REPO_DIR=/home/fivgoweb/repositories/Raport-Bayangan
    - export WEBROOT=/home/fivgoweb/harapanbangsa.my.id
    
    # 1. Update symlink storage jika belum ada
    - /bin/ln -sfn $REPO_DIR/storage/app/public $WEBROOT/storage
    
    # 2. Sinkronkan hanya isi folder public Laravel ke domain webroot
    - /bin/rsync -av --delete --exclude='.well-known' --exclude='cgi-bin' $REPO_DIR/public/ $WEBROOT/
    
    # 3. Jalankan optimasi Laravel
    - /usr/local/bin/php $REPO_DIR/artisan config:cache
    - /usr/local/bin/php $REPO_DIR/artisan route:cache
    - /usr/local/bin/php $REPO_DIR/artisan view:cache