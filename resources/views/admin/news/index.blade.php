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
            $('#search-input').on('input', function() {
                performSearch();
            });

            $('#sortField, #sortDirection, #perPage').change(function() {
                $('#search-form').submit();
            });

            function performSearch() {
                const query = $('#search-input').val().toLowerCase();
                $('.news-item').each(function() {
                    const title = $(this).find('.news-title').text().toLowerCase();
                    const description = $(this).find('.news-description').text().toLowerCase();
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
                    <h3 class="nk-block-title page-title">{{ $title }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('news.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus me-1"></em>Tambah Berita</a>
                </div>
            </div>
            <div class="col-md-12">
                <form id="search-form" method="GET" action="{{ route('news') }}" class="mt-3 align-items-center">
                    <div class="row justify-content-between align-items-center g-2">
                        <div class="col-md-3">
                            <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                <label class="mb-0">
                                    <input type="text" id="search-input" name="search" class="form-control form-control-sm"
                                        placeholder="Ketik untuk mencari" value="{{ request()->get('search') }}" aria-controls="DataTables_Table_0">
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
            <div id="news-content" class="row">
                @foreach ($news as $index => $newsItem)
                    <div class="col-12 col-sm-6 col-md-4 mt-3 news-item">
                        <div class="card card-bordered">
                            <img src="{{ Storage::url($newsItem->thumbnail) }}" class="card-img-top thumbnail-image"
                                alt="">
                            <div class="card-inner">
                                <h5 class="card-title news-title">{{ $newsItem->title }}</h5>
                                <p class="card-text news-description">{{ $newsItem->description }}</p>
                                <a href="#" class="btn btn-primary d-flex justify-content-center">Lihat Berita</a>
                            </div>
                            <div
                                class="card-footer border-top text-muted d-flex justify-content-between align-items-center">
                                <div>{{ \Carbon\Carbon::parse($newsItem->updated_at)->translatedFormat('d F Y') }}</div>
                                <div class="dropdown">
                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                        <em class="icon ni ni-more-h"></em>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                        <ul class="link-list-plain">
                                            <li><a href="{{ route('news.edit', $newsItem->slug) }}">Edit</a></li>
                                            <li>
                                                <a href="#" class="delete-link">Hapus</a>
                                                <form class="delete-form"
                                                    action="{{ route('news.delete', $newsItem->slug) }}" method="POST"
                                                    style="display: none;">
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
                    @if ($news->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $news->previousPageUrl() }}" rel="prev">Prev</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @for ($i = 1; $i <= $news->lastPage(); $i++)
                        @if ($i == $news->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $i }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $news->url($i) }}">{{ $i }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($news->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $news->nextPageUrl() }}" rel="next">Next</a>
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
