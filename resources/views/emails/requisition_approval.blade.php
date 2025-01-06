<html>
    <body>
        <p>Dear {{ $userName }},</p>
        <p>The requisition with ID: {{ $requisitionCode }} is waiting for approval. <a href="{{ url('/requisitions/' . $requisitionId) }}">Click here to review</a></p>
    </body>
</html>
