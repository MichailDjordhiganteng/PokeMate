# Landing page premium PokePartner

Landing page publik mengikuti brief modern SaaS/adventure dengan identitas navy, biru, dan kuning. Route `/` tetap bernama `home`; alur login, registrasi, dan rental memakai route Laravel yang sudah ada.

## Struktur

- `resources/views/landing.blade.php`: urutan section landing.
- `resources/views/layouts/landing.blade.php`: layout pemasaran khusus, terpisah dari layout dashboard.
- `resources/views/landing/`: navbar, hero, stats, how-it-works, featured, types, benefits, CTA, dan footer.
- `public/css/landing.css`: palette, layout maksimal 1248px, tipografi, komponen, breakpoint, hover, dan reveal.
- `public/js/landing.js`: navigasi mobile, header saat scroll, active anchor, reveal, dan spotlight hero yang ringan.
- `app/View/Composers/LandingPageComposer.php`: data presentasi read-only untuk landing.

Stylesheet dashboard tidak dimuat di landing. Sebaliknya, stylesheet dan script landing tidak dimuat di halaman admin/trainer. Ikon, logo Poké Ball PNG, dan komponen notifikasi yang sudah ada tetap dipakai.

## Data dan tautan

- Statistik jumlah Pokémon dan tipe dihitung dari database, bukan angka contoh.
- Featured partners dibatasi 4 unit dan memprioritaskan status `available`, lalu diurutkan berdasarkan kode.
- Hero memakai partner pertama dari koleksi tersebut. Nama, kode, level, status, tipe, gambar, dan tarif berasal dari model yang sama dengan katalog.
- Jika inventaris kosong, landing tetap berfungsi dengan gambar Poké Ball dan empty state; tidak menampilkan partner fiktif.
- Type explorer menggunakan ID tipe yang tersedia. Jika suatu tipe belum ada, tautan menuju katalog tanpa filter yang tidak valid.
- Detail partner dan katalog tetap mengikuti autentikasi yang sudah ada. Akun admin diarahkan ke menu pengelolaan yang sesuai perannya.
- Landing tetap memakai layout pemasaran saat dibuka oleh akun admin maupun trainer.

## Responsiveness dan aksesibilitas

- Hero dua kolom di desktop, satu kolom pada viewport 900px ke bawah.
- Featured grid: 4 kolom di atas 1150px, 2 kolom pada tablet, 1 kolom pada 520px ke bawah.
- Stats: 4 kolom desktop, 2 kolom pada layar kecil.
- Navigasi mobile memakai tombol berlabel, `aria-expanded`, `aria-controls`, dan `inert` saat tertutup; bisa ditutup dengan Escape dan fokus kembali ke tombol.
- Anchor memiliki offset untuk header sticky. Tautan/footer dan CTA memakai tujuan nyata.
- Gambar Pokémon memiliki alt text; gambar logo dekoratif berada di dalam tautan dengan accessible name.
- Focus ring tersedia untuk keyboard. Reveal dan hover menghormati `prefers-reduced-motion`.
- Konten terlihat secara default. Reveal hanya diaktifkan bila JavaScript dan IntersectionObserver tersedia.

## Performance

- Tidak ada dependency animasi baru, video autoplay, atau animasi infinite.
- Reveal memakai IntersectionObserver; listener scroll dan pointer memakai requestAnimationFrame.
- Hero image dimuat dengan prioritas tinggi; gambar featured memakai lazy loading dan ukuran eksplisit.
- Floating badge ditempatkan di luar baris harga preview agar tidak menghalangi informasi rental.

## Verifikasi

- Browser diuji pada lebar 1440, 1024, 900, 768, 390, 360, dan 320px.
- Grid responsif, sticky header, anchor, hamburger, Escape/fokus, reduced motion, CTA registrasi, dan alur detail tamu → login berhasil diperiksa.
- Audit akhir tidak menemukan horizontal overflow, gambar rusak, atau error JavaScript.
- Login admin/trainer dan akses detail rental yang sudah ada juga diperiksa kembali di browser.
- PHPUnit: **27 tes lulus, 358 assertions**; mencakup data landing, fallback inventaris kosong, dan tautan sesuai peran.
- Tidak ada perubahan skema database dan tidak diperlukan migrasi untuk redesign landing.
