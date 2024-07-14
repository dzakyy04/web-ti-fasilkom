@extends('admin.layouts.app')

@push('css')
    <style>
        .thumbnail-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('assets/js/libs/datatable-btns.js?ver=3.0.3') }}"></script>
    <script src="{{ asset('assets/js/example-toastr.js?ver=3.0.3') }}"></script>
    @if (session()->has('success'))
        <script>
            let message = @json(session('success'));
            NioApp.Toast(`<h5>Berhasil</h5><p>${message}</p>`, 'success', {
                position: 'top-right',
            });
        </script>
    @endif
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
    </script>
    <script>
        $(document).ready(function() {
            $('#search-input').on('input', function() {
                performSearch();
            });

            $('#sortField, #sortDirection, #perPage').change(function() {
                $('#search-form').submit();
            });

            function performSearch() {
                const query = $('#search-input').val().toLowerCase();
                $('.announcements-item').each(function() {
                    const title = $(this).find('.announcements-title').text().toLowerCase();
                    const description = $(this).find('.announcements-description').text().toLowerCase();
                    if (title.includes(query) || description.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="col-md-12">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Pengumuman</h3>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus me-1"></em>Tambah Pengumuman</span>
                    </a>
                </div>
            </div>
            <div class="col-md-12">
                <form id="search-form" method="GET" action="{{ route('news') }}" class="mt-3 align-items-center">
                    <div class="row justify-content-between align-items-center g-2">
                        <div class="col-md-3">
                            <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                <label class="mb-0">
                                    <input type="text" id="search-input" name="search"
                                        class="form-control form-control-sm" placeholder="Ketik untuk mencari"
                                        value="{{ request()->get('search') }}" aria-controls="DataTables_Table_0">
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="dataTables_length d-flex align-items-center justify-content-end">
                                <label class="mb-0 me-2"><span class="d-none d-sm-inline-block">Show</span></label>
                                <div class="form-control-select">
                                    <select id="perPage" name="perPage" aria-controls="DataTables_Table_0"
                                        class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value="3" {{ $perPage == '3' ? 'selected' : '' }}>3</option>
                                        <option value="10" {{ $perPage == '10' ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPage == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $perPage == '50' ? 'selected' : '' }}>50</option>
                                        <option value="75" {{ $perPage == '75' ? 'selected' : '' }}>75</option>
                                        <option value="100" {{ $perPage == '100' ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="announcements-content" class="row">
                @foreach ($announcements as $index => $announcement)
                    <div class="col-12 col-sm-6 col-md-4 mt-3 announcements-item">
                        <div class="card card-bordered">
                            <img src="{{ Storage::url($announcement->thumbnail) }}" class="card-img-top thumbnail-image"
                                alt="">
                            <div class="card-inner">
                                <h5 class="card-title announcements-title">{{ $announcement->title }}</h5>
                                <p class="card-text announcements-desc">{{ $announcement->description }}</p>
                                <a href="#" class="btn btn-primary d-flex justify-content-center">Lihat
                                    Pengumuman</a>
                            </div>
                            <div
                                class="card-footer border-top text-muted d-flex justify-content-between align-items-center">
                                <dev>{{ \Carbon\Carbon::parse($announcement->updated_at)->translatedFormat('d F Y') }}
                                </dev>
                                <div class="dropdown">
                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger"
                                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                        <ul class="link-list-plain">
                                            <li><a href="{{ route('announcements.edit', $announcement->slug) }}">Edit</a>
                                            </li>
                                            <li>
                                                <a href="#" class="delete-link">Hapus</a>
                                                <form class="delete-form"
                                                    action="{{ route('announcements.delete', $announcement->slug) }}"
                                                    method="POST" style="display: none;">
                                                    @method('DELETE')
                                                    @csrf
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <nav>
                <ul class="pagination mt-3">
                    {{-- Previous Page Link --}}
                    @if ($announcements->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $announcements->previousPageUrl() }}" rel="prev">Prev</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @for ($i = 1; $i <= $announcements->lastPage(); $i++)
                        @if ($i == $announcements->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $i }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $announcements->url($i) }}">{{ $i }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($announcements->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $announcements->nextPageUrl() }}" rel="next">Next</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">Next</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
@endsection
