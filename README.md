# WATERWATCH HORIZON

Aplikasi Smart Water Monitoring berbasis HTML, CSS, Vanilla JavaScript, PHP 8+, MySQL/MariaDB, Leaflet/OpenStreetMap, dan Chart.js.

## Fitur yang tersedia
- Login/logout berbasis session PHP, password hashing, regenerasi session ID, dan CSRF token.
- Dashboard statistik dan laporan terbaru berbasis database.
- CRUD create/read laporan, validasi server-side, unggah foto JPG/PNG/WEBP maksimal 5 MB.
- Water Map dengan marker laporan dari database, filter kategori/status, lokasi perangkat dan pelacakan opsional.
- Monitoring History dengan pencarian, filter, pagination.
- Statistics dinamis melalui endpoint PHP dan Chart.js.
- AI Water Scanner melalui endpoint AI vision yang kompatibel dengan OpenAI Chat Completions. Tanpa konfigurasi AI, fitur menampilkan error yang jelas dan tidak memalsukan hasil.

## Kebutuhan
- PHP 8.1+ dengan ekstensi PDO MySQL, fileinfo, cURL, dan JSON.
- MySQL 8+ atau MariaDB.
- Browser modern. Geolocation memerlukan HTTPS saat deployment (localhost diizinkan).
- Koneksi internet untuk font Google, Leaflet tiles, Chart.js, dan peta OpenStreetMap.

## Instalasi lokal (XAMPP)
1. Ekstrak folder `waterwatch-horizon` ke `C:\\xampp\\htdocs\\`.
2. Jalankan Apache dan MySQL melalui XAMPP.
3. Buka phpMyAdmin atau MySQL CLI, lalu jalankan `database/schema.sql`.
4. Atur kredensial database melalui environment server. Default lokal di `config/database.php` adalah host `127.0.0.1`, database `waterwatch_horizon`, user `root`, password kosong. Untuk deployment, wajib atur environment variables DB_*.
5. Dari terminal pada root proyek, jalankan `php scripts/create-demo-user.php`. Jika PHP CLI tidak ada di PATH, jalankan script tersebut melalui browser sementara, lalu hapus/larang akses script setelah akun dibuat.
6. Buka `http://localhost/waterwatch-horizon/`.
7. Login demo: `demo@waterwatch.local` / `WaterWatch123!`.

## Konfigurasi AI
Atur environment variables pada konfigurasi server:
- `AI_API_URL`: endpoint Chat Completions yang mendukung input gambar.
- `AI_API_KEY`: secret provider, hanya disimpan di server.
- `AI_MODEL`: model vision yang tersedia pada akun provider.

Contoh endpoint OpenAI-compatible: `https://api.openai.com/v1/chat/completions`. Provider/model harus mendukung format pesan vision yang digunakan. Jangan meletakkan API key di JavaScript, repository, atau folder publik. Scanner hanya menganalisis visual dan bukan pengujian laboratorium.

## Konfigurasi produksi dan keamanan
- Letakkan `storage/reports` di luar web root jika memungkinkan. Untuk konfigurasi ini, folder upload disimpan di project dan berisi hanya nama acak serta file yang lolos validasi MIME; konfigurasi web server tetap perlu mencegah eksekusi script di folder tersebut.
- Jangan deploy dengan kredensial demo atau password database kosong.
- Wajib gunakan HTTPS, batasi rate request, lakukan backup, dan siapkan kebijakan retensi data.
- Session login, CSRF, prepared statements, output escaping, batas upload, dan pengecekan kepemilikan laporan telah diterapkan pada jalur utama.
- Lokasi GPS dikirim hanya setelah pengguna menekan tombol dan memberikan izin. Peta tidak mengirim lokasi ke server secara otomatis.
- Demo ini belum menyediakan panel admin untuk mengubah status. Status laporan tidak berubah otomatis menjadi Resolved.

## Struktur penting
- `index.php`, `dashboard.php`, `report.php`, `scanner.php`, `map.php`, `history.php`, `statistics.php`, `report-detail.php`: halaman aplikasi.
- `config/`: konfigurasi dan koneksi PDO.
- `includes/`: autentikasi, CSRF, layout, helper.
- `api/`: endpoint JSON untuk laporan, statistik, lokasi, dan analisis AI.
- `services/AIService.php`: konektor provider vision.
- `database/`: skema SQL dan seed awal.
- `assets/css/`, `assets/js/`, `assets/images/`: antarmuka.
- `storage/reports/`: file bukti unggahan.

## Catatan verifikasi
Kode disusun sebagai proyek awal yang dapat dijalankan setelah konfigurasi lingkungan. Jalankan pengujian di lingkungan PHP/MySQL Anda sebelum digunakan di produksi; tidak ada klaim bahwa pengujian runtime telah dilakukan pada mesin ini.
