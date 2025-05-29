<?php
namespace App\Services\PlatformValidation;


interface PlatformValidatorInterface
{
    public function validate(array $data): void;
}
