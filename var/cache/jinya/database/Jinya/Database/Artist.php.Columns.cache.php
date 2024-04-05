<?php
global $Artist__Columns;

$Artist__Columns = [
'byProperty' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'email' => new Jinya\Database\Cache\CacheColumn('email', 'email', null, false, true, false, NULL),
'enabled' => new Jinya\Database\Cache\CacheColumn('enabled', 'enabled', new Jinya\Cms\Database\Converter\BooleanConverter(), false, true, false, NULL),
'twoFactorToken' => new Jinya\Database\Cache\CacheColumn('twoFactorToken', 'two_factor_token', null, false, false, false, NULL),
'roles' => new Jinya\Database\Cache\CacheColumn('roles', 'roles', new Jinya\Cms\Database\Converter\PhpSerializerConverter(), false, true, false, NULL),
'artistName' => new Jinya\Database\Cache\CacheColumn('artistName', 'artist_name', null, false, true, false, NULL),
'profilePicture' => new Jinya\Database\Cache\CacheColumn('profilePicture', 'profile_picture', null, false, false, false, NULL),
'aboutMe' => new Jinya\Database\Cache\CacheColumn('aboutMe', 'about_me', null, false, false, false, NULL),
'failedLoginAttempts' => new Jinya\Database\Cache\CacheColumn('failedLoginAttempts', 'failed_login_attempts', null, false, false, false, NULL),
'loginBlockedUntil' => new Jinya\Database\Cache\CacheColumn('loginBlockedUntil', 'login_blocked_until', new Jinya\Database\Converters\NullableDateConverter('Y-m-d H:i:s'), false, false, false, NULL),
'prefersColorScheme' => new Jinya\Database\Cache\CacheColumn('prefersColorScheme', 'prefers_color_scheme', new Jinya\Cms\Database\Converter\NullableBooleanConverter(), false, false, false, NULL),
'password' => new Jinya\Database\Cache\CacheColumn('password', 'password', null, false, true, false, NULL),

],
'bySqlName' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'email' => new Jinya\Database\Cache\CacheColumn('email', 'email', null, false, true, false, NULL),
'enabled' => new Jinya\Database\Cache\CacheColumn('enabled', 'enabled', new Jinya\Cms\Database\Converter\BooleanConverter(), false, true, false, NULL),
'two_factor_token' => new Jinya\Database\Cache\CacheColumn('twoFactorToken', 'two_factor_token', null, false, false, false, NULL),
'roles' => new Jinya\Database\Cache\CacheColumn('roles', 'roles', new Jinya\Cms\Database\Converter\PhpSerializerConverter(), false, true, false, NULL),
'artist_name' => new Jinya\Database\Cache\CacheColumn('artistName', 'artist_name', null, false, true, false, NULL),
'profile_picture' => new Jinya\Database\Cache\CacheColumn('profilePicture', 'profile_picture', null, false, false, false, NULL),
'about_me' => new Jinya\Database\Cache\CacheColumn('aboutMe', 'about_me', null, false, false, false, NULL),
'failed_login_attempts' => new Jinya\Database\Cache\CacheColumn('failedLoginAttempts', 'failed_login_attempts', null, false, false, false, NULL),
'login_blocked_until' => new Jinya\Database\Cache\CacheColumn('loginBlockedUntil', 'login_blocked_until', new Jinya\Database\Converters\NullableDateConverter('Y-m-d H:i:s'), false, false, false, NULL),
'prefers_color_scheme' => new Jinya\Database\Cache\CacheColumn('prefersColorScheme', 'prefers_color_scheme', new Jinya\Cms\Database\Converter\NullableBooleanConverter(), false, false, false, NULL),
'password' => new Jinya\Database\Cache\CacheColumn('password', 'password', null, false, true, false, NULL),

],
];