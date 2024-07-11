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
@endpush

@section('content')
    <div class="col-md-12">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Berita</h3>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('news.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus me-1"></em>Tambah Berita</span>
                    </a>
                </div>
            </div>
            <div class="row">
                @foreach ($news as $index => $newsItem)
                    <div class="col-12 col-sm-6 col-md-4 mt-3">
                        <div class="card card-bordered">
                            <img src="{{ Storage::url($newsItem->thumbnail) }}" class="card-img-top thumbnail-image"
                                alt="">
                            <div class="card-inner">
                                <h5 class="card-title">{{ $newsItem->title }}</h5>
                                <p class="card-text">{{ $newsItem->description }}</p>
                                <a href="#" class="btn btn-primary d-flex justify-content-center">Lihat
                                    Berita</a>
                            </div>
                            <div
                                class="card-footer border-top text-muted d-flex justify-content-between align-items-center">
                                <dev>{{ \Carbon\Carbon::parse($newsItem->updated_at)->translatedFormat('d F Y') }}</dev>
                                <div class="dropdown">
                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger"
                                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                        <ul class="link-list-plain">
                                            <li><a href="{{ route('news.edit', $newsItem->slug) }}">Edit</a></li>
                                            <li>
                                                <a href="#" class="delete-link">Hapus</a>
                                                <form class="delete-form" action="{{ route('news.delete', $newsItem->slug) }}"
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
        </div>
    </div>
@endsection
