<?php

namespace App\Services;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['reservations']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Propriété',
            'Client',
            'Date d\'arrivée',
            'Date de départ',
            'Montant',
            'Statut'
        ];
    }

    public function map($reservation): array
    {
        return [
            $reservation->id,
            $reservation->property->title,
            $reservation->user->name,
            $reservation->check_in->format('d/m/Y'),
            $reservation->check_out->format('d/m/Y'),
            number_format($reservation->total_price, 2) . ' BIF',
            $reservation->status
        ];
    }

    public function title(): string
    {
        return 'Rapport ' . now()->format('d/m/Y');
    }
}
