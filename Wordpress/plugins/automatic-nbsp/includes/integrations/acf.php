<?php

/*
 * Integrations with Advanced custom fields plugin 
 * @see http://www.advancedcustomfields.com
 */

function dgwt_nbsp_run_on_acf( $value ) {

	$new_value = $value;

	if ( !empty( $value ) ) {
		$new_value = webtroter_automatic_nbsp( $value );
	}

	return $new_value;
}

if ( isset( $dgwt_nbsp_settings[ 'acf' ] ) && $dgwt_nbsp_settings[ 'acf' ] === '1' ) {
	add_filter( 'acf_load_value-text', 'dgwt_nbsp_run_on_acf' );
	add_filter( 'acf_load_value-textarea', 'dgwt_nbsp_run_on_acf' );
	add_filter( 'acf_load_value-wysiwyg', 'dgwt_nbsp_run_on_acf' );
	add_filter( 'acf/load_value/type=text', 'dgwt_nbsp_run_on_acf' );
	add_filter( 'acf/load_value/type=textarea', 'dgwt_nbsp_run_on_acf' );
	add_filter( 'acf/load_value/type=wysiwyg', 'dgwt_nbsp_run_on_acf' );
}