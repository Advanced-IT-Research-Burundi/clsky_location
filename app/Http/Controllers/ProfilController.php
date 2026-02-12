<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User; 

class ProfilController extends Controller
{
    // Afficher la page profil
    public function edit()
    {
        return view('profile.edit');
    }

    // Mettre à jour le profil
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $imagePath = $request->file('avatar')->store('profiles', 'public');
            $user->avatar = $imagePath;
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    // Supprimer le compte
    public function destroy()
    {
        $user = auth()->user();

        // Supprimer l'avatar s'il existe
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('login')
            ->with('success', 'Votre compte a été supprimé.');
    }
}
