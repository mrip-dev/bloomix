<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;

class OrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;
    public $for; // admin or customer

    public function __construct(Sale $sale, $for = 'admin')
    {
        $this->sale = $sale;
        $this->for = $for;
    }

    public function build()
    {
        $subject = $this->for === 'admin'
            ? 'New Order Received - Invoice ' . $this->sale->invoice_no
            : 'Your Order Confirmation - Invoice ' . $this->sale->invoice_no;

        return $this->subject($subject)
            ->markdown('emails.order-notification');
    }
}
