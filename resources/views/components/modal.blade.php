@props(['id', 'title'])
<dialog id="{{ $id }}" class="edit-dialog" aria-labelledby="{{ $id }}-title"><div class="dialog-heading"><h2 id="{{ $id }}-title">{{ $title }}</h2><button type="button" class="feedback-close" data-close-dialog aria-label="Tutup formulir">×</button></div><div class="dialog-body">{{ $slot }}</div></dialog>
