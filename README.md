<div align="center">

# ⚡ PokePartner

### Pokémon Adventure Companion Rental System

**Temukan partner Pokémon yang tepat untuk menemani setiap petualangan.**

</div>

---

## 📌 Tentang Project

**PokePartner** adalah aplikasi berbasis web yang memungkinkan pengguna berperan sebagai **Trainer** untuk menyewa Pokémon sebagai partner atau companion dalam sebuah petualangan.

Trainer dapat melihat katalog Pokémon, mencari Pokémon berdasarkan nama, melakukan filter berdasarkan tipe, melihat detail Pokémon, serta melakukan rental dalam periode tertentu.

Sementara itu, **Admin** bertanggung jawab dalam pengelolaan data Pokémon, tipe Pokémon, Trainer, transaksi rental, proses pengembalian, keterlambatan, histori transaksi, serta laporan.

Project ini dikembangkan sebagai implementasi sistem informasi rental dengan menerapkan konsep:

- Authentication
- Authorization
- Role Management
- CRUD
- Relational Database
- One-to-One Relationship
- One-to-Many Relationship
- Many-to-Many Relationship
- Transaction Management
- Validation
- Rental History
- Reporting
- Database Migration
- Database Seeder

---

# 👥 Identitas Kelompok

### Nama Kelompok
`Isi nama kelompok`

### Nama Team
`Isi nama team`

### Anggota Team

| No | Nama | NIM | Role |
|---|---|---|---|
| 1 | Nama Anggota | NIM | Project Manager |
| 2 | Nama Anggota | NIM | Frontend Developer |
| 3 | Nama Anggota | NIM | Backend Developer |
| 4 | Nama Anggota | NIM | Database / Documentation |

---

# 🎯 Tujuan Project

PokePartner dikembangkan untuk:

1. Menyediakan sistem rental Pokémon yang terstruktur.
2. Mempermudah Trainer dalam mencari Pokémon yang tersedia.
3. Mengelola data Pokémon dan tipe Pokémon.
4. Mengelola transaksi rental dan pengembalian.
5. Membatasi jumlah Pokémon yang dapat dirental.
6. Mengatur durasi maksimal rental.
7. Menghitung keterlambatan pengembalian.
8. Menyimpan histori transaksi.
9. Memberikan kontrol penuh kepada Admin terhadap data master dan transaksi.
10. Menyediakan laporan rental.

---

# 👤 User Role

Sistem memiliki dua jenis pengguna utama.

## 🧑‍💼 Admin

Admin memiliki akses untuk:

- Login ke sistem.
- Melihat dashboard Admin.
- Mengelola data Trainer.
- Menambah, melihat, mengubah, dan menghapus Pokémon.
- Mengelola Type Pokémon.
- Melihat seluruh transaksi rental.
- Melihat Active Rental.
- Melihat Overdue Rental.
- Memproses pengembalian Pokémon.
- Melihat histori rental.
- Melihat statistik sistem.
- Mencetak laporan rental.

---

## 🧢 Trainer

Trainer memiliki akses untuk:

- Register.
- Login.
- Logout.
- Melihat profile.
- Mengubah profile.
- Melihat katalog Pokémon.
- Mencari Pokémon berdasarkan nama.
- Melakukan filter berdasarkan Type.
- Melihat detail Pokémon.
- Melakukan rental Pokémon.
- Melihat Pokémon yang sedang dirental.
- Melakukan request pengembalian.
- Melihat histori rental miliknya.

---

# ✨ Fitur Utama

## 🔐 Authentication

- Register Trainer
- Login
- Logout
- Role-based Authorization
- Protected Route

---

## 👤 Trainer Profile

Setiap Trainer hanya memiliki satu profile.

Informasi profile dapat berisi:

- Nama Trainer
- Nomor telepon
- Alamat
- Trainer Level
- Avatar

---

## 🐾 Pokémon Management

Admin dapat melakukan CRUD terhadap data Pokémon.

Data Pokémon meliputi:

- Pokémon Code
- Pokémon Name
- Level
- Description
- Rental Price
- Status
- Image
- Type

Nama Pokémon boleh sama, tetapi setiap unit harus mempunyai kode yang berbeda.

Contoh:

```text
Pikachu
Code: PKC-001

Pikachu
Code: PKC-002
```

---

# 🔥 Multiple Pokémon Type

Satu Pokémon dapat memiliki lebih dari satu Type.

Contoh:

```text
Charizard
├── Fire
└── Flying
```

```text
Bulbasaur
├── Grass
└── Poison
```

Relasi Pokémon dengan Type menggunakan konsep **Many-to-Many**.

---

# 🔎 Search & Filter

Trainer dapat mencari Pokémon berdasarkan nama.

Trainer juga dapat melakukan filter berdasarkan:

- Fire
- Water
- Grass
- Electric
- Flying
- Poison
- Psychic
- Normal
- dan Type lainnya.

---

# 🛒 Rental System

Trainer dapat melakukan rental Pokémon yang memiliki status:

```text
Available
```

Sebelum transaksi dilakukan, sistem akan melakukan validasi terhadap:

- Status Pokémon.
- Jumlah Pokémon aktif milik Trainer.
- Durasi rental.

---

# 📋 Business Rules

PokePartner memiliki beberapa aturan utama.

### Maksimal Pokémon

Satu Trainer hanya dapat memiliki maksimal:

```text
2 Pokémon aktif
```

Jika Trainer sudah memiliki dua Pokémon dalam rental aktif, maka rental baru akan ditolak.

---

### Durasi Rental

Durasi rental maksimal adalah:

```text
5 hari
```

Trainer tidak dapat memilih due date lebih dari lima hari sejak tanggal rental dimulai.

---

### Availability

Pokémon hanya dapat dirental apabila statusnya:

```text
Available
```

Pokémon dengan status berikut tidak dapat dirental:

```text
On Adventure
Resting
Unavailable
```

---

# 🔄 Pokémon Status

Status Pokémon pada sistem:

```text
Available
    ↓
Rental
    ↓
On Adventure
    ↓
Return
    ↓
Resting
    ↓
Available
```

### Available

Pokémon tersedia untuk dirental.

### On Adventure

Pokémon sedang digunakan oleh Trainer.

### Resting

Pokémon baru selesai dari petualangan dan belum dapat dirental.

### Unavailable

Pokémon sedang tidak tersedia.

---

# 📦 Rental Status

Transaksi rental memiliki beberapa status:

```text
Active
Return Requested
Overdue
Completed
Cancelled
```

### Active

Rental sedang berlangsung.

### Return Requested

Trainer sudah meminta pengembalian Pokémon.

### Overdue

Rental melewati batas waktu pengembalian.

### Completed

Rental telah selesai dan diproses oleh Admin.

### Cancelled

Rental dibatalkan.

---

# ↩️ Return Process

Trainer tidak dapat menyelesaikan proses pengembalian secara langsung.

Flow pengembalian:

```text
Trainer
   ↓
Request Return
   ↓
Admin menerima request
   ↓
Admin memproses pengembalian
   ↓
Sistem mengecek due date
   ↓
Apakah terlambat?
   ↓
Yes ────── No
 ↓          ↓
Late Fee   Completed
 ↓
Completed
```

Hanya **Admin** yang dapat menyelesaikan proses pengembalian.

---

# 💰 Late Fee

Jika Pokémon dikembalikan setelah `due_date`, sistem akan menentukan bahwa transaksi telah mengalami keterlambatan.

Informasi keterlambatan akan disimpan pada:

```text
late_fee
```

---

# 🕒 Rental History

Trainer dapat melihat:

- Rental aktif.
- Rental sebelumnya.
- Pokémon yang pernah dirental.
- Tanggal rental.
- Due date.
- Status rental.
- Total pembayaran.
- Late fee.

Trainer hanya dapat melihat histori miliknya sendiri.

Admin dapat melihat seluruh histori transaksi.

---

# 📊 Dashboard

## Trainer Dashboard

Dashboard Trainer menampilkan:

- Active Rental.
- Jumlah Pokémon yang sedang digunakan.
- Due Date.
- Overdue Warning.
- Available Pokémon.
- Rental History terbaru.

---

## Admin Dashboard

Dashboard Admin menampilkan:

- Total Pokémon.
- Available Pokémon.
- Pokémon On Adventure.
- Resting Pokémon.
- Total Trainer.
- Active Rental.
- Overdue Rental.
- Completed Rental.

---

# 🗄️ Database Structure

Database utama PokePartner terdiri dari:

```text
users
trainer_profiles
pokemons
types
pokemon_types
rentals
rental_details
```

Relasi utama:

```text
USER
 │
 │ 1 : 1
 ▼
TRAINER_PROFILE


USER
 │
 │ 1 : N
 ▼
RENTAL
 │
 │ 1 : N
 ▼
RENTAL_DETAIL
 │
 │ N : 1
 ▼
POKEMON
 │
 │ N : N
 ▼
TYPE
```

---

# 🧩 Entity Relationship Diagram

Tambahkan gambar ERD ke folder project, misalnya:

```text
docs/erd-pokepartner.png
```

Kemudian tampilkan menggunakan:

```markdown
![ERD PokePartner](docs/erd-pokepartner.png)
```

### ERD PokePartner

> Tambahkan screenshot ERD final pada bagian ini.

---

# 🗃️ Struktur Data Utama

## Users

Digunakan untuk menyimpan akun Admin dan Trainer.

```text
id
full_name
email
password
role
created_at
updated_at
```

---

## Trainer Profiles

Menyimpan informasi profile Trainer.

```text
id
user_id
phone
address
trainer_level
avatar
created_at
updated_at
```

---

## Pokémons

Menyimpan data Pokémon.

```text
id
pokemon_code
pokemon_name
pokemon_level
description
rental_price
status
pokemon_image
created_at
updated_at
```

---

## Types

Menyimpan Type Pokémon.

```text
id
type
description
created_at
updated_at
```

---

## Pokémon Types

Pivot table antara Pokémon dan Type.

```text
pokemon_id
type_id
```

---

## Rentals

Menyimpan transaksi rental.

```text
id
rental_code
trainer_id
start_date
due_date
rental_status
total_price
late_fee
return_requested_at
returned_at
processed_by_admin_id
created_at
updated_at
```

---

## Rental Details

Menyimpan Pokémon yang terdapat dalam suatu transaksi rental.

```text
id
rental_id
pokemon_id
rental_price
created_at
updated_at
```

---

# 🔁 Main Application Flow

```text
START
  ↓
Register / Login
  ↓
Dashboard Trainer
  ↓
Browse Pokémon
  ↓
Search / Filter
  ↓
View Pokémon Detail
  ↓
Rent Pokémon
  ↓
Validation
  ├── Pokémon Available?
  ├── Active Pokémon < 2?
  └── Duration <= 5 Days?
  ↓
Rental Created
  ↓
Pokémon → On Adventure
  ↓
Adventure
  ↓
Request Return
  ↓
Admin Process Return
  ↓
Check Overdue
  ↓
Rental Completed
  ↓
Pokémon → Resting / Available
  ↓
END
```

---

# 🛠️ Tech Stack

> Sesuaikan bagian ini dengan teknologi yang benar-benar digunakan project.

### Backend

```text
Laravel / PHP
```

### Frontend

```text
Blade
HTML
CSS
JavaScript
Tailwind CSS / Bootstrap
```

### Database

```text
MySQL
```

### Development Tools

```text
Visual Studio Code
Git
GitHub
XAMPP / Laragon
```

---

# ⚙️ Installation

## 1. Clone Repository

```bash
git clone https://github.com/USERNAME/pokepartner.git
```

Masuk ke folder project:

```bash
cd pokepartner
```

---

## 2. Install Dependency

Apabila menggunakan Laravel:

```bash
composer install
```

Kemudian:

```bash
npm install
```

---

## 3. Environment Configuration

Copy `.env.example`:

```bash
cp .env.example .env
```

Untuk Windows:

```bash
copy .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

## 4. Database Configuration

Buat database baru, misalnya:

```text
pokepartner
```

Kemudian ubah konfigurasi `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pokepartner
DB_USERNAME=root
DB_PASSWORD=
```

---

## 5. Migration & Seeder

Jalankan:

```bash
php artisan migrate --seed
```

Atau untuk melakukan reset database:

```bash
php artisan migrate:fresh --seed
```

---

## 6. Run Application

```bash
php artisan serve
```

Kemudian buka:

```text
http://127.0.0.1:8000
```

---

# 🔑 Demo Account

## Admin

```text
Email    : admin@pokepartner.test
Password : password
```

## Trainer

```text
Email    : trainer@pokepartner.test
Password : password
```

> Gunakan akun di atas hanya untuk development/demo.

---

# 🌱 Seeder Data

Project menyediakan data awal berupa:

### User

- Admin
- Trainer

### Pokémon Type

- Fire
- Water
- Grass
- Electric
- Flying
- Poison
- Psychic
- Normal

### Pokémon

Contoh:

```text
PKC-001 | Pikachu
PKC-002 | Pikachu
PKC-003 | Charizard
PKC-004 | Bulbasaur
PKC-005 | Squirtle
```

---

# ✅ Validation

Beberapa validasi yang diterapkan:

```text
Email
→ Required
→ Unique

Password
→ Required
→ Minimal 8 karakter

Pokemon Code
→ Required
→ Unique

Rental Price
→ Required
→ Numeric
→ > 0

Pokemon Level
→ Numeric

Start Date
→ Required

Due Date
→ Maksimal 5 hari

Active Pokémon
→ Maksimal 2 Pokémon / Trainer
```

---

# 🔒 Security

Sistem menerapkan:

- Password hashing.
- Authentication middleware.
- Role-based middleware.
- Authorization.
- Form validation.
- Protected Admin Route.
- Trainer tidak dapat melihat rental Trainer lain.

---

# 📑 Documentation

Dokumentasi project meliputi:

- Business Requirement Document (BRD)
- Flowchart
- Entity Relationship Diagram (ERD)
- Product Requirement Document (PRD)
- UI/UX Design
- User Acceptance Testing (UAT)
- Manual Book
- Project Documentation

Dokumen dapat disimpan pada:

```text
/docs
```

Contoh:

```text
docs/
├── BRD-PokePartner.pdf
├── PRD-PokePartner.pdf
├── ERD-PokePartner.png
├── Flowchart-PokePartner.png
├── UAT-PokePartner.pdf
└── Manual-Book-PokePartner.pdf
```

---

# 🖼️ Screenshots

## Login

> Tambahkan screenshot Login.

```markdown
![Login](docs/screenshots/login.png)
```

## Trainer Dashboard

> Tambahkan screenshot Trainer Dashboard.

```markdown
![Trainer Dashboard](docs/screenshots/trainer-dashboard.png)
```

## Pokémon Catalogue

> Tambahkan screenshot Pokémon Catalogue.

```markdown
![Pokemon Catalogue](docs/screenshots/pokemon-catalogue.png)
```

## Pokémon Detail

> Tambahkan screenshot detail Pokémon.

```markdown
![Pokemon Detail](docs/screenshots/pokemon-detail.png)
```

## My Rental

> Tambahkan screenshot My Rental.

```markdown
![My Rental](docs/screenshots/my-rental.png)
```

## Admin Dashboard

> Tambahkan screenshot Admin Dashboard.

```markdown
![Admin Dashboard](docs/screenshots/admin-dashboard.png)
```

---

# 🎥 Demo Video

Demo website dapat dilihat melalui:

**Demo Video:**  
`Masukkan link Google Drive / YouTube di sini`

Video demo sebaiknya menjelaskan:

1. Login Admin.
2. Login Trainer.
3. Pokémon Catalogue.
4. Search & Filter.
5. Rental Pokémon.
6. Validasi maksimal Pokémon.
7. My Rental.
8. Request Return.
9. Process Return oleh Admin.
10. Rental History.
11. Dashboard Admin.

---

# 📁 Project Structure

Contoh apabila menggunakan Laravel:

```text
PokePartner/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   │
│   └── Models/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── public/
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│   └── web.php
│
├── docs/
│   ├── screenshots/
│   ├── BRD-PokePartner.pdf
│   ├── PRD-PokePartner.pdf
│   └── ERD-PokePartner.png
│
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

# 🧪 Testing

Beberapa scenario utama yang perlu diuji:

- User dapat register.
- User dapat login.
- Admin dapat CRUD Pokémon.
- Admin dapat CRUD Type.
- Pokémon dapat memiliki multiple Type.
- Pokémon Code harus unique.
- Trainer dapat melakukan search.
- Trainer tidak dapat menyewa Pokémon yang tidak Available.
- Trainer tidak dapat memiliki lebih dari 2 Pokémon aktif.
- Durasi rental tidak dapat lebih dari 5 hari.
- Trainer dapat melakukan request return.
- Hanya Admin yang dapat memproses return.
- Sistem dapat mendeteksi overdue.
- Trainer hanya dapat melihat transaksi miliknya.
- Admin dapat melihat seluruh transaksi.

---

# 📌 Project Status

```text
🟡 In Development
```

Progress:

- [x] BRD
- [x] ERD
- [x] PRD
- [ ] UI/UX Design
- [ ] Authentication
- [ ] Trainer Profile
- [ ] Pokémon CRUD
- [ ] Pokémon Type
- [ ] Pokémon Catalogue
- [ ] Search & Filter
- [ ] Rental System
- [ ] Return System
- [ ] Late Fee
- [ ] Dashboard
- [ ] Reporting
- [ ] UAT
- [ ] Manual Book
- [ ] Final Documentation

---

# 📝 License

Project ini dikembangkan untuk keperluan **akademik dan pembelajaran**.

Pokémon dan seluruh nama karakter terkait merupakan hak milik dari pemegang hak kekayaan intelektual masing-masing.

PokePartner merupakan project fan-made/non-commercial dan tidak berafiliasi dengan Nintendo, Game Freak, Creatures Inc., maupun The Pokémon Company.

---

<div align="center">

## ⚡ PokePartner

### Your Partner for Every Adventure.

Made with ❤️ by **[Nama Team]**

</div>
