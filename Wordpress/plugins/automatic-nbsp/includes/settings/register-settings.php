<?php

/*
 * WordPress Settings API
 */


if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Register settings
 */
function dgwt_nbsp_register_settings() {

	if ( false == get_option( 'dgwt_automatic_nbsp' ) ) {
		add_option( 'dgwt_automatic_nbsp' );
	}

	foreach ( dgwt_nbsp_get_registered_settings() as $section => $settings ) {

		add_settings_section(
		'dgwt_nbsp_settings_' . $section, __return_null(), '__return_false', 'dgwt_nbsp_settings_' . $section
		);

		foreach ( $settings as $option ) {

			$name = isset( $option[ 'name' ] ) ? $option[ 'name' ] : '';

			add_settings_field(
			'dgwt_automatic_nbsp[' . $option[ 'id' ] . ']', $name, function_exists( 'dgwt_nbsp_' . $option[ 'type' ] . '_callback' ) ? 'dgwt_nbsp_' . $option[ 'type' ] . '_callback' : 'dgwt_nbsp_missing_callback', 'dgwt_nbsp_settings_' . $section, 'dgwt_nbsp_settings_' . $section, array(
				'id'		 => isset( $option[ 'id' ] ) ? $option[ 'id' ] : null,
				'desc'		 => !empty( $option[ 'desc' ] ) ? $option[ 'desc' ] : '',
				'name'		 => isset( $option[ 'name' ] ) ? $option[ 'name' ] : null,
				'info'		 => isset( $option[ 'info' ] ) ? $option[ 'info' ] : null,
				'section'	 => $section,
				'size'		 => isset( $option[ 'size' ] ) ? $option[ 'size' ] : null,
				'options'	 => isset( $option[ 'options' ] ) ? $option[ 'options' ] : '',
				'std'		 => isset( $option[ 'std' ] ) ? $option[ 'std' ] : ''
			)
			);
		}
	}

	// Creates settings in the options table
	register_setting( 'dgwt_nbsp_settings', 'dgwt_automatic_nbsp', 'dgwt_nbsp_settings_validation' );
}

add_action( 'admin_init', 'dgwt_nbsp_register_settings' );


/*
 * Create fields
 */

function dgwt_nbsp_get_registered_settings() {

	$dgwt_nbsp_settings = array(
		// Generalne ustawienia
		'general'	 => apply_filters( 'dgwt_nbsp_settings_general', array(
			'header1'			 => array(
				'id'	 => 'header1',
				'name'	 => '<strong>' . __( 'Automatic NBSP settings', 'automatic-nbsp' ) . '</strong>',
				'type'	 => 'header'
			),
			'words'				 => array(
				'id'	 => 'words',
				'name'	 => __( "Words or phrases: <br /> Each word or phrase on a new line", 'automatic-nbsp' ),
				'desc'	 => __( "Add <code>&#38;nbsp</code> after each word or phrase from the list.", 'automatic-nbsp' ),
				'type'	 => 'textarea',
				'std'	 => ''
			),
			'case_sensitive'	 => array(
				'id'	 => 'case_sensitive',
				'name'	 => __( "Case sensitive", 'automatic-nbsp' ),
				'desc'	 => __( "If enabled, you need to type variants of word manually. For example: 'and', 'AND', 'And' etc.", 'automatic-nbsp' ),
				'type'	 => 'checkbox',
				'std'	 => '0'
			),
			'before_punctuation' => array(
				'id'	 => 'before_punctuation',
				'name'	 => __( "Punctuation marks", 'automatic-nbsp' ),
				'desc'	 => __( "Add <code>&amp;nbsp;</code> before punctuation marks as", 'automatic-nbsp' ) . ' <code>!</code><code>?</code><code>:</code><code>;</code><code>%</code><code>«</code><code>»</code>',
				'type'	 => 'checkbox',
				'std'	 => '1'
			)
		)
		),
		'scope'		 => apply_filters( 'dgwt_nbsp_settings_general', array(
			'header2'	 => array(
				'id'	 => 'header2',
				'name'	 => '<strong>' . __( 'Scope', 'automatic-nbsp' ) . '</strong>',
				'type'	 => 'header'
			),
			'content'	 => array(
				'id'	 => 'content',
				'name'	 => __( "Content", 'automatic-nbsp' ),
				'desc'	 => __( "Add non-breaking spaces to contents", 'automatic-nbsp' ),
				'type'	 => 'checkbox',
				'std'	 => '1'
			),
			'title'		 => array(
				'id'	 => 'title',
				'name'	 => __( "Title", 'automatic-nbsp' ),
				'desc'	 => __( "Add non-breaking spaces to titles", 'automatic-nbsp' ),
				'type'	 => 'checkbox',
				'std'	 => '0'
			),
			'excerpt'	 => array(
				'id'	 => 'excerpt',
				'name'	 => __( "Excerpt", 'automatic-nbsp' ),
				'desc'	 => __( "Add non-breaking spaces to excerpts", 'automatic-nbsp' ),
				'type'	 => 'checkbox',
				'std'	 => '0'
			),
			'comment'	 => array(
				'id'	 => 'comment',
				'name'	 => __( "Comment text", 'automatic-nbsp' ),
				'desc'	 => __( "Add non-breaking spaces to comments", 'automatic-nbsp' ),
				'type'	 => 'checkbox',
				'std'	 => '0'
			),
			'widget'	 => array(
				'id'	 => 'widget',
				'name'	 => __( "Widget text", 'automatic-nbsp' ),
				'desc'	 => __( "Add non-breaking spaces to widgets", 'automatic-nbsp' ),
				'type'	 => 'checkbox',
				'std'	 => '0'
			),
			'acf'	 => array(
				'id'	 => 'acf',
				'name'	 => __( "Advanced Custom Fields", 'automatic-nbsp' ),
				'desc'	 => __( "Add non-breaking spaces to the ACF field types like: text, textarea and wysiwyg", 'automatic-nbsp' ),
				'type'	 => 'checkbox',
				'std'	 => '0'
			),
			'custom'	 => array(
				'id'	 => 'custom',
				'name'	 => __( "Custom", 'automatic-nbsp' ),
				'desc'	 => __( 'Use <code>&lt;?php auto_nbsp($content); ?&gt;</code> to print the custom text with the automatic <code>&amp;nbsp;</code>. Use <code>&lt;?php auto_nbsp($content, false); ?&gt;</code> to only return.', 'automatic-nbsp' ),
				'type'	 => 'info',
			)
		)
		)
	);

	return $dgwt_nbsp_settings;
}

/*
 * Walicaja pól formularza z ustawieniami
 */

function dgwt_nbsp_settings_validation( $input = array() ) {
	global $dgwt_nbsp_settings;

	if ( empty( $_POST[ '_wp_http_referer' ] ) ) {
		return $input;
	}

	// Odkodowanie _wp_http_referer do postaci zmiennych
	parse_str( $_POST[ '_wp_http_referer' ], $referrer );

	// Pobranie informacji o aktualnej zakładce
	$tab = isset( $referrer[ 'tab' ] ) ? $referrer[ 'tab' ] : 'general';

	// Pobranie białej listy wszystkich opcji utworzonych przez wtyczkę i rozszerzenia
	$settings = dgwt_nbsp_get_registered_settings();

	// W przypadku braku zmian wartości opcji, zwróci pustą tablice. 
	$input = $input ? $input : array();


	// Pętla po wszytskich opcjach wtyczki. 
	if ( !empty( $settings[ $tab ] ) ) {
		foreach ( $settings[ $tab ] as $key => $value ) {

			// Usuwa opcję z tablicy, gdy jest pusta. Przydaje się przy input typu checkbox
			if ( empty( $input[ $key ] ) ) {
				if ( isset( $dgwt_nbsp_settings[ $key ] ) ) {
					unset( $dgwt_nbsp_settings[ $key ] );
				}
			}
		}
	}


	$output = array_merge( $dgwt_nbsp_settings, $input );


	return $output;
}

/**
 * Tworzy elementy menu typu TABS
 */
function dgwt_nbsp_get_settings_tabs() {

	$tabs			 = array();
	$tabs[ 'general' ] = __( 'General', 'automatic-nbsp' );


	$tabs[ 'scope' ] = __( 'Scope', 'automatic-nbsp' );


	return apply_filters( 'dgwt_nbsp_settings_tabs', $tabs );
}

/**
 * Input type text Callback
 * Przetwarza opcje typu input text
 */
function dgwt_nbsp_text_callback( $args ) {
	global $dgwt_nbsp_settings;

	if ( isset( $dgwt_nbsp_settings[ $args[ 'id' ] ] ) )
		$value	 = $dgwt_nbsp_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	$size	 = ( isset( $args[ 'size' ] ) && !is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html	 = '<input type="text" class="' . $size . '-text" id="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']" name="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Info callback
 */
function dgwt_nbsp_info_callback( $args ) {

	$html = '<label>' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Input type checkbox Callback
 * Przetwarza opcje typu input text
 */
function dgwt_nbsp_checkbox_callback( $args ) {
	global $dgwt_nbsp_settings;

	$checked = isset( $dgwt_nbsp_settings[ $args[ 'id' ] ] ) ? checked( 1, $dgwt_nbsp_settings[ $args[ 'id' ] ], false ) : '';
	$html	 = '<input type="checkbox" id="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']" name="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']" value="1" ' . $checked . '/>';
	$html .= '<label for="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';
	echo $html;
}

/**
 * Input type textarea Callback
 * Przetwarza opcje typu textarea
 */
function dgwt_nbsp_textarea_callback( $args ) {
	global $dgwt_nbsp_settings;

	if ( isset( $dgwt_nbsp_settings[ $args[ 'id' ] ] ) )
		$value	 = $dgwt_nbsp_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	$size	 = ( isset( $args[ 'size' ] ) && !is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html	 = '<textarea class="large-text" cols="50" rows="5" id="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']" name="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Select
 * Przetwarza opcje typu select
 */
function dgwt_nbsp_select_callback( $args ) {
	global $dgwt_nbsp_settings;

	if ( isset( $dgwt_nbsp_settings[ $args[ 'id' ] ] ) )
		$value	 = $dgwt_nbsp_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	$html = '<select id="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']" name="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']"/>';

	foreach ( $args[ 'options' ] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="dgwt_automatic_nbsp[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Przetwarza opcje typu header
 */
function dgwt_nbsp_header_callback( $args ) {
	echo '<hr/>';
}

/**
 * Brak funkcji zwrotnej
 */
function dgwt_nbsp_missing_callback( $args ) {
	printf( __( 'Missing function callback <strong>%s</strong>', 'automatic-nbsp' ), $args[ 'id' ] );
}
