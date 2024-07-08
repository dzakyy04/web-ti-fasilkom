@extends('admin.layouts.app')

@push('js')
    <script src="{{ asset('assets/js/libs/datatable-btns.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/example-toastr.js?ver=3.0.3') }}"></script>
    <script>
        $(document).ready(function() {
            const datatableWrap = $(".datatable-wrap");
            const wrappingDiv = $("<div>").addClass("w-100").css("overflow-x", "scroll");
            datatableWrap.children().appendTo(wrappingDiv);
            datatableWrap.append(wrappingDiv);
        });
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
                    <a href="{{ route('articles.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus me-1"></em>Tambah Dosen</span>
                    </a>
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
                                <td>
                                    <a href="" class="btn btn-primary btn-xs rounded-pill">
                                        <em class="ni ni-eye"></em>
                                    </a>
                                    <a href="" class="btn btn-warning btn-xs rounded-pill">
                                        <em class="ni ni-edit"></em>
                                    </a>
                                    <button class="btn btn-danger btn-xs rounded-pill" data-bs-toggle="modal"
                                        data-bs-target="#deleteArticleModal">
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
@endsection
