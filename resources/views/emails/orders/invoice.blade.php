@component('mail::message')
# Invoice for Order #{{ $order->uuid }}

**Hi {{ $order->name }},**  
Thank you for your order. Below are your order details:

@component('mail::panel')
**Order ID:** {{ $order->id }}  
**Date:** {{ $order->created_at->format('d M, Y') }}  
**Status:** {{ ucfirst($order->status) }}
@endcomponent

@component('mail::table')
| Product       | Quantity | Price    | Subtotal  |
|---------------|----------|----------|-----------|
@foreach ($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ₹{{ number_format($item->price, 2) }} | ₹{{ number_format($item->price * $item->quantity, 2) }} |
@endforeach
@endcomponent

**Shipping:** ₹{{ number_format($order->shipping_charge, 2) }}  
**Total:** ₹{{ number_format($order->total + $order->shipping_charge, 2) }}

@component('mail::button', ['url' => route('order.confirmation', $order)])
View Order
@endcomponent

If you have any questions, feel free to reach out.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
