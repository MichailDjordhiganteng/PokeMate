# Progres berdasarkan PDF pengguna

Acuan: **Progress PokePartner - Google Spreadsheet.pdf**, 6 halaman. Halaman 1–3 berisi daftar tugas; halaman 4–6 berisi catatan/bukti yang berurutan. Kolom **Awal PDF** mempertahankan status asli, sedangkan kolom **Kondisi sekarang** mencatat hasil implementasi saat ini.

## Fondasi, autentikasi, dashboard, dan Pokémon

| No. | Fitur / tugas | Awal PDF | Kondisi sekarang / bukti |
| --- | --- | --- | --- |
| 1 | Setup Laravel & koneksi MySQL | Selesai | Konfigurasi MySQL tersedia; contoh `.env` ada di README. Verifikasi sesi ini memakai SQLite, bukan server MySQL. |
| 2 | Migrasi 7 tabel bisnis | Selesai | Migrasi awal tersedia dan berhasil dijalankan; migrasi tambahan menyimpan tujuan/catatan rental. |
| 3 | Model & relasi Eloquent | Selesai | Relasi trainer, detail rental, Pokémon, tipe, dan admin pemroses dipakai serta diuji dalam alur rental. |
| 4 | Seeder Admin, Trainer, 18 tipe, 8 Pokémon | Selesai | Seeder tersedia dan berhasil dijalankan pada database lokal. |
| 5 | Login Admin/Trainer | Selesai | `AccountAndMasterDataTest`: login berhasil/gagal, redirect sesuai role. |
| 6 | Register Trainer + profil | Selesai | `AccountAndMasterDataTest`: profil dibuat; input role admin diabaikan. |
| 7 | Logout & pembatasan role | Selesai | Tes logout, akses tamu, akses lintas role, dan kepemilikan transaksi. |
| 8 | Dashboard Admin sementara | Selesai | Diganti dashboard dinamis dengan statistik dan rental terbaru. |
| 9 | Dashboard Trainer sementara | Selesai | Diganti dashboard bergaya mockup: identitas, kuota, jatuh tempo, tim, rekomendasi. |
| 10 | Read / daftar Pokémon | Selesai | Daftar, pagination, dan render halaman diuji. |
| 11 | Search nama atau kode | Perlu diuji | `PokemonChecklistTest`: pencarian nama/kode pada halaman admin dan katalog trainer. |
| 12 | Create / tambah Pokémon | Selesai | Pembuatan Pokémon dan relasi tipe diuji. |
| 13 | Validasi kode unik | Perlu diuji | `PokemonChecklistTest`: create/update duplikat ditolak; kode sendiri tetap boleh disimpan. |
| 14 | Update / edit Pokémon | Selesai | Update tarif diuji; partner `on_adventure` tidak dapat diubah. |
| 15 | Upload / ganti gambar | Perlu diuji | `PokemonChecklistTest`: upload, ganti, hapus gambar lama, dan penolakan isi file non-gambar. |
| 16 | Delete: Controller destroy | Selesai | Tersedia, dilengkapi row locking dan pemeriksaan ulang sebelum hapus. |
| 17 | Delete: Route DELETE | Selesai | Resource route tersedia dan dipanggil dalam tes. |
| 18 | Delete: tombol & flash error | Belum | Tombol hapus dan konfirmasi tersedia; flash error tampil melalui layout dan diuji. |
| 19 | Delete: uji hapus unit tanpa rental | Belum | Unit **PKM010** diuji: record, relasi tipe, dan gambar terhapus. |
| 20 | Delete: blok riwayat rental / on_adventure | Belum | Dua kondisi diuji terpisah, termasuk riwayat yang sudah `completed`; detail transaksi dan gambar tetap ada. |

## Fitur lanjutan

| No. | Fitur / tugas | Awal PDF | Kondisi sekarang / bukti |
| --- | --- | --- | --- |
| 21 | CRUD tipe Pokémon | Belum | Tambah/edit/hapus; tipe yang terpakai tidak bisa dihapus. Tes di `AccountAndMasterDataTest` dan `RentalWorkflowTest`. |
| 22 | Manajemen Trainer / Admin | Belum | `/admin/trainers` dan `/admin/admins`. Akun admin dapat ditambah/edit/hapus; akun sendiri dan riwayat pemrosesan dilindungi. |
| 23 | Katalog & pencarian Pokémon | Belum | Katalog dengan filter tipe/status, pencarian nama/kode, detail, serta tautan sewa. |
| 24 | Edit profil Trainer | Belum | Nama/email/kontak/avatar/password; password lama diperlukan untuk mengganti password. |
| 25 | Pemesanan Pokémon | Belum | Form, kalkulator, konfirmasi tanpa menyimpan, lalu transaksi rental/detail dan harga dari server. |
| 26 | Maksimum 2 Pokémon aktif per Trainer | Belum | Hitung detail dari rental belum selesai. Status `active`, `late`, `return_requested` diuji; slot kembali setelah selesai. |
| 27 | Durasi sewa maksimal 5 hari | Belum | Validasi 1–5 hari dan tanggal mulai tidak boleh di masa lalu; skenario negatif diuji. |
| 28 | Status Pokémon saat disewa | Belum | `available` → `on_adventure` dalam transaksi; pemesanan ganda ditolak. |
| 29 | Pengajuan pengembalian oleh Trainer | Belum | Hanya pemilik transaksi; berubah ke `return_requested`, partner tetap ditahan. |
| 30 | Konfirmasi pengembalian oleh Admin | Belum | Hanya admin; menentukan kondisi partner dan menyimpan admin pemroses. Pemrosesan ulang ditolak. |
| 31 | Denda keterlambatan | Belum | Rp 100.000/hari kalender hingga tanggal pengajuan; keterlambatan admin tidak menambah denda. Diuji dengan perjalanan waktu. |
| 32 | Riwayat transaksi Trainer | Belum | Riwayat hanya milik pengguna; selesai/dibatalkan dipisahkan dari rental berjalan. |
| 33 | Laporan Admin & cetak | Belum | Ringkasan/filter, CSV, halaman cetak seluruh hasil filter (tanpa pagination), simpan PDF melalui browser. Tes memeriksa 23 transaksi dalam hasil cetak. |
| 34 | Adventure Match berdasarkan tipe | Belum | Rekomendasi rule-based dengan tipe sesuai medan dan anggaran; filter diuji. |
| 35 | Integrasi UI sesuai mockup | Belum | Layout dan halaman disesuaikan dengan HTML ekspor Stitch: sidebar admin, dashboard/grafik, tabel, modal, autentikasi dua kolom, katalog, pilihan medan, checkout, tiket, pengembalian, dan profil. Audit browser desktop 1440px/mobile 390px lulus tanpa overflow halaman/error JS. Lihat `docs/UI.md`. |
| 36 | Pengujian validasi, role, alur rental | Belum | Feature tests mencakup skenario positif/negatif, kepemilikan, kuota, stok, denda, pengguna, gambar, dan laporan. |
| 37 | Dokumentasi & README | Belum | README instalasi, konfigurasi MySQL, akun demo, aturan, pengujian, cetak laporan; dokumen pemetaan dan progres tersedia. |
| 38 | Cek Git status, branch, remote | Belum | Sudah diperiksa. Remote `https://github.com/MichailDjordhiganteng/PokeMate.git`. Pengembangan dilanjutkan pada branch `feature/pokepartner-rental-stitch-ui`, terpisah dari `main`. |
| 39 | Commit & push branch aman | Belum | Commit dibuat atas instruksi pengguna pada branch `feature/pokepartner-rental-stitch-ui`; pengiriman ditujukan hanya ke branch tersebut tanpa merge ke `main`. `.env`, database lokal, dan file pengujian sementara diabaikan Git. |

## Verifikasi yang masih membutuhkan lingkungan tambahan

- Tes otomatis dijalankan pada SQLite in-memory; koneksi dan perilaku concurrency MySQL belum diuji pada sesi ini.
- Cetak telah diuji dari hasil HTML/server, termasuk seluruh baris dan filter. Dialog cetak dan pagination fisik kertas masih memerlukan pengujian dengan printer atau penyimpanan PDF pengguna. Tampilan halaman aplikasi sudah diperiksa melalui browser desktop/mobile.
- Mockup menyebut jam/toleransi pada beberapa contoh; PDF tidak menetapkan rumus detail. Implementasi saat ini memakai hari kalender sebagaimana README.
- Commit/push dikerjakan atas instruksi eksplisit pengguna, bukan berdasarkan checklist dokumen saja.

## Perintah pengecekan

Hasil pengujian otomatis setelah penyesuaian UI Stitch: **20 tes lulus, 243 assertions**. Laravel Pint juga dijalankan untuk merapikan format PHP. Audit browser mencakup halaman tamu, admin, dan trainer pada desktop/mobile, modal edit, validasi, menu responsif, pilihan durasi, serta alur ke halaman konfirmasi tanpa membuat rental baru.

```bash
php artisan test
php artisan view:cache
php artisan route:list --except-vendor
git diff --check
```

Gunakan `php.exe` bila menjalankan dari WSL menggunakan PHP XAMPP yang terdeteksi pada lingkungan ini.
