<?php
namespace App\Lib\Quill\Listeners;

use nadar\quill\BlockListener;
use nadar\quill\Line;
use nadar\quill\Lexer;

class CodeBlock extends BlockListener
{

    const ATTRIBUTE_LIST = 'code-block';

    public function process(Line $line)
    {
        // check if input is json, decodes to an array and checks if the key "mention"
        // exsts, if yes return the value for this key.
        $codeblock = $line->getAttribute(self::ATTRIBUTE_LIST);
        if ($codeblock) {
            $this->pick($line, [self::ATTRIBUTE_LIST => $codeblock]);
            $line->setDone();
        }
    }

    public function render(Lexer $lexer)
    {
        $isOpen = false;
        $codeTag = 'pre';
        foreach ($this->picks() as $pick) {
            $first = $this->getFirstLine($pick);
            // while from first to the pick line and store content in buffer
            $buffer = null;
            $first->while(function (&$index, Line $line) use (&$buffer, $pick) {
                $index++;
                $buffer.= $line->getInput() . "\n";
                $line->setDone();
                if ($index == $pick->line->getIndex()) {
                    return false;
                }
            });

            // defines whether this attribute list element is the last one of a list serie.
            $isLast = false;
            // go to the next element with endlinew and check if it contains a list type until then
            $hasNextInside = false;
            $pick->line->whileNext(function (Line $line) use (&$hasNextInside) {
                // we found the next list elemnt, stop thie while loop
                if ($line->getAttribute(self::ATTRIBUTE_LIST)) {
                    return false;
                }
                // if one of those new lines contains a endnew line or newline or is block level store this information
                if ($line->hasEndNewline() || $line->hasNewline() || ($line->isJsonInsert() && !$line->isInline())) {
                    $hasNextInside = true;
                }
            });
            // There was a newline element until next list element, so end of list has reached.
            if ($hasNextInside) {
                $isLast = true;
            }

            $output = null;

            /*if ($isOpen) {
                $output .= '</' . $codeTag . '>';
                $isOpen = false;
            }*/

            // create the opining pre tag if:
            //  a. its not already open AND $isLast is false (which means not the last element)
            //  b. or its the first the pick inside the picked elements list https://github.com/nadar/quill-delta-parser/issues/8
            if ((!$isOpen && !$isLast) || (!$isOpen && $pick->isFirst())) {
                $output .= '<' . $codeTag . '>';
                $isOpen = true;
            }

            // write the code contents
            $output.= $buffer;

            // close the opening pre tag if:
            //   a. its the last element and the tag is opened.
            //   b. or its the last element in the picked list.
            if (($isOpen && $isLast) || ($isOpen && $pick->isLast())) {
                $output .= '</' . $codeTag . '>';
                $isOpen = false;
            }
            $pick->line->output = $output;
            $pick->line->setDone();
        }
    }
}
