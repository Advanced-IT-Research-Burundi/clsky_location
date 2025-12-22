<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
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
            ->with(['images', 'user', 'services'])
            ->withCount('reservations')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when(request('type'), fn ($q, $type) => $q->where('type', $type))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(request('min_price'), fn ($q, $price) => $q->where('price', '>=', $price))
            ->when(request('max_price'), fn ($q, $price) => $q->where('price', '<=', $price))
            ->when(request('bedrooms'), fn ($q, $bedrooms) => $q->where('bedrooms', '>=', $bedrooms))
            ->when(request('bathrooms'), fn ($q, $bathrooms) => $q->where('bathrooms', '>=', $bathrooms))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        $property = new Property();
        $services = Service::all();

        return view('properties.create', compact('property', 'services'));
    }

    public function store(PropertyStoreRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->validated();
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            $property = Property::create($data);

            if ($request->filled('services')) {
                $property->services()->sync($request->services);
            }

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
            return back()->with('error', 'Erreur lors de la création')->withInput();
        }
    }

    public function show(Property $property)
    {
        $property->load(['images', 'user', 'services', 'reservations.user']);
        return view('properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $services = Service::all();
        $property->load('services');

        return view('properties.edit', compact('property', 'services'));
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

            if ($request->hasFile('images')) {
                $this->handleImageUpload($property, $request->file('images'));
            }

            \DB::commit();

            return redirect()->route('properties.index')
                ->with('success', 'Propriété mise à jour avec succès');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour')->withInput();
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
            $property->delete();

            \DB::commit();

            return redirect()->route('properties.index')
                ->with('success', 'Propriété supprimée');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression');
        }
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
                    ->fit($dim[0], $dim[1], fn ($c) => $c->aspectRatio())
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

    protected function deletePropertyImage(PropertyImage $image)
    {
        $basePath = 'properties/' . $image->property_id;
        foreach (['thumb', 'medium', 'large', 'original'] as $size) {
            Storage::disk('public')->delete("$basePath/$size/" . basename($image->image_path));
        }
        $image->delete();
    }
}
