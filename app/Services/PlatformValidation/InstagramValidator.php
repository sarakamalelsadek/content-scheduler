<?php
namespace App\Services\PlatformValidation;

use Illuminate\Validation\ValidationException;

class InstagramValidator implements PlatformValidatorInterface
{
    public function validate(array $data): void
    {
        if (empty($data['image'])) {
            throw ValidationException::withMessages([
                'image' => 'Instagram post must have an image.',
            ]);
        }
    }
}