<?php

/*
 * Useful functions
 */

if (!defined('ABSPATH'))
    exit;

/*
 * Get the list of phrases 
 */

function dgwt_nbsp_get_phrases() {
    global $dgwt_nbsp_settings;

    // Check that conjunctions are defined
    if (!isset($dgwt_nbsp_settings['words']) || empty($dgwt_nbsp_settings['words'])) {
        return false;
    }

    // Phrases
    $phrases_raw = $dgwt_nbsp_settings['words'];

    // Create array by new line
    $phrases_array = preg_split("/\r\n|\n|\r/", $phrases_raw);


    if (!$phrases_array || empty($phrases_array)) {
        return false;
    }

    $counter = 0;

    // Remove empty line and escape
    foreach ($phrases_array as $phrase) {

        // Strip whitespace from the beginning and end of a string
        $trimed_phrases = trim($phrase);
		
		//Escape special chars
		$phrases_array[$counter] = preg_replace("/(\.|\+|\?|\*|\^|\,|\:|\;|\"|\'|\/)/", "\\\\$1", $trimed_phrases);
		
        // Empty line
        if (empty($phrase)) {
            unset($phrases_array[$counter]);
        }

        $counter++;
    }

    // Reorder array keys
    $phrases = array_values($phrases_array);

    // Case sensitive
    if (!isset($dgwt_nbsp_settings['case_sensitive']) || $dgwt_nbsp_settings['case_sensitive'] != 1) {
        $phrases = dgwt_nbsp_all_phrases_variants($phrases);
    }

    // Removes duplicate values
    $phrases_list = array_unique($phrases);

    // Sort phrases by numbers of containing words.
    $reorder = usort($phrases_list, "dgwt_nbsp_reorder_phrases");

    if (!empty($phrases_list) && $reorder) {
        return $phrases_list;
    } else {
        return false;
    }
}

/*
 * Sort phrases by numbers of containing words.
 * Phrases that contain more words need to be replaced first
 */

function dgwt_nbsp_reorder_phrases($a, $b) {

    $a_value = str_word_count($a);

    $b_value = str_word_count($b);

    if ($a_value == $b_value) {
        return 0;
    }
    return ($a_value > $b_value) ? -1 : 1;
}

/*
 * Convert phrases to lowercase and uppercase
 */

function dgwt_nbsp_all_phrases_variants($phrases) {

    $all_variants = array();

    foreach ($phrases as $phrase) {

        $all_variants[] = strtolower($phrase);

        $all_variants[] = strtoupper($phrase);

        if (strlen($phrase) > 1) {
            $all_variants[] = ucfirst($phrase);
        }
    }

    return $all_variants;
}

/*
 * Formats the phrase when you add spaces
 * Callback function of preg_replace_callback.
 * Replace all whitespaces for a &nbsp; in a phrase.
 * For single word nothing change
 * @param $matches
 */

function dgwt_nbsp_format_matches($matches) {
    global $dgwt_nbsp_settings;
    $o = $dgwt_nbsp_settings;
    
    
    /*
     * Possible beginnings of phrases.
     * Sometimes phrases can start with other characters than whitespace e.g. 
     */
    $beginnings = dgwt_nbsp_get_phrases_beginnings();

    // Temporary strip whitespace from the beginning and end of a string
    $phrase_clear = trim($matches[0]);

    $phrase_nbsp = preg_replace('/\\s/', "&nbsp;", $phrase_clear);

    // Get first character.
    $first_char = mb_substr($phrase_nbsp, 0, 1, get_bloginfo('charset'));

    $whitespace_first = true;
    foreach ($beginnings as $beginning) {

        if ($first_char === $beginning) {
            $phrase = $phrase_nbsp . '&nbsp;';
            $whitespace_first = false;
        }
    }

    // Restore whitespace
    if ($whitespace_first) {
        $phrase = ' ' . $phrase_nbsp . '&nbsp;';
    }
    
    
    if(isset($o['before_punctuation']) && $o['before_punctuation'] == '1'){
        $marks = dgwt_nbsp_get_punctuation_marks();
        
        if(in_array($phrase_clear, $marks)){
            if ($phrase_clear === '«') {
                // Special case, space should be after «, not before
                $phrase = ' ' . $phrase_clear . '&nbsp;';
            } else {
                $phrase = '&nbsp;' . $phrase_clear;
            }
        }
       
    }
    
    return $phrase;
}


/*
 * Possible beginnings of phrases
 * Sometimes phrases can start with other characters than whitespace e.g. 
 * @return array of allowed characters
 */

function dgwt_nbsp_get_phrases_beginnings() {

    $beginnings = array(
        '\\s', // whitespace
        '>'
    );

    return $beginnings;
}

/*
 * Get list of words/phrases by language
 * @return array of words/phrases
 */

function dgwt_nbsp_get_phrases_by_lang() {

    $phrases = array();


    // English
    $phrases[] = array(
        'code' => 'en',
        'name' => __('English', 'automatic-nbsp'),
        'phrases' => array(
            'as',
            'so',
            'or',
            'if',
            'at',
            'in',
            'on',
            'and',
            'but',
            'nor',
            'e.g.',
            'for',
            'yet',
            'now',
            'til',
            'who',
            'why',
            'lest',
            'once',
            'even',
            'than',
            'that',
            'when',
            'after',
            'as if',
            'since',
            'until',
            'which',
            'where',
            'while',
            'before',
            'though',
            'unless',
            'whoever',
            'even if',
            'because',
            'whereas',
            'just as',
            'so that',
            'if only',
            'if when',
            'if then',
            'whether',
            'where if',
            'wherever',
            'inasmuch',
            'provided',
            'although',
            'now when',
            'now that',
            'whenever',
            'as though',
            'now since',
            'supposing',
            'as long as',
            'as much as',
            'as soon as',
            'rather than',
            'even though',
            'in order that',
            'provided that',
    ));

    // Polish
    $phrases[] = array(
        'code' => 'pl',
        'name' => __('Polish', 'automatic-nbsp'),
        'phrases' => array(
            'a',
            'i',
            'z',
            'w',
            'o',
            'u',
            'na',
            'np.',
            'nt.',
            'że',
            'do',
            'za',
            'na',
            'ku',
            'po',
            'ni',
            'bo',
            'dla',
            'czy',
            'lub',
            'pod',
            'ale',
            'aby',
            'ani',
            'nad',
            'zaś',
            'prof.',
            'znad',
            'przy',
            'spod',
            'oraz',
            'albo',
            'bądź',
            'obok',
            'choć',
            'lecz',
            'koło',
            'więc',
            'przy',
            'spoza',
            'przez',
            'czyli',
            'zatem',
            'toteż',
            'jeżeli',
            'dokoła',
            'jednak',
            'przeto',
            'tudzież',
            'to jest',
            'dlatego',
            'ponieważ',
            'natomiast',
            'mianowicie',
            'aczkolwiek',
    ));


    return $phrases;
}

if (!function_exists('auto_nbsp')) {
    /*
     * Adds nbsp to the custom text
     * @param string $content
     * @param bool $echo - return or echo
     */

    function auto_nbsp($content, $echo = true) {
        if ($echo) {
            echo webtroter_automatic_nbsp($content);
        } else {
            return webtroter_automatic_nbsp($content);
        }
    }

}


/*
 * All punctuation marks
 */

function dgwt_nbsp_get_punctuation_marks(){
    
    $marks = array('!','?',':',';','%','«','»');
    
    return $marks;
}

/*
 * Get a regexp pattern built based on the punctuation mark
 * @return regexp used to search into the text content
 */

function dgwt_nbsp_get_pattern_for_punctuation_mark( $mark ) {
    if ($mark === '«') {
        // Special case, space should be after «, not before
        $pattern = '/\\' . $mark . '\\s+/';
    } else {
        $pattern = '/\\s+\\' . $mark . '/';
    }
    return $pattern;
}