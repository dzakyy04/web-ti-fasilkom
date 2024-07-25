@extends('admin.layouts.app')

@push('css')
    <style>
        #photo-preview {
            max-height: 200px;
            max-width: 200px;
            margin: 10px 0;
        }

        .thumbnail-image {
            height: 350px;
            object-fit: cover;
            width: 100%;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('assets/js/libs/datatable-btns.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/example-toastr.js?ver=3.0.3') }}"></script>
    <script>
        function previewNewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('add_photo_preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('add_photo_preview').style.display = 'none';
            }
        }

        $(document).ready(function() {
            const datatableWrap = $(".datatable-wrap");
            const wrappingDiv = $("<div>").addClass("w-100").css("overflow-x", "scroll");
            datatableWrap.children().appendTo(wrappingDiv);
            datatableWrap.append(wrappingDiv);

            let educationIndex = {{ old('educations') ? count(old('educations')) : 1 }};
            let editEducationIndex = 0;

            @if ($errors->any())
                $('#modalForm').modal('show');
            @endif

            function previewOldPhoto(photoUrl) {
                var oldPreview = $('#photo-preview');
                oldPreview.attr('src', `{{ Storage::url('${photoUrl}') }}`);
                oldPreview.show();
            }

            $('#edit_photo').change(function(event) {
                previewPhoto(event);
            });

            function previewPhoto(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('photo-preview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('photo-preview').style.display = 'none';
                }
            }

            $('.edit-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('leaders.find', ':id') }}";
                url = url.replace(':id', lecturerId);

                // Fetch admin data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#editModal').modal('show');
                        $('#editForm').attr('action', "{{ route('leaders.update', ':id') }}"
                            .replace(':id', lecturerId));
                        $('#editForm #edit_name').val(response.name);
                        $('#editForm #edit_position').val(response.position);
                        $('#editForm #edit_description').val(response.description);

                        if (response.photo) {
                            previewOldPhoto(response.photo.replace('public/', ''));
                        } else {
                            $('#photo-preview')
                                .hide();
                        }
                    }
                });
            });

            $('.delete-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('leaders.find', ':id') }}";
                url = url.replace(':id', lecturerId);

                // Fetch admin data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#deleteModal').modal('show');
                        $('#deleteForm').attr('action',
                            "{{ route('leaders.delete', ':id') }}"
                            .replace(':id', lecturerId));
                        $("#deleteText").text(
                            "Apakah anda yakin ingin menghapus pimpinan " +
                            response.name + "?");
                    }
                });
            });

            $('.show-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('leaders.find', ':id') }}";
                url = url.replace(':id', lecturerId);

                // Fetch admin data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        let photoUrl = response.photo.replace('public/', '');
                        $('#showModal').modal('show');
                        $('#showPhoto').attr('src', `{{ Storage::url('${photoUrl}') }}`);
                        $('#showName').text(response.name);
                        $('#showPosition').text(response.position);
                        $('#showDescription').text(response.description);
                    }
                });
            });
        });

        @if (session()->has('success'))
            let message = @json(session('success'));
            NioApp.Toast(`<h5>Berhasil</h5><p>${message}</p>`, 'success', {
                position: 'top-right',
            });
        @endif
    </script>
    <script>
        $(document).ready(function() {
            const datatableWrap = $(".datatable-wrap");
            const wrappingDiv = $("<div>").addClass("w-100").css("overflow-x", "scroll");
            datatableWrap.children().appendTo(wrappingDiv);
            datatableWrap.append(wrappingDiv);

            $('.delete-link').click(function(event) {
                event.preventDefault();
                $('.delete-form').submit();
            });
        });

        function previewContent(name, description, thumbnail, createdAt) {

            $('#previewName').text(name);

            var previewPhoto = $('#previewPhoto');
            if (thumbnail) {
                previewPhoto.attr('src', thumbnail);
                previewPhoto.show();
            } else {
                previewPhoto.hide();
            }

            $('#previewDescription').html(description);
            $('#previewModal').modal('show');
        }
    </script>
@endpush

@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Pimpinan</h3>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalForm">
                                        <em class="icon ni ni-plus me-1"></em>Tambah Pimpinan</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div id="leaders-content" class="row g-gs">
                @foreach ($leaders as $index => $leader)
                    <div class="col-xxl-3 col-lg-4 col-sm-6 leaders-item">
                        <div class="card card-bordered product-card">
                            <div class="product-thumb">
                                <img src="{{ Storage::url($leader->photo) }}" class="card-img-top thumbnail-image"
                                    alt="">
                                <ul class="product-badges">
                                    <li><span class="badge bg-primary">{{ $leader->position }}</span></li>
                                </ul>
                                <ul class="product-actions mb-3">
                                    <li> <button class="btn btn-primary rounded-pill show-button"
                                            data-id="{{ $leader->id }}">
                                            <em class="ni ni-eye"></em>
                                        </button></li>
                                    <li> <button class="btn btn-warning rounded-pill edit-button"
                                            data-id="{{ $leader->id }}">
                                            <em class="ni ni-edit"></em>
                                        </button></li>
                                    <li> <button class="btn btn-danger rounded-pill delete-button"
                                            data-id="{{ $leader->id }}">
                                            <em class="ni ni-trash"></em>
                                        </button></li>
                                </ul>
                            </div>
                            <div class="card-inner text-center">
                                <h5 class="product-title">{{ $leader->name }}</h5>
                                <p class="product-tags">{{ Str::limit($leader->description, 100, '...') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Lihat Berita</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <h2 class="mb-1" id="previewName"></h2>
                    <img class="img-fluid mb-3" id="previewPhoto" class="w-full" src="" style="display: none;"
                        alt="Thumbnail">
                    <div id="previewDescription"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pimpinan</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-3">
                            <img src="" alt="" class="img-fluid" id="showPhoto" style="max-height: 300px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Nama</td>
                                        <td id="showName"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Jabatan</td>
                                        <td id="showPosition"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Deskripsi</td>
                                        <td id="showDescription"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pimpinan</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('leaders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="d-flex align-items-center justify-content-start">
                                    <img id="add_photo_preview" class="img-fluid" src="#" alt="Photo Preview"
                                        style="display: none; max-width: 200px; max-height: 200px;">
                                </div>
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="edit_photo">Foto</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                        name="photo" id="photo" onchange="previewNewPhoto(event)" required>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="name">Nama</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" id="name" value="{{ old('name') }}"
                                    placeholder="Masukkan nama" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="position">Jabatan</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    name="position" id="position" value="{{ old('position') }}"
                                    placeholder="Masukkan posisi" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Deskripsi</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control no-resize" name="description" placeholder="Masukkan deskripsi" id="description"
                                    value="{{ old('description') }}" required></textarea>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><em
                                    class="ni ni-save me-1"></em>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pimpinan</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="d-flex align-items-center justify-content-start">
                                    <img id="photo-preview" class="img-fluid" src="#" alt="Photo Preview"
                                        style="max-width: 200px; max-height: 200px;">
                                </div>
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="edit_photo">Foto</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                        name="photo" id="edit_photo" onchange="previewPhoto(event)">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_name">Nama</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_position">Jabatan</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="position" id="edit_position" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_description">Deskripsi</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control no-resize" name="description" placeholder="Masukkan deskripsi" id="edit_description"
                                    value="{{ old('description') }}" required></textarea>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><em
                                    class="ni ni-save me-1"></em>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Pimpinan</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="" method="POST" enctype="multipart/form-data">
                        @method('DELETE')
                        @csrf
                        <p id="deleteText"></p>
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger"><em
                                    class="ni ni-trash me-1"></em>Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
