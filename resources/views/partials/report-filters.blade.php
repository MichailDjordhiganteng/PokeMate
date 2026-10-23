<form class="filters" method="GET">
    <div class="filter-wide"><label for="search">Trainer / Penyewa</label><div class="catalog-search"><x-icon name="search" /><input id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama trainer..."></div></div>
    <div><label for="status">Status Rental</label><select id="status" name="status"><option value="">Semua Status</option>@foreach(['pending', 'active', 'return_requested', 'completed', 'late', 'cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ __('status.'.$status) }}</option>@endforeach</select></div>
    <div><label for="from">Tanggal Mulai Dari</label><input type="date" name="from" id="from" value="{{ request('from') }}"></div><div><label for="to">Sampai</label><input type="date" name="to" id="to" value="{{ request('to') }}"></div><button class="navy">Terapkan Filter</button><a class="button secondary" href="{{ url()->current() }}">Reset</a>
</form>
