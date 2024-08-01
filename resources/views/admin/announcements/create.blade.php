@extends('admin.layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/editors/summernote.css?ver=3.0.3') }}">
    <style>
        #thumbnail-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 10px 0;
            display: none;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('assets/js/libs/editors/summernote.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/editors.js?ver=3.0.3') }}"></script>
    <script>
        function previewThumbnail(event) {
            var preview = document.getElementById('thumbnail-preview');
            preview.style.display = 'block';
            preview.src = URL.createObjectURL(event.target.files[0]);
        }

        function formatDate(dateString) {
            const options = {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            const date = new Date(dateString.replace(/-/g, '/'));
            return date.toLocaleDateString('id-ID', options);
        }

        $(document).ready(function() {
            $('#title').on('input', function() {
                var title = $(this).val();
                var slug = title.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Hapus karakter yang bukan huruf, angka, atau spasi
                    .replace(/\s+/g, '-') // Ganti spasi dengan tanda hubung
                    .replace(/-+/g,
                    '-'); // Ganti beberapa tanda hubung berturut-turut dengan satu tanda hubung
                $('#slug').val(slug);
            });
        });

        function previewContent() {
            var title = $('#title').val();
            var content = $('#content').val();
            var thumbnail = $('#thumbnail').val();

            // Check if required fields are empty
            if (!title && !content && !thumbnail) {
                $('#modalAlert').modal('show'); // Show the danger modal
                return; // Stop further execution
            }

            var createdAt = formatDate('{{ now()->format('Y-m-d H:i:s') }}'); // Menggunakan now() untuk tanggal hari ini
            var thumbnailSrc = thumbnail ? URL.createObjectURL($('#thumbnail')[0].files[0]) : '';

            $('#previewTitle').text(title);
            $('#previewCreatedAt').html('<i class="icon ni ni-calendar text-warning"></i> ' +
                createdAt); // Memasukkan ikon kalender dan tanggal

            var previewThumbnail = $('#previewThumbnail');
            if (thumbnailSrc) {
                previewThumbnail.attr('src', thumbnailSrc);
                previewThumbnail.css('display', 'block');
            } else {
                previewThumbnail.attr('src', '');
                previewThumbnail.css('display', 'none');
            }

            $('#previewContent').html(content);
            $('#previewModal').modal('show');
        }
    </script>
@endpush

@section('content')
    <div class="container">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="preview-block">
                    <span class="preview-title-lg overline-title">Masukkan Pengumuman</span>
                    <form method="post" action="{{ route('announcements.store') }}" enctype="multipart/form-data"
                        class="is-alter form-validate form-control-wrap">
                        @csrf
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="title">Judul</label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="title"
                                            class="form-control @error('title') is-invalid @enderror" name="title"
                                            placeholder="Masukkan judul pengumuman" value="{{ old('title') }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="slug">Slug</label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="slug"
                                            class="form-control @error('slug') is-invalid @enderror" name="slug"
                                            placeholder="Otomatis terisi berdasarkan pengumuman" value="{{ old('slug') }}"
                                            required>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label" for="thumbnail">Thumbnail</label>
                                    <div class="form-control-wrap">
                                        <img id="thumbnail-preview" src="#" alt="Thumbnail Preview">
                                        <input type="file" id="thumbnail"
                                            class="form-control @error('thumbnail') is-invalid @enderror" name="thumbnail"
                                            placeholder="Contoh: B/735-11/02/01/Smh" value="{{ old('thumbnail') }}" required
                                            accept="image/*" onchange="previewThumbnail(event)">
                                        @error('thumbnail')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label" for="content">Konten</label>
                                    <div class="form-control-wrap">
                                        <textarea class="summernote-basic @error('content') is-invalid @enderror" name="content" id="content">{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-primary mt-2" onclick="previewContent()"><em
                                            class="icon ni ni-eye me-1"></em>Pratinjau</button>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary"><em class="ni ni-save me-1"></em>
                                    Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Pratinjau</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <h2 class="mb-1" id="previewTitle"></h2>
                    <p class="mb-3 sub-text" id="previewCreatedAt"></p>
                    <img class="img-fluid mb-3" id="previewThumbnail" class="w-full" src="" style="display: none;"
                        alt="Thumbnail">
                    <div id="previewContent"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Tutup</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAlert" tabindex="-1" role="dialog" aria-labelledby="modalAlertLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body modal-body-lg text-center">
                    <div class="nk-modal">
                        <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                        <h4 class="nk-modal-title">Data masih kosong!</h4>
                        <div class="nk-modal-text">
                            <p class="lead">Mohon lengkapi semua data sebelum melihat pratinjau.</p>
                        </div>
                        <div class="nk-modal-action mt-5">
                            <button type="button" class="btn btn-lg btn-mw btn-light"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div><!-- .modal-body -->
            </div>
        </div>
    </div>
@endsection
