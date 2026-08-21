<?php

return [
    'jwt' => [
        'private_key_path' => dirname(__DIR__) . '/src/Auth/Infrastructure/Security/keys/private.pem',
        'public_key_path' => dirname(__DIR__) . '/src/Auth/Infrastructure/Security/keys/public.pem',
    ],
];
