<?php
namespace App\Services\PlatformValidation;

use Illuminate\Validation\ValidationException;

class TwitterValidator implements PlatformValidatorInterface
{
    public function validate(array $data): void
    {
        if (strlen($data['content']) > 280) {
            throw ValidationException::withMessages([
                'content' => 'Twitter content cannot exceed 280 characters.',
            ]);
        }
    }
}