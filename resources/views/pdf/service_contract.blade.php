<!-- resources/views/pdf/service_contract.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Contract PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
        }

        .big-title {
            font-size: 24px;
            font-weight: bold;
        }

        .breadcrumbs {
            margin-bottom: 20px;
        }

        .breadcrumbs ol {
            list-style: none;
            padding: 0;
        }

        .breadcrumbs ol li {
            display: inline;
            margin-right: 5px;
        }

        .breadcrumbs ol li::after {
            content: "/";
            margin-left: 5px;
        }

        .breadcrumbs ol li:last-child::after {
            content: "";
        }

        .box {
            padding: 20px;
            border: 1px solid #a9a9a9;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-wrap {
            width: 100%;
        }

        .table-1-el {
            width: 100%;
            border-collapse: collapse;
        }

        .table-1-el th, .table-1-el td {
            border: 1px solid #a9a9a9;
            padding: 8px;
            text-align: left;
        }

        .table-td-border-none {
            border: 1px solid #a9a9a9;
            text-align: left;
            border-collapse: collapse;
        }

        .table-td-border-none td,
        .table-td-border-none th {
            border: 1px solid #fafafa;
            text-align: left;
            padding: 5px;
        }

        .table-td-border-none tr:first-child td,
        .table-td-border-none tr:first-child th {
            border-top-color: #a9a9a9;
        }

        .table-td-border-none tr:last-child td,
        .table-td-border-none tr:last-child th {
            border-bottom-color: #a9a9a9;
        }

        .table-td-border-none tr td {
            border-right-color: #a9a9a9;
        }

        .table-td-border-none tr th {
            border-left-color: #a9a9a9;
        }

        .text-primary {
            color: #007bff;
        }

        .text-bold {
            font-weight: bold;
        }

        .font-medium {
            font-weight: 500;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-end {
            justify-content: flex-end;
        }

        .w-full {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .mb-14 {
            margin-bottom: 14px;
        }

        .mb-34 {
            margin-bottom: 34px;
        }

        .pb-17 {
            padding-bottom: 17px;
        }

        .ml-20 {
            margin-left: 20px;
        }
    </style>
</head>
<body>
<div>

    <div class="form-wrap">
        <div class="boxes">
            <h1 style="text-align: center">Service Contract</h1>

            <!-- Quotation Information -->
            <div class="box border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[2.6rem] pb-25">
                <table class="mb-10" style="width: 100%">
                    <tr>
                        <td><span class="text-primary text-bold">Contract:</span> {{ $contract->service_contract_number }}</td>
                        <td><span class="text-primary text-bold">Start Date:</span> {{ $contract->start_duration_date }}</td>
                        <td><span class="text-primary text-bold">End Date :</span> {{ $contract->end_duration_date }}</td>
                    </tr>
                </table>
            </div>

            <!-- Customer Information -->
            <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-34">
                <div class="font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                    <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">1.</span>
                    <span>Customer Info</span>
                </div>
                <div class="grid md:grid-cols-2 gap-[8rem]">
                    <table class="table-1-el w-full">
                        <tr>
                            <th>Status</th>
                            <td>{{ $contract->status_text }}</td>
                        </tr>
                        <tr>
                            <th>Customer ID</th>
                            <td>{{ $contract->customer->code ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $contract->customer->name ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>POC</th>
                            <td>{{ $contract->customer->poc_first_name ?? '--' }} {{ $contract->customer->poc_last_name ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>POC Contact</th>
                            <td>{{ $contract->customer->poc_phone ?? '--' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Vehicle Specifications -->
            <div class="">
                <div class="font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                    <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">2.</span>
                    <span>Vehicle Specifications</span>
                </div>
                @foreach ($vehicles as $vehicle)
                    <div class="grid grid-cols-2 gap-[8rem] border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-25 mb-34">
                        <table class="table-td-border-none w-full">
                            <tr>
                                <th>Vehicle Number</th>
                                <td>{{ $vehicle->license_plate }}</td>
                            </tr>
                            <tr>
                                <th>End User</th>
                                <td>{{ $vehicle->end_user }}</td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>{{ $vehicle->type }}</td>
                            </tr>
                            <tr>
                                <th>Chassis Salesperson / Contact No.</th>
                                <td>{{ $vehicle->contact_no }}</td>
                            </tr>
                            <tr>
                                <th>Vehicle Voltage</th>
                                <td>{{ $vehicle->vehicle_voltage }}</td>
                            </tr>
                            <tr>
                                <th>Chassis Delivery</th>
                                <td>{{ $vehicle->chassis_delivery }}</td>
                            </tr>
                            <tr>
                                <th>Chassis No.</th>
                                <td>{{ $vehicle->chassis_no }}</td>
                            </tr>
                            <tr>
                                <th>Refrigeration Serial Number</th>
                                <td>{{ $vehicle->refrigeration_serial_number }}</td>
                            </tr>
                            <tr>
                                <th>Vehicle Make</th>
                                <td>{{ $vehicle->vehicle_make }}</td>
                            </tr>
                            <tr>
                                <th>Other Info</th>
                                <td>{{ $vehicle->other_info }}</td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td>{{ $vehicle->model }}</td>
                            </tr>
                            <tr>
                                <th>Vehicle Class</th>
                                <td>{{ $vehicle->vehicle_class }}</td>
                            </tr>
                            <tr>
                                <th>Type of Plate</th>
                                <td>{{ $vehicle->type_of_plate }}</td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>

            <!-- PIC Info -->
            <div class="border-0 border-b-[1px] border-solid border-[#EBE9F1] mb-[5.6rem] pb-[5.6rem] mb-34">
                <div class="font-medium pb-17 mb-17 border-0 border-b-[1px] border-solid border-[#EBE9F1]">
                    <span class="w-24 h-24 inline-flex items-center justify-center border-[1px] rounded-[50%] mr-8 border-solid">3.</span>
                    <span>PIC Info</span>
                </div>
                <div class="grid grid-cols-2 gap-[8rem]">
                    <table class="table-1-el w-full">
                        <tr>
                            <th>PIC Name</th>
                            <td>{{ $contract->pic_first_name }} {{ $contract->pic_last_name }}</td>
                        </tr>
                        <tr>
                            <th>PIC Number</th>
                            <td>{{ $contract->pic_phone_number }}</td>
                        </tr>
                        <tr>
                            <th>PIC Email</th>
                            <td>{{ $contract->pic_email }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($contract->remark)
                <div class="mb-16 border-0 border-[1px] border-solid border-[#EBE9F1] p-24 py-15 rounded-[.5rem] mb-34">
                    <p class="text-primary text-bold-500 mb-10">Remark:</p>
                    {{ $contract->remark }}
                </div>
            @endif

            <!-- Remarks and Summary -->
            <div class="box border-0 mb-[5.6rem] pb-[5.6rem]">
                <div class="d-flex justify-content-between">
                    <div class="d-flex justify-content-end">
                        <table>
                            @if ($contract->value)
                                <tr>
                                    <td class="font-bold mb-14">Subtotal:</td>
                                    <td class="mb-14 pl-24 ml-20">${{ number_format($contract->value, 2) }}</td>
                                </tr>
                            @endif
                            @if ($contract->tax_amount)
                                <tr>
                                    <td class="font-bold mb-14">Discount:</td>
                                    <td class="mb-14 pl-24 ml-20">${{ number_format($contract->discount, 2) }}</td>
                                </tr>
                            @endif
                            @if ($contract->tax_amount)
                                <tr>
                                    <td class="font-bold mb-14">GST ({{ $contract->tax_rate }}%):</td>
                                    <td class="mb-14 pl-24 ml-20">${{ number_format($contract->tax_amount, 2) }}</td>
                                </tr>
                            @endif
                            @if ($contract->total)
                                <tr>
                                    <td class="font-bold mb-14">Total:</td>
                                    <td class="mb-14 pl-24 ml-20">${{ number_format($contract->total, 2) }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
