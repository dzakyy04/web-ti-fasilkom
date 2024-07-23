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

            $('#search-input').on('input', function() {
                performSearch();
            });

            $('#sortField, #sortDirection, #perPage').change(function() {
                performSearch();
            });

            function performSearch() {
                const query = $('#search-input').val();
                const perPage = $('#perPage').val();

                $.ajax({
                    url: '{{ route('news') }}',
                    type: 'GET',
                    data: {
                        search: query,
                        perPage: perPage
                    },
                    success: function(data) {
                        $('#news-content').html($(data).find('#news-content').html());
                        $('.pagination').html($(data).find('.pagination').html());
                    }
                });
            }
        });

        function previewContent(title, content, thumbnail, createdAt) {
            var formattedDate = formatDate(createdAt);

            $('#previewTitle').text(title);
            $('#previewCreatedAt').html('<i class="icon ni ni-calendar text-warning"></i> ' + formattedDate);

            var previewThumbnail = $('#previewThumbnail');
            if (thumbnail) {
                previewThumbnail.attr('src', thumbnail);
                previewThumbnail.show();
            } else {
                previewThumbnail.hide();
            }

            $('#previewContent').html(content);
            $('#previewModal').modal('show');
        }

        function formatDate(dateString) {
            const options = {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            const date = new Date(dateString.replace(/-/g, '/'));
            return date.toLocaleDateString('id-ID', options);
        }
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
                        <em class="icon ni ni-plus me-1"></em>Tambah Berita</span>
                    </a>
                </div>
            </div>
            <div class="col-md-12">
                <form id="search-form" method="GET" action="{{ route('news') }}" class="mt-3 align-items-center">
                    <div class="row justify-content-between align-items-center g-2">
                        <div class="col-md-3">
                            <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                <label class="mb-0">
                                    <input type="text" id="search-input" class="form-control form-control-sm"
                                        placeholder="Ketik untuk mencari" aria-controls="DataTables_Table_0">
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="dataTables_length d-flex align-items-center justify-content-end">
                                <label class="mb-0 me-2"><span class="d-none d-sm-inline-block">Show</span></label>
                                <div class="form-control-select">
                                    <select id="perPage" name="perPage" aria-controls="DataTables_Table_0"
                                        class="custom-select custom-select-sm form-control form-control-sm">
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
                                <p class="card-text news-desc">{{ $newsItem->content }}</p>
                                <button type="button" class="btn btn-primary w-100 d-flex justify-content-center"
                                    onclick="previewContent(
                                    '{{ $newsItem->title }}',
                                    '{{ $newsItem->content }}',
                                    '{{ Storage::url($newsItem->thumbnail) }}',
                                    '{{ $newsItem->created_at }}'
                                )">Lihat
                                    Berita</button>
                            </div>
                            <div
                                class="card-footer border-top text-muted d-flex justify-content-between align-items-center">
                                <div>{{ \Carbon\Carbon::parse($newsItem->created_at)->translatedFormat('d F Y') }}
                                </div>
                                <div class="dropdown">
                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger"
                                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                        <ul class="link-list-plain">
                                            <li><a href="{{ route('news.edit', $newsItem->slug) }}">Edit</a></li>
                                            <li>
                                                <a href="#" class="delete-link">Hapus</a>
                                                <form class="delete-form" action="{{ route('news.delete', $newsItem->slug) }}" method="POST" style="display: none;">
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
                    @if ($news->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $news->previousPageUrl() }}" rel="prev">Prev</a>
                        </li>
                    @endif

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
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Lihat Berita</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <h2 class="mb-1" id="previewTitle"></h2>
                    <p class="mb-3 sub-text" id="previewCreatedAt"></p>
                    <img class="img-fluid mb-3" id="previewThumbnail" class="w-full" src="" style="display: none;" alt="Thumbnail">
                    <div id="previewContent"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
