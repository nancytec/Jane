@component('mail::message')
# Hi, {{ $user->firstname  }}
<p>Your {{$settings->app_name}} account was successfully created! 🎉🙏</p>
<p>Kindly verify your email address to ensure we have the working
    email, we need this to activate your account.</p>


@component('mail::button', ['url' => getenv('APP_URL').'/verify-email/'.$user->verification_token])
    Verify Email
@endcomponent

Thanks,<br>
{{$settings->app_name}}
@endcomponent
