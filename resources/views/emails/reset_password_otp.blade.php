@component('mail::message')
# Password Reset OTP

Hello,

Your One-Time Password (OTP) to reset your account password is:

@component('mail::panel')
**{{ $otp }}**
@endcomponent

This code will expire in 5 minutes.
If you did not request this, please ignore this email.

Thanks,
{{ config('app.name') }}
@endcomponent