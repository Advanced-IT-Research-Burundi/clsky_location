<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

public function update(Request $request)
{
    $user = auth()->user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:8|confirmed',
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect()->route('profile.edit')
        ->with('success', 'Profil mis à jour avec succès.');
}

public function destroy()
{
    $user = auth()->user();
    auth()->logout();
    $user->delete();

    return redirect('/')->with('success', 'Compte supprimé.');
}

