<?php declare(strict_types=1);

return [
    'not_authorized' => 'ログインしていません。',
    'auth_failed' => 'ログインに失敗しました。IDまたはパスワードをご確認ください。',
    'auth_too_many_attempts' => 'ログイン試行回数が上限に達しました。:seconds 秒後に再試行してください。',

    'rate_limit_error' => 'リクエストが制限されました。しばらくしてから再試行してください。',
    'send_email_error' => 'メール送信に失敗しました。時間を置いて再度お試しください。',
    'validation_error' => 'バリデーションエラー',
    'unexpected_error' => '予期しないエラーが発生しました。',

    'email_invalid_verification_parameters' => 'このメールアドレス確認トークンは無効です。',
    'email_not_verified' => 'メールアドレスを確認できていません。確認メールのリンクをクリックしてメールアドレスの確認を完了してください。',
    'email_already_verified' => 'すでにメールアドレスを確認済みです。確認メールに記載されたログインページからログインしてください。',
    'failed_send_verification_email_due_to_user_not_found' => '確認メール送信に失敗しました。メールアドレスをご確認ください。',
    'failed_verification_email' => 'メールアドレスの確認に失敗しました。',
    'verify_email' => [
        'title' => 'メールアドレスの確認 - お宿検索',
        'intro' => '以下のボタンをクリックして、メールアドレスを確認してください。',
        'button' => 'メールアドレスを確認',
        'outro' => 'このメールに心当たりがない場合は、無視してください。',
        'signature' => 'お宿検索',
        'fallback' => 'もし「メールアドレスを確認」ボタンがうまく動作しない場合は、以下のURLをコピーしてブラウザに貼り付けてください。',
    ],
    'reset_password' => [
        'title' => 'パスワード再設定 - お宿検索',
        'intro' => '以下のボタンからパスワードを再設定してください。',
        'button' => 'パスワードを再設定する',
        'outro' => 'このメールに心当たりがない場合は、無視してください。',
        'signature' => 'お宿検索',
        'fallback' => 'リンクが表示されない場合はこちらをコピーしてブラウザに貼り付けてください：',
    ],

    'invalid_current_password' => '現在のパスワードが一致しません。',

    'image_not_found' => '画像が見つかりませんでした。',

    'thumbnail_decode_error' => 'ファイルの解析に失敗しました。base64形式の画像ファイルであることを確認してください。',
    'thumbnail_too_large' => '画像サイズは :size KB以内にしてください。',

    // Attributes
    'keyword' => '検索キーワード',
    'sort' => '並び順',

    'name' => '名称',
    'url' => 'URL',
    'address' => '住所',
    'thumbnail' => '画像',
    'description' => '説明',
    'price_min' => '最低価格（円）',
    'price_max' => '最高価格（円）',

    'budget_min' => '予算下限（円）',
    'budget_max' => '予算上限（円）',
];