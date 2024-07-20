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
                var competencyId = $(this).data('id');
                var url = "{{ route('graduates.graduate-competencies.find', ':id') }}";
                url = url.replace(':id', competencyId);
    
                // Fetch data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#editModal').modal('show');
                        $('#editForm').attr('action',
                            "{{ route('graduates.graduate-competencies.update', ':id') }}"
                            .replace(':id', competencyId));
                        $('#edit_name').val(response.name);
                        $('#edit_description').val(response.description);
    
                        if (response.photo) {
                            var photoUrl = response.photo.replace('public/', '/storage/');
                            $('#photo-preview').attr('src', photoUrl).show();
                        } else {
                            $('#photo-preview').hide();
                        }
                    },
                    error: function() {
                        alert('Failed to fetch data');
                    }
                });
            });
    
            $('.delete-button').click(function() {
                var competencyId = $(this).data('id');
                var url = "{{ route('graduates.graduate-competencies.find', ':id') }}";
                url = url.replace(':id', competencyId);
    
                // Fetch data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#deleteModal').modal('show');
                        $('#deleteForm').attr('action',
                            "{{ route('graduates.graduate-competencies.delete', ':id') }}".replace(
                                ':id', competencyId));
                        $("#deleteText").text(
                            "Apakah anda yakin ingin menghapus kompetensi lulusan " + response.name + "?");
                    },
                    error: function() {
                        alert('Failed to fetch data');
                    }
                });
            });
    
            $('.show-button').click(function() {
                var competencyId = $(this).data('id');
                var url = "{{ route('graduates.graduate-competencies.find', ':id') }}";
                url = url.replace(':id', competencyId);
    
                // Fetch data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#showModal').modal('show');
                        $('#showName').text(response.name);
                        $('#showDescription').text(response.description);
                        if (response.photo) {
                            var photoUrl = response.photo.replace('public/', '/storage/');
                            $('#showPhoto').attr('src', photoUrl).show();
                        } else {
                            $('#showPhoto').hide();
                        }
                    },
                    error: function() {
                        alert('Failed to fetch data');
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
                    <h3 class="nk-block-title page-title">{{ $title }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">
                        <em class="icon ni ni-plus me-1"></em>Tambah Kompetensi Lulusan
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
                            <th class="text-nowrap text-center align-middle">Deskripsi</th>
                            <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($graduateCompetencies as $index => $competency)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ Storage::url($competency['photo']) }}" alt="" class="img-fluid"
                                        style="width: 100px;">
                                </td>
                                <td>{{ $competency['name'] ?? 'N/A' }}</td>
                                <td>{{ $competency['description'] ?? 'N/A' }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs rounded-pill show-button"
                                        data-id="{{ $competency['id'] }}">
                                        <em class="ni ni-eye"></em>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs rounded-pill edit-button"
                                        data-id="{{ $competency['id'] }}">
                                        <em class="ni ni-edit"></em>
                                    </button>
                                    <button class="btn btn-danger btn-xs rounded-pill delete-button"
                                        data-id="{{ $competency['id'] }}">
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

    {{-- Add Modal --}}
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kompetensi Lulusan</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('graduates.graduate-competencies.store') }}" method="POST"
                        enctype="multipart/form-data">
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
                                    name="name" id="name" value="{{ old('name') }}" placeholder="Masukkan nama"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="name">Deskripsi</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('description') is-invalid @enderror"
                                    name="description" id="description" value="{{ old('description') }}"
                                    placeholder="Masukkan nama" required>
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
                <h5 class="modal-title">Edit Kompetensi Lulusan</h5>
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
                                <img id="photo-preview" class="img-fluid" src="#" alt="Photo Preview" style="max-width: 200px; max-height: 200px; display: none;">
                            </div>
                            <div class="custom-file position-relative mt-1">
                                <label class="form-label" for="edit_photo">Foto</label>
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" id="edit_photo" onchange="previewPhoto(event)">
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
                        <label class="form-label" for="edit_description">Deskripsi</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" name="description" id="edit_description" required>
                        </div>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary"><em class="ni ni-save me-1"></em>Simpan</button>
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
                <h5 class="modal-title">Hapus Kompetensi Lulusan</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="deleteForm" action="" method="POST">
                    @method('DELETE')
                    @csrf
                    <p id="deleteText">Apakah anda yakin ingin menghapus kompetensi lulusan ini?</p>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-danger"><em class="ni ni-trash me-1"></em>Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    {{-- Show Modal --}}
    <div class="modal fade" id="showModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kompetensi Lulusan</h5>
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
@endsection
