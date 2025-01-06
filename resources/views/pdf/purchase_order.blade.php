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
            <th style="text-align: left; padding: 2px 25px 2px 0">PO Number</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['code'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Created date</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['created_at'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Vendor</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['vendor']['name'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Vendor address</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['vendor_address'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Vendor Phone</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['vendor_phone'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Reference No.</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['code'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Delivery address</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['delivery_address'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Status</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['status_text'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">EDD</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['edd'] }}</td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 2px 25px 2px 0">Remarks</th>
            <td style="text-align: left; padding: 2px 25px 2px 0">{{ $order['remark'] }}</td>
        </tr>
    </table>

    <div class="text-18 font-medium mb-17" style="margin-top: 50px">Item List</div>
    <hr style="border-bottom: 0.5px solid #EBE9F1"/>
    <div class="table-responsive">
        <table class="table no-borders" style="border-collapse: collapse; width: 100%">
            <thead>
            <tr>
                <th style="border: 0.5px solid #EBE9F1;">
                    Item ID
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Item Name
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Description
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Location
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Unit Price
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Quantity
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Discount
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    GST
                </th>
                <th style="border: 0.5px solid #EBE9F1;">
                    Total Price
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">{{ $order['code'] }} - {{ $item['id'] }}</td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">
                        {{ $item['product_name'] }}
                    </td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">{{ $item['description'] }}</td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">HWEEJAN SINGAPORE</td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">${{ $item['unit_price'] }}</td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">{{ $item['quantity'] }}</td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">{{ $item['discount'] ?? 0 }}</td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">{{ $item['gst']['value'] }}%</td>
                     <td style="border: 0.5px solid #EBE9F1; padding: 5px">{{ $item['total'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <span class="block border-0 border-t-[1px] border-solid border-[#EBE9F1] my-26"></span>
@stop
