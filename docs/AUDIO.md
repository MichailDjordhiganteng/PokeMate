# Backsound PokePartner

## Berkas dan komponen

- Sumber yang diberikan pengguna: `../Pokemon RedBlue Opening.mp3`.
- Aset web: `public/audio/pokemon-redblue-opening.mp3` (sekitar 4,5 MB; durasi terdeteksi browser 113,1 detik).
- UI bersama: `resources/views/partials/audio-player.blade.php`.
- Stylesheet: `public/css/audio-player.css`.
- Perilaku: `public/js/audio-player.js`.
- Komponen dipasang sekali pada layout landing serta layout aplikasi (login/register/admin/trainer). Halaman cetak terpisah tidak menambahkan pemutar kedua.

## Penggunaan

1. Saat kunjungan pertama, sistem mencoba autoplay dengan volume 20%. Jika browser memblokirnya, klik halaman atau tekan **Lanjutkan musik** pada dock **Backsound** di kanan bawah.
2. Gunakan tombol pengaturan di sebelah kanan untuk membuka slider volume dan tombol mute.
3. Tombol **Matikan musik** menghentikan pemutaran dan menyimpan pilihan mati.
4. Tombol **Bisukan** hanya menonaktifkan suara; posisi lagu tetap berjalan.

Lagu memakai native `loop`: selesai diputar, lagu otomatis kembali ke awal. Tidak ada timer yang mematikan musik setelah beberapa menit. Volume awal 20% dan autoplay dicoba pada kunjungan pertama. `preload="none"` tetap digunakan agar ketika pilihan tersimpan adalah **Mati**, file audio tidak dimuat. Autoplay bersuara tetap mengikuti kebijakan browser dan tidak dapat dijamin pada browser yang belum memberikan izin.

## Preferensi dan navigasi

- `localStorage` key: `pokepartner.audio.v1`.
- Data tersimpan: status nyala/mati, volume 0–1, mute, dan posisi terakhir lagu.
- Posisi diperbarui berkala selama pemutaran dan disimpan saat halaman ditinggalkan.
- Ketika halaman Laravel berikutnya dibuka, pemutar memulihkan preferensi, melakukan seek ke posisi terakhir, lalu mencoba melanjutkan lagu.
- Navigasi Laravel biasa memuat dokumen baru: mekanisme ini adalah **resume posisi**, bukan audio context SPA yang dijamin tanpa jeda. Browser dapat memblokir autoplay saat kunjungan pertama atau setelah navigasi; jika demikian, tombol berubah menjadi **Lanjutkan musik** dan pemutaran dicoba kembali setelah interaksi pengguna.
- Pengaturan mati tidak memulai musik lagi saat reload atau berpindah halaman. Perubahan volume/mute/mati juga diterapkan pada tab terbuka lain melalui event storage, tanpa memulai audio dari event tersebut.
- Jika penyimpanan browser tidak tersedia, kontrol tetap berfungsi selama halaman terbuka.

## UI dan aksesibilitas

- Tombol memiliki label dan status `aria-pressed`/`aria-expanded`.
- Slider mendukung keyboard dan menampilkan persentase volume.
- Panel dapat ditutup dengan tombol, klik di luar, atau Escape; Escape mengembalikan fokus ke tombol pengaturan.
- Panel dibatasi viewport dan dapat bergulir pada layar pendek.
- Equalizer dekoratif mengikuti `prefers-reduced-motion`; kontrol tidak dicetak.

## Verifikasi

- Pemutaran MP3 asli, loop setelah mendekati akhir lagu, slider keyboard, mute/unmute, stop, dan pemulihan posisi/volume telah diuji di browser Edge/Chromium.
- Pemulihan otomatis setelah navigasi ke login berhasil pada browser pengujian.
- Skenario browser menolak autoplay dan localStorage tidak tersedia diuji; UI tetap berfungsi tanpa error JavaScript.
- Tampilan kontrol desktop/mobile dan pengembalian fokus Escape diperiksa.
- Seluruh 27 tes aplikasi (358 assertions) tetap lulus setelah integrasi layout audio.
