<?php
define( 'MYPROJECTNAME_VERSION', 1.0 );

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
		'primary'	=>	__( 'Primary Menu', 'myprojectname' )
	)
);

// Cleanup WP Menu html
function css_attributes_filter($var) {
     return is_array($var) ? array_intersect($var, array('current-menu-item')) : '';
}
add_filter('nav_menu_css_class', 'css_attributes_filter', 100, 1);
add_filter('page_css_class', 'css_attributes_filter', 100, 1);

/*-----------------------------------------------------------------------------------*/
/* Activate sidebar for Wordpress use
/*-----------------------------------------------------------------------------------*/
function myprojectname_register_sidebars() {
	register_sidebar(array(				
		'id' => 'sidebar', 					
		'name' => 'Sidebar',				
		'description' => 'Take it on the side...', 
		'before_widget' => '<div>',	
		'after_widget' => '</div>',	
		'before_title' => '<h3 class="side-title">',	
		'after_title' => '</h3>',		
		'empty_title'=> '',	
	));
} 
// adding sidebars to Wordpress (these are created in functions.php)
add_action( 'widgets_init', 'myprojectname_register_sidebars' );

/*-----------------------------------------------------------------------------------*/
/* Custom Post Types => Custom
/*-----------------------------------------------------------------------------------*/
function create_post_type() { 
  register_post_type('curstom', array(
    'label' => 'Customs',
    'singular_label' => 'Custom',
    'public' => true
  ));
}
add_action( 'init', 'create_post_type' );

/*-----------------------------------------------------------------------------------*/
/* Ajout des "images à la une" dans les articles et les pages
/*-----------------------------------------------------------------------------------*/
add_theme_support( 'post-thumbnails' , array('post', 'page')); 

/*-----------------------------------------------------------------------------------*/
/* Limit excerpt length
/*-----------------------------------------------------------------------------------*/
function new_excerpt_length($length) {
 return 19;
}
add_filter('excerpt_length', 'new_excerpt_length');

/*-----------------------------------------------------------------------------------*/
/* Remove default WYSIWYG editor in Custom
/*-----------------------------------------------------------------------------------*/
function hide_editor() {
	if(isset($_GET['post']) || isset($_POST['post_ID'])){
    	$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
    }
    if( !isset( $post_id ) ) return;
    $template_file = get_post_meta($post_id, '_wp_page_template', true);
    
    if($template_file == 'custom.php'){
        remove_post_type_support('page', 'editor');
    }
}
add_action( 'admin_init', 'hide_editor' );

/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles and Scripts
/*-----------------------------------------------------------------------------------*/

function mysascredit_scripts()  { 
	// header
	wp_enqueue_style( 'myprojectname-style', get_template_directory_uri() . '/css/style.css', '10000', 'all' );
	
	// footer
	wp_enqueue_script( 'myprojectname-jquery', get_template_directory_uri() . '/js/jquery-1.11.1.min.js', array(), MYPROJECTNAME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'myprojectname_scripts' );