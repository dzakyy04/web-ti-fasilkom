@extends('admin.layouts.app')

@push('css')
    <style>
        #photo-preview {
            max-height: 200px;
            max-width: 200px;
            margin: 10px 0;
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
                        console.log(response);
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
@endpush

@section('content')
    <div class="col-md-12">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Pimpinan</h3>
                </div>
                <div class="nk-block-head-content">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">
                        <em class="icon ni ni-plus me-1"></em>Tambah Pimpinan</span>
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <table class="datatable-init nk-tb-list nk-tb-ulist table table-hover table-bordered table-responsive-md"
                    data-auto-responsive="false">
                    <thead>
                        <tr class="table-light nk-tb-item nk-tb-head">
                            <th class="text-nowrap text-center align-middle">No</th>
                            <th class="text-nowrap text-center align-middle">Foto</th>
                            <th class="text-nowrap text-center align-middle">Nama</th>
                            <th class="text-nowrap text-center align-middle">Jabatan</th>
                            <th class="text-nowrap text-center align-middle">Deskripsi</th>
                            <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaders as $index => $leader)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ Storage::url($leader->photo) }}" alt="" class="img-fluid"
                                        style="width: 100px;">
                                </td>
                                <td>{{ $leader->name }}</td>
                                <td>{{ $leader->position }}</td>
                                <td>{{ $leader->description }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs rounded-pill show-button"
                                        data-id="{{ $leader->id }}">
                                        <em class="ni ni-eye"></em>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs rounded-pill edit-button"
                                        data-id="{{ $leader->id }}">
                                        <em class="ni ni-edit"></em>
                                    </button>
                                    <button class="btn btn-danger btn-xs rounded-pill delete-button"
                                        data-id="{{ $leader->id }}">
                                        <em class="ni ni-trash"></em>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Show Modal --}}
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
                                <textarea class="form-control no-resize @error('description') is-invalid @enderror" rows="5" name="description" value="{{ old('description') }}"
                                    id="description" required>
                            </textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <textarea class="form-control no-resize @error('description') is-invalid @enderror" rows="5" name="description"
                                    id="edit_description" required>
                            </textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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