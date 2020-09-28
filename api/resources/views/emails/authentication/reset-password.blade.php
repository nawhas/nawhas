@component('mail::message')
# Reset Password

To reset your password, click the button below.
If you didn't make this request, feel free to ignore this email.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

Thanks,<br>
The Team at Nawhas.com
@endcomponent
