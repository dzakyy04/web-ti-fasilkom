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
        });
    </script>
    <script>
        function previewThumbnail(event) {
            var preview = document.getElementById('thumbnail-preview');
            preview.style.display = 'block';
            preview.src = URL.createObjectURL(event.target.files[0]);
        }

        $(document).ready(function() {
            $('#title').on('input', function() {
                var title = $(this).val();
                var slug = title.toLowerCase().replace(/\s+/g, '-');
                $('#slug').val(slug);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            const datatableWrap = $(".datatable-wrap");
            const wrappingDiv = $("<div>").addClass("w-100").css("overflow-x", "scroll");
            datatableWrap.children().appendTo(wrappingDiv);
            datatableWrap.append(wrappingDiv);

            $('.delete-link').click(function(event) {
                event.preventDefault();
                const slug = $(this).data('slug');
                $('#delete-form-' + slug).submit();
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
                    url: '{{ route('announcements') }}',
                    type: 'GET',
                    data: {
                        search: query,
                        perPage: perPage
                    },
                    success: function(data) {
                        $('#announcements-content').html($(data).find('#announcements-content').html());
                        $('.pagination').html($(data).find('.pagination').html());
                    }
                });
            }
        });

        function previewContent(newsSlug) {
            $.ajax({
                url: '{{ route('announcements.find', '') }}/' + newsSlug,
                type: 'GET',
                success: function(data) {
                    console.log(data);
                    var formattedDate = formatDate(data.created_at);

                    $('#previewTitle').text(data.title);
                    $('#previewCreatedAt').html('<i class="icon ni ni-calendar text-warning"></i> ' +
                        formattedDate);

                    var previewThumbnail = $('#previewThumbnail');
                    if (data.thumbnail) {
                        var thumbnailUrl = '{{ Storage::url('') }}' + data.thumbnail.replace('public/', '');
                        previewThumbnail.attr('src', thumbnailUrl);
                        previewThumbnail.show();
                    } else {
                        previewThumbnail.hide();
                    }

                    $('#previewContent').html(data.content);
                    $('#previewModal').modal('show');
                }
            });
        }

        function formatDate(dateString) {
            const options = {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                // handle invalid date case
                return "Invalid Date";
            }
            return date.toLocaleDateString('id-ID', options);
        }
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
                <form id="search-form" method="GET" action="{{ route('announcements') }}" class="mt-3 align-items-center">
                    <div class="row justify-content-between align-items-center g-2">
                        <div class="col-md-3">
                            <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                <label class="mb-0">
                                    <input type="text" id="search-input" class="form-control form-control-sm"
                                        placeholder="Ketik untuk mencari"aria-controls="DataTables_Table_0">
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
            <div id="announcements-content" class="row">
                @foreach ($announcements as $index => $announcement)
                    <div class="col-12 col-sm-6 col-md-4 mt-3 announcements-item">
                        <div class="card card-bordered">
                            <img src="{{ Storage::url($announcement->thumbnail) }}" class="card-img-top thumbnail-image"
                                alt="">
                            <div class="card-inner">
                                <h5 class="card-title announcements-title">{{ $announcement->title }}</h5>
                                <p class="card-text announcements-desc">{{ $announcement->content }}</p>
                                <button type="button" class="btn btn-primary w-100 d-flex justify-content-center"
                                    onclick="previewContent('{{ $announcement->slug }}')">Lihat Pengumuman</button>
                            </div>
                            <div
                                class="card-footer border-top text-muted d-flex justify-content-between align-items-center">
                                <dev>{{ \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('d F Y') }}
                                </dev>
                                <div class="dropdown">
                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger"
                                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                        <ul class="link-list-plain">
                                            <li><a href="{{ route('announcements.edit', $announcement->slug) }}">Edit</a>
                                            </li>
                                            <li>
                                                <a href="#" class="delete-link"
                                                    data-slug="{{ $announcement->slug }}">Hapus</a>
                                                <form id="delete-form-{{ $announcement->slug }}" class="delete-form"
                                                    action="{{ route('announcements.delete', $announcement->slug) }}" method="POST"
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
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Lihat Pengumuman</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <h2 class="mb-1" id="previewTitle"></h2>
                    <p class="mb-3 sub-text" id="previewCreatedAt"></p>
                    <img class="img-fluid mb-3" id="previewThumbnail" class="w-full" src=""
                        style="display: none;" alt="Thumbnail">
                    <div id="previewContent"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Tutup</a>
                </div>
            </div>
        </div>
    </div>
    
@endsection
