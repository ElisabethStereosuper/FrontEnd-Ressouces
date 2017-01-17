<?php

/*
 * Wordpress filters
 */

if ( !defined( 'ABSPATH' ) )
	exit;

global $dgwt_nbsp_settings;

/*
 * Adds &nbsp; to content, excerpt, comments and widgets
 */

function webtroter_automatic_nbsp( $content ) {
	global $dgwt_nbsp_settings;
	$o = $dgwt_nbsp_settings;


	// Get phrases list
	$phrases = dgwt_nbsp_get_phrases();

	$pattern = array();

	// ADD PATTERNS - If phrases exists
	if ( $phrases ) {

		foreach ( $phrases as $phrase ) {

			//Possible beginnings of phrases
			$beginnings = dgwt_nbsp_get_phrases_beginnings();

			// Pattern: beginning + word/phrase + whitespace
			foreach ( $beginnings as $beginning ) {
				$pattern[] = '/' . $beginning . '+' . $phrase . '+\\s+/';
			}
		}
	}


	// ADD PATTERNS - Punctuation marks
	if ( isset( $o[ 'before_punctuation' ] ) && $o[ 'before_punctuation' ] == '1' ) {
		foreach ( dgwt_nbsp_get_punctuation_marks() as $mark ) {
			$pattern[] = dgwt_nbsp_get_pattern_for_punctuation_mark($mark);
		}
	}


	// Add &nbsp;
	if ( !empty( $pattern ) ) {
		$new_content = preg_replace_callback( $pattern, 'dgwt_nbsp_format_matches', $content );
	}

	return $new_content;
}

add_filter( 'the_content', 'webtroter_automatic_nbsp' );

if ( isset( $dgwt_nbsp_settings[ 'excerpt' ] ) && $dgwt_nbsp_settings[ 'excerpt' ] === '1' ) {
	add_filter( 'the_excerpt', 'webtroter_automatic_nbsp' );
}
if ( isset( $dgwt_nbsp_settings[ 'comment' ] ) && $dgwt_nbsp_settings[ 'comment' ] === '1' ) {
	add_filter( 'comment_text', 'webtroter_automatic_nbsp' );
}
if ( isset( $dgwt_nbsp_settings[ 'widget' ] ) && $dgwt_nbsp_settings[ 'widget' ] === '1' ) {
	add_filter( 'widget_text', 'webtroter_automatic_nbsp' );
}

/*
 * Adds &nbsp; to titles
 */

function webtroter_automatic_nbsp_title( $title ) {
	global $dgwt_nbsp_settings;
	$o = $dgwt_nbsp_settings;

	// Get phrases list
	$phrases = dgwt_nbsp_get_phrases();

	$new_title = $title;

	// If phrases exists
	if ( $phrases ) {

		$pattern = array();

		foreach ( $phrases as $phrase ) {

			// Pattern: whitespace + word/phrase + whitespace
			$pattern[] = '/\\s+' . $phrase . '+\\s+/';
		}

		// Add &nbsp;
		$new_title = preg_replace_callback( $pattern, 'dgwt_nbsp_format_matches', $title );
	}


	// ADD PATTERNS - Punctuation marks
	if ( isset( $o[ 'before_punctuation' ] ) && $o[ 'before_punctuation' ] == '1' ) {
		foreach ( dgwt_nbsp_get_punctuation_marks() as $mark ) {
			$pattern[] = dgwt_nbsp_get_pattern_for_punctuation_mark($mark);
		}
	}

	// Add &nbsp;
	$new_title = preg_replace_callback( $pattern, 'dgwt_nbsp_format_matches', $title );

	return $new_title;
}

if ( isset( $dgwt_nbsp_settings[ 'title' ] ) && $dgwt_nbsp_settings[ 'title' ] === '1' ) {
	add_filter( 'the_title', 'webtroter_automatic_nbsp_title' );
}
