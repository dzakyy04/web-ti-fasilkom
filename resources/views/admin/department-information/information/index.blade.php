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

            @if ($errors->any())
                @if (session('edit_competency_id') && old('_method') == 'PUT')
                    $('#editModal').modal('show');
                    var competencyId = "{{ session('edit_competency_id') }}";
                    var url = "{{ route('informations.find', ':id') }}".replace(':id', competencyId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#editForm').attr('action',
                                "{{ route('informations.update', ':id') }}".replace(':id',
                                    competencyId));

                            const oldName = "{{ old('name') }}";
                            const oldDescription = "{{ old('description') }}";

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
                var url = "{{ route('informations.find', ':id') }}".replace(':id', competencyId);
                $.ajax({
                    url: "{{ route('informations.session', ':id') }}".replace(':id',
                        competencyId),
                    type: 'GET',
                    success: function() {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                $('#editModal').modal('show');
                                $('#editForm').attr('action',
                                    "{{ route('informations.update', ':id') }}"
                                    .replace(':id', competencyId));
                                $('#edit_description').val(response.description);
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
            var url = "{{ route('informations.find', ':id') }}";
            url = url.replace(':id', competencyId);

            // Fetch data via AJAX
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#deleteModal').modal('show');
                    $('#deleteForm').attr('action',
                        "{{ route('informations.delete', ':id') }}".replace(
                            ':id', competencyId));
                    $("#deleteText").text(
                        "Apakah anda yakin ingin menghapus informasi jurusan ini ?");
                },
                error: function() {
                    alert('Failed to fetch data');
                }
            });
        });

        $('.show-button').click(function() {
            var competencyId = $(this).data('id');
            var url = "{{ route('informations.find', ':id') }}";
            url = url.replace(':id', competencyId);

            // Fetch data via AJAX
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#showModal').modal('show');
                    $('#showDescription').text(response.description);
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
                    <h3 class="nk-block-title page-title">Informasi</h3>
                </div>
                <div class="nk-block-head-content">
                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                        title="Batas maksimum satu informasi">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm"
                            @if ($disableAddButton) disabled @endif>
                            <em class="icon ni ni-plus me-1"></em>Tambah Informasi</span>
                    </button>
                    </span>
                </div>
            </div>
            <div class="mt-3">
                <table class="datatable-init nk-tb-list nk-tb-ulist table table-hover table-bordered table-responsive-md"
                    data-auto-responsive="false">
                    <thead>
                        <tr class="table-light nk-tb-item nk-tb-head">
                            <th class="text-nowrap text-center align-middle">No</th>
                            <th class="text-nowrap text-center align-middle">Deskripsi</th>
                            <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($informations as $index => $information)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $information->description }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs rounded-pill show-button"
                                        data-id="{{ $information->id }}">
                                        <em class="ni ni-eye"></em>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs rounded-pill edit-button"
                                        data-id="{{ $information->id }}">
                                        <em class="ni ni-edit"></em>
                                    </button>
                                    <button class="btn btn-danger btn-xs rounded-pill delete-button"
                                        data-id="{{ $information->id }}">
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
                    <h5 class="modal-title">Detail Informasi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tbody>
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
                    <h5 class="modal-title">Tambah Informasi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('informations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="description">Deskripsi</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control no-resize" name="description" placeholder="Masukkan deskripsi" id="description"
                                    value="{{ old('description') }}" required></textarea>
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
                    <h5 class="modal-title">Edit Informasi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="edit_description">Deskripsi</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control no-resize" name="description" placeholder="Masukkan deskripsi" id="edit_description"
                                    value="{{ old('description') }}" required></textarea>
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
                    <h5 class="modal-title">Hapus Informasi</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <p id="deleteText">Apakah anda yakin ingin menghapus informasi?</p>
                    <form id="deleteForm" action="" method="POST">
                        @method('DELETE')
                        @csrf
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger"><em
                                    class="ni ni-trash me-1"></em>Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
