<?php

if ( !defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', 'dgwt_nbsp_admin_menu');

function dgwt_nbsp_admin_menu() {

    add_submenu_page('options-general.php', __('Automatic NBSP options', 'automatic-nbsp'), __('Automatic NBSP', 'automatic-nbsp'), 'manage_options', 'dgwt_nbsp_options', 'dgwt_nbsp_options_page');
    
}

?>