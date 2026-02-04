<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Upload image (original uniquement) dans public/
     */
    public function handlePropertyImage(UploadedFile $image, int|string $propertyId): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $relativePath = "uploads/properties/{$propertyId}";
        $this->ensureDirectory($relativePath);

        $this->optimizeImage($image)
            ->save(public_path("{$relativePath}/{$filename}"));

        return "{$relativePath}/{$filename}";
    }

    /**
     * Version avancée (même résultat, transformations optionnelles)
     */
    public function handlePropertyImageAdvanced(UploadedFile $image, int|string $propertyId): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $relativePath = "uploads/properties/{$propertyId}";
        $this->ensureDirectory($relativePath);

        $this->manager
            ->read($image->getPathname())
            ->brightness(5)
            ->contrast(10)
            ->sharpen(15)
            ->encodeByExtension($image->getClientOriginalExtension(), 80)
            ->save(public_path("{$relativePath}/{$filename}"));

        return "{$relativePath}/{$filename}";
    }

    /**
     * Supprimer l’image depuis public/
     */
    public function deletePropertyImages(string $imagePath): bool
    {
        $fullPath = public_path($imagePath);

        return File::exists($fullPath)
            ? File::delete($fullPath)
            : false;
    }

    /**
     * Retourne l’URL publique
     */
    public function getImageUrl(?string $path): ?string
    {
        return $path ? asset($path) : null;
    }

    /**
     * Optimisation simple
     */
    public function optimizeImage(UploadedFile $image)
    {
        return $this->manager
            ->read($image->getPathname())
            ->encodeByExtension(
                $image->getClientOriginalExtension(),
                80
            );
    }

    /**
     * Validation image
     */
    public function isValidImage(UploadedFile $file): bool
    {
        return $file->isValid()
            && in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])
            && $file->getSize() <= 5 * 1024 * 1024;
    }

    /**
     * Dimensions de l’image
     */
    public function getImageDimensions(string $path): ?array
    {
        $fullPath = public_path($path);

        if (!File::exists($fullPath)) {
            return null;
        }

        $img = $this->manager->read($fullPath);

        return [
            'width'  => $img->width(),
            'height' => $img->height(),
        ];
    }

    /**
     * Crée le dossier si nécessaire
     */
    protected function ensureDirectory(string $relativePath): void
    {
        $fullPath = public_path($relativePath);

        if (!File::isDirectory($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }
    }
}
