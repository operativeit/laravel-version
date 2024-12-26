<?php

if (! function_exists('version')) {

    function version()
    {
        $version = json_decode(file_get_contents(base_path('version.json')), false, 512, JSON_THROW_ON_ERROR);

        return Str::replaceMatches('/{([^}]+)}/', function (array $matches) use ($version) {
            return $version->{$matches[1]};
        }, 'v{major}.{minor}.{patch}');
    }
}

if (! function_exists('codename')) {

    function codename()
    {
        return 'supernova';
    }
}
