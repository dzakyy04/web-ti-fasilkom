@extends('admin.layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title mb-2">Berita</h4>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="d-flex">
                        <a href="{{ route('articles.create') }}" class="btn btn-primary mb-2 me-2">
                            <em class="icon ni ni-plus me-1"></em>Tambah Berita</span>
                        </a>

                    </div>
                    <table
                        class="datatable-init nk-tb-list nk-tb-ulist table table-hover table-bordered table-responsive-md"
                        data-auto-responsive="false">
                        <thead>
                            <tr class="table-light nk-tb-item nk-tb-head">
                                <th class="text-nowrap text-center align-middle">No</th>
                                <th class="text-nowrap text-center align-middle">Judul</th>
                                </th>
                                <th class="text-nowrap text-center align-middle">Thumbnail
                                <th class="text-nowrap text-center align-middle">Terakhir Diubah
                                </th>
                                <th class="text-nowrap text-center no-export align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($articles as $index => $article)
                                <tr class="text-center align-middle">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $article->title }}</td>
                                    <td>
                                        <img src="{{ Storage::url($article->thumbnail) }}" class="img-fluid rounded"
                                            style="width: 150px">
                                    </td>
                                    <td>{{ $article->updated_at }}</td>

                                    <td class="text-nowrap justify-content-center align-items-center text-center">
                                        <a href="" class="btn btn-primary btn-xs rounded-pill">
                                            <em class="ni ni-eye"></em>
                                        </a>
                                        <a href="" class="btn btn-warning btn-xs rounded-pill">
                                            <em class="ni ni-edit"></em>
                                        </a>
                                        <button class="btn btn-danger btn-xs rounded-pill" data-bs-toggle="modal"
                                            data-bs-target="#deleteArticleModal" data-slug="{{ $article->slug }}"
                                            data-title="{{ $article->title }}">
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
    </div>
@endsection
