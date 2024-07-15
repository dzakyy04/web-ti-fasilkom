<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait MapsResponse
{
    public function mapLecturers(Collection $lecturers)
    {
        return $lecturers->map(function ($lecturer) {
            return [
                'id' => $lecturer->id,
                'nama' => $lecturer->name,
                'nip' => $lecturer->nip,
                'nidn' => $lecturer->nidn,
                'jabatan' => $lecturer->position,
                'foto' => $lecturer->photo,
                'pendidikan' => $lecturer->educations->map(function ($education) {
                    return [
                        'jenjang' => $education->degree,
                        'jurusan' => $education->major,
                        'institusi' => $education->institution,
                    ];
                }),
                'bidangPenelitian' => $lecturer->researchFields->map(function ($field) {
                    return [
                        'nama' => $field->name,
                    ];
                }),
            ];
        })->toArray();
    }

    public function mapArticles(Collection $articles)
    {
        return $articles->map(function ($article) {
            return [
                'id' => $article->id,
                'judul' => $article->title,
                'slug' => $article->slug,
                'konten' => $article->content,
                'thumbnail' => $article->thumbnail,
                'tanggalDibuat' => $article->created_at,
                'tanggalDiperbarui' => $article->updated_at,
            ];
        })->toArray();
    }
}
