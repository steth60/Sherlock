<?php

return [
    'rp' => [
        'name' => env('WEBAUTHN_RP_NAME', 'Your App Name'),
        'id' => env('WEBAUTHN_RP_ID', 'localhost'),
    ],
    'authenticator_selection_criteria' => [
        'authenticatorAttachment' => null,
        'residentKey' => null,
        'userVerification' => 'preferred',
    ],
    'timeout' => 60000,
    'attestation_conveyance' => 'none',
];