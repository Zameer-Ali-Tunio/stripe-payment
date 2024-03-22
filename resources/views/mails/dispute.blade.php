<!DOCTYPE html>
<html>

<head>
    <title>Dispute</title>
</head>

<body>
    <h1>Dispute</h1>
    @if($flag)
    <p>Dear {{ $dispute->invoice->user->name }},</p>
    @endif
    <p>Details:</p>
    <ul>
        <li>Amount: ${{ $dispute->invoice->amount }}</li>
        <li>Transaction ID: {{ $dispute->invoice->transaction_id }}</li>
        @if($flag)
        <p>status: {{ $dispute->status }},</p>
        @endif
    </ul>
</body>

</html>
