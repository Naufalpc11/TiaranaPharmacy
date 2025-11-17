<?php

namespace App\Http\Controllers;

use App\Services\MedicationCatalog;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class MedicationImageController extends Controller
{
    protected MedicationCatalog $medicationCatalog;

    public function __construct(MedicationCatalog $medicationCatalog)
    {
        $this->medicationCatalog = $medicationCatalog;
    }

    public function __invoke(string $slug)
    {
        $medication = $this->medicationCatalog->findBySlug($slug);

        if (! $medication || empty($medication['dataset_image'])) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $imagePath = resource_path('images/dataset/'.$medication['dataset_image']);

        if (! is_file($imagePath)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $mimeType = File::mimeType($imagePath) ?: 'image/jpeg';

        return response()->file($imagePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
