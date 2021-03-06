<?php
/*
 *  This file is part of PHPLibs <http://phplibs.kmfa.net/>.
 *  
 *  Copyright (c) 2010 < squarious at gmail dot com > .
 *  
 *  PHPLibs is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  PHPLibs is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with PHPLibs.  If not, see <http://www.gnu.org/licenses/>.
 *  
 */


//! Create a new tag
/**
 * @return Output_HTMLTag
 * Enter description here ...
 */
function tag()
{	
    return new Output_HTMLTag(func_get_args());    
}

//! Create a tag and echo it
/**
 * * @return Output_HTMLTag
 * Enter description here ...
 */
function etag()
{	
    $tag = new Output_HTMLTag(func_get_args());
    if (!$tag->append_to_default_parent())
        echo $tag;
    return $tag;
}

//! Human readable dump of a tag tree
function dump_tag($tag, $prepend = "")
{
    echo $prepend . $tag->tag . "\n";

    foreach ($tag->childs as $child)
    {	
        if (is_object($child))
            dump_tag($child, $prepend . "  ");
        else
            echo $prepend . "  " . '"' . $child . "\"\n";
    }
}

//! Human readable file size
/**
 * It is preferable to display size closer to the unit that
 * results with less digits and without using floating point. It is better
 * to use 1.2K or 1K than 1200bytes.
 *  @param $size The size in bytes
 *  @param $postfix The string to be postfixed after measurement unit.
 */
function html_human_fsize($size, $postfix = 'ytes')
{	
    if ($size < 1024)
        return $size . ' b' . $postfix;
    else if ($size < 1048576)
        return ceil($size/1024) . ' KB' . $postfix;
    else if ($size < 1073741824)
        return ceil($size/1048576) . ' MB' . $postfix;
    return ceil($size/1073741824) . ' GB' . $postfix;
}

/**
 * @param DateTime $dt
 * @return Output_DateFormat
 */
function date_exformat($dt)
{
    return new Output_DateFormat($dt);
}

//! Escape all html control characters from a text and return the result
function esc_html($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function etag_var_dump()
{
	$args = func_get_args();
	ob_start();
	call_user_func_array('var_dump', $args);
	$data = ob_get_clean();
	etag('pre', $data);
}

//! Escape white space
function esc_sp($str, $tab_width = '4', $nobreak = true)
{
    $esc_char = ($nobreak?'&nbsp;':'&ensp;');
    $str = str_replace(' ', $esc_char, $str);
    $str = mb_ereg_replace("\t", str_repeat($esc_char, $tab_width), $str);
    return $str;
}

//! Escape javascript code
function esc_js($str)
{
    $str = mb_ereg_replace("\\\\", "\\\\", $str);
    $str = mb_ereg_replace("\"", "\\\"", $str);
    $str = mb_ereg_replace("'", "\\'", $str);
    $str = mb_ereg_replace("\r\n", "\\n", $str);
    $str = mb_ereg_replace("\r", "\\n", $str);
    $str = mb_ereg_replace("\n", "\\n", $str);
    $str = mb_ereg_replace("\t", "\\t", $str);
    $str = mb_ereg_replace("<", "\\x3C", $str); // for inclusion in HTML
    $str = mb_ereg_replace(">", "\\x3E", $str);
    return $str;
}

//! Add google analytics code
function html_ga_code($site_id, $return_code = false)
{
    $code = '<script type="text/javascript">';
    $code .= 'var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");';
    $code .= 'document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' ';
    $code .= 'type=\'text/javascript\'%3E%3C/script%3E"));';
    $code .= '</script>';
    $code .= '<script type="text/javascript">';
    $code .= 'try {';
    $code .= 'var pageTracker = _gat._getTracker("' . $site_id . '");';
    $code .= 'pageTracker._trackPageview();';
    $code .= '} catch(err) {}</script>';
    if ($return_code)
        return $code;

    echo $code;
    return true;
}

// Find links in html text and linkfy them
function html_linkify_urls($text, $replace_text = '<a href="${0}" target="_blank">${0}</a>')
{
    return preg_replace('/((?:http|ftp):\/\/[^\s\<\>]*)/im', $replace_text, $text);
}

/**
 * Encode rfc2231
 * @link http://stackoverflow.com/questions/4968272/how-can-i-encode-a-filename-in-php-according-to-rfc-2231
 */ 
function rfc2231_encode($name, $value, $charset='', $lang='', $ll=78) {
    if (strlen($name) === 0 || preg_match('/[\x00-\x20*\'%()<>@,;:\\\\"\/[\]?=\x80-\xFF]/', $name)) {
        // invalid parameter name;
        return false;
    }
    /*
     * doesn't work for utf-8
     * if (strlen($charset) !== 0 && !preg_match('/^[A-Za-z]{1,8}(?:-[A-Za-z]{1,8})*$/', $charset)) {
        // invalid charset;
        return false;
    }*/
    if (strlen($lang) !== 0 && !preg_match('/^[A-Za-z]{1,8}(?:-[A-Za-z]{1,8})*$/', $lang)) {
        // invalid language;
        return false;
    }
    $value = "$charset'$lang'".preg_replace_callback('/[\x00-\x20*\'%()<>@,;:\\\\"\/[\]?=\x80-\xFF]/', function($match) { return rawurlencode($match[0]); }, $value);
    $nlen = strlen($name);
    $vlen = strlen($value);
    if (strlen($name) + $vlen > $ll-3) {
        $sections = array();
        $section = 0;
        for ($i=0, $j=0; $i<$vlen; $i+=$j) {
            $j = $ll - $nlen - strlen($section) - 4;
            $sections[$section++] = substr($value, $i, $j);
        }
        for ($i=0, $n=$section; $i<$n; $i++) {
            $sections[$i] = " $name*$i*=".$sections[$i];
        }
        return implode(";\r\n", $sections);
    } else {
        return " $name*=$value";
    }
}
