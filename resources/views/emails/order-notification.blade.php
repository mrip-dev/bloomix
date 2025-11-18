@component('mail::message')

@if ($for === 'admin')
# New Order Received
@else
# Thank You! Your Order Has Been Placed
@endif

**Invoice:** {{ $sale->invoice_no }}  
**Customer:** {{ $sale->customer_name }}  
**Phone:** {{ $sale->customer_phone }}  
**Address:** {{ $sale->customer_address }}  
**Status:** {{ $sale->status }}

## Order Summary
Total Price: {{ $sale->total_price }}  
Discount: {{ $sale->discount_amount }}  
Final Amount: {{ $sale->receivable_amount }}

## Products
@foreach ($sale->saleDetails as $item)
- **{{ $item->product->name }}**  
  Quantity: {{ $item->quantity }}  
  Price: {{ $item->price }}  
  Total: {{ $item->total }}
@endforeach

@if ($for === 'customer')
---
### We will contact you soon regarding delivery.
@endif

@endcomponent
