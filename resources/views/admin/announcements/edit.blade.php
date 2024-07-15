@extends('admin.layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/editors/summernote.css?ver=3.0.3') }}">
    <style>
        #thumbnail-preview-new,
        #thumbnail-preview-old {
            max-height: 200px;
            max-width: 200px;
            margin: 10px 0;
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-XXX" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/libs/editors/summernote.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/editors.js?ver=3.0.3') }}"></script>
    <script>
        function formatDate(dateString) {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            const date = new Date(dateString.replace(/-/g, '/'));
            return date.toLocaleDateString('id-ID', options);
        }

        $(document).ready(function() {
            $('#title').on('input', function() {
                var title = $(this).val();
                var slug = title.toLowerCase().replace(/\s+/g, '-');
                $('#slug').val(slug);
            });

            var oldThumbnail = "{{ Storage::url($announcement->thumbnail) }}";
            $("#thumbnail-preview-old").attr("src", oldThumbnail);
            $("#thumbnail").change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $("#thumbnail-preview-old").css("display", "none");
                        $("#thumbnail-preview-new").attr("src", e.target.result);
                        $("#thumbnail-preview-new").css("display", "block");
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        function previewContent() {
            var title = $('#title').val();
            var createdAt = formatDate('{{ $announcement->created_at }}');
            var thumbnail = $('#thumbnail').val() ? URL.createObjectURL($('#thumbnail')[0].files[0]) : "{{ Storage::url($announcement->thumbnail) }}";
            var content = $('#content').val();

            $('#previewTitle').text(title);
            $('#previewCreatedAt').html('<i class="icon ni ni-calendar text-warning"></i> ' + createdAt); // Memasukkan ikon kalender dan tanggal
            $('#previewThumbnail').attr('src', thumbnail);
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
                    <span class="preview-title-lg overline-title">Edit Berita</span>
                    <form method="post" action="{{ route('announcements.update', $announcement->slug) }}" enctype="multipart/form-data"
                        class="is-alter form-validate form-control-wrap">
                        @method('PUT')
                        @csrf
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="title">Judul</label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="title"
                                            class="form-control @error('title') is-invalid @enderror" name="title"
                                            placeholder="Masukkan judul berita" value="{{ old('title', $announcement->title) }}"
                                            required>
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
                                            placeholder="Otomatis terisi berdasarkan judul"
                                            value="{{ old('slug', $announcement->slug) }}" required>
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
                                        <img id="thumbnail-preview-old" class="img-fluid rounded-1 h-30 w-15 mt-2 shadow-sm"
                                            src="{{ Storage::url($announcement->thumbnail) }}" alt="Thumbnail Preview">
                                        <img id="thumbnail-preview-new" class="img-fluid rounded-1 h-30 w-15 mt-2 shadow-sm"
                                            style="display: none" alt="New Thumbnail Preview">
                                        <input type="file" id="thumbnail"
                                            class="form-control @error('thumbnail') is-invalid @enderror" name="thumbnail"
                                            placeholder="Upload thumbnail baru" accept="image/*">
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
                                        <textarea class="summernote-basic" name="content" id="content">{{ $announcement->content ?? old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-primary mt-2"
                                        onclick="previewContent()"><em class="icon ni ni-eye me-1"></em>Pratinjau</button>
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
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog"
        aria-labelledby="previewModalLabel" aria-hidden="true">
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
                    <p class="mb-3 sub-text" id="previewCreatedAt"></p> <!-- Tempat untuk menampilkan Updated At -->
                    <img class="img-fluid mb-3" id="previewThumbnail" class="w-full" src="" alt="Thumbnail">
                    <div id="previewContent"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-secondary"
                        data-bs-dismiss="modal" aria-label="Close">Tutup</a>    
                </div>
            </div>
        </div>
    </div>
@endsection
