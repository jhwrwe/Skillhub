# README.md

# Aplikasi Kursus Sederhana (Laravel)

Dokumentasi ini siap untuk langsung di-copy paste ke file `README.md` di repository-mu.

## Ringkasan singkat

Aplikasi ini adalah platform kursus sederhana dengan relasi many-to-many antara peserta (Pengguna) dan Kelas.
Fitur utama:

* Autentikasi: register, login, logout
* Dashboard user dan dashboard admin
* CRUD kelas untuk admin
* Peserta bisa join / leave kelas
* Admin bisa mendaftarkan atau mengeluarkan peserta dari kelas
* Pivot menyimpan `registration_date` untuk melacak waktu pendaftaran

## Teknologi

* Backend: Laravel (PHP)
* Database: MySQL
* Templating: Blade
* Dependency: Composer (PHP), NPM opsional untuk asset

## Struktur data penting

* Model `Pengguna` (kolom: id, nama_lengkap, email, password, alamat, role)
* Model `Kelas` (kolom: id, nama_kelas, deskripsi, instruktor, waktu_mulai, waktu_selesai, status)
* Relasi many-to-many antara `Pengguna` dan `Kelas` via pivot table (misal `kelas_pengguna`)

  * Pivot menyimpan `registration_date`
* Model `Kelas` sebaiknya memiliki method `calculateStatus()` yang mengembalikan status: upcoming, ongoing, atau completed
* Scope `Kelas::ongoing()` dan `Kelas::upcoming()` berguna untuk query berdasarkan status

## Persiapan lingkungan (local)

1. Clone repo

```bash
git clone https://github.com/jhwrwe/Skillhub.git
cd Skillhub
```

2. Install dependency PHP

```bash
composer install
```

3. Copy environment dan generate app key

```bash
cp .env.example .env
php artisan key:generate
```

4. Edit file `.env` untuk koneksi database
   Contoh:

```
APP_NAME="KursusApp"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=secret
```

5. Migrasi database

```bash
php artisan migrate
```

6. (Opsional) Seed data jika ada seeder

```bash
php artisan db:seed --class=KelasSeeder
php artisan db:seed --class=AdminSeeder
```

7. Jalankan aplikasi

```bash
php artisan serve
# buka http://127.0.0.1:8000
```

8. (Opsional) Asset pipeline

```bash
npm install
npm run dev
```

9. (opsional) testing menggunakan php unit
```bash
php artisan test
```

10. (Opsional) Jika testing ingin mau satu-satu
```bash
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/KelasTest.php
php artisan test tests/Feature/PenggunaTest.php
```

## Membuat akun admin (opsional)

Jika belum ada seeder admin, buat via tinker:

```bash
php artisan tinker

>>> use App\Models\Pengguna;
>>> use Illuminate\Support\Facades\Hash;
>>> Pengguna::create([
... 'nama_lengkap' => 'Admin',
... 'email' => 'admin@example.com',
... 'password' => Hash::make('password123'),
... 'role' => 'admin'
... ]);
```

## Daftar route penting (sesuai `web.php` di project)

* Public

```text
GET  /                -> view: auth.login (homepage)
```

* Auth (guest)

```text
GET  /login           -> AuthController@showLogin       name: login
POST /login           -> AuthController@login
GET  /register        -> AuthController@showRegister    name: register
POST /register        -> AuthController@register
```

* Logout

```text
POST /logout          -> AuthController@logout          name: logout (middleware: auth)
```

* User (middleware: auth)

```text
GET  /dashboard               -> DashboardController@index        name: dashboard

GET  /kelas                   -> KelasController@index            name: kelas.index
GET  /kelas/{kelas}           -> KelasController@showDetail       name: kelas.detail
GET  /my-kelas                -> KelasController@myKelas         name: kelas.my
POST /kelas/{kelas}/join      -> KelasController@join            name: kelas.join
POST /kelas/{kelas}/leave     -> KelasController@leave           name: kelas.leave
```

* Admin (middleware: auth, admin) prefix: /admin, route name prefix: admin.

```text
GET    /admin/dashboard                                -> DashboardController@adminDashboard   name: admin.dashboard

GET    /admin/kelas                                    -> KelasController@adminIndex           name: admin.kelas.index
GET    /admin/kelas/create                             -> KelasController@create               name: admin.kelas.create
POST   /admin/kelas                                    -> KelasController@store                name: admin.kelas.store
GET    /admin/kelas/{kelas}/show                       -> KelasController@show                 name: admin.kelas.show
POST   /admin/kelas/{kelas}/daftarkan-peserta          -> KelasController@daftarkanPeserta     name: admin.kelas.daftarkan-peserta
GET    /admin/kelas/{kelas}/edit                       -> KelasController@edit                 name: admin.kelas.edit
PUT    /admin/kelas/{kelas}                            -> KelasController@update               name: admin.kelas.update
DELETE /admin/kelas/{kelas}                            -> KelasController@destroy              name: admin.kelas.destroy
DELETE /admin/kelas/{kelas}/peserta/{pengguna}        -> KelasController@removePeserta        name: admin.kelas.remove-peserta

GET    /admin/peserta                                  -> PenggunaController@index             name: admin.peserta.index
GET    /admin/peserta/create                           -> PenggunaController@create            name: admin.peserta.create
POST   /admin/peserta                                  -> PenggunaController@store             name: admin.peserta.store
GET    /admin/peserta/{pengguna}                       -> PenggunaController@show              name: admin.peserta.show
GET    /admin/peserta/{pengguna}/edit                  -> PenggunaController@edit              name: admin.peserta.edit
PUT    /admin/peserta/{pengguna}                       -> PenggunaController@update            name: admin.peserta.update
DELETE /admin/peserta/{pengguna}                       -> PenggunaController@destroy           name: admin.peserta.destroy

POST   /admin/peserta/{pengguna}/daftar-kelas          -> PenggunaController@daftarKelas       name: admin.peserta.daftar-kelas
DELETE /admin/peserta/{pengguna}/batal-kelas/{kelas}  -> PenggunaController@batalKelas        name: admin.peserta.batal-kelas
```

Catatan: pastikan middleware `auth` aktif untuk route user dan middleware `admin` atau gate `isAdmin` tersedia untuk route admin.

## Validasi dan logika penting

* Semua input create/update divalidasi via `$request->validate()`
* Pada operasi join/daftar: cek status kelas dengan `calculateStatus()` untuk mencegah pendaftaran ke kelas yang sudah selesai
* Saat attach many-to-many selalu isi `registration_date` di pivot
* Cegah admin menghapus akun sendiri di `PenggunaController::destroy`

## Testing

Jika ada test:

```bash
php artisan test
```

## Troubleshooting umum

* Migrasi gagal: cek kredensial database di `.env` dan pastikan database sudah ada
* Unique constraint error saat update: gunakan rule unique yang mengecualikan id yang sedang diupdate
* Masalah timezone saat hitung status kelas: set timezone di `config/app.php` atau di `.env` agar konsisten


## Kontribusi

1. Fork repository
2. Buat branch fitur/bugfix `git checkout -b feat/nama-fitur`
3. Commit dan push
4. Buka pull request dan jelaskan perubahan

