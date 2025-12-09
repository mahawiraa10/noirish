## 🚀 Fitur Utama

-   **Tampilan Modern**: Desain responsif menggunakan Tailwind CSS & Alpine.js.
-   **Sistem Keranjang**: Kelola belanjaan dengan validasi stok otomatis.
-   **Checkout & Pembayaran**: Terintegrasi dengan **Midtrans** (Bisa bayar pake QRIS, VA, E-Wallet, dll).
-   **Lacak Pesanan**: Fitur tracking order buat customer tanpa perlu login.
-   **Dashboard Admin**:
    -   Laporan Penjualan Bulanan (Auto-reset setiap bulan).
    -   Metrik Pelanggan Aktif.
    -   Manajemen Produk, Kategori, dan Stok.
    -   Proses Order (Update Resi & Status).
-   **FAQ**: Halaman tanya jawab interaktif.

## 🛠 Teknologi yang Dipakai

-   **Backend**: Laravel 10/11
-   **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
-   **Database**: MySQL
-   **Payment Gateway**: Midtrans Snap

## ⚙️ Cara Install & Menjalankan Project

Ikuti langkah-langkah berikut secara berurutan:

### 1. Clone Repository
Download source code-nya ke komputer lu:
```bash
git clone [https://github.com/mahawiraa10/noirish.git](https://github.com/mahawiraa10/noirish.git)
cd noirish
````

### 2\. Install Library (Dependencies)

Install semua paket PHP dan JavaScript yang dibutuhkan:

```bash
composer install
npm install
```

### 3\. Atur Environment (.env)

Duplikat file `.env.example` lalu ubah namanya menjadi `.env`:

```bash
cp .env.example .env
```

Buka file `.env` tersebut dan sesuaikan konfigurasi Database & Midtrans:

```env
DB_DATABASE=noirish
DB_USERNAME=root
DB_PASSWORD=

# Masukkan Server Key & Client Key dari Dashboard Midtrans Sandbox
MIDTRANS_SERVER_KEY=masukkan_server_key_disini
MIDTRANS_CLIENT_KEY=masukkan_client_key_disini
MIDTRANS_IS_PRODUCTION=false
```

### 4\. Generate Key Aplikasi

Buat kunci enkripsi baru untuk aplikasi:

```bash
php artisan key:generate
```

### 5\. Setup Database

Pastikan lu sudah membuat database kosong bernama `noirish` di MySQL, lalu jalankan perintah ini untuk mengisi tabel dan data awal:

```bash
php artisan migrate:fresh --seed
```

### 6\. Link Storage Gambar

Agar gambar produk bisa muncul di browser:

```bash
php artisan storage:link
```

### 7\. Jalankan Aplikasi

Buka dua terminal berbeda untuk menjalankan server:

**Terminal 1 (Backend):**

```bash
php artisan serve
```

**Terminal 2 (Frontend Assets):**

```bash
npm run dev
```

Website sekarang bisa diakses di: `http://127.0.0.1:8000`

-----

## 🔐 Akun Login (Default)

Gunakan akun berikut untuk masuk ke sistem:

**Akun Admin:**

  - **Email**: admin@noirish.com
  - **Password**: password


## 📄 Lisensi

Project ini open-source di bawah lisensi [MIT license](https://opensource.org/licenses/MIT).
