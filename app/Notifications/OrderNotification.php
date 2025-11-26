<?php

namespace App\Notifications;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderNotification extends Notification
{
    use Queueable;

    public $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Order Confirmation - Invoice ' . $this->sale->invoice_no)
            ->greeting('Hello ' . $this->sale->customer_name)
            ->line('Thank you for your order.')
            ->line('Invoice No: ' . $this->sale->invoice_no)
            ->line('Total Amount: ' . $this->sale->total_price)
            ->salutation('Regards, ' . config('app.name'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'invoice_no' => $this->sale->invoice_no,
            'amount'     => $this->sale->total_price,
            'sale_id'    => $this->sale->id,
        ];
    }
}
