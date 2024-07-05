<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index()
    {
        $title = 'Berita';
        $articles = Article::get();
        return view('admin.articles.index', compact('title', 'articles'));
    }

    public function create()
    {
        $title = 'Tambah Berita';
        return view('admin.articles.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:articles',
            'thumbnail' => 'required|file|mimes:png,jpg,jpeg',
            'description' => 'required',
            'content' => 'required',
        ]);

        $uniqueSlug = $this->makeUniqueSlug($request->slug);

        $uploadedFile = $request->file('thumbnail');
        $originalName = $uploadedFile->getClientOriginalName();
        $thumbnailName = "$uniqueSlug" . '-' . $originalName;
        $thumbnailPath = $uploadedFile->storeAs('public/berita/thumbnail', $thumbnailName);

        $content = $request->content;
        $dom = new \DomDocument();
        $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $k => $img) {
            $data = $img->getAttribute('src');
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $image_name = "public/berita/konten/$uniqueSlug" . time() . $k . '.png';
            Storage::put($image_name, $data);

            $img->removeAttribute('src');
            $img->setAttribute('src', env('APP_URL') . Storage::url($image_name));
        }

        $content = $dom->saveHTML();

        $summernote = new Article();

        $summernote->title = $request->title;
        $summernote->slug = $uniqueSlug;
        $summernote->thumbnail = $thumbnailPath;
        $summernote->description = $request->description;
        $summernote->content = $content;

        $summernote->save();

        return redirect()->route('articles')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit($slug)
    {
        $title = 'Edit Berita';
        $article = Article::where('slug', $slug)->firstOrFail();
        return view('admin.articles.edit', compact('title', 'article'));
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'thumbnail' => 'file|mimes:png,jpg,jpeg',
            'description' => 'required',
            'content' => 'required',
        ]);

        $article = Article::where('slug', $slug)->firstOrFail();

        $newSlug = Str::slug($request->title, '-');
        $uniqueSlug = $this->makeUniqueSlug($newSlug, $article->id);

        $dataToUpdate = [
            'title' => $request->title,
            'slug' => $uniqueSlug,
            'description' => $request->description,
            'content' => $request->content,
        ];

        if ($request->hasFile('thumbnail')) {
            Storage::delete($article->thumbnail);

            $uploadedFile = $request->file('thumbnail');
            $originalName = $uploadedFile->getClientOriginalName();
            $thumbnailName = "$uniqueSlug" . '-' . $originalName;
            $thumbnailPath = $uploadedFile->storeAs('public/berita/thumbnail', $thumbnailName);

            $dataToUpdate['thumbnail'] = $thumbnailPath;
        }

        $content = $request->content;
        $dom = new \DomDocument();
        $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $k => $img) {
            $data = $img->getAttribute('src');
            if (strpos($data, 'data:image') === 0) {
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $image_name = "public/berita/konten/$uniqueSlug" . time() . $k . '.png';
                Storage::put($image_name, $data);

                $img->removeAttribute('src');
                $img->setAttribute('src', env('APP_URL') . Storage::url($image_name));
            }
        }

        $content = $dom->saveHTML();
        $dataToUpdate['content'] = $content;

        $article->update($dataToUpdate);

        return redirect()->route('articles')->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        Storage::delete($article->thumbnail);

        $article->delete();

        return redirect()->route('articles')->with('success', 'Berita berhasil dihapus.');
    }

    private function makeUniqueSlug($slug, $currentSlug = null)
    {
        $uniqueSlug = $slug;
        $counter = 2;

        while (Article::where('slug', $uniqueSlug)->whereNot('slug', $currentSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }

        return $uniqueSlug;
    }
}
