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

    public function mapFacilities(Collection $facilities)
    {
        return $facilities->map(function ($facility) {
            return [
                'id' => $facility->id,
                'nama' => $facility->name,
                'lokasi' => $facility->location,
                'foto' => $facility->photo,
            ];
        })->toArray();
    }

    public function mapLeaders(Collection $leaders)
    {
        return $leaders->map(function ($leader) {
            return [
                'id' => $leader->id,
                'nama' => $leader->name,
                'jabatan' => $leader->position,
                'deskripsi' => $leader->description,
                'foto' => $leader->photo,
            ];
        })->toArray();
    }

    public function mapAdmins(Collection $admins)
    {
        return $admins->map(function ($admin) {
            return [
                'id' => $admin->id,
                'name' => $admin->name,
                'foto' => $admin->photo,
                'lokasi' => $admin->location
            ];
        })->toArray();
    }

    public function mapCompetencies(Collection $competencies, string $type)
    {
        return $competencies->map(function ($competency) use ($type) {
            $mappedCompetency = [];

            if ($type === 'support' || $type === 'graduate') {
                $mappedCompetency['nama'] = $competency->name;
            }

            $mappedCompetency['deskripsi'] = $competency->description;

            if ($type === 'graduate') {
                $mappedCompetency['icon'] = $competency->icon;
            }

            return $mappedCompetency;
        })->toArray();
    }

    public function mapMissions(Collection $visionMission)
    {
        return $visionMission->map(function ($mission) {
            return [
                'icon' => $mission->icon,
                'judul' => $mission->title,
                'deskripsi' => $mission->description,
            ];
        })->toArray();
    }
}
