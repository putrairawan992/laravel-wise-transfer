<?php

return [
    'disk' => env('KYC_STORAGE_DISK', env('AWS_BUCKET') ? 's3' : 'local'),
    'visibility' => env('KYC_STORAGE_VISIBILITY', 'private'),
    'face' => [
        'match_threshold' => (float) env('KYC_FACE_MATCH_THRESHOLD', 0.45),
        'stable_frames' => (int) env('KYC_FACE_STABLE_FRAMES', 5),
        'public_stable_frames' => (int) env('KYC_FACE_PUBLIC_STABLE_FRAMES', 6),
        'ambiguous_gap' => (float) env('KYC_FACE_AMBIGUOUS_GAP', 0.05),
    ],
];

