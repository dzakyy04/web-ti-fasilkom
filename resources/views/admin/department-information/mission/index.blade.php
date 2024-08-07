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
                    var url = "{{ route('missions.find', ':id') }}".replace(':id', competencyId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#editForm').attr('action',
                                "{{ route('missions.update', ':id') }}".replace(':id',
                                    competencyId));

                            const oldName = "{{ old('name') }}";
                            const oldDescription = "{{ old('description') }}";

                            $('#edit_name').val(oldName ? oldName : response.title);
                            $('#edit_description').val(oldDescription ? oldDescription : response
                                .description);

                            if (response.icon) {
                                var photoUrl = response.icon.replace('public/', '/storage/');
                                $('#photo-preview').attr('src', photoUrl).show();
                            } else {
                                $('#photo-preview').hide();
                            }
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
                var url = "{{ route('missions.find', ':id') }}".replace(':id', competencyId);
                $.ajax({
                    url: "{{ route('missions.session', ':id') }}".replace(':id',
                        competencyId),
                    type: 'GET',
                    success: function() {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                $('#editModal').modal('show');
                                $('#editForm').attr('action',
                                    "{{ route('missions.update', ':id') }}"
                                    .replace(':id', competencyId));
                                $('#edit_name').val(response.title);
                                $('#edit_description').val(response.description);

                                if (response.icon) {
                                    var photoUrl = response.icon.replace('public/',
                                        '/storage/');
                                    $('#photo-preview').attr('src', photoUrl)
                                        .show();
                                } else {
                                    $('#photo-preview').hide();
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
        });


        $('.delete-button').click(function() {
            var competencyId = $(this).data('id');
            var url = "{{ route('missions.find', ':id') }}";
            url = url.replace(':id', competencyId);

            // Fetch data via AJAX
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#deleteModal').modal('show');
                    $('#deleteForm').attr('action',
                        "{{ route('missions.delete', ':id') }}".replace(
                            ':id', competencyId));
                    $("#deleteText").text(
                        "Apakah anda yakin ingin menghapus misi " +
                        response.title + "?");
                },
                error: function() {
                    alert('Failed to fetch data');
                }
            });
        });

        $('.show-button').click(function() {
            var competencyId = $(this).data('id');
            var url = "{{ route('missions.find', ':id') }}";
            url = url.replace(':id', competencyId);

            // Fetch data via AJAX
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#showModal').modal('show');
                    $('#showName').text(response.title);
                    $('#showDescription').text(response.description);
                    if (response.icon) {
                        var photoUrl = response.icon.replace('public/', '/storage/');
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
                        <em class="icon ni ni-plus me-1"></em>Tambah Misi
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <table class="datatable-init nk-tb-list nk-tb-ulist table table-hover table-bordered table-responsive-md"
                    data-auto-responsive="false">
                    <thead>
                        <tr class="table-light nk-tb-item nk-tb-head">
                            <th class="text-nowrap text-center align-middle">No</th>
                            <th class="text-nowrap text-center align-middle">Icon</th>
                            <th class="text-nowrap text-center align-middle">Judul</th>
                            <th class="text-nowrap text-center align-middle">Deskripsi</th>
                            <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($missions as $index => $mission)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ Storage::url($mission->icon) }}" alt="" class="img-fluid"
                                        style="width: 50px;">
                                </td>
                                <td>{{ $mission->title }}</td>
                                <td class="text-start">{{ $mission->description }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs rounded-pill show-button"
                                        data-id="{{ $mission->id }}">
                                        <em class="ni ni-eye"></em>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs rounded-pill edit-button"
                                        data-id="{{ $mission->id }}">
                                        <em class="ni ni-edit"></em>
                                    </button>
                                    <button class="btn btn-danger btn-xs rounded-pill delete-button"
                                        data-id="{{ $mission->id }}">
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
                    <h5 class="modal-title">Tambah Misi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('missions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="d-flex align-items-center justify-content-start">
                                    <img id="add_photo_preview" class="img-fluid" src="#" alt="Photo Preview"
                                        style="display: none; max-width: 200px; max-height: 200px;">
                                </div>
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="icon">Ikon (.svg)</label>
                                    <input type="file" class="form-control @error('icon') is-invalid @enderror"
                                        name="icon" id="icon" onchange="previewNewPhoto(event)" required>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="name">Nama</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="title" id="name" value="{{ old('title') }}" placeholder="Masukkan judul"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Deskripsi</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control no-resize @error('description') is-invalid @enderror" name="description" id="description"
                                    placeholder="Masukkan deskripsi">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><em class="ni ni-save me-1"></em>Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                    <h5 class="modal-title">Edit Misi</h5>
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
                                        style="max-width: 200px; max-height: 200px; display: none;">
                                </div>
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="edit_photo">Ikon (.svg)</label>
                                    <input type="file" class="form-control @error('icon') is-invalid @enderror"
                                        name="icon" id="edit_photo" onchange="previewPhoto(event)">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_name">Judul</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="title" id="edit_name"
                                    value="{{ old('title') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_description">Deskripsi</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control no-resize @error('description') is-invalid @enderror" name="description"
                                    id="edit_description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><em class="ni ni-save me-1"></em>Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                    <h5 class="modal-title">Hapus Misi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="" method="POST">
                        @method('DELETE')
                        @csrf
                        <p id="deleteText">Apakah anda yakin ingin menghapus misi ini?</p>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger"><em
                                    class="ni ni-trash me-1"></em>Hapus</button>
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
                    <h5 class="modal-title">Detail Misi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-3">
                            <img src="" alt="" class="img-fluid" id="showPhoto"
                                style="max-height: 300px;">
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
