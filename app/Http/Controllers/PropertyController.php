<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyDetail;
use App\Models\Service;
use App\Http\Requests\PropertyStoreRequest;
use App\Http\Requests\PropertyUpdateRequest;
use App\Services\AvailabilityService;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PropertyController extends Controller
{
    protected $imageSizes = [
        'thumb' => [150, 150],
        'medium' => [400, 300],
        'large' => [800, 600]
    ];

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth');
    }

    public function index()
    {
        $properties = Property::query()
            ->with(['images', 'user', 'services', 'details'])
            ->withCount('reservations')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when(request('type'), fn($q, $type) => $q->where('type', $type))
            ->when(request('status'), fn($q, $status) => $q->where('status', $status))
            ->when(request('min_price'), fn($q, $price) => $q->where('price', '>=', $price))
            ->when(request('max_price'), fn($q, $price) => $q->where('price', '<=', $price))
            ->when(request('bedrooms'), fn($q, $bedrooms) => $q->where('bedrooms', '>=', $bedrooms))
            ->when(request('bathrooms'), fn($q, $bathrooms) => $q->where('bathrooms', '>=', $bathrooms))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        $property = new Property();
        return view('properties.create', compact('property'));
    }

    public function store(PropertyStoreRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->validated();
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            $property = Property::create($data);

            // Synchroniser les services
            if ($request->filled('services')) {
                $property->services()->sync($request->services);
            }

            // Gérer les détails de la propriété
            $this->handlePropertyDetails($property, $request);

            // Gérer les images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if (!$this->imageService->isValidImage($image)) {
                        continue;
                    }

                    $imagePath = $this->imageService->handlePropertyImage($image, $property->id);
                    $property->images()->create([
                        'image_path' => $imagePath,
                        'is_primary' => $property->images()->count() === 0
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('properties.index')
                ->with('success', 'Propriété créée avec succès');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur création propriété: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création')->withInput();
        }
    }

    public function show(Property $property)
    {
        $property->load(['images', 'user', 'services', 'reservations.user', 'details']);
        return view('properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $property->load(['services', 'details']);
        return view('properties.edit', compact('property'));
    }

    public function update(PropertyUpdateRequest $request, Property $property)
    {
        try {
            \DB::beginTransaction();

            $data = $request->validated();

            if ($property->title !== $data['title']) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            $property->update($data);

            $property->services()->sync($request->services ?? []);

            $this->handlePropertyDetails($property, $request);

            if ($request->hasFile('images')) {
                $images = $request->file('images');

                if (!is_array($images)) {
                    $images = [$images];
                }

                foreach ($images as $image) {
                    if (!$image->isValid()) {
                        continue;
                    }

                    if (!$this->imageService->isValidImage($image)) {
                        continue;
                    }

                    $imagePath = $this->imageService->handlePropertyImage($image, $property->id);

                    $property->images()->create([
                        'image_path' => $imagePath,
                        'is_primary' => $property->images()->count() === 0
                    ]);
                }
            }

            \DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Propriété mise à jour avec succès',
                    'redirect' => route('properties.index')
                ]);
            }

            return redirect()->route('properties.index')
                ->with('success', 'Propriété mise à jour avec succès');
        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Erreur mise à jour propriété: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Property $property)
    {
        try {
            \DB::beginTransaction();

            foreach ($property->images as $image) {
                $this->deletePropertyImage($image);
            }

            $property->services()->detach();
            $property->details()->delete();
            $property->delete();

            \DB::commit();

            return redirect()->route('properties.index')
                ->with('success', 'Propriété supprimée');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression');
        }
    }
    protected function handlePropertyDetails(Property $property, Request $request)
    {
        // Suppression des anciennes caractéristiques
        $property->details()->delete();

        if (!$request->filled('details')) {
            return;
        }

        $details = [];

        foreach ($request->details as $detail) {
            if (
                empty($detail['title']) &&
                empty($detail['value']) &&
                empty($detail['description'])
            ) {
                continue;
            }

            $details[] = [
                'title' => $detail['title'] ?? '',
                'value' => $detail['value'] ?? '',
                'description' => $detail['description'] ?? null,
                'user_id' => auth()->id(),
            ];
        }

        if (!empty($details)) {
            $property->details()->createMany($details);
        }
    }


    protected function generateSKU(Property $property)
    {
        $prefix = strtoupper(substr($property->type ?? 'PROP', 0, 3));
        $id = str_pad($property->id, 5, '0', STR_PAD_LEFT);
        $random = strtoupper(Str::random(3));

        return "{$prefix}.{$id}{$random}";
    }

    protected function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = 1;

        while (Property::where('slug', $slug)->exists()) {
            $slug = Str::slug($title) . '-' . $count++;
        }

        return $slug;
    }

    protected function handleImageUpload($property, array $images)
    {
        foreach ($images as $image) {
            if (!$image->isValid()) continue;

            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $basePath = 'properties/' . $property->id;

            foreach ($this->imageSizes as $size => $dim) {
                $resized = Image::make($image)
                    ->fit($dim[0], $dim[1], fn($c) => $c->aspectRatio())
                    ->encode(null, 80);

                Storage::disk('public')->put("$basePath/$size/$filename", $resized);
            }

            $original = $image->storeAs("$basePath/original", $filename, 'public');

            $property->images()->create([
                'image_path' => $original,
                'is_primary' => $property->images()->count() === 0
            ]);
        }
    }
    public function deleteImage(PropertyImage $image)
    {
        try {
            $image = PropertyImage::findOrFail($image->id);
            $image->delete();
            // $this->deletePropertyImage($image);

            return response()->json([
                'success' => true,
                'message' => 'Image supprimée avec succès'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'.$e->getMessage()
            ], 500);
        }
    }
}
