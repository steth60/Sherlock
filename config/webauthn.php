<?php

use Cose\Algorithms;

return [
    'rp' => [
        'name' => env('WEBAUTHN_RP_NAME', 'My Application'),
        'id' => env('WEBAUTHN_RP_ID', 'example.com'),
    ],

    'timeout' => 60000,

    'attestation' => 'direct',

    'public_key_credential_parameters' => [
        (string) Algorithms::COSE_ALGORITHM_ES256,
        (string) Algorithms::COSE_ALGORITHM_ES512,
        (string) Algorithms::COSE_ALGORITHM_RS256,
        (string) Algorithms::COSE_ALGORITHM_EdDSA,
        (string) Algorithms::COSE_ALGORITHM_ES384,
    ],

    'extensions' => [
        'appid' => 'https://sherlock.codeneko.co',
    ],
];