@php
    $locale = app()->getLocale();
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('project.verify_email.title') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F6FFF0; font-family:Arial, sans-serif; color:#333;">

    <div style="max-width:600px; margin:40px auto; background-color:#ffffff; padding:32px; border-radius:8px;">
        {{-- タイトル --}}
        <h1 style="font-size:20px; margin-bottom:24px;">
            {{ __('project.verify_email.title') }}
        </h1>

        {{-- 本文 --}}
        <p style="font-size:16px; line-height:1.6;">
            {{ __('project.verify_email.intro') }}
        </p>

        {{-- ボタン --}}
        <div style="margin:24px 0;">
            <a href="{{ $actionUrl }}" style="display:inline-block; background-color:#90C226; color:#fff; text-decoration:none; padding:12px 24px; border-radius:4px; font-weight:bold;">
                {{ __('project.verify_email.button') }}
            </a>
        </div>

        {{-- 補足 --}}
        <p style="font-size:14px; color:#555;">
            {{ __('project.verify_email.outro') }}
        </p>

        {{-- 署名 --}}
        <p style="margin-top:32px;">
            {!! __('project.verify_email.signature') !!}
        </p>

        {{-- URL fallback --}}
        <hr style="margin:32px 0; border:none; border-top:1px solid #eee;">
        <p style="font-size:14px; color:#666;">
            {{ __('project.verify_email.fallback') }}<br>
            <a href="{{ $actionUrl }}" style="word-break:break-all;">{{ $actionUrl }}</a>
        </p>
    </div>

</body>
</html>