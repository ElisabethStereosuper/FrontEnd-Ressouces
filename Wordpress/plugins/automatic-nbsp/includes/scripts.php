<?php
/**
 * Register scripts
 */

if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Załącza skrypty js oraz arkusze styli CSS na zapleczu
 */
function dgwt_nbsp_admin_scripts() {


	$js_dir  = DGWT_NBSP_URL . 'assets/js/';
	$css_dir = DGWT_NBSP_URL . 'assets/css/';

        
	wp_enqueue_script( 'automatic-nbsp', $js_dir . 'admin-scripts.js', array( 'jquery' ), DGWT_NBSP_VERSION, false );
	
	wp_enqueue_style( 'automatic-nbsp', $css_dir . 'admin-style.css', array(), DGWT_NBSP_VERSION );
        
        
}
add_action( 'admin_enqueue_scripts', 'dgwt_nbsp_admin_scripts');
