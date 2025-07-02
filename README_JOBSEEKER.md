# Job Seekers Platform

Aplikasi Laravel untuk platform pencarian kerja yang memungkinkan pencari kerja untuk mendaftar, memvalidasi data, dan melamar pekerjaan.

## Fitur Utama

### 1. **Authentication System**
- Login dan Register
- Session-based authentication
- Logout functionality

### 2. **Dashboard**
- Menampilkan status validasi data
- Menampilkan daftar lamaran kerja
- Link ke fitur-fitur utama

### 3. **Data Validation**
- Request validasi data untuk verifikasi keahlian
- Form untuk mengisi kategori pekerjaan, pengalaman kerja, dan alasan
- Status tracking: Pending, Accepted, Rejected

### 4. **Job Vacancies**
- Daftar lowongan pekerjaan dari berbagai perusahaan
- Detail lowongan pekerjaan
- Apply untuk multiple posisi sekaligus

### 5. **Job Applications**
- Submit lamaran untuk posisi yang dipilih
- Tracking status lamaran
- Notes untuk perusahaan

## Halaman yang Tersedia

### Public Pages
- **Home** (`/`) - Landing page dengan informasi platform
- **Login** (`/login`) - Halaman login
- **Register** (`/register`) - Halaman registrasi

### Protected Pages (Perlu Login)
- **Dashboard** (`/dashboard`) - Halaman utama setelah login
- **Data Validation Create** (`/data-validation/create`) - Form request validasi data
- **Job Vacancies** (`/job-vacancies`) - Daftar lowongan kerja
- **Job Details** (`/job-vacancies/{id}`) - Detail lowongan dan form aplikasi

## Struktur UI

Aplikasi ini menggunakan:
- **Bootstrap 5** untuk styling
- **Laravel Blade** templates
- **FontAwesome** untuk icons
- **Responsive design** yang mobile-friendly

### Layout Structure
```
resources/views/
├── layouts/
│   └── app.blade.php          # Main layout template
├── auth/
│   ├── login.blade.php        # Login page
│   └── register.blade.php     # Registration page
├── data-validation/
│   └── create.blade.php       # Data validation request form
├── job-vacancies/
│   ├── index.blade.php        # Job listings page
│   └── show.blade.php         # Job detail and application page
├── dashboard.blade.php        # Dashboard page
└── home.blade.php            # Landing page
```

## Cara Menjalankan

1. **Setup Environment**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. **Setup Database**
   ```bash
   php artisan migrate
   ```

3. **Start Development Server**
   ```bash
   php artisan serve
   ```

4. **Akses Aplikasi**
   - Buka browser dan kunjungi `http://localhost:8000`

## Fitur Authentication

Aplikasi menggunakan session-based authentication sederhana:
- Login akan menyimpan session `user_logged_in` dan `user_name`
- Logout akan menghapus session tersebut
- Protected routes akan mengecek keberadaan session

## Sample Data

Aplikasi menyertakan sample data untuk demonstrasi:
- Contoh validasi data dengan status Pending dan Accepted
- Contoh lowongan kerja dari berbagai perusahaan
- Contoh lamaran kerja dengan berbagai status

## Teknologi yang Digunakan

- **Backend**: Laravel 11
- **Frontend**: Bootstrap 5, Blade Templates
- **Database**: SQLite (default)
- **Icons**: FontAwesome 6
- **Styling**: Custom CSS + Bootstrap

## Routes Tersedia

### Public Routes
- `GET /` - Home page
- `GET /login` - Login form
- `POST /login` - Process login
- `GET /register` - Registration form
- `POST /register` - Process registration

### Protected Routes
- `GET /dashboard` - User dashboard
- `GET /data-validation/create` - Data validation request form
- `POST /data-validation` - Submit data validation request
- `GET /job-vacancies` - Job listings
- `GET /job-vacancies/{id}` - Job details
- `POST /job-applications` - Submit job application
- `POST /logout` - Logout

## Customization

Untuk mengkustomisasi tampilan:
1. Edit file CSS di `public/css/custom.css`
2. Modifikasi layout di `resources/views/layouts/app.blade.php`
3. Update individual pages sesuai kebutuhan

## Notes

- Aplikasi ini menggunakan session sederhana untuk authentication demo
- Untuk production, gunakan Laravel's built-in authentication
- Database operations saat ini menggunakan sample data
- Implementasikan logic sebenarnya untuk CRUD operations sesuai kebutuhan
