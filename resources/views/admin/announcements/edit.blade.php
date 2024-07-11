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
    <script src="{{ asset('assets/js/libs/editors/summernote.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/editors.js?ver=3.0.3') }}"></script>
    <script>
        $(document).ready(function() {
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

            $('#title').on('input', function() {
                var title = $(this).val();
                var slug = title.toLowerCase().replace(/\s+/g, '-');
                $('#slug').val(slug);
            });
        });
    </script>
@endpush

@section('content')
    <div class="container">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="preview-block">
                    <span class="preview-title-lg overline-title">Edit Pengumuman</span>
                    <form method="post" action="{{ route('announcements.update', $announcement->slug) }}"
                        enctype="multipart/form-data" class="is-alter form-validate form-control-wrap">
                        @method('PUT')
                        @csrf
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="title">Judul</label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="title"
                                            class="form-control @error('title') is-invalid @enderror" name="title"
                                            placeholder="Masukkan judul pengumuman"
                                            value="{{ old('title', $announcement->title) }}" required>
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
                                    <label class="form-label" for="description">Deskripsi</label>
                                    <div class="form-control-wrap">
                                        <textarea id="description" class="form-control no-resize @error('description') is-invalid @enderror" name="description"
                                            placeholder="Masukkan deskripsi pengumuman" required>{{ old('description', $announcement->description) }}</textarea>
                                        @error('description')
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
                                            placeholder="Contoh: B/735-11/02/01/Smh" value="{{ old('thumbnail') }}"
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
                                        <textarea class="summernote-basic" name="content" id="content">{{ old('content', $announcement->content) }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
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
@endsection
