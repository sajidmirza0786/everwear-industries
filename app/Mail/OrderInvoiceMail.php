<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf; // Ensure this is imported

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->relationLoaded('items.product') ? $order : $order->load('items.product');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // FIX: Use dot notation 'pdfs.order' to reference resources/views/pdfs/order.blade.php
        $pdf = Pdf::loadView('pdfs.order', ['order' => $this->order])
            ->setPaper('a4', 'portrait')
            ->output(); 

        return $this->subject('Your Invoice from ' . config('app.name'))
                    ->markdown('emails.orders.invoice')
                    ->attachData($pdf, 'invoice_' . $this->order->id . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}