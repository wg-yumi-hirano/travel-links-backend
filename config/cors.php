<?php declare(strict_types=1);

return [
  'paths' => ['api/*', 'sanctum/csrf-cookie'],
  'allowed_methods' => ['*'],
  'allowed_origins' => explode(',', env('APP_URLS')),
  'allowed_headers' => ['*'],
  'supports_credentials' => true,
];
