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
        $(document).ready(function() {
            const datatableWrap = $(".datatable-wrap");
            const wrappingDiv = $("<div>").addClass("w-100").css("overflow-x", "scroll");
            datatableWrap.children().appendTo(wrappingDiv);
            datatableWrap.append(wrappingDiv);

            @if ($errors->any())
                @if (session('edit_sop_id') && old('_method') == 'PUT')
                    $('#editModal').modal('show');
                    var sopId = "{{ session('edit_sop_id') }}";
                    var url = "{{ route('sops.find', ':id') }}".replace(':id', sopId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#editForm').attr('action',
                                "{{ route('sops.update', ':id') }}".replace(':id',
                                    sopId));

                            const oldName = "{{ old('title') }}";
                            $('#edit_title').val(oldName ? oldName : response.title);
                            if (response.file) {
                                $('#edit_file_name').text(response.file.split('/').pop());
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
                var sopId = $(this).data('id');
                var url = "{{ route('sops.find', ':id') }}".replace(':id', sopId);
                $.ajax({
                    url: "{{ route('sops.session', ':id') }}".replace(':id',
                        sopId),
                    type: 'GET',
                    success: function() {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                $('#editModal').modal('show');
                                $('#editForm').attr('action',
                                    "{{ route('sops.update', ':id') }}"
                                    .replace(':id', sopId));
                                $('#edit_title').val(response.title);
                                if (response.file) {
                                    $('#edit_file_name').text(response.file.split(
                                        '/').pop());
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
                var sopId = $(this).data('id');
                var url = "{{ route('sops.find', ':id') }}".replace(':id', sopId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#deleteModal').modal('show');
                        $('#deleteForm').attr('action',
                            "{{ route('sops.delete', ':id') }}".replace(':id',
                                sopId));
                        $("#deleteText").text("Apakah anda yakin ingin menghapus " +
                            response.title + "?");
                    },
                    error: function() {
                        alert('Failed to fetch data');
                    }
                });
            });

            $('.show-button').click(function() {
                var sopId = $(this).data('id');
                var url = "{{ route('sops.find', ':id') }}".replace(':id', sopId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#showModal').modal('show');
                        $('#showTitle').text(response.title);

                        if (response.file) {
                            var fileUrl = response.file.replace('public/', '/storage/');
                            $('#showPhoto').attr('src', fileUrl).show();
                            $('#showDownload').attr('href', fileUrl).show();
                        } else {
                            $('#showPhoto').hide();
                            $('#showDownload').hide();
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
        });
    </script>
@endpush

@section('content')
    <div class="col-md-12">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-name page-name">{{ $title }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">
                        <em class="file ni ni-plus me-1"></em>Tambah SOP
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <table class="datatable-init nk-tb-list nk-tb-ulist table table-hover table-bordered table-responsive-md"
                    data-auto-responsive="false">
                    <thead>
                        <tr class="table-light nk-tb-item nk-tb-head">
                            <th class="text-nowrap text-center align-middle">No</th>
                            <th class="text-nowrap text-center align-middle">File</th>
                            <th class="text-nowrap text-center align-middle">Judul</th>
                            <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sops as $index => $sop)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ Storage::url($sop->file) }}"
                                        class="btn btn-sm rounded-pill btn-danger" target="_blank">
                                        <em class="ni ni-download mx-1"></em>Download
                                    </a>
                                </td>
                                <td>{{ $sop->title }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs rounded-pill show-button"
                                        data-id="{{ $sop->id }}">
                                        <em class="ni ni-eye"></em>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs rounded-pill edit-button"
                                        data-id="{{ $sop->id }}">
                                        <em class="ni ni-edit"></em>
                                    </button>
                                    <button class="btn btn-danger btn-xs rounded-pill delete-button"
                                        data-id="{{ $sop->id }}">
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
                    <h5 class="modal-name">Tambah SOP</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="file ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('sops.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="file">File</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        name="file" id="file" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="title">Judul</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    name="title" id="title" value="{{ old('title') }}" placeholder="Masukkan judul" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
                    <h5 class="modal-name">Edit SOP</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="file ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="form-label" for="edit_file_name">Nama File</label>
                            <div class="form-control-wrap">
                                <span id="edit_file_name"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="edit-file">File</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        name="file" id="edit-file">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_title">Judul</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    name="title" id="edit_title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Show Modal --}}
    <div class="modal fade" id="showModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-name">Detail SOP</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="file ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="showTitle">Judul</label>
                        <div class="form-control-wrap">
                            <span id="showTitle"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="showPhoto">File</label>
                        <div class="form-control-wrap">
                            <a id="showDownload" href="#" class="btn btn-sm rounded-pill btn-danger"
                                target="_blank">
                                <em class="ni ni-download mx-1"></em>Download
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-name">Hapus SOP</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="file ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <p id="deleteText"></p>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
