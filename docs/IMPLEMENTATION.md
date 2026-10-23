# Pemetaan implementasi

Repository awal berisi autentikasi, model/migrasi/seeder, CRUD Pokémon, serta dashboard statis. Mockup ditemukan di folder saudara `stitch_pokepartner_pok_mon_rental_system`.

| Mockup | Implementasi |
| --- | --- |
| Companion system | `/` |
| Login / registrasi trainer | `/login`, `/register` |
| Dashboard admin / trainer | `/admin/dashboard`, `/trainer/dashboard` |
| Kelola Pokémon | `/admin/pokemons` beserta tambah/edit/hapus/upload gambar |
| Kelola tipe | `/admin/types` |
| Kelola trainer | `/admin/trainers` |
| Kelola akun admin (checklist PDF) | `/admin/admins` |
| Monitoring dan detail rental admin | `/admin/rentals`, `/admin/rentals/{rental}` |
| Pengembalian partner admin | `/admin/returns` |
| Laporan rental | `/admin/reports`, `/admin/reports/export`, `/admin/reports/print` |
| Katalog Pokédex | `/trainer/catalog` |
| Detail Pokémon | `/trainer/catalog/{pokemon}` |
| Adventure Match | `/trainer/adventure-match` |
| Form sewa | `/trainer/rentals/create/{pokemon}` |
| Konfirmasi rental | POST `/trainer/rentals/confirm` |
| Rental berhasil | `/trainer/rentals/{rental}` dengan notifikasi sukses |
| Rental saya | `/trainer/rentals` |
| Ajukan pengembalian | `/trainer/rentals/{rental}/return` |
| Riwayat rental | `/trainer/rentals/history` |
| Profil saya | `/trainer/profile` |

## Acuan checklist dan batas verifikasi

Checklist pengguna telah diterima dalam bentuk `Progress PokePartner - Google Spreadsheet.pdf` (6 halaman). Status tiap fitur dan bukti dicatat di `docs/PROGRESS.md`. Berkas PDF sumber berada di folder saudara repository; isi status awal tetap dipertahankan dalam kolom tersendiri pada dokumen progres.

- UI disesuaikan kembali dengan HTML ekspor Stitch dan `DESIGN.md`: sidebar/topbar admin, dashboard 8 KPI dan grafik data, tabel inventaris, modal edit, login/register dua kolom, katalog 4 kolom, kartu medan Adventure Match, detail partner, checkout, tiket rental, pengembalian, riwayat, dan kartu identitas trainer. Halaman telah diperiksa di browser pada lebar 1440px dan 390px; tidak ditemukan overflow horizontal halaman atau error JavaScript dalam audit akhir. Rincian ada di `docs/UI.md`.
- Rekomendasi menggunakan pemetaan tipe/medan yang eksplisit, bukan model machine learning.
- Denda memakai hari kalender; mockup menyebut jam dan toleransi 60 menit yang perlu dipastikan sebagai kebutuhan final.
- Upload avatar dan gambar Pokémon tersedia (JPG/PNG/WebP, maksimal 2 MB).
- Penyewaan dimulai sebagai `active` dan langsung menahan partner; tidak ada alur pembayaran atau persetujuan awal admin.
- Ekspor berupa CSV kompatibel Excel, bukan workbook XLSX.
- Cetak memakai halaman khusus berisi seluruh transaksi yang sesuai filter dan stylesheet A4 landscape; PDF dapat disimpan melalui dialog cetak browser.
- Git sudah diperiksa; remote mengarah ke repository PokeMate. Pengembangan disimpan pada branch `feature/pokepartner-rental-stitch-ui`, terpisah dari `main`. Riwayat `origin/main` terbaru diikutkan ke branch fitur sebelum pengiriman; `main` tidak menjadi target push.
