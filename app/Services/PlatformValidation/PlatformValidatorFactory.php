<?php
namespace App\Services\PlatformValidation;

class PlatformValidatorFactory
{
    public static function make(string $platformType): PlatformValidatorInterface
    {
        return match ($platformType) {
            'twitter' => new TwitterValidator(),
            'instagram' => new InstagramValidator(),
            'linkedin' => new LinkedInValidator(),
            default => new DefaultValidator(),
        };
    }
}