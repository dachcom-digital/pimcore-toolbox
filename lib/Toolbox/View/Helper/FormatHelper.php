<?php

namespace Toolbox\View\Helper;

class FormatHelper extends \Zend_View_Helper_Abstract {

    public function formatHelper() {
        return $this;
    }

    /**
     * Truncate text or HTML respecting word boundaries and HTML markup
     *
     * @param string $text to truncate
     * @param        $maxCharacters
     * @param string $append
     * @param bool   $respectWordBoundaries
     * @param bool   $respectHtml
     *
     * @return string
     */
    public function truncate($text, $maxCharacters, $append = '...', $respectWordBoundaries = true, $respectHtml = true) {

        if ($respectHtml) {
            $content = $this->_truncateHtml($text, $maxCharacters, $append, $respectWordBoundaries);
        } else {
            $content = $this->_truncateText($text, $maxCharacters, $append, $respectWordBoundaries);
        }

        return $content;
    }




    private function _truncateText($text, $maxCharacters, $append, $respectWordBoundaries) {

        if ($maxCharacters) {
            if (iconv_strlen($text) > abs($maxCharacters)) {
                $truncatePosition = false;
                if ($maxCharacters < 0) {
                    $text = substr($text, $maxCharacters);
                    if ($respectWordBoundaries) {
                        $truncatePosition = strpos($text, ' ');
                    }
                    $text = $truncatePosition ? $append . substr($text, $truncatePosition) : $append . $text;
                } else {
                    $text = substr($text, 0, $maxCharacters);
                    if ($respectWordBoundaries) {
                        $truncatePosition = strrpos($text, ' ');
                    }
                    $text = $truncatePosition ? substr($text, 0, $truncatePosition) . $append : $text . $append;
                }
            }
        }

        return $text;
    }

    private function _truncateHtml($text, $maxCharacters, $append, $respectWordBoundaries) {

        // if the plain text is shorter than the maximum length, return the whole text
        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $maxCharacters) {
            return $text;
        }

        // splits all html-tags to scanable lines
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
        $total_length = strlen($append);
        $open_tags    = array();
        $truncate     = '';

        foreach ($lines as $line_matchings) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($line_matchings[1])) {
                // if it's an "empty element" with or without xhtml-conform closing slash
                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    // do nothing
                    // if tag is a closing tag
                } else {
                    if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } else {
                        if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                            // add tag to the beginning of $open_tags list
                            array_unshift($open_tags, strtolower($tag_matchings[1]));
                        }
                    }
                }
                // add html-tag to $truncate'd text
                $truncate .= $line_matchings[1];
            }
            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
            if ($total_length + $content_length > $maxCharacters) {
                // the number of characters which are left
                $left            = $maxCharacters - $total_length;
                $entities_length = 0;
                // search for html entities
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entities_length <= $left) {
                            $left--;
                            $entities_length += strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            } else {
                $truncate .= $line_matchings[2];
                $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if ($total_length >= $maxCharacters) {
                break;
            }
        }

        // if the words shouldn't be cut in the middle...
        if (!$respectWordBoundaries) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $append;

        // close all unclosed html-tags
        foreach ($open_tags as $tag) {
            $truncate .= '</' . $tag . '>';
        }

        return $truncate;
    }
}