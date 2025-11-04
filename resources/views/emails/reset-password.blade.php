@php
    $locale = app()->getLocale();
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('project.reset_password.title') }}</title>
</head>
<body style="background-color:#F6FFF0; font-family:Arial, sans-serif; color:#333;">
    <div style="max-width:600px; margin:40px auto; background-color:#fff; padding:32px; border-radius:8px;">
        <h1>{{ __('project.reset_password.title') }}</h1>
        <p>{{ __('project.reset_password.intro') }}</p>
        <div style="margin:24px 0;">
            <a href="{{ $actionUrl }}" style="background-color:#90C226; color:#fff; padding:12px 24px; border-radius:4px; text-decoration:none;">
                {{ __('project.reset_password.button') }}
            </a>
        </div>
        <p>{{ __('project.reset_password.outro') }}</p>
        <hr>
        <p>{{ __('project.reset_password.fallback') }}<br>
            <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
        </p>
    </div>
</body>
</html>