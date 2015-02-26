<?php define( 'SUPER_VERSION', 1.0 );

/*-----------------------------------------------------------------------------------*/
/* Add Rss feed support to Head section
/*-----------------------------------------------------------------------------------*/
add_theme_support( 'automatic-feed-links' );

/*-----------------------------------------------------------------------------------*/
/* Hide Wordpress version and stuff for security, hide login errors
/*-----------------------------------------------------------------------------------*/
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

add_filter('login_errors', create_function('$a', "return null;"));

function remove_comment_author_class( $classes ) {
	foreach( $classes as $key => $class ) {
		if(strstr($class, "comment-author-")) {
			unset( $classes[$key]
 );
		}
	}
	return $classes;
}
add_filter( 'comment_class' , 'remove_comment_author_class' );

/*-----------------------------------------------------------------------------------*/
/* Register main menu for Wordpress use
/*-----------------------------------------------------------------------------------*/
register_nav_menus( 
	array(
		'primary'	=>	__( 'Primary Menu', 'naked' ), 
	)
);

// Cleanup WP Menu html
function css_attributes_filter($var) {
     return is_array($var) ? array_intersect($var, array('current-menu-item', 'current_page_parent')) : '';
}
add_filter('nav_menu_css_class', 'css_attributes_filter', 100, 1);
add_filter('page_css_class', 'css_attributes_filter', 100, 1);

/*-----------------------------------------------------------------------------------*/
/* Activate sidebar for Wordpress use
/*-----------------------------------------------------------------------------------*/
function super_register_sidebars() {
	register_sidebar(array(				
		'id' => 'sidebar', 					
		'name' => 'Sidebar',				
		'description' => 'Take it on the side...', 
		'before_widget' => '',	
		'after_widget' => '',	
		'before_title' => '<h3>',	
		'after_title' => '</h3>',		
		'empty_title'=> ''
	));
} 
add_action( 'widgets_init', 'super_register_sidebars' );

/*-----------------------------------------------------------------------------------*/
/* Enlever le lien par défaut autour des images
/*-----------------------------------------------------------------------------------*/
function wpb_imagelink_setup() {
	$image_set = get_option( 'image_default_link_type' );
    if ($image_set !== 'none') {
        update_option('image_default_link_type', 'none');
    }
}
add_action('admin_init', 'wpb_imagelink_setup', 10);

/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles and Scripts
/*-----------------------------------------------------------------------------------*/

function super_scripts()  { 
		// header
		wp_enqueue_style( 'super-style', get_template_directory_uri() . '/css/style.css', '10000', 'all' );
		wp_enqueue_script( 'super-modernizr', get_template_directory_uri() . '/js/modernizr-min.js', array(), SUPER_VERSION);
		
		// footer
	    wp_deregister_script('jquery');
		wp_enqueue_script( 'super-jquery', get_template_directory_uri() . '/js/jquery-1.11.1.min.js', array(), SUPER_VERSION, true );
  
}
add_action( 'wp_enqueue_scripts', 'super_scripts' );