<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport - {{ now()->format('d/m/Y') }}</title>
    <style>
        body { font-family: DejaVu Sans; }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { margin-bottom: 30px; }
        .stats-item { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'activité</h1>
        <p>Période : {{ $period['start'] }} - {{ $period['end'] }}</p>
    </div>

    <div class="stats">
        <h2>Statistiques Générales</h2>
        <div class="stats-item">
            <strong>Revenus totaux:</strong> {{ number_format($stats['total_revenue'], 2) }} USD
        </div>
        <div class="stats-item">
            <strong>Nombre de réservations:</strong> {{ $stats['total_reservations'] }}
        </div>
        <div class="stats-item">
            <strong>Taux d'occupation moyen:</strong> {{ number_format($stats['average_occupancy'], 1) }}%
        </div>
        <div class="stats-item">
            <strong>Nouveaux clients:</strong> {{ $stats['new_customers'] }}
        </div>
    </div>

    <h2>Détail des Réservations</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Propriété</th>
                <th>Client</th>
                <th>Période</th>
                <th>Montant</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->property->title }}</td>
                    <td>{{ $reservation->user->name }}</td>
                    <td>
                        {{ $reservation->check_in->format('d/m/Y') }} -
                        {{ $reservation->check_out->format('d/m/Y') }}
                    </td>
                    <td>{{ number_format($reservation->total_price, 2) }} USD</td>
                    <td>{{ $reservation->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
