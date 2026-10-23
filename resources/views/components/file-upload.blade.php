@props(['id', 'name', 'accept' => 'image/jpeg,image/png,image/webp'])
<div class="upload-control"><input class="visually-hidden" type="file" id="{{ $id }}" name="{{ $name }}" accept="{{ $accept }}" data-file-upload><label for="{{ $id }}" class="button secondary small"><x-icon name="plus" />Pilih Gambar</label><span data-file-name>Belum ada file dipilih</span></div>
