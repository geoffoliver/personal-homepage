<?php

namespace App\Lib;

class ImportUtils {

    public static function fixText($str) {
        // replace \uXXXX characters with actual UTF chatacters
        if (strpos($str, '\u') !== false) {
            $str = utf8_decode(preg_replace_callback("/\\\\u00([0-9a-f]{2})\\\\u00([0-9a-f]{2})/", function($matches) {
                return chr(hexdec($matches[0])) . chr(hexdec($matches[1]));
            }, $str));
        }

        // replace octothorpes with html entity version
        $str = str_replace('#', '&#35;', $str);

        // return the cleaned up string
        return $str;
    }

    public static function checkPath($path, $io)
    {
        if (!is_dir($path)) {
            $io->error(__('Path {0} is not a directory', $path));
            return false;
        }

        return self::checkFile($path, $io);
    }

    public static function checkFile($path, $io)
    {
        if (!file_exists($path)) {
            $io->error(__('Invalid path: {0}', $path));
            return false;
        }

        if (!is_readable($path)) {
            $io->error(__('Path {0} is unreadable', $path));
            return false;
        }

        return true;
    }

}
