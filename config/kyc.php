<?php

return [
    'disk' => env('KYC_STORAGE_DISK', env('AWS_BUCKET') ? 's3' : 'local'),
    'visibility' => env('KYC_STORAGE_VISIBILITY', 'private'),
];

