<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Services\ReservationRequestNotification as ServicesReservationRequestNotification;
use Carbon\Carbon;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with('images')
            ->where('status', 'available')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($q, $type) {
                $q->where('type', $type);
            })
            ->when($request->min_price, function ($q, $price) {
                $q->where('price', '>=', $price);
            })
            ->when($request->max_price, function ($q, $price) {
                $q->where('price', '<=', $price);
            })
            ->when($request->bedrooms, function ($q, $bedrooms) {
                $q->where('bedrooms', '>=', $bedrooms);
            });

        $properties = $query
            ->latest()
            ->paginate(6)
            ->withQueryString();   

        return view('client.properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        // $property->load(['images', 'amenities']);
        $property->load(['images']);

        // Vérifier les disponibilités pour les 3 prochains mois
        $availableDates = $this->getAvailableDates($property, now(), now()->addMonths(3));

        return view('client.properties.show', compact('property', 'availableDates'));
    }

    public function reserve(Request $request, Property $property)
    {
        try {
            $request->validate([
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ], [
                'check_in.required' => 'The check-in date is required.',
                'check_in.date' => 'The check-in date must be a valid date.',
                'check_in.after' => 'The check-in date must be a date after today.',
                'check_out.required' => 'The check-out date is required.',
                'check_out.date' => 'The check-out date must be a valid date.',
                'check_out.after' => 'The check-out date must be after the check-in date.',
                'guests.required' => 'The number of guests is required.',
                'guests.integer' => 'The number of guests must be an integer.',
                'guests.min' => 'The number of guests must be at least 1.',
                'notes.string' => 'The notes must be a string.',
            ]);



            // Vérifier la disponibilité pour ces dates
            if (!$this->isAvailable($property, $request->check_in, $request->check_out)) {
                return back()->with('error', 'Ces dates ne sont pas disponibles.');
            }
            // Calculer le prix total
            $numberOfDays = Carbon::parse($request->check_in)
                ->diffInDays(Carbon::parse($request->check_out));
            $totalPrice = $property->price * $numberOfDays;

            // Créer la réservation
            $reservation = Reservation::create([
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'guests' => $request->guests,
                'total_price' => $totalPrice,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            // Notifier le propriétaire
            // $property->user->notify(new ServicesReservationRequestNotification($reservation));

            return redirect()->route('client.reservations.show', $reservation)
                ->with('success', 'Votre demande de réservation a été envoyée.');
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
            return back()->with('error', 'Une erreur s\'est produite lors de la création de la reservation.');
        }
    }

    protected function getAvailableDates($property, $startDate, $endDate)
    {
        $reservations = $property->reservations()
            ->where('status', '!=', 'cancelled')
            ->where('check_out', '>=', $startDate)
            ->where('check_in', '<=', $endDate)
            ->get(['check_in', 'check_out']);

        $dates = collect();
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $isAvailable = true;
            foreach ($reservations as $reservation) {
                if ($current->between($reservation->check_in, $reservation->check_out)) {
                    $isAvailable = false;
                    break;
                }
            }
            if ($isAvailable) {
                $dates->push($current->format('Y-m-d'));
            }
            $current->addDay();
        }

        return $dates;
    }

    protected function isAvailable($property, $checkIn, $checkOut)
    {
        return !$property->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();
    }
}
