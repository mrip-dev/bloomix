@extends('pdf.layouts.master2')

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Invoice')</th>
                <th>@lang('Date')</th>
                <th>@lang('Supplier')</th>
                <th>@lang('Warehouse')</th>
                <th>@lang('Driver Name') | @lang('Contact')</th>
                <th>@lang('Vehicle No') | @lang('Fare')</th>
                <th>@lang('Payable')</th>
                <th>@lang('Due')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td> {{ $loop->iteration }} </td>
                    <td> {{ $purchase->invoice_no }} </td>
                    <td> {{ showDateTime($purchase->purchase_date, 'm/d/Y') }} </td>
                    <td> {{ $purchase->supplier?->name }} </td>
                    <td> {{ $purchase->warehouse->name }} </td>
                    <td>
                        <span class="text--success fw-bold"> {{ $purchase->driver_name }}</span>
                        <br>
                        +{{ $purchase->driver_contact }}
                    </td>
                    <td>
                        {{ $purchase->vehicle_number }}
                        <br>
                        {{ $purchase->fare }}
                    </td>
                    </td>
                    <td> {{ showAmount($purchase->payable_amount) }} </td>
                    <td>
                        @if ($purchase->due_amount < 0)
                            -
                        @endif
                        {{ showAmount(abs($purchase->due_amount)) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
