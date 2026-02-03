<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Service;
use App\Http\Requests\PropertyStoreRequest;
use App\Http\Requests\PropertyUpdateRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class PropertyController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('auth');
        $this->imageService = $imageService;
    }

    public function index()
    {
        $properties = Property::with(['images', 'user', 'services', 'details'])
            ->latest()
            ->paginate(12);

        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        return view('properties.create', [
            'property' => new Property()
        ]);
    }

    public function store(PropertyStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            $property = Property::create($data);

            // Services
            $property->services()->sync($request->services ?? []);

            // Détails
            $this->handlePropertyDetails($property, $request);

            // Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if (!$this->imageService->isValidImage($image)) continue;

                    $path = $this->imageService->handlePropertyImage($image, $property->id);

                    $property->images()->create([
                        'image_path' => $path,
                        'is_primary' => !$property->images()->exists()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('properties.index')
                ->with('success', 'Propriété créée avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return back()->with('error', 'Erreur lors de la création')->withInput();
        }
    }

    public function show(Property $property)
    {
        $property->load(['images', 'user', 'services', 'details']);
        return view('properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $property->load(['images', 'services', 'details']);
        return view('properties.edit', compact('property'));
    }

    public function update(PropertyUpdateRequest $request, Property $property)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            if ($property->title !== $data['title']) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            $property->update($data);

            // Services
            $property->services()->sync($request->services ?? []);

            // Détails
            $this->handlePropertyDetails($property, $request);

            // SUPPRESSION DES IMAGES EXISTANTES 
            if ($request->filled('removed_images')) {
                $images = PropertyImage::whereIn('id', $request->removed_images)->get();

                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            //AJOUT DES NOUVELLES IMAGES 
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if (!$this->imageService->isValidImage($image)) continue;

                    $path = $this->imageService->handlePropertyImage($image, $property->id);

                    $property->images()->create([
                        'image_path' => $path,
                        'is_primary' => !$property->images()->exists()
                    ]);
                }
            }

            // GARANTIR IMAGE PRINCIPALE
            if (!$property->images()->where('is_primary', true)->exists()) {
                $property->images()->first()?->update(['is_primary' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('properties.index')
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Property $property)
    {
        DB::beginTransaction();

        try {
            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $property->services()->detach();
            $property->details()->delete();
            $property->delete();

            DB::commit();

            return redirect()->route('properties.index')
                ->with('success', 'Propriété supprimée');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return back()->with('error', 'Erreur lors de la suppression');
        }
    }

    public function deleteImage(PropertyImage $image)
    {
        try {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();

            return response()->json([
                'success' => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    protected function handlePropertyDetails(Property $property, Request $request)
    {
        if (!$request->has('details')) return;

        $property->details()->delete();

        $details = [];

        foreach ($request->details as $detail) {
            if (empty($detail['title']) && empty($detail['value'])) continue;

            $details[] = [
                'title' => $detail['title'] ?? '',
                'value' => $detail['value'] ?? '',
                'description' => $detail['description'] ?? null,
                'user_id' => auth()->id(),
            ];
        }

        if ($details) {
            $property->details()->createMany($details);
        }
    }

    protected function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $i = 1;

        while (Property::where('slug', $slug)->exists()) {
            $slug = Str::slug($title) . '-' . $i++;
        }

        return $slug;
    }
}
