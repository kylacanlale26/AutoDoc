<?php

return [
    'url' => env('OCR_API_URL', 'http://127.0.0.1:3000/api/v1/ocr/process'),
    'token' => env('OCR_API_TOKEN'),
    'timeout' => (int) env('OCR_API_TIMEOUT_SECONDS', 180),
];
