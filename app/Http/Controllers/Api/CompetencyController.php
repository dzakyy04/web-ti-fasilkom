<?php

namespace App\Http\Controllers\Api;

use App\Models\Competency;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompetencyController extends Controller
{
    use MapsResponse;

    public function getAll()
    {
        $mainCompetencies = Competency::where('type', 'main')->get();
        $supportCompetencies = Competency::where('type', 'support')->get();
        $graduateCompetencies = Competency::where('type', 'graduate')->get();

        $mappedMainCompetencies = $this->mapCompetencies($mainCompetencies, 'main');
        $mappedSupportCompetencies = $this->mapCompetencies($supportCompetencies, 'support');
        $mappedGraduateCompetencies = $this->mapCompetencies($graduateCompetencies, 'graduate');

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Success'
            ],
            'data' => [
                'kompetensiUtama' => $mappedMainCompetencies,
                'kompetensiPendukung' => $mappedSupportCompetencies,
                'kompetensiLulusan' => $mappedGraduateCompetencies
            ]
        ]);
    }
}
