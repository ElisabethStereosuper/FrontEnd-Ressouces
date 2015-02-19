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
/* Détecter s'il s'agit d'un article (exclusion des autres types de post des 
/* résultats de recherche)
/*-----------------------------------------------------------------------------------*/
function search_filter($query) {
  if ( $query->is_main_query() ) {
    if ($query->is_search) {
      $query->set('post_type', 'post');
    }
  }
}
add_action('pre_get_posts','search_filter');

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
     return is_array($var) ? array_intersect($var, array('current-menu-item', 'current_page_parent')) : '';
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
add_action( 'widgets_init', 'myprojectname_register_sidebars' );

/*-----------------------------------------------------------------------------------*/
/* Custom Post Types => Custom
/*-----------------------------------------------------------------------------------*/
function create_post_type() { 
  register_post_type('curstom', array(
    'label' => 'Customs',
    'singular_label' => 'Custom',
    'public' => true,
    'supports' => array('title', 'editor', 'thumbnail')
  ));
}
add_action( 'init', 'create_post_type' );

/*-----------------------------------------------------------------------------------*/
/* Ajout des "images à la une" dans les articles et les pages
/*-----------------------------------------------------------------------------------*/
add_theme_support( 'post-thumbnails' , array('post', 'page')); 

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
/* Add a class to Prev and Next posts links
/*-----------------------------------------------------------------------------------*/
function nextposts_link_attributes() {
    return 'class="btnIt"';
}
add_filter('next_posts_link_attributes', 'nextposts_link_attributes');

function prevposts_link_attributes() {
    return 'class="btnItPrev"';
}
add_filter('previous_posts_link_attributes', 'prevposts_link_attributes');

/*-----------------------------------------------------------------------------------*/
/* Custom excerpt
/*-----------------------------------------------------------------------------------*/
function improved_trim_excerpt($text) {
    global $post;
    if ( '' == $text ) {
        $text = get_the_content('');
        $text = apply_filters('the_content', $text);
        $text = strip_tags($text, '<p>');
        $excerpt_length = 43;
        $words = explode(' ', $text, $excerpt_length + 1);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            array_push($words, '...');
            $text = implode(' ', $words);
        }
    }
    return $text;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');

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
/* Do not display admin bar
/*-----------------------------------------------------------------------------------*/
function my_function_admin_bar(){
    return false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');

/*-----------------------------------------------------------------------------------*/
/* WPML
/*-----------------------------------------------------------------------------------*/
// Languages Switcher
function lang_switcher(){
    if (!class_exists('SitePress')) return '';
    $languages = icl_get_languages('skip_missing=0&orderby=code&order=desc');
    $actives = '';
    if (!empty($languages)) {
        echo '<ul id="menu-langues">';
        foreach ($languages as $l){
            $actives .= '<li'.($l['active']?' class="active"':'').'><a href="' . $l['url'] . '" data-lang="' . $l['language_code'] . '">' . $l['language_code'] . '</a></li>';
        }
        echo $actives . '</ul>';
    }
}

// Clean WPML head
remove_action( 'wp_head', array($sitepress, 'meta_generator_tag' ) );
define('ICL_DONT_LOAD_NAVIGATION_CSS', true);
define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
define('ICL_DONT_LOAD_LANGUAGES_JS', true);

/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles and Scripts
/*-----------------------------------------------------------------------------------*/

function mysascredit_scripts()  { 
	// header
	wp_enqueue_style( 'myprojectname-style', get_template_directory_uri() . '/css/style.css', '10000', 'all' );
	
	// footer
    wp_deregister_script('jquery');
	wp_enqueue_script( 'myprojectname-jquery', get_template_directory_uri() . '/js/jquery-1.11.1.min.js', array(), MYPROJECTNAME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'myprojectname_scripts' );