<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\FacilityVisit;

class FacilityVisitScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $facilityVisit;

    public function __construct(FacilityVisit $facilityVisit)
    {
        $this->facilityVisit = $facilityVisit;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'facility_visit_id' => $this->facilityVisit->id,
            'vendor_id' => $this->facilityVisit->vendor_id,
            'scheduled_date' => $this->facilityVisit->scheduled_date,
            'status' => $this->facilityVisit->status,
            'message' => 'Your facility visit has been scheduled for ' . $this->facilityVisit->scheduled_date->format('M d, Y'),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Facility Visit Scheduled')
            ->line('Your facility visit has been scheduled.')
            ->line('Scheduled Date: ' . $this->facilityVisit->scheduled_date->format('M d, Y'))
            ->line('An inspector will be assigned to your visit and will contact you to coordinate the details.')
            ->action('View Details', url('/vendor/facility-visits/' . $this->facilityVisit->id));
    }
} 