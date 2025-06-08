<?php
namespace App\Utils;

class Validator {
    public static function validate(array $input, array $requiredFields): void {
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                throw new \InvalidArgumentException("El campo '$field' es requerido", 400);
            }
            
            // Validación adicional según el campo
            if ($field === 'title' && strlen($input[$field]) > 255) {
                throw new \InvalidArgumentException("El título no puede exceder 255 caracteres", 400);
            }
            
            if ($field === 'status' && !in_array($input[$field], ['draft', 'published'])) {
                throw new \InvalidArgumentException("Estado no válido", 400);
            }
        }
    }
}