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
    <script>
        $(document).ready(function() {
            $('.js-select2').select2({
                tags: true,
                tokenSeparators: [','],
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                },
                insertTag: function(data, tag) {
                    data.push(tag);
                }
            });

            var select2Container = $('.js-select2').data('select2').$container;
            select2Container.attr('data-placeholder', 'Pilih atau buat riset baru');
        });
    </script>
    <script src="{{ asset('assets/js/libs/datatable-btns.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/example-toastr.js?ver=3.0.3') }}"></script>
    <script>
        function previewPhoto(event) {
            var preview = $('.photo-preview');
            preview.show();
            preview.attr('src', URL.createObjectURL(event.target.files[0]));
        }


        $(document).ready(function() {
            const datatableWrap = $(".datatable-wrap");
            const wrappingDiv = $("<div>").addClass("w-100").css("overflow-x", "scroll");
            datatableWrap.children().appendTo(wrappingDiv);
            datatableWrap.append(wrappingDiv);

            let educationIndex = {{ old('educations') ? count(old('educations')) : 1 }};
            let editEducationIndex = 0;

            $('#addEducation').click(function() {
                $('#educations').append(`
                <div class="education mb-2">
                        <input type="text" class="form-control mb-1" name="educations[${educationIndex}][degree]" placeholder="Masukkan gelar (contoh: S1)" required>
                        <input type="text" class="form-control mb-1" name="educations[${educationIndex}][institution]" placeholder="Masukkan institusi" required>
                        <input type="text" class="form-control mb-1" name="educations[${educationIndex}][major]" placeholder="Masukkan jurusan" required>
                        <button type="button" class="btn btn-dim btn-danger btn-sm btn-remove-education mt-2"><em class="ni ni-cross me-1"></em>Batal Tambah Pendidikan</button>
                    </div>
                `);
                educationIndex++;
            });

            $('#educations').on('click', '.btn-remove-education', function() {
                $(this).closest('.education').remove();
            });

            $('#addResearchField').click(function() {
                $('#research_fields').append(`
                <input type="text" class="form-control mb-1" name="research_fields[]" placeholder="Masukkan bidang riset" required>
            `);
            });

            $('#edit_addEducation').click(function() {
                $('#edit_educations').append(`
                <div class="education mb-2">
                    <input type="text" class="form-control mb-1" name="educations[${editEducationIndex}][degree]" placeholder="Masukkan gelar (contoh: S1)" required>
                    <input type="text" class="form-control mb-1" name="educations[${editEducationIndex}][institution]" placeholder="Masukkan institusi" required>
                    <input type="text" class="form-control mb-1" name="educations[${editEducationIndex}][major]" placeholder="Masukkan jurusan" required>
                </div>
            `);
                editEducationIndex++;
            });

            $('#edit_addResearchField').click(function() {
                $('#edit_research_fields').append(`
                <input type="text" class="form-control mb-1" name="research_fields[]" placeholder="Masukkan bidang riset" required>
            `);
            });

            @if ($errors->any())
                $('#modalForm').modal('show');
            @endif

            function previewOldPhoto(photoUrl) {
                var oldPreview = $('#photo-preview');
                oldPreview.attr('src', `{{ Storage::url('${photoUrl}') }}`);
                oldPreview.show();
            }

            function previewPhoto(event) {
                const [file] = event.target.files;
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('photo-preview');
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }

            $('#edit_photo').change(function(event) {
                previewPhoto(event);
            });

            $('.edit-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('lecturers.find', ':id') }}";
                url = url.replace(':id', lecturerId);

                // Fetch lecturer data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#editModal').modal('show');
                        $('#editForm').attr('action', "{{ route('lecturers.update', ':id') }}"
                            .replace(':id', lecturerId));
                        $('#editForm #edit_name').val(response.name);
                        $('#editForm #edit_nip').val(response.nip);
                        $('#editForm #edit_nidn').val(response.nidn);
                        $('#editForm #edit_position').val(response.position);

                        if (response.photo) {
                            previewOldPhoto(response.photo.replace('public/', ''));
                        } else {
                            $('#photo-preview')
                                .hide();
                        }

                        // Clear existing education inputs
                        $('#editForm #edit_educations').empty();

                        // Populate education inputs
                        $.each(response.educations, function(index, education) {
                            $('#editForm #edit_educations').append(`
                            <div class="education mb-2">
                                <input type="text" class="form-control mb-1" name="educations[${index}][degree]" value="${education.degree}" placeholder="Gelar (misal: S1)" required>
                                <input type="text" class="form-control mb-1" name="educations[${index}][institution]" value="${education.institution}" placeholder="Institusi" required>
                                <input type="text" class="form-control mb-1" name="educations[${index}][major]" value="${education.major}" placeholder="Jurusan" required>
                            </div>
                        `);
                        });

                        // Set editEducationIndex based on the number of existing educations
                        editEducationIndex = response.educations.length;

                        // Clear existing research field inputs
                        $('#editForm #edit_research_fields').empty();

                        // Populate research field inputs
                        $.each(response.research_fields, function(index, researchField) {
                            $('#editForm #edit_research_fields').append(`
                            <input type="text" class="form-control mb-1" name="research_fields[]" value="${researchField.name}" placeholder="Bidang Riset" required>
                        `);
                        });
                    }
                });
            });

            $('.delete-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('lecturers.find', ':id') }}";
                url = url.replace(':id', lecturerId);

                // Fetch lecturer data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#deleteModal').modal('show');
                        $('#deleteForm').attr('action', "{{ route('lecturers.delete', ':id') }}"
                            .replace(':id', lecturerId));
                        $("#deleteText").text("Apakah anda yakin ingin menghapus dosen " +
                            response.name + "?");
                    }
                });
            });

            $('.show-button').click(function() {
                var lecturerId = $(this).data('id');
                var url = "{{ route('lecturers.find', ':id') }}";
                url = url.replace(':id', lecturerId);

                // Fetch lecturer data via AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        let photoUrl = response.photo.replace('public/', '');
                        $('#showModal').modal('show');
                        $('#showPhoto').attr('src', `{{ Storage::url('${photoUrl}') }}`);
                        $('#showName').text(response.name);
                        $('#showNip').text(response.nip);
                        $('#showNidn').text(response.nidn);
                        $('#showPosition').text(response.position);


                        let educationHtml = '<ul>';
                        response.educations.forEach(function(education) {
                            educationHtml +=
                                `<li>${education.degree} ${education.major} - ${education.institution}</li>`;
                        });
                        educationHtml += '</ul>';
                        $('#showEducation').html(educationHtml);

                        var researchFieldHtml = '<ol class="list-group list-group-numbered">';
                        response.research_fields.forEach(function(field) {
                            researchFieldHtml += `<li>${field.name}</li>`;
                        });
                        researchFieldHtml += '</ol>';
                        $('#showResearchField').html(researchFieldHtml);
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
                    <h3 class="nk-block-title page-title">Dosen</h3>
                </div>
                <div class="nk-block-head-content">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">
                        <em class="icon ni ni-plus me-1"></em>Tambah Dosen</span>
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
                            <th class="text-nowrap text-center align-middle">NIP</th>
                            <th class="text-nowrap text-center align-middle">NIDN</th>
                            <th class="text-nowrap text-center align-middle">Jabatan</th>
                            <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lecturers as $index => $lecturer)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ Storage::url($lecturer->photo) }}" alt="" class="img-fluid"
                                        style="width: 100px;">
                                </td>
                                <td>{{ $lecturer->name }}</td>
                                <td>{{ $lecturer->nip }}</td>
                                <td>{{ $lecturer->nidn }}</td>
                                <td>{{ $lecturer->position }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs rounded-pill show-button"
                                        data-id="{{ $lecturer->id }}">
                                        <em class="ni ni-eye"></em>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs rounded-pill edit-button"
                                        data-id="{{ $lecturer->id }}">
                                        <em class="ni ni-edit"></em>
                                    </button>
                                    <button class="btn btn-danger btn-xs rounded-pill delete-button"
                                        data-id="{{ $lecturer->id }}">
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
                    <h5 class="modal-title">Detail Dosen</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row">
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
                                        <td class="fw-bold">NIP</td>
                                        <td id="showNip"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">NIDN</td>
                                        <td id="showNidn"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Jabatan</td>
                                        <td id="showPosition"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Riwayat Pendidikan</td>
                                        <td id="showEducation"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Bidang Riset</td>
                                        <td id="showResearchField"></td>
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
                    <h5 class="modal-title">Tambah Dosen</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('lecturers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="photo">Foto</label>
                            <div class="form-control-wrap">
                                <img class="photo-preview" src="#" alt="Photo Preview">
                                <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                    name="photo" id="photo" onchange="previewPhoto(event)" required>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                            <label class="form-label" for="nip">NIP</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('nip') is-invalid @enderror"
                                    name="nip" id="nip" value="{{ old('nip') }}"
                                    placeholder="Masukkan NIP" required>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nidn">NIDN</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('nidn') is-invalid @enderror"
                                    name="nidn" id="nidn" value="{{ old('nidn') }}"
                                    placeholder="Masukkan NIDN" required>
                                @error('nidn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="position">Jabatan</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    name="position" id="position" value="{{ old('position') }}"
                                    placeholder="Masukkan jabatan">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="educations">Riwayat Pendidikan</label>
                            <div id="educations">
                                @if (old('educations'))
                                    @foreach (old('educations') as $index => $education)
                                        <div class="education mb-2">
                                            <input type="text"
                                                class="form-control mb-1 @error('educations.' . $index . '.degree') is-invalid @enderror"
                                                name="educations[{{ $index }}][degree]"
                                                value="{{ $education['degree'] }}" placeholder="Gelar (misal: S1)"
                                                required>
                                            <input type="text"
                                                class="form-control mb-1 @error('educations.' . $index . '.institution') is-invalid @enderror"
                                                name="educations[{{ $index }}][institution]"
                                                value="{{ $education['institution'] }}" placeholder="Institusi" required>
                                            <input type="text"
                                                class="form-control mb-1 @error('educations.' . $index . '.major') is-invalid @enderror"
                                                name="educations[{{ $index }}][major]"
                                                value="{{ $education['major'] }}" placeholder="Jurusan" required>
                                            @error('educations.' . $index . '.degree')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @error('educations.' . $index . '.institution')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @error('educations.' . $index . '.major')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if ($index > 0)
                                                <button type="button"
                                                    class="btn btn-danger btn-dim btn-sm btn-remove-education">Batal Tambah
                                                    Pendidikan</button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="education mb-2">
                                        <input type="text" class="form-control mb-1" name="educations[0][degree]"
                                            placeholder="Masukkan gelar (contoh: S1)" required>
                                        <input type="text" class="form-control mb-1" name="educations[0][institution]"
                                            placeholder="Masukkan institusi" required>
                                        <input type="text" class="form-control mb-1" name="educations[0][major]"
                                            placeholder="Masukkan jurusan" required>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="addEducation"><em
                                    class="ni ni-plus me-1"></em>Tambah Pendidikan</span></button>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="research_fields">Bidang Riset</label>
                            <div id="research_fields" class="mb-2">
                                <select class="form-select js-select2" name="research_fields[]" multiple="multiple">
                                    <option selected disabled>Pilih Jenis Bidang Riset</option>
                                    <option value="Pemrosesan Bahasa Alami">Pemrosesan Bahasa Alami</option>
                                    <option value="Sistem Terdistribusi">Sistem Terdistribusi</option>
                                    <option value="Grafika dan Visualisasi">Grafika dan Visualisasi</option>
                                    <option value="Sains Data dan Pengenalan Pola">Sains Data dan Pengenalan Pola</option>
                                </select>
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
                    <h5 class="modal-title">Edit Dosen</h5>
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
                                <div class="custom-file mt-3 position-relative">
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
                            <label class="form-label" for="edit_nip">NIP</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="nip" id="edit_nip" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_nidn">NIDN</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="nidn" id="edit_nidn">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_position">Jabatan</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="position" id="edit_position">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_educations">Riwayat Pendidikan</label>
                            <div id="edit_educations">
                                <!-- Education fields will be populated dynamically via JavaScript -->
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="edit_addEducation">Tambah
                                Pendidikan</button>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_research_fields">Bidang Riset</label>
                            <div id="edit_research_fields">
                                <!-- Research fields will be populated dynamically via JavaScript -->
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="edit_addResearchField">Tambah
                                Bidang Riset</button>
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
                    <h5 class="modal-title">Hapus Dosen</h5>
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
