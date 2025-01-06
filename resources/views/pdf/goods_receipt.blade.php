@extends('pdf.layout')
@section('content')
    <style>
        table {
            font-size: 10px;
        }

        th {
            white-space: nowrap;
        }
    </style>
    <hr style="border-bottom: 0.5px solid #EBE9F1"/>
    <table class="table-1-el w-full" style="margin-top: 30px">
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">GRN number</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $goodsReceipt['code'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Date</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $goodsReceipt['created_at'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Supplier</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $goodsReceipt['order']['vendor']['name'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Invoice Number</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $goodsReceipt['order']['code'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Requester</th>
            {{-- <td style="text-align: left; padding: 2px 25px 2px 0">{{ implode(', ', array_map(fn($item) => $item['name'], json_decode($goodsReceipt['requester_name'], true) ?: [])) }}</td> --}}
        </tr>
    </table>

    <div class="text-18 font-medium mb-17" style="margin-top: 50px">Item List</div>
    <hr style="border-bottom: 0.5px solid #EBE9F1"/>
    <div class="table-responsive">
        <table class="table no-borders" style="border-collapse: collapse; width: 100%">
            <thead>
            <tr>
                <th style="border: 0.5px solid #EBE9F1;">
                    Item Category
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Item ID
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Item Name
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Quantity
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Receive Quantity
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($goodsReceipt['items'] as $item)
                <tr>
                    <td style="border: 0.5px solid #EBE9F1; padding: 5px">
                        {{ $item['product']['category']['prefix'] }}
                    </td>
                    <td style="border: 0.5px solid #EBE9F1; padding: 5px">
                        {{ $item['product']['sku'] }}
                    </td>
                    <td style="border: 0.5px solid #EBE9F1; padding: 5px">
                        {{ $item['product']['name'] }}
                    </td>
                    <td style="border: 0.5px solid #EBE9F1; padding: 5px">
                        {{ $item['quantity'] }}
                    </td>
                    <td style="border: 0.5px solid #EBE9F1; padding: 5px">
                        {{ $item['receive_quantity'] }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
@stop
