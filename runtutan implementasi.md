# RUNTUTAN IMPLEMENTASI

**Proyek :** Sistem Informasi Klinik Berbasis Web
**Framework :** Laravel 13 + MySQL (db_klinik)
**Standar Function :** `nama_modul_data()`, `nama_modul_add()`, `nama_modul_edit()`, `nama_modul_hapus()`, dan function lain sesuai proses
**Pelaksanaan :** SMK Assalafiyyah Sleman — Proyek Kejuruan PPLG, Tahun Pelajaran 2026/2027

---

## RENCANA LANGKAH (Roadmap)
| No | Langkah | Status |
|----|---------|--------|
| 1 | Persiapan: database `db_klinik` (MySQL) + konfigurasi `.env` | Selesai |
| 2 | Buat semua tabel via migration | Selesai |
| 3 | Login + hak akses 3 role (admin, dokter, pasien) | Selesai |
| 4 | Dashboard per role | Selesai |
| 5 | CRUD data dasar (pasien, dokter, tindakan, obat) | Selesai |
| 6 | Jadwal dokter (cegah tabrakan) | Selesai |
| 7 | Pendaftaran + antrean (nomor otomatis) | Selesai |
| 8 | Pemeriksaan + rekam medis | Selesai |
| 9 | Resep + detail resep + stok obat | Selesai |
| 10 | Pembayaran + detail pembayaran | Selesai |
| 11 | Pencarian & filter | Selesai |
| 12 | Laporan + cetak | Selesai |
| 13 | Pengujian sistem menyeluruh | Sedang dikerjakan |

---

## LOG PERUBAHAN

> Catatan setiap langkah akan ditambahkan di bawah ini setelah diterapkan.

### LANGKAH 1 — Persiapan Database & Konfigurasi — SELESAI (Rabu, 2 September 2026)

**Perintah yang diberikan:**
1. Buat database `db_klinik` di phpMyAdmin Laragon (collation `utf8mb4_unicode_ci`).
2. Edit `.env` bagian database: ubah dari SQLite ke MySQL (`DB_CONNECTION=mysql`, `DB_DATABASE=db_klinik`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_USERNAME=root`, `DB_PASSWORD=` kosong).
3. Jalankan `php artisan config:clear` dan `php artisan migrate:status`.

**Perubahan yang dilakukan:**
- Database `db_klinik` berhasil dibuat di MySQL Laragon.
- File `.env` dikonfigurasi untuk terhubung ke MySQL.

**Hasil:** `php artisan migrate:status` menampilkan "Migration table not found" — ini NORMAL dan sekaligus membuktikan koneksi Laravel ke `db_klinik` berhasil. Tabel belum ada karena migration belum dibuat (dikerjakan pada Langkah 2).

> **Catatan:** Database final yang dipakai adalah **`db_syaklinik`** sesuai pilihan user (bukan `db_klinik`). Nama DB belum disesuaikan dengan SPK; dicatat sebagai nilai yang perlu dipertimbangkan.

### LANGKAH 2 — Membuat Tabel Database (Migration) — SELESAI (Rabu, 2 September 2026)

**Perintah yang diberikan:**
1. Hapus migration bawaan `database/migrations/0001_01_01_000000_create_users_table.php` (tabel `users` tidak dipakai).
2. 16 perintah `php artisan make:migration` (pasien, dokter, pengguna, sessions, jadwal_dokter, tindakan, obat, pendaftaran, antrean, pemeriksaan, pemeriksaan_tindakan, rekam_medis, resep, detail_resep, pembayaran, detail_pembayaran) secara berurutan.
3. Isi isi tiap file migration dengan struktur kolom + relasi foreign key.
4. Jalankan `php artisan migrate`.

**Perubahan yang dilakukan:**
- File `database/migrations/` diperbarui: 15 migration baru (16 dikurangi 1 `users`), isi kode lengkap struktur tabel & relasi.
- Database terkoneksi ke `db_syaklinik`; 21 tabel berhasil dibuat, meliputi semua tabel wajib SPK: pengguna (role admin/dokter/pasien), pasien, dokter, jadwal_dokter, pendaftaran, antrean (status menunggu/dipanggil/diperiksa/selesai/batal), pemeriksaan, pemeriksaan_tindakan, rekam_medis, tindakan, obat, resep, detail_resep, pembayaran, detail_pembayaran (jenis_pelayanan) + tabel pendukung session/cache/jobs.

**Hasil:** `php artisan migrate:status` seluruhnya `Ran`. Semua tabel terverifikasi ada di MySQL.

### LANGKAH 3 — Login & Hak Akses (3 role: admin, dokter, pasien) — SELESAI (Rabu, 2 September 2026)

**Perintah yang diberikan:**
- Buat model: `app/Models/Pengguna.php`, `app/Models/Pasien.php`, `app/Models/Dokter.php`.
- Edit `config/auth.php`: model auth diarahkan ke `App\Models\Pengguna` (bukan `User` bawaan) supaya login memakai tabel `pengguna`.
- Buat `app/Http/Controllers/Auth/LoginController.php` (login_show, login_aksi, login_keluar).
- Buat `app/Http/Middleware/RoleMiddleware.php` untuk hak akses per role.
- Edit `bootstrap/app.php`: daftarkan alias middleware `role`.
- Isi `routes/web.php`: rute `/`, `/login`, `/logout`, dan 3 dashboard terproteksi `auth` + `role`.
- Buat tampilan: `resources/views/auth/login.blade.php`, `resources/views/layouts/app.blade.php`, `resources/views/dashboard/{admin,dokter,pasien}.blade.php`.
- Isi `database/seeders/PenggunaSeeder.php` & `DatabaseSeeder.php`, lalu `php artisan db:seed`.

**Perubahan yang dilakukan:**
- Sistem autentikasi custom memakai tabel `pengguna` dengan kolom `role` (admin/dokter/pasien) + relasi ke pasien & dokter.
- Middleware `role` membatasi akses halaman berdasarkan role.
- 3 akun awal dibuat: admin/admin123, dokter/dokter123, pasien/pasien123 + 1 data dokter & 1 pasien contoh.

**Bug & perbaikan:**
1. Error "Table `users` doesn't exist" → `config/auth.php` masih memakai model `User`; diperbaiki jadi `Pengguna`.
2. Error "Undefined variable $roles" di `RoleMiddleware.php` → parameter method `$role` tidak cocok dengan pemakaian `$roles`; diperbaiki menjadi `[$role]`.

**Hasil:** Login berhasil untuk ketiga role dan masing-masing diarahkan ke dashboard sesuai hak aksesnya.

### LANGKAH 4 — Dashboard Per Role & Integrasi Template Soft UI — SELESAI

**Perubahan yang dilakukan:**
- Integrasi template Soft UI Dashboard ke layout utama (`layouts/app.blade.php`) dengan sidebar, navbar, footer, serta aset CSS/JS di `public/assets/`.
- Dashboard admin: statistik jumlah pasien, dokter, pendaftaran hari ini, antrean aktif, obat hampir habis, pemeriksaan hari ini.
- Dashboard dokter: antrean hari ini, jumlah diperiksa, jumlah resep dibuat, jadwal praktik.
- Dashboard pasien: riwayat pendaftaran, jumlah diperiksa, resep obat, informasi jadwal dokter.
- 8 model baru dibuat (Antrean, Pendaftaran, Pemeriksaan, Resep, DetailResep, Pembayaran, DetailPembayaran, RekamMedis).
- `DashboardController` dengan 3 method: `dashboard_admin()`, `dashboard_dokter()`, `dashboard_pasien()`.

**Bug & perbaikan:**
1. Route admin `rekam-medis` berbenturan URL dengan route dokter (Laravel 13: route dengan URL+method identik saling menimpa) → diselesaikan dengan satu route bersama `role:admin,dokter` + middleware variadik.
2. RoleMiddleware awalnya hanya menerima 1 role → dimodifikasi ke parameter variadik `string ...$roles`.
3. Menu sidebar admin tersembunyi di bawah layar (kelebihan item) → ditambahkan CSS `overflow-y: auto` pada sidebar.
4. Migration `jadwal_dokter` typo kolom `kotak` → `kuota`; enum `hari` huruf kecil → huruf besar; enum status `dokter.tidak-aktif` → `tidak_aktif`.
5. Bug flash sukses palsu di Pendaftaran → hasil `simpanAntrean()` dicek sebelum menampilkan sukses.

**Hasil:** Ketiga dashboard menampilkan data real dari database dengan template Soft UI Dashboard.

### LANGKAH 5 — CRUD Data Dasar (Pasien, Dokter, Jadwal, Obat, Tindakan) — SELESAI

**Modul yang dibuat:**
- **Data Pasien** (admin): `pasien_data()`, `pasien_form()`, `pasien_simpan()`, `pasien_hapus()` — CRUD lengkap + akun login opsional.
- **Data Dokter** (admin): `dokter_data()`, `dokter_form()`, `dokter_simpan()`, `dokter_hapus()` — CRUD lengkap + akun login opsional.
- **Jadwal Dokter** (admin): `jadwal_data()`, `jadwal_form()`, `jadwal_simpan()`, `jadwal_hapus()` — CRUD + validasi tabrakan jadwal + cek hari pendaftaran.
- **Data Obat** (admin): `obat_data()`, `obat_form()`, `obat_simpan()`, `obat_hapus()` — CRUD + status habis/tersedia otomatis.
- **Data Tindakan** (admin): `tindakan_data()`, `tindakan_form()`, `tindakan_simpan()`, `tindakan_hapus()` — CRUD.

**Catatan:**
- Validasi pendaftaran menggunakan `Carbon::dayOfWeek` + pemetaan `['Senin'=>1,...,'Minggu'=>0]` supaya bebas bahasa.
- Flatpickr ditambahkan global di layout: semua `input[type=date]` menampilkan format `d/m/Y` (locale id, `disableMobile`, `minDate` dari atribut min).
- Form Data Dokter & Data Pasien memiliki bagian "Akun Login (opsional)" untuk membuat/mengedit akun login peran dokter/pasien (password ter-hash otomatis via casts `hashed`).

**Hasil:** Semua modul CRUD dasar berfungsi, sidebar admin terhubung ke setiap halaman.

### LANGKAH 6 — Pendaftaran + Antrean Otomatis — SELESAI

**Modul yang dibuat:**
- **Pendaftaran** (admin): `pendaftaran_data()`, `pendaftaran_form()`, `pendaftaran_simpan()`, `pendaftaran_hapus()` + `pendaftaran_saya()`, `pendaftaran_daftar()` (pasien).
  - Nomor pendaftaran otomatis: `DAFTAR-YYYYMMDD-XXXX`.
  - Validasi kuota dokter + cek hari pendaftaran sesuai jadwal.
- **Antrean** (admin): `antrean_data()` + filter dokter/status; `antrean_status()` untuk transisi `menunggu→dipanggil→diperiksa→selesai` + `batal` (mengubah status pendaftaran).
- Pendaftaran pasien dapat dilakukan melalui sidebar "Pendaftaran Saya" / "Daftar" (role pasien).

**Hasil:** Pendaftaran otomatis membuat antrean dengan status `menunggu`. Admin dapat mengelola alur antrean.

### LANGKAH 7 — Pemeriksaan — SELESAI

**Modul yang dibuat:**
- **Pemeriksaan** (dokter): `pemeriksaan_data()`, `pemeriksaan_form()`, `pemeriksaan_simpan()`, `pemeriksaan_hapus()`.
  - Data: antrean hari ini untuk diperiksa + riwayat pemeriksaan dengan filter keyword/periode.
  - Form: keluhan, pemeriksaan fisik, diagnosis, multi-tindakan (dengan biaya per tindakan dari pivot), catatan.
  - Simpan: sync tindakan + buat/update rekam medis otomatis + ubah status antrean → `diperiksa`.
  - Hapus: restore status antrean + hapus rekam medis terkait dalam transaksi.

**Relasi tambahan:**
- `Pemeriksaan` → `resep()` (hasOne), `pembayaran()` (hasOne).
- Tombol "Buat Resep" muncul di riwayat pemeriksaan jika belum punya resep.

**Hasil:** Dokter dapat memeriksa pasien dari antrean, menyimpan data pemeriksaan, dan secara otomatis membuat rekam medis.

### LANGKAH 8 — Rekam Medis (Akses Admin & Dokter) — SELESAI

**Modul yang dibuat:**
- **Rekam Medis** (admin & dokter): `rekam_medis_data()`, `rekam_medis_detail($pasienId)`.
  - Admin melihat semua rekam medis; dokter hanya miliknya sendiri.
  - Pencarian: nama/NIK/no. RM pasien + filter periode.
  - Detail: daftar kunjungan per pasien (dokter, keluhan, diagnosis, tindakan, catatan).
- Satu route bersama untuk admin & dokter dengan middleware `role:admin,dokter`.
- Sidebar admin & dokter mengarah ke `route('rekam-medis.data')`.

**Bug & perbaikan:**
- RoleMiddleware `role:admin,dokter` dipecah Laravel menjadi 2 argumen terpisah → diperbaiki dengan parameter variadik `string ...$roles`.

**Hasil:** Admin dan dokter dapat mengakses Rekam Medis tanpa 403. Data terfilter sesuai hak akses.

### LANGKAH 9 — Resep + Detail Resep + Stok Obat — SELESAI

**Modul yang dibuat:**
- **Resep** (dokter membuat, admin & dokter melihat): `resep_data()`, `resep_form()`, `resep_simpan()`, `resep_ditebus()`, `resep_hapus()`.
  - Resep terkait pemeriksaan; satu pemeriksaan maksimal 1 resep.
  - Multi obat: pilih obat, jumlah, aturan pakai → subtotal otomatis (harga × jumlah).
  - **Stok obat berkurang otomatis** saat resep disimpan.
  - Validasi stok: jika stok tidak mencukupi, pesan error menampilkan obat yang kurang.
  - Hapus resep: stok otomatis dikembalikan.
  - Status `belum_ditebus` → `ditebus` (tombol toggle).
  - Tombol "Buat Resep" muncul di riwayat pemeriksaan.
- Sidebar admin & dokter terhubung ke `route('resep.data')`.

**Alur stok (transaksi database):**
- Edit resep: stok lama dikembalikan dulu → validasi stok baru → kurangi stok baru.
- Jika stok menipis → status obat otomatis berubah `tersedia` / `habis`.

**Hasil:** Dokter dapat membuat resep multi obat; stok berkurang/berkembalikan otomatis; admin dapat melihat dan menandai resep sudah ditebus.

### LANGKAH 10 — Pembayaran + Detail Pembayaran (Total Otomatis) — SELESAI

**Modul yang dibuat:**
- **Pembayaran** (admin): `pembayaran_data()`, `pembayaran_form()`, `pembayaran_buat()`, `pembayaran_detail()`, `pembayaran_bayar()`, `pembayaran_hapus()`.
  - Form: daftar pemeriksaan yang belum dibuatkan pembayaran, dengan rincian tindakan + obat + total otomatis.
  - Nomor pembayaran otomatis: `BYR-YYYYMMDD-XXX`.
  - Detail: rincian tagihan (tindakan + obat) + form terima pembayaran.
  - Bayar: jika `bayar >= total` → status lunas + hitung kembalian; jika kurang → pesan error.
  - Hapus hanya untuk yang belum lunas.

**Hasil:** Admin dapat membuat pembayaran otomatis berdasarkan tindakan & resep, menerima pembayaran, dan melihat status.

### LANGKAH 11 — Pencarian & Filter — SELESAI

**Fitur pencarian yang diterapkan:**
- **Pemeriksaan**: filter keyword nama pasien + periode tanggal (dari/sampai).
- **Resep**: filter keyword nama/NIK/no. RM pasien + periode + status ditebus.
- **Pembayaran**: filter keyword nomor/nama pasien + periode + status lunas/belum.
- **Rekam Medis**: filter keyword nama/NIK/no. RM + periode.
- Semua filter menggunakan `?q=...&dari=...&sampai=...&status=...` dengan `withQueryString()` untuk pagination.

**Hasil:** Pengguna dapat mencari dan memfilter data di seluruh modul utama.

### LANGKAH 12 — Laporan (8 jenis) + Cetak — SELESAI

**Modul laporan (admin):**
8 jenis laporan sesuai SPK:
1. Data Pasien
2. Data Dokter
3. Pendaftaran Pasien
4. Antrean
5. Pemeriksaan
6. Rekam Medis
7. Obat
8. Pembayaran (hanya lunas, dengan total pendapatan)

Semua laporan dapat difilter berdasarkan periode (dari/sampai).

**Modul cetak:**
- `laporan_cetak()` — cetak laporan apapun (8 jenis) dengan format rapi, tombol "Cetak / Simpan PDF" via `window.print()`.
- `pasien_cetak()` — cetak daftar pasien.
- `resep_cetak()` — cetak daftar resep dengan total nilai.
- `pembayaran_cetak()` — cetak daftar pembayaran dengan total pendapatan.

Semua halaman cetak terbuka di tab baru (`target="_blank"`) dan memiliki format print-friendly tanpa sidebar/navbar.

**Hasil:** Admin dapat melihat 8 jenis laporan dengan filter periode dan mencetaknya sebagai dokumen PDF.

### LANGKAH 13 — Perbaikan Bug & Kerapian (Pengujian Menyeluruh) — SELESAI (Selasa, 15 September 2026)

**Perbaikan pada sesi ini:**
1. **Bug login NIK pasien:** validasi `$request->validate()` setelah `Auth::attempt()` tidak melakukan logout → pasien tetap "masuk" padahal NIK kosong/salah. Diperbaiki dengan pengecekan manual: NIK wajib 16 digit untuk pasien, dicocokkan dengan `$user->pasien->nik`, lalu `logout()` + `invalidate()` saat gagal.
2. **Sidebar pasien "dobel warna oren":** menu "Pendaftaran Saya" memakai pola `request()->is('pendaftaran*')` yang juga aktif di `/pendaftaran/daftar` → diubah menjadi `pendaftaran/saya*` sehingga tidak menyala bersamaan dengan "Daftar Periksa".
3. **Menu mati `href="#"` dihapus:** admin "Pemeriksaan" (admin tak punya route pemeriksaan) dan pasien "Riwayat & Rekam Medis" (riwayat sudah tampil di dashboard pasien).
4. **Efek tekan/klik menu sidebar:** ditambahkan CSS agar `.nav-link:active/:focus` tidak menampilkan overlay warna oren ganda.
5. **Dashboard admin:** label "Modul menyusul" pada kartu Pendaftaran & Transaksi Pembayaran dihapus → kini menautkan ke halaman modul (pendaftaran, antrean, pembayaran) yang sudah jadi.
6. **Laporan data dokter:** kolom status menampilkan teks `'Aktif'` hardcoded → diperbaiki menjadi nilai `$r->status` asli.
7. **JS form resep:** handler hapus baris dirapikan (menghilangkan kode duplikat) tetap menghitung ulang subtotal & total.
8. **Verifikasi route:** seluruh menu sidebar & dashboard sudah memakai route yang benar-benar ada (62 route) dan tidak ada lagi `href="#"`.
9. **Fitur baru "Pembayaran Saya" (role pasien):** pasien dapat melihat seluruh tagihan (rincian tindakan + obat, total, status lunas/belum lunas) dan langsung membayar dari akunnya (validasi jumlah ≥ total, otomatis status → lunas + hitung kembalian). Ditambah route `pembayaran.saya` & `pembayaran.pasien_bayar` (total route menjadi 64), view `pembayaran/saya.blade.php`, dan menu sidebar pasien. Pemeriksaan yang belum dibuat tagihannya oleh admin juga ditampilkan sebagai "Belum Ada Tagihan".
10. **Bug error 500 halaman Resep (dokter):** `@json($obatList->map(fn($o) => [...]))` di `resep/form.blade.php` — kompiler Blade memotong ekspresi yang mengandung closure `fn()`, menghasilkan ParseError "Unclosed '[' does not match ')'" sehingga semua halaman yang membuka resep (termasuk dari halaman Pemeriksaan) rusak. Diperbaiki dengan menghitung array di blok `@php` lalu `@json($obatJson)`. Terverifikasi: `/pemeriksaan`, `/resep/form`, `/dashboard/dokter` semua 200.
11. **Password akun demo `dr. sari` disetel ulang ke `dokter123`** agar login dokter berfungsi untuk pengujian (sebelumnya password di database tidak cocok).
12. **Fitur "Jadwal Multi Hari":** form tambah jadwal kini memakai checkbox hari sehingga admin bisa membuat jadwal beberapa hari sekaligus (misal Senin, Rabu, Jumat dalam satu kali simpan). Pengecekan bentrok jam dilakukan untuk semua hari terpilih SEBELUM disimpan (parsial tidak terjadi), dan pesan error menyebutkan hari yang bentrok. Mode edit tetap satu hari (read-only) agar tidak merusak identitas record. Terverifikasi via HTTP: 2 jadwal tersimpan sekaligus, jadwal bentrok ditolak.
13. **Fitur Stok Masuk / Stok Keluar + Riwayat Stok:** form tambah obat tidak lagi menerima input stok — stok awal otomatis 0 (status `habis`). Stok hanya berubah melalui tombol **Stok Masuk** (bertambah) dan **Stok Keluar** (berkurang) di halaman Data Obat (modal per obat, validasi: keluar tidak boleh melebihi stok). Setiap mutasi dicatat ke tabel baru `stok_history` (jenis masuk/keluar, jumlah, keterangan, tanggal, pengguna) dan dapat dilihat di halaman baru **Riwayat Stok** (filter obat & jenis) sehingga pengeluaran obat terlacak. Pemakaian obat via resep otomatis tercatat sebagai stok keluar "Dipakai resep" dan pengembalian saat resep dihapus tercatat sebagai stok masuk. Terverifikasi via HTTP: tambah obat (stok 0) → masuk 10 → keluar 4 = stok 6 + 2 riwayat; keluar 99 ditolak (stok tidak mencukupi).

14. **Antrean Dokter Diperkaya:** halaman Pemeriksaan bagian "Antrean untuk Diperiksa" kini punya filter **tanggal antrean** (tidak terbatas hari ini) dan kotak **Cari nama pasien**. Dokter juga dapat **memanggil pasien sendiri** (tombol Panggil, status `menunggu` ke `dipanggil`) tanpa harus menunggu admin. Terverifikasi via HTTP: cari nama menampilkan/tidak menampilkan sesuai filter, filter tanggal menampilkan antrean hari lain (17 Sep), tombol Panggil mengubah status menjadi Dipanggil dengan flash sukses.
15. **Halaman Pemeriksaan dipisah jadi 2 menu:** menu **Antrean Pasien** kini membuka halaman sendiri `/pemeriksaan/antrean` (khusus daftar antrean + Panggil/Periksa + filter tanggal/nama), sedangkan menu **Pemeriksaan** hanya menampilkan **Riwayat Pemeriksaan** (filter q/dari/sampai, Edit, Buat Resep, Hapus). Sidebar dokter tidak lagi menyala dobel. Terverifikasi: `/pemeriksaan/antrean` (title "Antrean Pasien") dan `/pemeriksaan` (title "Pemeriksaan Pasien") masing-masing 200 dan menampilkan bagian yang berbeda.
16. **Tombol "+ Tambah Obat" di form resep mati:** seluruh skrip form resep memakai jQuery (`$('....')`) tetapi jQuery tidak pernah dimuat, sehingga tombol Tambah Obat, pemilihan obat, penghitungan subtotal/total, dan baris obat pertama (auto-generate) tidak berfungsi. Diperbaiki dengan memuat jQuery 3.7.1 CDN khusus halaman `resep/form.blade.php` sebelum skrip inline. Terverifikasi: tag jQuery + `const obatList` muncul di HTML, halaman 200. (Catatan: password `dr. sari` disetel ulang ke `dokter123` karena hash di DB tidak lagi cocok.)
17. **Tagihan otomatis saat resep ditebus:** sebelumnya tombol **Ditebus** hanya mengubah status resep — tagihan baru muncul kalau admin membuatnya manual lewat modul Pembayaran, sehingga pasien acap tidak melihat tagihan di "Pembayaran Saya". Kini saat resep ditandai **Ditebus**, sistem langsung membuat pembayaran (tindakan + seluruh obat resep, status `belum_lunas`) otomatis untuk pasien tersebut (dicegah jika sudah ada). Terverifikasi via HTTP: resep `ditebus` → muncul flash "tagihan otomatis dibuat"; di DB tercipta pembayaran total 140.000 (tindakan 100.000 + obat 2×20.000) dengan detail yang benar. Backfill juga dilakukan untuk resep yang sudah terlanjur ditebus: pemeriksaan Adriana (id 2) dibuatkan tagihan BYR-20260917-001 Rp300.000.

18. **Perapian tampilan seluruh fitur admin:** agar setiap halaman admin konsisten dan mudah dibaca — (a) label filter diseragamkan ("Pencarian", "Status", "Hari", "Tanggal") plus tombol **Cari** (btn-dark) & **Reset** (btn-secondary) di pasien, dokter, jadwal, tindakan, obat, dan pendaftaran; (b) status mentah diubah menjadi badge berlabel: dokter `aktif`/`tidak_aktif` → **Aktif**/**Tidak Aktif**, obat **Tersedia**/**Habis**/**Kedaluwarsa**; (c) badge "Belum Lunas" pada pembayaran diubah dari merah (`bg-danger`) menjadi kuning (`bg-warning text-dark`) agar tidak terkesan error; (d) jam jadwal dipotong ke `HH:MM` (sebelumnya tumpah menampilkan detik); (e) halaman Laporan: kolom teks panjang (keluhan, diagnosis, tindakan) dipotong dengan `Str::limit`, semua kolom status menjadi badge berwarna, relasi nullable dibuat aman (`?? '-'`), dan baris kosong menjadi `text-muted py-4`; (f) alert duplikat di Data Obat dihapus (memakai alert global layout) sisa alert error modal; (g) sisa blok JavaScript mati di halaman Tindakan dibersihkan.
19. **Route mati dihapus:** `GET /tindakan/form` (`TindakanController::tindakan_form`) mengembalikan `view('tindakan.form')` yang **tidak pernah ada** → berpotensi error 500 dan tidak dipakai siapa pun (tambah/edit tindakan memakai modal di halaman Data Tindakan). Route + method dihapus; total route menjadi 68.
20. **Data demo (±50 obat & 50 resep) diisi via `Database\Seeders\DemoDataSeeder`:** seeder yang sudah ada di proyek **diperbaiki agar berhasil jalan** (sebelumnya gagal di PHP 8: `++$array[$key]` memunculkan *undefined array key* saat kunci pertama kali diakses) dan ditambahkan pencocokan nomor dengan data lama (`MAX(nomor...)` per tanggal) sehingga tidak bentrok dengan pendaftaran/pembayaran yang sudah ada. Hasil run: **50 obat baru** (total 52; 2 sengaja `habis`, 2 `kedaluwarsa` sebagai contoh status), **50 resep** (total 51) lengkap dengan 50 pendaftaran, 50 antrean (status `diperiksa`), 50 pemeriksaan, 50 rekam medis, 17 tagihan otomatis (10 lunas), detail resep 94, dan riwayat stok keluar 47. Tanggal menyebar 2026-08-01 s.d. 2026-09-19. Dijalankan: `php artisan db:seed --class=DemoDataSeeder`. Seluruh 19 halaman (termasuk cetak) terverifikasi render tanpa error. Cara isi ulang: hapus kunci cache `demo_data_seeded` lalu seed lagi.
21. **URL form/edit mengikuti penamaan SPK (menghapus pola query string `?id=`):** sesuai ketentuan pengajar, pemanggilan seperti `/pasien/form?id=5` tidak boleh — semua form/edit kini memakai URL path bernama `/{modul}/add` dan `/{modul}/edit/{id}` (mis. `/pasien/add`, `/pasien/edit/5`). Perubahan di `routes/web.php` (route `pasien.form`→`pasien.add`/`pasien.edit`, dst. untuk dokter, obat, jadwal, pendaftaran, pemeriksaan, pembayaran, resep) dan 8 controller (`X_form` dipecah menjadi `X_add`/`X_edit`; `resep_add(Request $request, $pemeriksaanId = null)` menerima id pemeriksaan via segment path dan mengirim variabel view `presetPemeriksaanId`, `pemeriksaan_add` menerima `pendaftaranId` dari `/pemeriksaan/add/{pendaftaran}`). Seluruh view yang memanggil `route('X.form', [...])` diperbarui menjadi `route('X.add')` / `route('X.edit', $id)` / `route('pemeriksaan.add', $pendaftaran->id)` / `route('resep.add', ['pemeriksaan' => $id])`, dan `resep/form.blade.php` memakai `$presetPemeriksaanId` menggantikan `request('pemeriksaan_id')`. Terverifikasi: 16 URL path add/edit cocok ke route yang tepat (`php artisan route:list`), 15 method add/edit di controller merender view tanpa error, tidak ada lagi referensi `.form` di view/controller, `view:clear` + `view:cache` sukses.

22. **Seluruh CRUD diubah menjadi modal (tanpa pindah halaman):** sesuai permintaan "semua CRUD memakai modal biar tidak ribet pindah halaman", tambah & edit di 8 modul kini dibuka sebagai **Bootstrap modal** di halaman data masing-masing (pola seperti Data Tindakan yang sudah modal). Route GET `/{modul}/add` & `/{modul}/edit/{id}` dan method `X_add`/`X_edit` dihapus — cukup `simpan` (POST) yang menangani tambah & edit (field hidden `id`), sehingga total route 57. Detail per modul: **pasien/dokter** (controller mengirim `$usernames` map akun login per pasien_id/dokter_id untuk kombinasi akun, modal `#modalPasien`/`#modalDokter`, tombol Edit ber `data-*`); **obat** (modal `#modalObat`, stok read-only — hanya berubah via Stok Masuk/Keluar, ditampilkan "Stok Saat Ini"); **jadwal** (`#modalJadwal`, mode **tambah** pakai checkbox `hari[]` multi hari, mode **edit** pakai hidden `hari` + readonly "Hari tidak bisa diubah saat edit"); **pendaftaran** (`#modalPendaftaran`, select pasien & jadwal, min tanggal hari ini); **pembayaran** (modal `#modalBuatPembayaran` berisi daftar pemeriksaan belum ditagih + tombol "Buat & Bayar" — `pembayaran_add` dihapus karena daftar kini disusun di `pembayaran_data`); **pemeriksaan** (partial `pemeriksaan/_modal.blade.php` dipakai dua halaman: tombol Periksa di antrean = buka modal add, tombol Edit di riwayat = isi modal dengan data; kedua controller mengirim `$tindakanList`; `#prxInfo` menampilkan pasien/no. pendaftaran/no. antrean); **resep** (partial `resep/_modal.blade.php` dengan baris obat dinamis + jQuery CDN + `#rsTglAdd`/`#rsTglAdd2` untuk menduplikasi tanggal add vs edit via `name` toggle — dipakai di `resep.data` (Edit + Buat Resep) DAN di `pemeriksaan.data` (tombol "Buat Resep" membuka modal di tempat, `data-pemeriksaan` preselect). File `form.blade.php` 8 modul dihapus. Terverifikasi: `php -l` 9 file OK, 6 halaman admin + 3 halaman dokter render tanpa error, `view:clear` sukses, tidak ada sisa `route('X.add')`/`route('X.edit')` atau `X_add`/`X_edit` di seluruh kode.

**Hasil:** Sidebar pasien tidak lagi dobel aktif, dashboard admin menuju modul yang ada, dan laporan menampilkan status benar. Semua halaman admin dirapikan (label filter, badge status, teks panjang dipotong) dan 19 halaman admin terverifikasi render tanpa error. Sisa pengujian menyeluruh oleh user lintas role.

---

## STATUS AKHIR

Semua modul SPK telah diterapkan:
- [x] Login + hak akses (admin, dokter, pasien)
- [x] Dashboard per role (data real)
- [x] CRUD data dasar (pasien, dokter, jadwal, obat, tindakan) — tambah/edit via modal
- [x] Pendaftaran + antrean otomatis (tambah/edit via modal)
- [x] Pemeriksaan + rekam medis otomatis (tambah/edit via modal, partial dipakai antrean & riwayat)
- [x] Resep + detail resep + stok obat otomatis (tambah/edit via modal, partial dipakai data resep & data pemeriksaan)
- [x] Pembayaran + detail pembayaran + total otomatis ("Buat Pembayaran" via modal berisi daftar pemeriksaan belum ditagih)
- [x] Pencarian & filter
- [x] Laporan (8 jenis) + filter periode
- [x] Cetak data & laporan
- [x] Validasi di seluruh form
- [x] Hak akses (admin, dokter, pasien)
- [ ] Pengujian sistem menyeluruh (belum)