<?php
// config/auth.php
return [
    'api_keys' => [
        'APP-ADMIN' => 'tu_super_key_secreta_123',  // Key para administración
        'APP-CLIENT' => 'clave_para_clientes_456'   // Key para clientes
    ],
    'require_auth' => false // Cambiar a false en desarrollo
];