<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Contract Invoice #{{ $projectContract->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        .no-borders td, .no-borders th {
            border: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .border th, .border td,
        th, td {
            padding: 5px;
            border: 1px solid black;
            text-align: left;
        }

        .header {
            text-align: center;
            font-size: 16px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .small {
            font-size: 10px;
        }

        .signature {
            height: 50px;
        }

        .text-center {
            text-align: center !important;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <table class="no-borders" style="border-collapse: collapse;">
        <tr>
            <td>
                <table class="no-borders">
                    <tr>
                        <td style="width: 170px; vertical-align: top;">
                            <img style="width: 170px;" src="{{ public_path('images/logo/logo-1.png') }}" alt="">
                        </td>
                        <td style="vertical-align: top;">
                            <p style="color: #515151;">
                                Head Office: 82 Joo Koon Circle, Jurong Ind Est., Singapore 629101. Tel: 6897 9339<br>
                                Fax: 6897 7337(Accs), 6863 4836(Sales), 6863 4632(Project)<br>
                                Co. Reg. No.: 199201209N GST Reg. No.: 19-9201209-N Website: http://www.hweejan.com.sg
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <h1 style="text-transform: uppercase; text-align: center">Project Contract Invoice</h1>
            </td>
        </tr>
    </table>

    <table class="no-borders" style="margin-left: -5px; margin-right: -5px; margin-bottom: 10px;">
        <tr>
            <td rowspan="2" style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Customer Name / Address
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <p style="display:block; min-height: 30px">
                                {{ $projectContract->customer->code }}<br>
                                {{ $projectContract->customer->name }}<br>
                                {{ $projectContract->customer->country->name }} {{ $projectContract->customer->address }} {{ $projectContract->customer->address_postal_code }}
                                <br>
                                Tel: ({{ $projectContract->customer->countryPhoneCode->phone_code }}
                                ) {{ $projectContract->customer->phone }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            SC No.
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->id }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Date
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->created_at->format('Y-m-d') }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Currency
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">SGD</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Term of payment
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->term_of_payment }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Customer reference no
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->customer_reference_no }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Vehicle No.
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->vehicles->pluck('license_plate')->implode(', ') }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Vehicle Mileage
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->vehicle?->warranty_mileage }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Running Hour
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block"></span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Confirmed By
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->project_order?->confirmed?->name }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="border">
                    <tr>
                        <th class="text-center">
                            Projectd By
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span style="min-height: 17px; display: block">{{ $projectContract->project_order?->technician?->name }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="no-borders" style="border: 1px solid #111">
        <thead>
        <tr>
            <th style="border-bottom: 1px solid #111">S/N</th>
            <th style="border-bottom: 1px solid #111">Item</th>
            <th style="border-bottom: 1px solid #111">Description</th>
            <th style="border-bottom: 1px solid #111">UoM</th>
            <th style="border-bottom: 1px solid #111">Quantity</th>
            <th style="border-bottom: 1px solid #111">Tax code</th>
            <th style="border-bottom: 1px solid #111">Tax amount</th>
            <th style="border-bottom: 1px solid #111">Discount</th>
            <th style="border-bottom: 1px solid #111">Total</th>
        </tr>
        </thead>
        <tbody>
        @php($index = 1)
        @foreach($projectContract->pricing as $price)
            <tr>
                <td>
                    {{ $index++ }}
                </td>
                <td>
                    {{ $price?->storageItem?->product?->sku }} | {{ $price?->storageItem?->product?->name }}
                </td>
                <td>
                    {{ $price?->storageItem?->product?->description }}
                </td>
                <td>
                    {{ $price?->storageItem?->product?->uom?->code }}
                </td>
                <td>
                    {{ $price?->quantity }}
                </td>
                <td>
                    {{ $price?->tax_code }}
                </td>
                <td>
                    {{ $price?->tax_amount }}
                </td>
                <td>
                    {{ $price?->discount }}
                </td>
                <td>
                    {{ $price?->total_amount }}
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td style="border-top: 1px solid #111111" colspan="9">
                {{ $footer }}
            </td>
        </tr>
        </tfoot>
    </table>

    <div class="section-title">Enhanced warranty provided:</div>
    <ul class="small">
        <li>Two years for refrigeration system for manufacturing defects, including 4 times servicing.</li>
        <li>One year for insulated box.</li>
        <li>Refer to enclosed Warranty Maintenance Schedule for servicing appointments.</li>
    </ul>

    <hr style="margin-bottom: 30px; border-color: #8e8e8e">

    <div class="footer">
        <table class="no-borders">
            <tr>
                <td class="small" style="border: 1px dashed; width: 350px">
                    *Received the above mentioned goods and projects in the good order & condition. <br>
                    *Goods sold are not returnable
                </td>
                <td style="text-align: center; vertical-align: top">
                    <span style="display: block; height: 20px"></span>
                    <hr>
                    <p><span style="text-transform: uppercase">Name / I.C. No. / Company Stamp & Signature</span><br>Goods
                        Received in Good Order & Condition</p>
                </td>
                <td style="text-align: center;  vertical-align: top">
                    <span style="display: block; height: 20px;"></span>
                    <hr>
                    <p><span style="text-transform: uppercase">Hwee Jan (S) PTE LTD</span></p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
