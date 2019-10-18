@component('mail::message')
# Invitation to join TCDD Metrics application

You have been invited to register for the TCDD Metrics application. Please click on the button below to register. 

You must register within 24 hours of receiving this email. If you do not register within 24 hours, you will have to send an email to <a href="mailto:paul.bovis@canada.ca">TCDD technical support</a> and request a new invite.

@component('mail::button', ['url' => $url])
Register
@endcomponent

Welcome!<br>
{{ config('app.admin') }}<br>
TCDD Support
@endcomponent
