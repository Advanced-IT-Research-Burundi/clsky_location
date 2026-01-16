<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PropertiesRevenueExport;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'all_time');
        $startDate = $this->getStartDate($period);

        $query = Property::with([
            'reservations.payments',
            'reservations.user',
            'images'
        ]);

        if ($startDate) {
            $query->whereHas('reservations', function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            });
        }

        $properties = $query->get()
            ->sortByDesc(function($property) {
                return $property->reservations->sum('total_paid');
            });

        // Calcul des statistiques
        $stats = $this->calculateStats($properties, $startDate);

        // Données pour le graphique
        $chartData = $this->getChartData($startDate);

        $tousTotalRevenue = Property::all()->flatMap(function ($property) {
            return $property->reservations->flatMap(function ($reservation) {
                return $reservation->payments;
            });
        })->sum('amount');

        return view('admin.dashboard', array_merge($stats, [
            'properties' => $properties,
            'chartData' => $chartData,
            'selectedPeriod' => $period,
            'tousTotalRevenue' => $tousTotalRevenue,
        ]));
    }

    private function getStartDate($period)
    {
        return match($period) {
            'today' => Carbon::today(),
            'this_week' => Carbon::now()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            'last_month' => Carbon::now()->subMonth()->startOfMonth(),
            'this_year' => Carbon::now()->startOfYear(),
            'last_year' => Carbon::now()->subYear()->startOfYear(),
            default => null
        };
    }

    private function calculateStats($properties, $startDate)
    {
        $reservations = $properties->flatMap->reservations
            ->when($startDate, function($reservations) use ($startDate) {
                return $reservations->where('created_at', '>=', $startDate);
            });

        return [
            'totalAmount' => $reservations->sum('total_paid'),
            'pendingAmount' => $reservations->where('status', 'pending')->sum('total_paid'),
            'completedAmount' => $reservations->where('status', 'completed')->sum('total_paid'),
            'refundedAmount' => $reservations->where('payment_status', 'refunded')->sum('total_paid'),
            'totalReservations' => $reservations->count(),
            'averagePrice' => $reservations->avg('total_price') ?? 0,
            'averageOccupancy' => $this->calculateAverageOccupancy($reservations),
            'revenueGrowth' => $this->calculateRevenueGrowth($reservations),
            'topProperties' => $this->getTopProperties($properties, 5),
            'recentReservations' => $this->getRecentReservations($reservations, 5)
        ];
    }

    private function getChartData($startDate)
    {
        $query = Reservation::with('payments')
            ->when($startDate, function($q) use ($startDate) {
                return $q->where('created_at', '>=', $startDate);
            });

        // Données mensuelles
        $monthlyData = $query->get()
            ->groupBy(function($reservation) {
                return $reservation->created_at->format('Y-m');
            })
            ->map(function($reservations) {
                return [
                    'revenue' => $reservations->sum('total_paid'),
                    'count' => $reservations->count(),
                    'average' => $reservations->avg('total_paid')
                ];
            });

        $labels = $monthlyData->keys()->map(function($month) {
            return Carbon::createFromFormat('Y-m', $month)->format('M Y');
        });

        return [
            'labels' => $labels->values(),
            'datasets' => [
                [
                    'label' => 'Revenus',
                    'data' => $monthlyData->pluck('revenue')->values(),
                    'borderColor' => '#0d6efd',
                    'backgroundColor' => 'rgba(13, 110, 253, 0.1)'
                ],
                [
                    'label' => 'Moyenne',
                    'data' => $monthlyData->pluck('average')->values(),
                    'borderColor' => '#198754',
                    'backgroundColor' => 'rgba(25, 135, 84, 0.1)'
                ]
            ]
        ];
    }

    private function calculateAverageOccupancy($reservations)
    {
        if ($reservations->isEmpty()) {
            return 0;
        }

        return $reservations->sum('guests') / $reservations->count();
    }

    private function calculateRevenueGrowth($reservations)
    {
        $currentMonth = $reservations
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('total_paid');

        $lastMonth = $reservations
            ->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
            ->where('created_at', '<', Carbon::now()->startOfMonth())
            ->sum('total_paid');

        if ($lastMonth == 0) {
            return 100;
        }

        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    private function getTopProperties($properties, $limit)
    {
        return $properties
            ->sortByDesc(function($property) {
                return $property->reservations->sum('total_paid');
            })
            ->take($limit)
            ->map(function($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'revenue' => $property->reservations->sum('total_paid'),
                    'reservations_count' => $property->reservations->count(),
                    'image' => $property->images->first()?->image_path
                ];
            });
    }

    private function getRecentReservations($reservations, $limit)
    {
        return $reservations
            ->sortByDesc('created_at')
            ->take($limit)
            ->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'property_title' => $reservation->property->title,
                    'guest_name' => $reservation->user->name,
                    'amount' => $reservation->total_paid,
                    'status' => $reservation->status,
                    'created_at' => $reservation->created_at->format('d/m/Y')
                ];
            });
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'all_time');
        $startDate = $this->getStartDate($period);

        $properties = Property::with([
            'reservations.payments',
            'reservations.user'
        ])
        ->when($startDate, function($query) use ($startDate) {
            $query->whereHas('reservations', function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            });
        })
        ->get();

        // Générer le rapport Excel ou CSV
        return Excel::download(
            new PropertiesRevenueExport($properties),
            'revenues-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
