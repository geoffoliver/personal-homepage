<?php
if (version_compare(PHP_VERSION, '7.2.1') < 0) {
    trigger_error('Your PHP version must be equal or higher than 7.2.1.' . PHP_EOL, E_USER_ERROR);
}

if (!extension_loaded('intl')) {
    trigger_error('You must enable the PHP intl extension.' . PHP_EOL, E_USER_ERROR);
}

if (!extension_loaded('mbstring')) {
    trigger_error('You must enable the PHP mbstring extension.' . PHP_EOL, E_USER_ERROR);
}

if (!extension_loaded('imagick')) {
    trigger_error('You must enable the PHP imagemagick extension.' . PHP_EOL, E_USER_ERROR);
}

if (!extension_loaded('xml')) {
    trigger_error('You must enable the PHP xml extension.' . PHP_EOL, E_USER_ERROR);
}

if (!extension_loaded('zip')) {
    trigger_error('You must enable the PHP zip extension.' . PHP_EOL, E_USER_ERROR);
}

// make sure ffmpeg is installed
$ffmpegPath = exec('which ffmpeg');
if ($ffmpegPath === '') {
    trigger_error('You must install FFMPEG to enable video thumbnails.' . PHP_EOL, E_USER_ERROR);
}
