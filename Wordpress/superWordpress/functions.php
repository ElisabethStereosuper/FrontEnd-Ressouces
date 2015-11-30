<?php define( 'SUPER_VERSION', 1.0 );

add_filter( 'auto_update_plugin', '__return_true' );
show_admin_bar(false);

/*-----------------------------------------------------------------------------------*/
/* General
/*-----------------------------------------------------------------------------------*/

// Theme support
add_theme_support( 'html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'widgets') );
add_theme_support( 'post-thumbnails' ); 

// Feed
add_theme_support( 'automatic-feed-links' );
function remove_comments_rss( $for_comments ){ return; }
add_filter('post_comments_feed_link', 'remove_comments_rss');

// Disable Tags
function super_unregister_tags() {
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'super_unregister_tags');


/*-----------------------------------------------------------------------------------*/
/* Hide Wordpress version and stuff for security, hide login errors
/*-----------------------------------------------------------------------------------*/

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');

add_filter('login_errors', create_function('$a', "return null;"));

function remove_comment_author_class( $classes ){
	foreach( $classes as $key => $class ){
		if(strstr($class, "comment-author-")) unset( $classes[$key] );
	}
	return $classes;
}
add_filter( 'comment_class' , 'remove_comment_author_class' );


/*-----------------------------------------------------------------------------------*/
/* Menus
/*-----------------------------------------------------------------------------------*/

register_nav_menus( 
	array(
		'primary' => 'Primary Menu', 
	)
);

// Cleanup WP Menu html
function css_attributes_filter($var) {
     return is_array($var) ? array_intersect($var, array('current-menu-item', 'current_page_parent')) : '';
}
add_filter('nav_menu_css_class', 'css_attributes_filter', 10, 1);
add_filter('page_css_class', 'css_attributes_filter', 10, 1);


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
		'before_title' => '',	
		'after_title' => '',		
		'empty_title'=> ''
	));
} 
add_action( 'widgets_init', 'super_register_sidebars' );


/*-----------------------------------------------------------------------------------*/
/* Admin
/*-----------------------------------------------------------------------------------*/

// Enlever le lien par défaut autour des images
function super_imagelink_setup(){
	$image_set = get_option( 'image_default_link_type' );
    if($image_set !== 'none')
        update_option('image_default_link_type', 'none');
}
add_action('admin_init', 'super_imagelink_setup', 10);

// Custom posts in the dashboard
function add_right_now_custom_post() {
    $post_types = get_post_types(array( '_builtin' => false ) , 'objects' , 'and');
    foreach($post_types as $post_type){
        $cpt_name = $post_type->name;
        if($cpt_name != 'acf'){
            $num_posts = wp_count_posts($post_type->name);
            $num = number_format_i18n($num_posts->publish);
            $text = _n($post_type->labels->name, $post_type->labels->name , intval($num_posts->publish));
            echo '<li class="'. $cpt_name .'-count"><tr><a class="'.$cpt_name.'" href="edit.php?post_type='.$cpt_name.'"><td></td>' . $num . ' <td>' . $text . '</td></a></tr></li>';
        }
    }
}
add_action('dashboard_glance_items', 'add_right_now_custom_post');


/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles and Scripts
/*-----------------------------------------------------------------------------------*/

function super_scripts()  { 
		// header
		wp_enqueue_style( 'super-style', get_template_directory_uri() . '/css/style.css', array(), SUPER_VERSION );
		wp_enqueue_script( 'super-modernizr', get_template_directory_uri() . '/js/modernizr-min.js', array(), null);
		
		// footer
	    wp_deregister_script('jquery');
		wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery-1.11.1.min.js', array(), null, true );
  
}
add_action( 'wp_enqueue_scripts', 'super_scripts' );