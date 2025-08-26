<?php

return [
  'paths' => ['api/*', 'sanctum/csrf-cookie'],
  'allowed_methods' => ['*'],
  'allowed_origins' => ['http://frontend'],
  'allowed_headers' => ['*'],
  'supports_credentials' => true,
];
