# Schedule API - Technical Test Back End Developer

Repository ini berisi solusi untuk Technical Test **Back End Developer Intern** di **PT Media Antar Nusa**. Aplikasi ini dibangun menggunakan **Laravel** untuk menghitung jadwal shift karyawan berdasarkan pola berulang yang dinamis, lengkap dengan fitur Export CSV dan Unit Testing.

## 📋 Fitur Utama

Aplikasi ini menyelesaikan 4 poin utama tantangan:

1.  **Cek Jadwal Spesifik:** API untuk memeriksa shift user tertentu pada tanggal tertentu (memperhitungkan offset hari).
2.  **List Jadwal:** Menampilkan daftar shift semua karyawan dalam rentang tanggal tertentu (JSON).
3.  **Export CSV:** Mengunduh jadwal dalam format Pivot Table (Tanggal sebagai Header Kolom).
4.  **Unit Testing:** Pengujian otomatis untuk memastikan logika algoritma shift (Modulo Arithmetic) berjalan akurat.

## 🛠 Teknologi

-   **Framework:** Laravel 10/11
-   **Language:** PHP 8.x
-   **Database:** MySQL
-   **Architecture:** Clean Controller & Service Logic

## 🚀 Cara Install (Installation)

Ikuti langkah ini untuk menjalankan proyek di komputer lokal:

### 1. Clone Repository

```bash
git clone [https://github.com/USERNAME_GITHUB_KAMU/schedule-api.git](https://github.com/USERNAME_GITHUB_KAMU/schedule-api.git)
cd schedule-api
```

### 2. Install Dependency

```bash
composer install
```

### 3. Setup Environment

Salin file `.env.example`, ubah menjadi `.env`, dan sesuaikan database.

```bash
cp .env.example .env
```

### 4. Generate Key & Migrasi Data

Aplikasi ini menggunakan Seeder untuk memasukkan data karyawan (Ahmad, Widi, Yono, Yohan) dan pola shift mereka sesuai soal.

```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Jalankan Server

```bash
php artisan serve
```

Aplikasi berjalan di http://127.0.0.1:8000

## 📚 Dokumentasi API (Endpoints)

### 1. List Jadwal

Menampilkan jadwal semua user dalam rentang tanggal

-   **URL:** `GET /api/schedules`
-   **Params:** `start_date`, `end_date`
-   **Contoh Request:**

```http
GET /api/schedules?start_date=2025-06-02&end_date=2025-06-13
```

Menampilkan jadwal per user dalam rentang tanggal

-   **URL:** `GET /api/schedules`
-   **Params:** `start_date`, `end_date`, `user_id`
-   **Contoh Request:**

```http
GET /api/schedules?start_date=2025-06-04&end_date=2025-06-10&user_id=004
```

### 2. Export CSV

Mengunduh jadwal seluruh user dalam format Excel/CSV.

-   **URL:** `GET /api/export-schedules`
-   **Params:** `start_date`, `end_date`
-   **Contoh Request:**

```http
GET /api/export-schedules?start_date=2025-01-01&end_date=2025-01-25
```

Mengunduh jadwal per user dalam rentang tanggal

-   **URL:** `GET /api/export-schedules`
-   **Params:** `start_date`, `end_date`, `user_id`
-   **Contoh Request:**

```http
GET /api/export-schedules?start_date=2025-01-01&end_date=2025-01-25&user_id=001
```

### 3. Cek Jadwal Spesifik

Mengecek shift per user di tanggal tertentu.

-   **URL:** `GET /api/check-schdule`
-   **Params:** `user_id`, `date`

-   **Contoh Request:**

```http
GET /api/api/check-schedule?user_id=001&date=2025-01-05
```
