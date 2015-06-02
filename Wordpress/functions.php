<?php
define( 'MYPROJECTNAME_VERSION', 1.0 );

/*-----------------------------------------------------------------------------------*/
/* General
/*-----------------------------------------------------------------------------------*/
// Theme support
add_theme_support( 'html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'widgets') );
add_theme_support( 'post-thumbnails', array( 'post' )); 

// Feed
add_theme_support( 'automatic-feed-links' );
function remove_comments_rss( $for_comments ){ return; }
add_filter('post_comments_feed_link', 'remove_comments_rss');

// Admin bar
show_admin_bar(false);

/*-----------------------------------------------------------------------------------*/
/* Hide Wordpress version and stuff for security, hide login errors
/*-----------------------------------------------------------------------------------*/
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');

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
/* Résultats de recherche: uniquement des articles
/*-----------------------------------------------------------------------------------*/
function search_filter($query) {
  if($query->is_main_query() && $query->is_search){
    $query->set('post_type', 'post');
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
  register_post_type('custom', array(
    'label' => 'Customs',
    'singular_label' => 'Custom',
    'public' => true,
    'publicly_queryable' => false,
    'query_var' => false,
    'menu_icon' => 'dashicons-calendar-alt',
    'supports' => array('title', 'editor', 'thumbnail')
  ));
}
add_action( 'init', 'create_post_type' );

function create_taxonomy(){
  register_taxonomy('cats', array('custom'), array(
    'hierarchical' => true,
    'label' => 'Cats',
    'singular_label' => 'Cat'
  ));
}
add_action( 'init', 'create_taxonomy' );

/*-----------------------------------------------------------------------------------*/
/* Nouvelle taille d'image
/*-----------------------------------------------------------------------------------*/ 
function myproject_thumbnail_sizes() {
    add_image_size( 'teacher-thumb', 220, 220, true );
}
add_action( 'after_setup_theme', 'myproject_thumbnail_sizes' );

/*-----------------------------------------------------------------------------------*/
/* Admin
/*-----------------------------------------------------------------------------------*/
// Remove default link around images
function imagelink_setup() {
    $image_set = get_option( 'image_default_link_type' );
    if($image_set !== 'none'){ update_option('image_default_link_type', 'none'); }
}
add_action('admin_init', 'imagelink_setup', 10);

// New button wysiwyg
if(!function_exists('avignon_button')){
    function avignon_button( $buttons ) {
        array_unshift( $buttons, 'styleselect' );
        return $buttons;
    }
}
add_filter( 'mce_buttons_2', 'avignon_button' );
 
if(!function_exists('avignon_mce_before_init')){
    function avignon_mce_before_init( $styles ) {
        $style_formats = array (
            array(
                'title' => 'Button',
                'selector' => 'a',
                'classes' => 'btn'
            )
        );
        $styles['style_formats'] = json_encode( $style_formats );
        return $styles;
    }
}
add_filter( 'tiny_mce_before_init', 'avignon_mce_before_init' );

if(!function_exists('avignon_init_editor_styles')){
    add_action( 'after_setup_theme', 'avignon_init_editor_styles' );
    function avignon_init_editor_styles() {
        add_editor_style();
    }
}

// Custom posts in the dashboard
function add_right_now_custom_post() {
    $args = array(
        '_builtin' => false
    );
    $output = 'objects';
    $operator = 'and';
    $post_types = get_post_types($args , $output , $operator);
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
/* Markup gallery
/*-----------------------------------------------------------------------------------*/
function my_post_gallery( $output, $attr) {
    global $post, $wp_locale;
    static $instance = 0;
    $instance++;

    if( isset($attr['orderby']) ){
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if( !$attr['orderby'] ) unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => '',
        'icontag'    => '',
        'captiontag' => '',
        'columns'    => 3,
        'size'       => 'presse-logo',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if( 'RAND' == $order ) $orderby = 'none';

    if( !empty($include) ){
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach( $_attachments as $key => $val ){
            $attachments[$val->ID] = $_attachments[$key];
        }
    }elseif ( !empty($exclude) ){
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }else{
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if( empty($attachments) ) return '';

    $selector = "gallery-{$instance}";
    $output = "<li id='$selector'>";

    foreach( $attachments as $id => $attachment ){
        $output .= '<div>' . wp_get_attachment_image($id, $size) . '</div>';
    }

    $output .= "</li>";
    return $output;
}
add_filter( 'post_gallery', 'my_post_gallery', 10, 2 );

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
	wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery-1.11.1.min.js', array(), MYPROJECTNAME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'myprojectname_scripts' );