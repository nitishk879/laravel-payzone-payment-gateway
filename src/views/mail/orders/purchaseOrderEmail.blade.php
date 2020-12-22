@component('mail::message')
    # A New order of ({{ $transaction->amount }}) has been received

    Price: {{ $transaction->amount ?? '0000' }}

    Status: {{ $transaction->status ?? '-' }}

    @component('mail::button', ['url' => config('app.app.url')])
        Bingo
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
