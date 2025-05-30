<?php

return [
    
    'platforms' => [
        'twitter' => env("TWITTER_CHARACTER_LIMIT",280),
        'linkedin' => env("LINKEDIN_CHARACTER_LIMIT",3000),
        'facebook' => env("FACEBOOK_CHARACTER_LIMIT",63,206),
    ],

];
