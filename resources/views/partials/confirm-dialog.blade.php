<dialog class="confirm-dialog" id="confirm-dialog" aria-labelledby="confirm-title" aria-describedby="confirm-description">
    <div class="confirm-topline" aria-hidden="true"></div>
    <button class="feedback-close confirm-close" type="button" data-cancel-confirm aria-label="Tutup konfirmasi">×</button>
    <div class="confirm-body">
        <span class="confirm-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M9 6V4h6v2M5 6l1 14h12l1-14M10 10v6m4-6v6"/></svg>
        </span>
        <span class="eyebrow">PokePartner · Konfirmasi Tindakan</span>
        <h2 id="confirm-title">Hapus data ini?</h2>
        <p id="confirm-description">Pastikan data yang dipilih sudah benar sebelum melanjutkan.</p>
        <div class="confirm-warning"><span aria-hidden="true">!</span><p>Data yang sudah dihapus tidak dapat dikembalikan.</p></div>
    </div>
    <div class="confirm-actions">
        <button class="secondary" type="button" data-cancel-confirm autofocus>Batal</button>
        <button class="confirm-delete" type="button" id="confirm-submit">Ya, Hapus</button>
    </div>
</dialog>
