<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana 3 port of the Zend Framework Dom Query
 * library. More information to follow...
 * 
 * Original code copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * 
 * Adapted for Kohana 3 by Karol Janyst <lapkom@gmail.com>
 *
 * @package Dom Query
 * @copyright (c) 2010 LapKom Karol Janyst
 * @license ISC License http://www.opensource.org/licenses/isc-license.txt
 */
class Dom_Query_Parser {

    public static function factory($path)
    {
        $path = (string) $path;
        if (strstr($path, ',')) {
            $paths       = explode(',', $path);
            $expressions = array();
            foreach ($paths as $path) {
                $xpath = self::factory(trim($path));
                if (is_string($xpath)) {
                    $expressions[] = $xpath;
                } elseif (is_array($xpath)) {
                    $expressions = array_merge($expressions, $xpath);
                }
            }
            return implode('|', $expressions);
        }

        $paths    = array('//');
        $path     = preg_replace('|\s+>\s+|', '>', $path);
        $segments = preg_split('/\s+/', $path);
        foreach ($segments as $key => $segment) {
            $pathSegment = self::_tokenize($segment);
            if (0 == $key) {
                if (0 === strpos($pathSegment, '[contains(')) {
                    $paths[0] .= '*' . ltrim($pathSegment, '*');
                } else {
                    $paths[0] .= $pathSegment;
                }
                continue;
            }
            if (0 === strpos($pathSegment, '[contains(')) {
                foreach ($paths as $key => $xpath) {
                    $paths[$key] .= '//*' . ltrim($pathSegment, '*');
                    $paths[]      = $xpath . $pathSegment;
                }
            } else {
                foreach ($paths as $key => $xpath) {
                    $paths[$key] .= '//' . $pathSegment;
                }
            }
        }

        if (1 == count($paths)) {
            return $paths[0];
        }
        return implode('|', $paths);
    }

    protected static function _tokenize($expression)
    {
        // Child selectors
        $expression = str_replace('>', '/', $expression);

        // IDs
        $expression = preg_replace('|#([a-z][a-z0-9_-]*)|i', '[@id=\'$1\']', $expression);
        $expression = preg_replace('|(?<![a-z0-9_-])(\[@id=)|i', '*$1', $expression);

        // arbitrary attribute strict equality
        $expression = preg_replace_callback(
            '|\[([a-z0-9_-]+)=[\'"]([^\'"]+)[\'"]\]|i',
            array(__CLASS__, '_create_equality_expression'),
            $expression
        );

        // arbitrary attribute contains full word
        $expression = preg_replace_callback(
            '|\[([a-z0-9_-]+)~=[\'"]([^\'"]+)[\'"]\]|i',
            array(__CLASS__, '_normalize_space_attribute'),
            $expression
        );

        // arbitrary attribute contains specified content
        $expression = preg_replace_callback(
            '|\[([a-z0-9_-]+)\*=[\'"]([^\'"]+)[\'"]\]|i',
            array(__CLASS__, '_create_contains_expression'),
            $expression
        );

        // Classes
        $expression = preg_replace(
            '|\.([a-z][a-z0-9_-]*)|i', 
            "[contains(concat(' ', normalize-space(@class), ' '), ' \$1 ')]", 
            $expression
        );

        /** ZF-9764 -- remove double asterix */
        $expression = str_replace('**', '*', $expression);

        return $expression;
    }

    protected function _create_equality_expression($matches)
    {
        return '[@' . strtolower($matches[1]) . "='" . $matches[2] . "']";
    }

    protected function _normalize_space_attribute($matches)
    {
        return "[contains(concat(' ', normalize-space(@" . strtolower($matches[1]) . "), ' '), ' " 
             . $matches[2] . " ')]";
    }

    protected function _create_contains_expression($matches)
    {
        return "[contains(@" . strtolower($matches[1]) . ", '" 
             . $matches[2] . "')]";
    }
}
