# Database Migrations & Seeders

## 📋 Struktur Database

Berdasarkan file database dump SQL yang disediakan, saya telah membuat migrations dan seeders lengkap untuk sistem JobSeeker.

## 🗄️ Tabel yang Dibuat

### 1. **Regionals** (`regionals`)
- `id` - Primary Key
- `province` - Nama provinsi
- `district` - Nama kabupaten/kota
- `timestamps`

### 2. **Job Categories** (`job_categories`)  
- `id` - Primary Key
- `job_category` - Nama kategori pekerjaan
- `timestamps`

### 3. **Societies** (`societies`)
- `id` - Primary Key  
- `id_card_number` - Nomor kartu identitas (unique, 8 karakter)
- `password` - Password (hashed)
- `name` - Nama lengkap
- `born_date` - Tanggal lahir
- `gender` - Jenis kelamin (male/female)
- `address` - Alamat lengkap
- `regional_id` - Foreign key ke regionals
- `login_tokens` - Token login (nullable)
- `timestamps`

### 4. **Admin Users** (`admin_users`)
- `id` - Primary Key
- `username` - Username (unique)
- `password` - Password (hashed)
- `timestamps`

### 5. **Validators** (`validators`)
- `id` - Primary Key
- `user_id` - Foreign key ke admin_users
- `role` - Role (officer/validator)
- `name` - Nama validator
- `timestamps`

### 6. **Validations** (`validations`)
- `id` - Primary Key
- `job_category_id` - Foreign key ke job_categories
- `society_id` - Foreign key ke societies
- `validator_id` - Foreign key ke validators (nullable)
- `status` - Status (accepted/declined/pending)
- `work_experience` - Pengalaman kerja
- `job_position` - Posisi pekerjaan yang diinginkan
- `reason_accepted` - Alasan mengapa harus diterima
- `validator_notes` - Catatan validator
- `timestamps`

### 7. **Job Vacancies** (`job_vacancies`)
- `id` - Primary Key
- `job_category_id` - Foreign key ke job_categories
- `company` - Nama perusahaan
- `address` - Alamat perusahaan
- `description` - Deskripsi lowongan
- `timestamps`

### 8. **Available Positions** (`available_positions`)
- `id` - Primary Key
- `job_vacancy_id` - Foreign key ke job_vacancies
- `position` - Nama posisi
- `capacity` - Kapasitas yang dibutuhkan
- `apply_capacity` - Kapasitas maksimal pelamar
- `timestamps`

### 9. **Job Apply Societies** (`job_apply_societies`)
- `id` - Primary Key
- `notes` - Catatan pelamar
- `date` - Tanggal melamar
- `society_id` - Foreign key ke societies
- `job_vacancy_id` - Foreign key ke job_vacancies
- `timestamps`

### 10. **Job Apply Positions** (`job_apply_positions`)
- `id` - Primary Key
- `date` - Tanggal melamar
- `society_id` - Foreign key ke societies
- `job_vacancy_id` - Foreign key ke job_vacancies
- `position_id` - Foreign key ke available_positions
- `job_apply_societies_id` - Foreign key ke job_apply_societies
- `status` - Status lamaran (pending/accepted/rejected)
- `timestamps`

## 📁 Files yang Dibuat

### Migrations (10 files):
```
database/migrations/
├── 2024_01_01_000001_create_regionals_table.php
├── 2024_01_01_000002_create_job_categories_table.php
├── 2024_01_01_000003_create_societies_table.php
├── 2024_01_01_000004_create_admin_users_table.php
├── 2024_01_01_000005_create_validators_table.php
├── 2024_01_01_000006_create_validations_table.php
├── 2024_01_01_000007_create_job_vacancies_table.php
├── 2024_01_01_000008_create_available_positions_table.php
├── 2024_01_01_000009_create_job_apply_societies_table.php
└── 2024_01_01_000010_create_job_apply_positions_table.php
```

### Seeders (9 files):
```
database/seeders/
├── RegionalSeeder.php
├── JobCategorySeeder.php
├── SocietySeeder.php
├── AdminUserSeeder.php
├── ValidatorSeeder.php
├── JobVacancySeeder.php
├── AvailablePositionSeeder.php
├── ValidationSeeder.php
├── JobApplicationSeeder.php
└── DatabaseSeeder.php (updated)
```

### Models (10 files):
```
app/Models/
├── Regional.php
├── JobCategory.php
├── Society.php
├── AdminUser.php
├── Validator.php
├── Validation.php
├── JobVacancy.php
├── AvailablePosition.php
├── JobApplySociety.php
└── JobApplyPosition.php
```

## 🎯 Sample Data yang Disediakan

### ✅ **Regionals** (3 records)
- DKI Jakarta - Central Jakarta
- DKI Jakarta - South Jakarta  
- West Java - Bandung

### ✅ **Job Categories** (10 records)
- Computing and ICT
- Construction and building
- Animals, land and environment
- Design, arts and crafts
- Education and training
- Healthcare and medicine
- Business and finance
- Manufacturing and engineering
- Transportation and logistics
- Hospitality and tourism

### ✅ **Societies** (10 records)
Sample accounts dengan ID Card Number dan password:
- `20210001` / `121212` - Omar Gunawan
- `20210002` / `121212` - Nilam Sinaga
- `20210003` / `121212` - Rosman Lailasari
- dst...

### ✅ **Admin Users & Validators** (7 records)
- `admin` / `admin123` - Super admin
- `validator1` sampai `validator3` / `121212`
- `officer1` sampai `officer3` / `121212`

### ✅ **Job Vacancies** (5 companies)
- PT. Maju Mundur Sejahtera
- PT. Tech Innovation
- PT. Digital Solutions
- PT. Creative Agency
- PT. Manufacturing Corp

### ✅ **Available Positions** (14 positions)
Berbagai posisi dari Programmer, Designer, Manager, dll.

### ✅ **Validations** (4 records)
Sample data validation dengan berbagai status

### ✅ **Job Applications** (3 records)
Sample lamaran kerja dengan status berbeda

## 🚀 Cara Menjalankan

### 1. **Run Migrations**
```bash
php artisan migrate
```

### 2. **Run Seeders**
```bash
php artisan db:seed
```

### 3. **Atau run sekaligus**
```bash
php artisan migrate:fresh --seed
```

## 🔑 Akun untuk Testing

### **Society Accounts:**
- ID Card: `20210001`, Password: `121212` (Omar Gunawan)
- ID Card: `20210002`, Password: `121212` (Nilam Sinaga)
- ID Card: `20210003`, Password: `121212` (Rosman Lailasari)

### **Admin Accounts:**
- Username: `admin`, Password: `admin123`
- Username: `validator1`, Password: `121212`
- Username: `officer1`, Password: `121212`

## 📊 Relationships

### **Foreign Key Constraints:**
- `societies.regional_id` → `regionals.id`
- `validators.user_id` → `admin_users.id`
- `validations.job_category_id` → `job_categories.id`
- `validations.society_id` → `societies.id`
- `validations.validator_id` → `validators.id`
- `job_vacancies.job_category_id` → `job_categories.id`
- `available_positions.job_vacancy_id` → `job_vacancies.id`
- `job_apply_societies.society_id` → `societies.id`
- `job_apply_societies.job_vacancy_id` → `job_vacancies.id`
- `job_apply_positions.society_id` → `societies.id`
- `job_apply_positions.job_vacancy_id` → `job_vacancies.id`
- `job_apply_positions.position_id` → `available_positions.id`
- `job_apply_positions.job_apply_societies_id` → `job_apply_societies.id`

## ✨ Fitur Models

- **Eloquent Relationships** - Semua relasi database sudah didefinisikan
- **Mass Assignment Protection** - Fillable attributes sudah ditentukan
- **Password Hashing** - Otomatis hash password untuk Society model
- **Date Casting** - Otomatis casting untuk tanggal
- **Hidden Attributes** - Password dan token disembunyikan dari serialization

Database ini siap digunakan untuk pengembangan aplikasi JobSeeker yang lengkap! 🎉
