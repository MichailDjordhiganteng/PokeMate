@if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <div class="notification-stack" aria-label="Notifikasi">
        @foreach(['success', 'error', 'warning', 'info'] as $type)
            @if(session($type))<x-notification :type="$type" :message="session($type)" />@endif
        @endforeach
        @if($errors->any())
            <x-notification type="error" title="Periksa kembali data Anda" :details="$errors->all()" :duration="0" dismiss-label="Tutup notifikasi validasi" />
        @endif
    </div>
@endif
