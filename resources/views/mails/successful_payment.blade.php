<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
</head>
<body>
    <h1>Payment Successful</h1>
    <p>Dear {{ $invoice->user->name }},</p>
    <p>We are pleased to inform you that your payment has been successfully processed.</p>
    <p>Details:</p>
    <ul>
        <li>Amount: ${{ $invoice->amount }}</li>
        <li>Transaction ID: {{ $invoice->transaction_id }}</li>
        <!-- Add more details as needed -->
    </ul>
    <p>Thank you for your payment.</p>
</body>
</html>
