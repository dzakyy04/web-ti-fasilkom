@extends('admin.layouts.app')

@push('css')
    <style>
        #file-preview {
            max-height: 200px;
            max-width: 200px;
            margin: 10px 0;
        }

        #showVideo {
            max-width: 100%;
            max-height: 300px;
            display: none;
        }

        .video-container {
            max-width: 100%;
            overflow: hidden;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('assets/js/libs/datatable-btns.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/example-toastr.js?ver=3.0.3') }}"></script>
    <script>
        function previewNewFile(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('add_file_preview');
                    const previewVideo = document.getElementById('add_video_preview');

                    if (file.type.startsWith('image/')) {
                        // Jika file adalah gambar
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        previewVideo.style.display = 'none';
                    } else if (file.type.startsWith('video/')) {
                        // Jika file adalah video
                        previewVideo.src = e.target.result;
                        previewVideo.style.display = 'block';
                        previewImage.style.display = 'none';
                    } else {
                        // Jika file bukan gambar atau video
                        previewImage.style.display = 'none';
                        previewVideo.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('add_file_preview').style.display = 'none';
                document.getElementById('add_video_preview').style.display = 'none';
            }
        }


        $(document).ready(function() {
            const datatableWrap = $(".datatable-wrap");
            const wrappingDiv = $("<div>").addClass("w-100").css("overflow-x", "scroll");
            datatableWrap.children().appendTo(wrappingDiv);
            datatableWrap.append(wrappingDiv);

            $('#edit_file').change(function(event) {
                previewFile(event);
            });

            function previewFile(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewImage = document.getElementById('file-preview');
                        const previewVideo = document.getElementById('video-preview');

                        if (file.type.startsWith('image/')) {
                            // Jika file adalah gambar
                            previewImage.src = e.target.result;
                            previewImage.style.display = 'block';
                            previewVideo.style.display = 'none';
                        } else if (file.type.startsWith('video/')) {
                            // Jika file adalah video
                            previewVideo.src = e.target.result;
                            previewVideo.style.display = 'block';
                            previewImage.style.display = 'none';
                        } else {
                            // Jika file bukan gambar atau video
                            previewImage.style.display = 'none';
                            previewVideo.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('file-preview').style.display = 'none';
                    document.getElementById('video-preview').style.display = 'none';
                }
            }

            @if ($errors->any())
                @if (session('edit_gallery_id') && old('_method') == 'PUT')
                    $('#editModal').modal('show');
                    var profileItemId = "{{ session('edit_gallery_id') }}";
                    var url = "{{ route('profile-galleries.find', ':id') }}".replace(':id', profileItemId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#editForm').attr('action',
                                "{{ route('profile-galleries.update', ':id') }}".replace(':id',
                                    profileItemId));

                            const oldType = "{{ old('type') }}";

                            // Set the type based on old value or response
                            if (oldType) {
                                if (oldType === 'image') {
                                    $('#editCustomRadio1').prop('checked', true);
                                } else if (oldType === 'video') {
                                    $('#editCustomRadio2').prop('checked', true);
                                }
                            } else {
                                if (response.type === 'image') {
                                    $('#editCustomRadio1').prop('checked', true);
                                } else if (response.type === 'video') {
                                    $('#editCustomRadio2').prop('checked', true);
                                }
                            }

                            // Preview old file
                            if (response.path) {
                                previewOldFile(response.path.replace('public/', ''), response.type);
                            } else {
                                $('#photo-preview').hide();
                                $('#video-preview').hide();
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
                var profileItemId = $(this).data('id');
                var url = "{{ route('profile-galleries.find', ':id') }}".replace(':id', profileItemId);
                $.ajax({
                    url: "{{ route('profile-galleries.session', ':id') }}".replace(':id',
                        profileItemId),
                    type: 'GET',
                    success: function() {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                console.log(response);
                                $('#editModal').modal('show');
                                $('#editForm').attr('action',
                                    "{{ route('profile-galleries.update', ':id') }}"
                                    .replace(':id',
                                        profileItemId));

                                if (response.type === 'image') {
                                    $('#editCustomRadio1').prop('checked', true);
                                } else if (response.type === 'video') {
                                    $('#editCustomRadio2').prop('checked', true);
                                }

                                if (response.path) {
                                    previewOldFile(response.path.replace('public/',
                                        ''), response.type);
                                } else {
                                    $('#file-preview').hide();
                                    $('#video-preview').hide();
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

            function previewOldFile(path, type) {
                var fileUrl = `{{ Storage::url('${path}') }}`;

                // Handle photo preview
                if (type === 'image') {
                    $('#file-preview').attr('src', fileUrl).show();
                    $('#video-preview').hide();
                }
                // Handle video preview
                else if (type === 'video') {
                    $('#video-preview').attr('src', fileUrl);
                    $('#video-preview')[0].load(); // Ensure the video loads properly
                    $('#video-preview').show();
                    $('#file-preview').hide();
                }
                // Handle unknown type
                else {
                    $('#file-preview').hide();
                    $('#video-preview').hide();
                }
            }

            $('.delete-button').click(function() {
                var profileItemId = $(this).data('id');
                var url = "{{ route('profile-galleries.find', ':id') }}".replace(':id', profileItemId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#deleteModal').modal('show');
                        $('#deleteForm').attr('action',
                            "{{ route('profile-galleries.delete', ':id') }}".replace(':id',
                                profileItemId));
                        $("#deleteText").text("Apakah anda yakin ingin menghapus item ini?");
                    },
                    error: function() {
                        alert('Failed to fetch data');
                    }
                });
            });

            $('.show-button').click(function() {
                var profileItemId = $(this).data('id');
                var url = "{{ route('profile-galleries.find', ':id') }}".replace(':id', profileItemId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#showModal').modal('show');
                        $('#showName').text(response.name);
                        $('#showType').text(response.type);

                        if (response.path) {
                            var fileUrl = response.path.replace('public/', '/storage/');

                            // Menampilkan konten berdasarkan tipe file
                            if (response.type === 'image') {
                                $('#showType').text('Foto');
                                $('#showPhoto').attr('src', fileUrl).show();
                                $('#showVideo').hide();
                            } else if (response.type === 'video') {
                                $('#showType').text('Video');
                                $('#showVideo').attr('src', fileUrl).show();
                                $('#showPhoto').hide();
                            }
                        } else {
                            $('#showPhoto').hide();
                            $('#showVideo').hide();
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
                    <h3 class="nk-block-title page-title">Galeri Profil</h3>
                </div>
                <div class="nk-block-head-content">
                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                        title="Batas maksimum satu galeri profil jurusan">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm"
                            @if ($disabledAddButton) disabled @endif>
                            <em class="icon ni ni-plus me-1"></em>Tambah Galeri Profil</span>
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
                            <th class="text-nowrap text-center align-middle">Tipe</th>
                            <th class="text-nowrap text-center align-middle">File</th>
                            <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($profiles as $index => $item)
                            <tr class="text-center align-middle">
                                <td>
                                    <a href="#"><span>{{ $index + 1 }}</span></a>
                                </td>
                                <td>
                                    <span>{{ $item->type === 'image' ? 'Foto' : 'Video' }}</span>
                                </td>
                                <td>
                                    @if ($item->type === 'image')
                                        <img src="{{ str_replace('public', '/storage', $item->path) }}" alt="Image"
                                            class="img-fluid" style="max-height: 100px;">
                                    @else
                                        <video src="{{ str_replace('public', '/storage', $item->path) }}" controls
                                            class="img-fluid" style="max-height: 100px;">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs rounded-pill show-button"
                                        data-id="{{ $item->id }}">
                                        <em class="ni ni-eye"></em>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs rounded-pill edit-button"
                                        data-id="{{ $item->id }}">
                                        <em class="ni ni-edit"></em>
                                    </button>
                                    <button class="btn btn-danger btn-xs rounded-pill delete-button"
                                        data-id="{{ $item->id }}">
                                        <em class="ni ni-trash"></em>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- .card-preview -->
        </div><!-- nk-block -->
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Galeri Profil</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('profile-galleries.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group col-xxl-3 col-md-6">
                            <label class="form-label">Tipe</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="customRadio1" name="type" value="image"
                                                autocomplete="off" required class="custom-control-input">
                                            <label class="custom-control-label" for="customRadio1">Foto</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="customRadio2" name="type" value="video"
                                                autocomplete="off" required class="custom-control-input">
                                            <label class="custom-control-label" for="customRadio2">Video</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="d-flex align-items-center justify-content-start">
                                    <img id="add_file_preview" src="#" alt="File Preview" class="img-fluid"
                                        src="#" style="display: none; max-width: 200px; max-height: 200px;">
                                    <video id="add_video_preview" controls
                                        style="display: none; max-width: 100%; height: auto;">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="file">File</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        name="file" id="file" onchange="previewNewFile(event)" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Galeri Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">

                        <div class="form-group col-xxl-3 col-md-6">
                            <label class="form-label">Tipe</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="editCustomRadio1" name="type" value="image"
                                                autocomplete="off" required class="custom-control-input">
                                            <label class="custom-control-label" for="editCustomRadio1">Foto</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-block">
                                        <div class="custom-control custom-control-sm custom-radio">
                                            <input type="radio" id="editCustomRadio2" name="type" value="video"
                                                autocomplete="off" required class="custom-control-input">
                                            <label class="custom-control-label" for="editCustomRadio2">Video</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="d-flex align-items-center justify-content-start">
                                    <img id="file-preview" src="#" alt="File Preview"
                                        style="display: none; max-width: 100%; height: auto;" />
                                    <video id="video-preview" src="#" controls
                                        style="display: none; max-width: 100%; height: auto;">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <div class="custom-file position-relative mt-1">
                                    <label class="form-label" for="edit_file">File</label>
                                    <input type="file" class="form-control" id="edit_file" name="file"
                                        accept="image/*,video/*" onchange="previewFile(event)">
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
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Hapus Galeri Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('DELETE')
                        <p id="deleteText">Apakah anda yakin ingin menghapus item ini?</p>
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger"><em
                                    class="ni ni-trash me-1"></em>Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Show Modal --}}
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showModalLabel">Detail Galeri Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-3">
                            <img id="showPhoto" src="#" alt="Photo Preview" class="img-fluid"
                                style="max-height: 300px; display: none;">
                            <div class="video-container">
                                <video id="showVideo" controls>
                                    <source src="#" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Tipe</td>
                                        <td id="showType"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
