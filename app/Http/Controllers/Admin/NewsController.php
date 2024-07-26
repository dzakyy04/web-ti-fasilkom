<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Berita';
        $perPage = $request->input('perPage', 10);
        $query = Article::where('type', 'news')->orderBy('created_at', 'desc');
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
    
        $news = $query->paginate($perPage);
    
        $news->transform(function ($newsItem) {
            $newsItem->content = Helper::processContent($newsItem->content);
            return $newsItem;
        });
    
        return view('admin.news.index', compact('title', 'news', 'perPage'));
    }
    
    public function create()
    {
        $title = 'Tambah Berita';
        return view('admin.news.create', compact('title'));
    }

    public function findNews($slug)
    {
        $news = Article::where('type', 'news')->where('slug', $slug)->firstOrFail();

        return response()->json($news);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:articles',
            'thumbnail' => 'required|file|mimes:png,jpg,jpeg',
            'content' => 'required',
        ]);

        $uniqueSlug = $this->makeUniqueSlug($request->slug);

        $uploadedFile = $request->file('thumbnail');
        $originalName = $uploadedFile->getClientOriginalName();
        $thumbnailName = "$uniqueSlug" . '-' . $originalName;
        $thumbnailPath = $uploadedFile->storeAs('public/berita/thumbnail', $thumbnailName);

        $content = $request->content;
        $content = $this->ensureValidHtml($content); // Ensure valid HTML

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
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

        $news = new Article();

        $news->title = $request->title;
        $news->slug = $uniqueSlug;
        $news->thumbnail = $thumbnailPath;
        $news->content = $content;
        $news->type = 'news';

        $news->save();

        return redirect()->route('news')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit($slug)
    {
        $title = 'Edit Berita';
        $newsItem = Article::where('slug', $slug)->firstOrFail();
        return view('admin.news.edit', compact('title', 'newsItem'));
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'thumbnail' => 'file|mimes:png,jpg,jpeg',
            'content' => 'required',
        ]);

        $news = Article::where('slug', $slug)->firstOrFail();

        $oldContent = $news->content;

        $newSlug = Str::slug($request->title, '-');
        $uniqueSlug = $this->makeUniqueSlug($newSlug, $news->id);

        $dataToUpdate = [
            'title' => $request->title,
            'slug' => $uniqueSlug,
            'content' => $request->content,
        ];

        if ($request->hasFile('thumbnail')) {
            Storage::delete($news->thumbnail);

            $uploadedFile = $request->file('thumbnail');
            $originalName = $uploadedFile->getClientOriginalName();
            $thumbnailName = "$uniqueSlug" . '-' . $originalName;
            $thumbnailPath = $uploadedFile->storeAs('public/berita/thumbnail', $thumbnailName);

            $dataToUpdate['thumbnail'] = $thumbnailPath;
        }

        // Handle content images
        $content = $request->content;
        $content = $this->ensureValidHtml($content); // Ensure valid HTML
        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $images = $dom->getElementsByTagName('img');

        $newImages = [];
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

                $newImages[] = env('APP_URL') . Storage::url($image_name);
            } else {
                $newImages[] = $data;
            }
        }

        $content = $dom->saveHTML();
        $dataToUpdate['content'] = $content;

        // Delete removed images
        $domOld = new \DomDocument();
        $domOld->loadHtml($oldContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $oldImages = $domOld->getElementsByTagName('img');

        foreach ($oldImages as $img) {
            $src = $img->getAttribute('src');
            if (!in_array($src, $newImages)) {
                $imagePath = str_replace(env('APP_URL') . '/storage/', 'public/', $src);
                Storage::delete($imagePath);
            }
        }

        $news->update($dataToUpdate);

        return redirect()->route('news')->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete($slug)
    {
        $news = Article::where('slug', $slug)->firstOrFail();

        // Delete thumbnail
        Storage::delete($news->thumbnail);

        // Delete content images
        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($news->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $imagePath = str_replace(env('APP_URL') . '/storage/', 'public/', $src);
            Storage::delete($imagePath);
        }

        $news->delete();

        return redirect()->route('news')->with('success', 'Berita berhasil dihapus.');
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

    private function ensureValidHtml($content)
    {
        if (strpos($content, '<div') === false) {
            $content = '<div id="article-content">' . $content . '</div>';
        }
        return $content;
    }
}
