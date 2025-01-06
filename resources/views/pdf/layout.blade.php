<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-size: 10px;
            position: relative;
            min-height: 95vh;
            text-transform: uppercase;
            font-family: "Montserrat", sans-serif;
        }

        td {
            padding: 0px 10px 0px 0;
            min-width: 70px;
        }

        .desc-term {
            padding-right: 0px;
            padding-left: 0px;
            font-size: 10px;
        }

        .desc-term p {
            margin-left: 30px;
        }

        .desc-term p > span {
            margin-left: -50px;
            padding-right: 40px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
@include('pdf.header')
@yield('content')
</body>
<script type="text/php">
    if (isset($pdf)) {
        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $size = 10;
        $font = $fontMetrics->getFont("Montserrat");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</html>
