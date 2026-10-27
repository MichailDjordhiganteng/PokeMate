# PokeMate / PokePartner

Aplikasi rental Pokémon berbasis Laravel 12 (PHP 8.2+). Dua peran: **admin** dan **trainer**. Tampilan menggunakan layout responsif dengan warna navy, kuning, dan mint dari mockup PokePartner.

UI telah disesuaikan dengan ekspor Stitch: sidebar admin, dashboard statistik/grafik, tabel dan modal operasional, login/register dua kolom, katalog berwarna berdasarkan tipe, kartu Adventure Match, checkout, tiket rental, dan profil trainer. Rincian acuan, komponen, dan verifikasi ada di `docs/UI.md`. Perubahan UI tidak memerlukan migrasi database tambahan.

Landing publik kini memakai layout pemasaran premium tersendiri: hero dua kolom dengan companion preview, featured Pokémon dari database, type explorer, manfaat, dan CTA. Navbar sticky, reveal, serta hover responsif dibangun tanpa library animasi tambahan. Lihat `docs/LANDING.md` untuk struktur komponen, sumber data, dan hasil pengujian.

Backsound **Pokémon Red/Blue Opening** tersedia melalui dock musik di kanan bawah pada landing dan halaman aplikasi. Sistem mencoba autoplay pada kunjungan pertama; jika browser memblokir, klik halaman atau tekan **Lanjutkan musik** untuk memulai loop. Volume, mute, pilihan mati, dan posisi lagu disimpan di browser. Rincian penggunaan dan pemulihan audio saat navigasi ada di `docs/AUDIO.md`.

## Menjalankan secara lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Pada Windows gunakan `copy .env.example .env` untuk menggantikan `cp`. Jika memakai WSL dengan PHP XAMPP yang tersedia di PATH, gunakan `php.exe` sebagai pengganti `php`. Default menggunakan SQLite; buat file kosong `database/database.sqlite` jika Laravel menanyakannya. Buka `http://127.0.0.1:8000`. Halaman aplikasi memakai CSS langsung di `public/css/`, sehingga tidak memerlukan proses build Node untuk dijalankan.

Untuk MySQL, ubah `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env` sebelum menjalankan migrasi. Untuk proyek dengan database yang sudah ada, jalankan `php artisan migrate` tanpa menghapus tabel.

Contoh konfigurasi MySQL lokal setelah membuat database `pokepartner`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pokepartner
DB_USERNAME=root
DB_PASSWORD=
```

### Akun demo setelah seeding

| Peran | Email | Password |
| --- | --- | --- |
| Admin | admin@gmail.com | password |
| Trainer | trainer@gmail.com | password |

## Fitur

- Landing page, registrasi trainer beserta profil, login, logout, pembatasan akses per peran.
- Dashboard dinamis admin dan trainer.
- Admin: CRUD Pokémon termasuk upload gambar dan relasi tipe; kelola tipe, trainer, dan akun admin; monitoring rental; pemeriksaan pengembalian; laporan dengan filter, cetak/simpan PDF, serta ekspor CSV yang dapat dibuka di Excel.
- Trainer: katalog dan filter tipe/status, detail Pokémon, Adventure Match berdasarkan medan/anggaran, formulir sewa dengan kalkulator biaya, konfirmasi, bukti rental, rental aktif, riwayat, pengajuan pengembalian, serta edit profil/password.


## Aturan rental

- Batas maksimal Pokémon yang dapat disewa setiap Trainer bersifat dinamis dan dapat diubah Admin melalui halaman Kelola Trainer.
- Pengaturan kuota disimpan pada tabel `rental_settings`, dengan nilai awal 2 Pokémon dan rentang pengaturan 1–20 Pokémon.
- Kuota dihitung berdasarkan jumlah Pokémon pada rental yang belum selesai, yaitu status `pending`, `active`, `late`, dan `return_requested`.
- Perubahan batas rental berlaku untuk penyewaan berikutnya. Rental yang sudah berjalan tidak otomatis dibatalkan jika Admin menurunkan batas.
- Durasi penyewaan adalah 1–5 hari.
- Pokémon harus berstatus `available` untuk dapat disewa.
- Harga rental dihitung oleh server berdasarkan tarif Pokémon yang tersimpan di database.
- Pokémon langsung berubah menjadi `on_adventure` ketika rental berhasil, termasuk untuk tanggal mulai mendatang.
- Pengembalian hanya dapat diajukan oleh pemilik rental setelah tanggal mulai tiba.
- Pokémon tetap tidak tersedia sampai Admin menyelesaikan pemeriksaan pengembalian.
- Admin menentukan kondisi Pokémon setelah pengembalian: tersedia, istirahat, atau tidak tersedia.
- Denda keterlambatan sebesar Rp 100.000 per hari kalender, dihitung sampai tanggal Trainer mengajukan pengembalian.
- Proses rental menggunakan database transaction dan row locking untuk membantu menjaga konsistensi kuota dan ketersediaan Pokémon.
- Setelah Admin menyelesaikan pengembalian, sistem mengirim bukti penyelesaian rental dalam format PDF ke email Trainer melalui Laravel Mail dan SMTP.
- Kredensial email disimpan dalam konfigurasi `.env`, bukan dalam source code.

Checklist telah dicocokkan dengan `Progress PokePartner - Google Spreadsheet.pdf` yang diberikan pengguna. Batas slot/durasi sesuai PDF; nominal denda mengikuti mockup. Denda saat ini dihitung per hari kalender, bukan toleransi per jam. Profil mendukung upload avatar. Gambar Pokémon demo memakai artwork PokeAPI dan memerlukan internet; gambar upload disajikan dari storage lokal.

## Cetak laporan dan kelola admin

- Buka **Laporan**, terapkan filter, lalu pilih **Cetak / Simpan PDF**. Halaman cetak memuat seluruh transaksi sesuai filter, termasuk yang berada di luar halaman pertama. Gunakan dialog cetak browser untuk memilih printer atau menyimpan PDF.
- Buka menu **Admin** untuk menambah, mencari, mengedit, atau menghapus akun admin. Password baru bersifat opsional saat edit. Mengganti password akun sendiri memerlukan password saat ini.

## Pengujian

### Notifikasi

Notifikasi tampil sebagai kartu di kanan atas dengan progress bar, watermark, dan ikon Poké Ball lokal:

| Flash session | Judul | Ikon | Durasi |
| --- | --- | --- | --- |
| `success` | Berhasil! | Friend Ball (hijau, titik merah/kuning) | 6,5 detik |
| `error` | Gagal! | Poké Ball (merah/putih) | 10 detik |
| `warning` | Perhatian! | Ultra Ball (hitam/kuning) | 10 detik |
| `info` | Informasi | Great Ball (biru/pink/putih) | 8 detik |

Gunakan `redirect()->back()->with('success', 'Pesan notifikasi')` atau jenis lainnya. Hitung mundur berhenti saat hover/fokus, tab tidak aktif, atau modal konfirmasi terbuka. Pesan validasi tidak ditutup otomatis. Semua notifikasi dapat ditutup melalui tombol ×. Ikon SVG berada di `public/images/pokeballs` dan tidak membutuhkan koneksi internet.

### Perintah tes

```bash
php artisan test
php artisan view:cache
php artisan route:list
```

Tes memakai SQLite in-memory dan memeriksa alur rental/pengembalian, perhitungan biaya/denda, kuota termasuk status `late`/`return_requested`, akses peran/kepemilikan, filter rekomendasi, perlindungan master data, upload/ganti/hapus gambar, kode duplikat, pencarian, pengelolaan admin, cetak seluruh hasil filter, serta render halaman utama. Migrasi dan seeding MySQL XAMPP serta tampilan browser desktop/mobile telah diverifikasi. Pengujian concurrency khusus MySQL belum dilakukan.

## Branch pengembangan

Perubahan fitur dan UI disimpan pada branch `feature/pokepartner-rental-stitch-ui`. Pengiriman perubahan ditujukan ke branch ini; penggabungan ke `main` dilakukan terpisah setelah review.

Lihat `docs/IMPLEMENTATION.md` untuk pemetaan mockup dan `docs/PROGRESS.md` untuk status setiap butir PDF beserta bukti implementasi/pengujian.
