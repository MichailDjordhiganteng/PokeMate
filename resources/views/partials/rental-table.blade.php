<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID Transaksi</th>
                @if($admin)
                    <th>Trainer Ekspedisi</th>
                @endif
                <th>Partner Pokémon</th>
                <th>Jadwal Sewa</th>
                <th>Status</th>
                <th>Durasi & Biaya</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rentals as $rental)
            <tr>
                <td><span class="cell-code">#RNT-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</span><br><small>{{ Str::limit($rental->destination ?? 'Ekspedisi Partner', 28) }}</small></td>
                @if($admin)
                    <td><div class="cell-user"><x-avatar :user="$rental->trainer" /><div><strong>{{ $rental->trainer->full_name }}</strong><small>#TRN-{{ str_pad($rental->trainer_id, 5, '0', STR_PAD_LEFT) }}</small></div></div></td>
                @endif
                <td>
                    @foreach($rental->rentalDetails as $detail)
                        <div class="cell-partner"><img src="{{ $detail->pokemon->imageUrl() ?? asset('images/pokeball.png') }}" alt="{{ $detail->pokemon->pokemon_name }}"><div><strong>{{ $detail->pokemon->pokemon_name }}</strong><small>{{ $detail->pokemon->pokemon_code }}</small></div></div>
                    @endforeach
                </td>
                <td>
                    <small>Mulai:</small> {{ $rental->start_date->format('d M Y') }}<br><small>Tempo:</small> <strong>{{ $rental->due_date->format('d M Y') }}</strong>
                    @if($rental->returned_at)
                        <br><small>Kembali: {{ $rental->returned_at->format('d M Y') }}</small>
                    @endif
                </td>
                <td>
                    <x-status :status="$rental->rental_status" />
                    @if(in_array($rental->rental_status, ['active', 'late']) && $rental->due_date->lt(today()))
                        <br><span class="badge late">Terlambat</span>
                    @endif
                </td>
                <td class="cell-money">
                    Rp {{ number_format($rental->total_price, 0, ',', '.') }}<br><small>{{ (int) $rental->start_date->diffInDays($rental->due_date) }} hari</small>
                    @if($rental->late_fee > 0)
                        <br><small>Denda: Rp {{ number_format($rental->late_fee, 0, ',', '.') }}</small>
                    @endif
                </td>
                <td>
                    <div class="rental-actions">
                        @if($admin)
                            @if($rental->rental_status === 'return_requested')
                                <a class="button small" href="{{ route('admin.returns.index') }}#return-{{ $rental->id }}">Proses Inspeksi</a>
                            @else
                                <span class="muted">{{ $rental->rental_status === 'completed' ? 'Terverifikasi' : 'Dipantau' }}</span>
                            @endif
                            <a class="button secondary small" href="{{ route('admin.rentals.show', $rental) }}">Lihat Detail</a>
                        @else
                            <a class="button secondary small" href="{{ route('trainer.rentals.show', $rental) }}">Lihat Detail</a>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ $admin ? 7 : 6 }}"><x-empty-state title="Belum ada aktivitas rental" description="Transaksi partner akan tampil di sini setelah petualangan dimulai." icon="report" /></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
