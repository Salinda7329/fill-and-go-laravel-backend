<!DOCTYPE html>
<html>
<body>
    <p>Dear Customer,</p>
    <p>Your top-up request for <strong>Rs. {{ number_format($topup->amount, 2) }}</strong>
        on {{ $topup->created_at->format('Y-m-d H:i') }} has been <strong>{{ $status }}</strong>.
    </p>
    @if($status == 'approved')
        <p>Your <strong>new account balance</strong> is <span style="color: #28a745;"><strong>Rs. {{ number_format($balance, 2) }}</strong></span>.</p>
    @elseif($status == 'rejected')
        <p>If you have questions, please contact support. Your current balance is <strong>Rs. {{ number_format($balance, 2) }}</strong>.</p>
    @endif
    <p>Thank you,<br>Fill&Go Team</p>
</body>
</html>
