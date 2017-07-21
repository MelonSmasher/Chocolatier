<?php

return [
    /*
     * Application
     */
    'debug_requests' => env('APP_DEBUG_REQUESTS', false),
    'name' => env('APP_NAME', 'Chocolatier Repository'),
    'shortname' => env('APP_SHORT_NAME', 'Chocolatier'),
    'description' => env('APP_DESCRIPTION', 'Chocolatier: NuGet/Chocolatey repository server'),
    # Storing links in a JSON string so they can be customized through the env file
    'links' => json_decode(env('APP_LINKS', '[{"href": "https://www.nuget.org", "title": "NuGet"},{"href": "https://chocolaty.org", "title": "Chocolatey"}]'), true),
    'display_links' => env('APP_DISPLAY_LINKS', false),
    /*
     * Packages
     */
    'hash_algorithm' => env('packages_hash_algorithm', 'SHA512'),
];