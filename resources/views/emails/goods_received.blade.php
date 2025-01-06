<html>
    <body>
        <p>Dear {{ $userName }},</p>
        <p>The items for urgent requisition <b>{{ $requisitionCode }}</b> has arrived. <a href="{{ url('/requisitions/' . $requisitionId) }}">Click here to see the detail</a></p>
    </body>
</html>
