<?php

namespace App\Lib;

use Cake\Routing\Router;

class ParsedownHashTags extends \Parsedown
{
    private $hashTagPattern = '/\B#([a-zA-Z0-9][\w-]+)/';
    // private $hashTagPattern = '/\B#([a-zA-Z][\w-]+)/';

    function __construct()
    {
        $this->InlineTypes['#'][]= 'UserMention';

        $this->inlineMarkerList .= '#';
    }

    protected function inlineUserMention($Excerpt)
    {
        if (preg_match($this->hashTagPattern, $Excerpt['context'], $matches)) {
            $url = Router::url([
                '_name' => 'tag',
                'tag' => mb_strtolower($matches[1]),
            ], true);

            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'a',
                    'text' => $matches[0],
                    'attributes' => [
                        'href' => $url,
                        'class' => 'hashtag', //style class of url
                    ],
                ],
            ];
        }
    }
    /*
    public static function findMentions($text)
    {
        if (preg_match_all($this->hashTagPattern, $text)) {
            $text = str_replace(["\r\n", "\r"], "\n", $text);
            $text = trim($text, "\n");
            $lines = explode("\n", $text);

            $mentions = [];
            $isBlockCode = false;
            foreach ($lines as $line) {
                if (empty($line)) {
                    continue;
                }

                if (($l = $line[0]) === '`' && strncmp($line, '```', 3) === 0 || $l === '~' && strncmp($line, '~~~', 3) === 0) {
                    if ($isBlockCode === false) {
                        $isBlockCode = true;
                    } else {
                        $isBlockCode = false;
                    }
                    continue;
                } elseif ($isBlockCode === true) {
                    continue;
                } elseif (($l = $line[0]) === ' ' && $line[1] === ' ' && $line[2] === ' ' && $line[3] === ' ' || $l === "\t") {
                    continue;
                } elseif (preg_match('/^`(.+?)`/s', $line)) {
                    continue;
                } else {
                    if (preg_match_all($pattern, $line, $matches)) {
                        $mentions = array_merge($mentions, $matches[1]);
                    }
                }
            }

            return array_unique($mentions);
        }

        return null;
    }
    */
}
