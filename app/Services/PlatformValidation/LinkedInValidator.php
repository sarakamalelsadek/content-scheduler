<?php
namespace App\Services\PlatformValidation;

use Illuminate\Validation\ValidationException;

class LinkedInValidator implements PlatformValidatorInterface
{
    public function validate(array $data): void
    {
        if (strlen($data['content']) > config("settings.platforms.linkedin")) {
            throw ValidationException::withMessages([
                'content' => 'LinkedIn content cannot exceed '.config("settings.platforms.linkedin").' characters.',
            ]);
        }
    }
}
