<?php

/*-----------------------------------------------------------------------------------*/
/* Menus
/*-----------------------------------------------------------------------------------*/
// Add a class to li if has subMenu
function sub_menu( $sorted_menu_items, $args ) {
    if( 'primary' === $args->theme_location ){
        $last_top = 0;
        foreach( $sorted_menu_items as $key => $obj ){
            if( 0 == $obj->menu_item_parent ){
                $last_top = $key;
            }else{
                $sorted_menu_items[$last_top]->classes['dropdown'] = 'hasSubMenu';
            }
        }
        return $sorted_menu_items;
    }else{
      return $sorted_menu_items;
    }
}
add_filter( 'wp_nav_menu_objects', 'sub_menu', 10, 2 );

// Cleanup primary
function css_attributes_filter($var, $item, $args) {
    if( 'primary' === $args->theme_location ){
        return is_array($var) ? array_intersect($var, array('current-menu-item', 'current_page_parent', 'hasSubMenu')) : '';
   }else{
        return is_array($var) ? array_intersect($var, array()) : '';
   }
}
add_filter('nav_menu_css_class', 'css_attributes_filter', 10, 3);
add_filter('page_css_class', 'css_attributes_filter', 10, 3);

/*-----------------------------------------------------------------------------------*/
/* Résultats de recherche: uniquement des articles
/*-----------------------------------------------------------------------------------*/
function search_filter($query){
    if(/*$query->is_main_query() && */$query->is_search){
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter( 'pre_get_posts', 'search_filter' );

// Limit title lenth
function think_title_length( $title ){
    $max = 92;
    if( strlen($title) > $max ){
        // return substr( $title, 0, $max ) . " &hellip;";
        return explode("\n", wordwrap($title, $max))[0] . " &hellip;";
    }else{
        return $title;
    }
}

// Comments form error
function think_die_handler($message, $title='', $args=array()){
    if(empty($_POST['errorcomment'])){
        $_POST['errorcomment'] = $message;
    }

    if(!session_id()){
        session_start();
    }

    $_SESSION = $_POST;
    session_write_close();

    $url = strtok(wp_get_referer(), '?');
    header('Location: ' . $url . '?error=true#commentform');
    die();
}
function get_think_die_handler(){
    return 'think_die_handler';
}
add_filter('wp_die_handler', 'get_think_die_handler');
// + in header.php :
// if(!session_id()){
//     session_start();
// }

// $_POST = $_SESSION;
// global $errorComment;
// $errorComment = isset($_POST['errorcomment']) ? $_POST['errorcomment'] : false;

// if(!isset($_GET['error'])){
//     if($errorComment){
//         $_POST['errorcomment'] = false;
//     }
//     session_destroy();
// }

/*-----------------------------------------------------------------------------------*/
/* Custom Post Types => Custom
/*-----------------------------------------------------------------------------------*/
function create_post_type(){
  register_post_type('custom', array(
    'label' => 'Customs',
    'labels' => array(
        'singular_name' => 'Custom stuff',
        'menu_name' => 'Custom'
    ),
    'singular_label' => 'Custom',
    'public' => true,
    'publicly_queryable' => false,
    'query_var' => false,
    'menu_icon' => 'dashicons-calendar-alt',
    'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions')
  ));
}
add_action( 'init', 'create_post_type' );

function create_taxonomy(){
  register_taxonomy('cats', array('custom'), array(
    'hierarchical' => true,
    'label' => 'Cats',
    'singular_label' => 'Cat',
    'show_admin_column' => true
  ));
}
add_action( 'init', 'create_taxonomy' );

// Define a page as the parent of post type
function super_save_custom_post_parent($data, $postarr){
    if( $postarr['post_type'] === 'custom' ){
        $data['post_parent'] = CUSTOM_PAGE_ID;
    }

    return $data;
}
add_action( 'wp_insert_post_data', 'super_save_custom_post_parent', '99', 2 );

// Define a page as the parent of post type in the menu
function super_correct_menu_parent_class($classes, $item){
    global $post;
    if(!$post) return $classes;

    $postType = get_post_type();
    if($postType == 'custom'){
        $item->object_id == $post->post_parent ? $classes[] = 'current_page_parent' : $classes = [];
    }

    return $classes;
}
add_filter( 'nav_menu_css_class', 'super_correct_menu_parent_class', 10, 2 );

/* Override posts per page settings */
function set_archive_number_posts( $query ){
    if( is_post_type_archive( 'custom' ) && !is_admin() ){
        $query->set( 'posts_per_page', 5 );
        return;
    }
}
add_action( 'pre_get_posts', 'set_archive_number_posts', 1 );

/*-----------------------------------------------------------------------------------*/
/* Custom ACF widgets
/*-----------------------------------------------------------------------------------*/
class Img_Widget extends ACF_Widget{
    function Img_Widget() {
        parent::__construct('Img_Widget', 'Wisembly - Lien avec image', array('description' => 'Affiche un lien vers un autre site avec une image pour contenu.'));
        $this->acf_group_id = 2035;
    }
    function widget($args, $instance){

        $acf_key = "widget_" . $this->id_base . "_" . $this->number;

        if(get_field('blogSidebarLien', $acf_key) && get_field('blogSidebarImg', $acf_key)){ ?>
            <section class='blogImg'>
                <a href='<?php the_field('blogSidebarLien', $acf_key) ?>' <?php if(get_field('blogSidebarTxt', $acf_key)){ ?> title='<?php the_field('blogSidebarTxt', $acf_key) ?>' <?php } ?> target='_blank'>
                    <?php echo wp_get_attachment_image( get_field('blogSidebarImg', $acf_key), 'medium' ); ?>
                </a>
            </section>
        <?php }
    }
}
$GLOBALS["acf_widget_types"][] = "Img_Widget";
register_widget('Img_Widget');

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

// Remove the classes on images
add_filter( 'get_image_tag_class', '__return_empty_string' );

// Change markup images in content
function think_insert_image($html, $id, $caption, $title, $align, $url) {
    $html5 = '<div class="post-img align' . $align . '">' . $html . '</div>';
    return $html5;
}
add_filter( 'image_send_to_editor', 'think_insert_image', 10, 9 );

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

        // Remove h1 and code
        $styles['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6';
        // Let only the colors you want
        $styles['textcolor_map'] = '[' . "'000000', 'Black', '979797', 'Grey', '932712', 'Red'" . ']';
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

// Show parent-child relationship for categories in the wordpress admin
function taxonomy_relationship($args){
    $args['checked_ontop'] = false;
    return $args;
}
add_filter('wp_terms_checklist_args', 'taxonomy_relationship');

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
/* Shortcode
/*-----------------------------------------------------------------------------------*/
function new_shortcode(){
    $code = '<div>NEW</div>';
    return $code;
}
add_shortcode( 'new', 'new_shortcode' );

/*-----------------------------------------------------------------------------------*/
/* Custom excerpt
/*-----------------------------------------------------------------------------------*/
function custom_wp_trim_excerpt($wpse_excerpt) {
    $raw_excerpt = $wpse_excerpt;

    if( '' == $wpse_excerpt ){
        $wpse_excerpt = get_the_content('');
        $wpse_excerpt = strip_shortcodes( $wpse_excerpt );
        $wpse_excerpt = apply_filters('the_content', $wpse_excerpt);
        $wpse_excerpt = str_replace(']]>', ']]&gt;', $wpse_excerpt);
        $wpse_excerpt = strip_tags($wpse_excerpt, '<em>,<i>,<a>,<strong>,<b>');

        $excerpt_length = apply_filters('excerpt_length', 58);
        $tokens = array();
        $excerptOutput = '';
        $count = 0;

        preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens);

        foreach($tokens[0] as $token){

            if($count >= $excerpt_length /*&& preg_match('/[\,\;\?\.\!]\s*$/uS', $token)*/){ //pour arreter l'extrait a un point ou une virgule, etc
                $excerptOutput .= trim($token);
                break;
            }

            $count++;
            $excerptOutput .= $token;
        }

        $wpse_excerpt = trim(force_balance_tags($excerptOutput));

        $excerpt_end = ' <a href="'. get_the_permalink() .'" class="lienVert" title="'. __('Lire', 'wisembly') . ' ' . get_the_title() .'">...'. __('Lire la suite', 'wisembly') .'</a>';
        $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);
        $wpse_excerpt .= $excerpt_more;

        return $wpse_excerpt;

    }
    return apply_filters('custom_wp_trim_excerpt', $wpse_excerpt, $raw_excerpt);
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'custom_wp_trim_excerpt');

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
	wp_enqueue_style( 'myprojectname-style', get_template_directory_uri() . '/css/style.css', array(), MYPROJECTNAME_VERSION );

	// footer
    wp_deregister_script('jquery');
	wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery-1.11.1.min.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'myprojectname_scripts' );
