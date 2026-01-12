<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use App\Http\Requests\ReservationStoreRequest;
use App\Http\Requests\ReservationUpdateRequest;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['property', 'user'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('search'), function($query, $search) {
                $query->whereHas('property', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    public function create(Property $property = null)
    {
        $properties = Property::where('status', 'available')->get();
        return view('reservations.create', compact('properties', 'property'));
    }

    public function store(ReservationStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $property = Property::findOrFail($data['property_id']);
            
            // Vérifier la disponibilité
            if (!$this->isPropertyAvailable($property, $data['check_in'], $data['check_out'])) {
                return back()->with('error', 'Cette période n\'est pas disponible.')->withInput();
            }

            // Calculer le prix total
            $numberOfDays = date_diff(
                date_create($data['check_in']), 
                date_create($data['check_out'])
            )->days;

            $data['total_price'] = $property->price * $numberOfDays;
            $data['user_id'] = auth()->id();

            $reservation = Reservation::create($data);

            return redirect()->route('reservations.show', $reservation)
                           ->with('success', 'Réservation créée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la création de la réservation.')
                        ->withInput();
        }
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['property', 'user']);
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $properties = Property::where('status', 'available')
                            ->orWhere('id', $reservation->property_id)
                            ->get();
        return view('reservations.edit', compact('reservation', 'properties'));
    }

    public function update(ReservationUpdateRequest $request, Reservation $reservation)
    {
        try {
            $data = $request->validated();

            if ($data['check_in'] !== $reservation->check_in || 
                $data['check_out'] !== $reservation->check_out) {
                    
                if (!$this->isPropertyAvailable(
                    $reservation->property, 
                    $data['check_in'], 
                    $data['check_out'], 
                    $reservation->id
                )) {
                    return back()->with('error', 'Cette période n\'est pas disponible.');
                }

                // Recalculer le prix total si les dates changent
                $numberOfDays = date_diff(
                    date_create($data['check_in']), 
                    date_create($data['check_out'])
                )->days;

                $data['total_price'] = $reservation->property->price * $numberOfDays;
            }

            $reservation->update($data);

            return redirect()->route('reservations.show', $reservation)
                           ->with('success', 'Réservation mise à jour avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de la réservation.');
        }
    }

    public function destroy(Reservation $reservation)
    {
        try {
            $reservation->delete();
            return redirect()->route('reservations.index')
                           ->with('success', 'Réservation supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la réservation.');
        }
    }

    protected function isPropertyAvailable($property, $checkIn, $checkOut, $excludeReservationId = null)
    {
        $query = $property->reservations()
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                          ->where('check_out', '>=', $checkOut);
                    });
            })
            ->where('status', '!=', 'cancelled');

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->count() === 0;
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $reservation->update([
            'status' => $request->status
        ]);

        return redirect()->route('reservations.index')->with('success', 'Statut de la réservation mis à jour.');
    }
}
