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

            @if ($errors->any())
                @if (session('edit_competency_id') && old('_method') == 'PUT')
                    $('#editModal').modal('show');
                    var competencyId = "{{ session('edit_competency_id') }}";
                    var url = "{{ route('admins.find', ':id') }}".replace(':id', competencyId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#editForm').attr('action',
                                "{{ route('admins.update', ':id') }}".replace(':id',
                                    competencyId));

                            const oldName = "{{ old('name') }}";
                            const oldDescription = "{{ old('description') }}";

                            $('#edit_name').val(oldName ? oldName : response.name);
                            $('#edit_description').val(oldDescription ? oldDescription : response
                                .description);
                        },
                        error: function() {
                            alert('Failed to fetch data');
                        }
                    });
                @else
                    $('#modalForm').modal('show');
                @endif
            @endif

            $('.edit-button').click(function() {
                var competencyId = $(this).data('id');
                var url = "{{ route('admins.find', ':id') }}".replace(':id', competencyId);
                $.ajax({
                    url: "{{ route('admins.session', ':id') }}".replace(':id',
                        competencyId),
                    type: 'GET',
                    success: function() {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                $('#editModal').modal('show');
                                $('#editForm').attr('action',
                                    "{{ route('admins.update', ':id') }}"
                                    .replace(':id', competencyId));
                                $('#editForm #edit_name').val(response.name);
                                if (response.location === 'Kampus Indralaya') {
                                    $('#editForm #editCustomRadio1').prop('checked',
                                        true);
                                } else if (response.location ===
                                    'Kampus Palembang') {
                                    $('#editForm #editCustomRadio2').prop('checked',
                                        true);
                                }

                                if (response.photo) {
                                    previewOldPhoto(response.photo.replace(
                                        'public/', ''));
                                } else {
                                    $('#photo-preview')
                                        .hide();
                                }
                            },
                            error: function() {
                                alert('Failed to fetch data');
                            }
                        });
                    },
                    error: function() {
                        alert('Failed to store competency ID in session');
                    }
                });
            });

            $('.delete-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('admins.find', ':id') }}";
                url = url.replace(':id', lecturerId);

                // Fetch admin data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#deleteModal').modal('show');
                        $('#deleteForm').attr('action',
                            "{{ route('admins.delete', ':id') }}"
                            .replace(':id', lecturerId));
                        $("#deleteText").text(
                            "Apakah anda yakin ingin menghapus admin " +
                            response.name + "?");
                    }
                });
            });

            $('.show-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('admins.find', ':id') }}";
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
                        $('#showLocation').text(response.location);
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
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Admin</h3>
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
                                        <em class="icon ni ni-plus me-1"></em>Tambah Admin</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div id="admins-content" class="row g-gs">
                @foreach ($admins as $index => $admin)
                    <div class="col-xxl-3 col-lg-4 col-sm-6 admins-item">
                        <div class="card card-bordered product-card">
                            <div class="product-thumb">
                                <img src="{{ Storage::url($admin->photo) }}" class="card-img-top thumbnail-image"
                                    alt="">
                                <ul class="product-badges">
                                    <li><span class="badge bg-primary">{{ $admin->location }}</span></li>
                                </ul>
                                <ul class="product-actions mb-3">
                                    <li> <button class="btn btn-primary rounded-pill show-button"
                                            data-id="{{ $admin->id }}">
                                            <em class="ni ni-eye"></em>
                                        </button></li>
                                    <li> <button class="btn btn-warning rounded-pill edit-button"
                                            data-id="{{ $admin->id }}">
                                            <em class="ni ni-edit"></em>
                                        </button></li>
                                    <li> <button class="btn btn-danger rounded-pill delete-button"
                                            data-id="{{ $admin->id }}">
                                            <em class="ni ni-trash"></em>
                                        </button></li>
                                </ul>
                            </div>
                            <div class="card-inner text-center">
                                <h5 class="product-title">{{ $admin->name }}</h5>
                                <p class="product-tags">{{ $admin->location }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Show Modal --}}
    <div class="modal fade" id="showModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Admin</h5>
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
                                        <td class="fw-bold">Lokasi</td>
                                        <td id="showLocation"></td>
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
                    <h5 class="modal-title">Tambah Admin</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admins.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="form-group col-xxl-6 col-md-12">
                            <label class="form-label">Lokasi</label>
                            <div class="row gy-2">
                                <div class="col-md-3 col-sm-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="customRadio1" name="location"
                                                value="Kampus Indralaya" autocomplete="off" required
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="customRadio1">Kampus
                                                Indralaya</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="customRadio2" name="location"
                                                value="Kampus Palembang" autocomplete="off" required
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="customRadio2">Kampus
                                                Palembang</label>
                                        </div>
                                    </div>
                                </div>
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
                    <h5 class="modal-title">Edit Admin</h5>
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
                        <div class="form-group col-xxl-3 col-md-6">
                            <label class="form-label">Lokasi</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="editCustomRadio1" name="location"
                                                value="Kampus Indralaya" autocomplete="off" required
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="editCustomRadio1">Kampus
                                                Indralaya</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="editCustomRadio2" name="location"
                                                value="Kampus Palembang" autocomplete="off" required
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="editCustomRadio2">Kampus
                                                Palembang</label>
                                        </div>
                                    </div>
                                </div>
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
                    <h5 class="modal-title">Hapus Admin</h5>
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
