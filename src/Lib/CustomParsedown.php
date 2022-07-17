<?php

namespace App\Lib;

use Cake\Routing\Router;

class CustomParsedown extends \Parsedown
{
    private $hashTagPattern = '/\B#([a-zA-Z0-9][\w-]+)/';

    function __construct()
    {
        $this->InlineTypes['#'][]= 'Hashtag';
        $this->inlineMarkerList .= '#';
    }

    protected function inlineHashtag($Excerpt)
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
                        'class' => 'hashtag p-category', //style class of url
                    ],
                ],
            ];
        }
    }
}
