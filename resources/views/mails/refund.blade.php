<!DOCTYPE html>
<html>

<head>
    <title>Refund</title>
</head>

<body>
    <h1>Refund</h1>
    @if($flag)
    <p>Dear {{ $refund->invoice->user->name }},</p>
    @endif
    <p>Details:</p>
    <ul>
        <li>Amount: ${{ $refund->invoice->amount }}</li>
        <li>Transaction ID: {{ $refund->invoice->transaction_id }}</li>
        @if($flag)
        <p>status: {{ $refund->status }},</p>
        @endif
    </ul>
</body>

</html>
