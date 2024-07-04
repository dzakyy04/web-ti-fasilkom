<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index()
    {
        $title = 'Berita';
        $articles = Article::get();
        return view('admin.articles.index', compact('title', 'articles'));
    }
}
