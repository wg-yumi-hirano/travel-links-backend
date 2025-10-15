<?php declare(strict_types=1);

return [
    'not_authorized' => 'ログインしていません。',
    'auth_failed' => 'ログインに失敗しました。IDまたはパスワードをご確認ください。',
    'auth_too_many_attempts' => 'ログイン試行回数が上限に達しました。:seconds 秒後に再試行してください。',

    'rate_limit_error' => 'リクエストが制限されました。しばらくしてから再試行してください。',
    'validation_error' => 'バリデーションエラー',
    'unexpected_error' => '予期しないエラーが発生しました。',

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
];