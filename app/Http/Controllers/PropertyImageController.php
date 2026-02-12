<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyImageControllerStoreRequest;
use App\Http\Requests\PropertyImageControllerUpdateRequest;
use App\Models\PropertyImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyImageController extends Controller
{
    public function index(Request $request)
    {
        $propertyImages = PropertyImage::all();

        return view('propertyImage.index', compact('propertyImages'));
    }

    public function create(Request $request)
    {
        return view('propertyImage.create');
    }

    public function store(PropertyImageControllerStoreRequest $request)
    {
        $propertyImage = PropertyImage::create($request->validated());

        $request->session()->flash('propertyImage.id', $propertyImage->id);

        return redirect()->route('propertyImages.index');
    }

    public function show(Request $request, PropertyImage $propertyImage)
    {
        return view('propertyImage.show', compact('propertyImage'));
    }

    public function edit(Request $request, PropertyImage $propertyImage)
    {
        return view('propertyImage.edit', compact('propertyImage'));
    }

    public function update(PropertyImageControllerUpdateRequest $request, PropertyImage $propertyImage)
    {
        $propertyImage->update($request->validated());

        $request->session()->flash('propertyImage.id', $propertyImage->id);

        return redirect()->route('propertyImages.index');
    }

    public function destroy(Request $request, PropertyImage $propertyImage)
    {
        $propertyImage->delete();

        return redirect()->route('propertyImages.index');
    }
}
