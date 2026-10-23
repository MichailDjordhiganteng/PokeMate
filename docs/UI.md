# Implementasi UI dari Stitch

## Acuan

- Tautan pengguna: `https://stitch.withgoogle.com/projects/15350270984150749189`.
- Ekspor lokal: `../stitch_pokepartner_pok_mon_rental_system/`.
- Design tokens: `pokepartner_companion_system/DESIGN.md`.
- Beberapa `screen.png` ekspor hanya berisi placeholder teks dan tidak dapat didekode sebagai gambar. Halaman terkait dibandingkan dengan hasil render `code.html`, tanpa mengubah berkas acuan.
- Tautan publik hanya memberikan shell aplikasi Stitch saat diakses dalam sesi ini; implementasi didasarkan pada ekspor lokal tersebut.

## Penyesuaian per area

| Area | Penyesuaian |
| --- | --- |
| Layout admin | Sidebar navy 288px, navigasi aktif kuning, topbar pencarian/tanggal, jumlah pengembalian, identitas admin, logout, drawer pada HP. |
| Dashboard admin | Hero operasional, 8 KPI, donut distribusi status, grafik rental/pengembalian 7 hari, pintasan, tabel transaksi terbaru. Statistik diambil dari database. |
| Inventaris Pokémon | KPI inventaris, filter tipe/status/urutan, thumbnail partner, badge elemen berwarna, status, tombol edit/hapus, form tipe berbentuk pilihan chip. |
| Tipe/trainer/admin | Tabel dengan ikon/identitas, statistik yang relevan, pencarian, modal tambah/edit, dan konfirmasi hapus yang sudah ada. |
| Monitoring/pengembalian | Ringkasan operasional, indikator keterlambatan, filter transaksi, daftar partner, blok pemeriksaan pengembalian dan kondisi partner. |
| Laporan | Filter dokumen, 5 KPI, masthead dokumen, tabel transaksi, ekspor dan cetak seluruh hasil filter. |
| Login/register | Layout dua kolom, ilustrasi dari mockup yang disimpan lokal, panel informasi, input berikon, toggle password, akun demo pada lingkungan lokal, opsi remember-me. |
| Navigasi trainer | Navbar dengan menu aktif, jumlah slot, avatar dan dropdown profil; menu lipat pada HP. |
| Katalog | Console filter, pencarian, urutan, grid 4 kolom desktop, warna artwork sesuai tipe, watermark, status, harga, detail dan sewa. |
| Adventure Match | Hero, 5 kartu medan pilihan, anggaran, hasil rekomendasi dan indikator kuota. Jalur `city` yang sudah ada tetap didukung backend. |
| Detail partner | Artwork besar, identitas dan tipe, informasi partner, pilihan durasi, kalkulator harga, tautan ke form sewa dengan durasi terpilih. |
| Form sewa | Breadcrumb, banner kuota, segmented duration, jatuh tempo dinamis, tujuan/catatan/persetujuan, kartu partner dan rincian biaya. |
| Konfirmasi | Stepper, kartu partner, ringkasan tanggal/durasi/harga, regulasi, ubah rincian tanpa kehilangan rencana. |
| Rental berhasil/detail | Hero berhasil saat pemesanan sukses, kartu partner, tiket transaksi, status, biaya aktual, pengajuan pengembalian. |
| Rental aktif/riwayat | Kartu tim petualangan dengan progress tanggal, tautan pengembalian, statistik milik pengguna, pencarian riwayat dan tabel transaksi. |
| Pengajuan pengembalian | Stepper pengajuan/inspeksi/selesai, kartu partner, rincian kontrak, estimasi denda, persetujuan dan tombol pengajuan. |
| Profil | Kartu identitas dengan avatar, tanggal bergabung dan jumlah ekspedisi, form kontak, upload foto, bagian keamanan/password. |

## Komponen dan perilaku

- Komponen Blade: `icon`, `brand`, `avatar`, `metric`, `page-heading`, `type-badge`, `status`, `pokemon-art`, `steps`, `modal`, `empty-state`, `file-upload`.
- Stylesheet desain: `public/css/stitch-ui.css`.
- Perilaku UI: `public/js/site.js` (menu, dialog, password, pilihan durasi, label file).
- Logo PNG, notifikasi Poké Ball, validasi form, konfirmasi hapus, dan micro-interactions permintaan pengguna tetap digunakan.
- Tabel lebar bergulir di dalam kontainernya pada HP; halaman tidak bergulir horizontal.
- Drawer admin mendukung fokus keyboard, Escape, backdrop, dan `inert` saat tertutup. Notifikasi validasi pada modal ditampilkan di dalam top-layer dialog.
- UI mengikuti data model yang ada. Nilai contoh mockup seperti XP, rating, sertifikasi, saldo/deposit, dan jumlah trainer tidak dipasang sebagai statistik palsu.

## Verifikasi

- Halaman tamu/admin/trainer diperiksa pada viewport **1440 × 1000** dan **390 × 1000**.
- Login, akses peran, menu mobile, modal tipe, validasi modal, durasi 5 hari, dan alur form → konfirmasi diuji di browser.
- Audit akhir tidak menemukan error JavaScript atau overflow horizontal dokumen.
- PHPUnit: **20 tes lulus, 243 assertions**, termasuk filter tipe/status dan urutan berdasarkan nilai database.
- Blade cache, Laravel Pint, dan `git diff --check` berhasil.
- Rumus rental/denda, pembatasan peran, dan proteksi riwayat tetap berlaku. Tidak diperlukan migrasi tambahan untuk perubahan UI ini.
