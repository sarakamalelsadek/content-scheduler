<?php
namespace App\Services\PlatformValidation;


class DefaultValidator implements PlatformValidatorInterface
{
    public function validate(array $data): void
    {
        // No special rules
    }
}