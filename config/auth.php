<?php

return [
    'require_auth' => true,
    'auth_header' => 'HTTP_API_KEY', // Nombre personalizable del header
    'token_length' => 64, // Longitud esperada de las API Keys
    'allowed_paths' => [ // Rutas que no requieren autenticación
        '/api/login',
        '/api/public'
    ]
];