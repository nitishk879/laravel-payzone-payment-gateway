@component('mail::message')
# Thank your for making a purchase from {{ config("app.name") }}. Please visit {{ config("app.url") }} for more purchase.

# Amount:
{!! $transaction->amount_ !!}

# Payment Status:
{{ $transaction->status }}

@component('mail::table')
    | Sr. | Particular | Details                             |
    | ----|:----------:| -----------------------------------:|
    | 1   | Amount     | {!! $transaction->amount_ !!}       |
    | 2   | Status     | {{ $transaction->status }}          |
    | 3   | Currency   | {{ $transaction->currency }}        |
    | 4   | Cross Ref. | {{ $transaction->cross_reference }} |
    | 5   | Date Time  | {{ $transaction->datetime_txt }}    |

@endcomponent

@component('mail::button', ['url' => config('app.url')])
Website
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
