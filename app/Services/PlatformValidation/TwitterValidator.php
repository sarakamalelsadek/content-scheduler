<?php
namespace App\Services\PlatformValidation;

use Illuminate\Validation\ValidationException;

class TwitterValidator implements PlatformValidatorInterface
{
    public function validate(array $data): void
    {
        if (strlen($data['content']) > config("settings.platforms.twitter")) {
            throw ValidationException::withMessages([
                'content' => 'Twitter content cannot exceed '.config("settings.platforms.twitter").' characters.',
            ]);
        }
    }
}