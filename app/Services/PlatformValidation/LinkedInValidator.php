<?php
namespace App\Services\PlatformValidation;

use Illuminate\Validation\ValidationException;

class LinkedInValidator implements PlatformValidatorInterface
{
    public function validate(array $data): void
    {
        if (strlen($data['content']) > 1300) {
            throw ValidationException::withMessages([
                'content' => 'LinkedIn content cannot exceed 1300 characters.',
            ]);
        }
    }
}
