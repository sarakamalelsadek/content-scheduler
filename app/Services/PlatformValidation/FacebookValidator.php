<?php
namespace App\Services\PlatformValidation;

use Illuminate\Validation\ValidationException;

class FacebookValidator implements PlatformValidatorInterface
{
    public function validate(array $data): void
    {
        if (strlen($data['content']) > config("settings.platforms.facebook")) {
            throw ValidationException::withMessages([
                'content' => 'Facebook content cannot exceed '.config("settings.platforms.facebook").' characters.',
            ]);
        }
    }
}