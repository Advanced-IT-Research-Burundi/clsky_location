<?php

namespace App\Services;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservationRequestNotification extends Notification
{
    public $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle demande de réservation')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Une nouvelle demande de réservation a été reçue pour votre propriété.')
            ->line('Propriété : ' . $this->reservation->property->title)
            ->line('Client : ' . $this->reservation->user->name)
            ->line('Dates : du ' . $this->reservation->check_in->format('d/m/Y') .
                  ' au ' . $this->reservation->check_out->format('d/m/Y'))
            ->line('Nombre d\'invités : ' . $this->reservation->guests)
            ->line('Montant total : ' . number_format($this->reservation->total_price, 2) . ' BIF')
            ->action('Voir la réservation', route('reservations.show', $this->reservation))
            ->line('Merci d\'utiliser notre plateforme !');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'user_id' => Auth::user()->id,
            'reservation_id' => $this->reservation->id,
            'message' => 'Nouvelle demande de réservation pour ' . $this->reservation->property->title,
            'amount' => $this->reservation->total_price
        ];
    }
}
