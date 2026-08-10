# SENTIMEN
### Sistem Evaluasi dan Notifikasi Terintegrasi Monitoring

> Aplikasi manajemen dan monitoring program kerja berbasis web untuk Aparatur Sipil Negara (ASN), 
> disubmit pada kompetisi **IAKA 2026**.

---

## Tentang SENTIMEN

**SENTIMEN** adalah sistem informasi berbasis web yang dirancang untuk mendukung evaluasi, 
monitoring, dan notifikasi terintegrasi atas pelaksanaan program dan kegiatan di lingkungan 
pemerintahan. Sistem ini menerapkan arsitektur multi-tenant per unit kerja, memungkinkan 
setiap instansi mengelola data programnya secara terisolasi dan aman.

### Fitur Utama

- **Manajemen Program & List** — Kelola program, sub-program, dan tugas secara hierarkis
- **Multi-Tenant Unit Kerja** — Isolasi data antar instansi/unit kerja secara otomatis
- **Monitoring Progress Real-time** — Pantau capaian program dengan dashboard interaktif
- **Gantt Chart & Kalender** — Visualisasi jadwal dan timeline program
- **Notifikasi & Reminder** — Pengingat otomatis untuk deadline tugas
- **Activity Log & Audit Trail** — Rekam jejak seluruh aktivitas pengguna
- **Laporan Progress** — Export dan analisis realisasi vs rencana

---

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Database**: MySQL
- **Frontend**: Blade + Alpine.js + Vite + TailwindCSS
- **Auth**: Session-based + reCAPTCHA + Force Password Change

---

## Instalasi

```bash
# Clone repository
git clone <repo-url>
cd task-app

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env, lalu:
php artisan migrate --seed

# Build assets
npm run build

# Jalankan server
php artisan serve
```

---

## Lisensi

MIT License — © {{ date('Y') }} SENTIMEN / IAKA 2026
