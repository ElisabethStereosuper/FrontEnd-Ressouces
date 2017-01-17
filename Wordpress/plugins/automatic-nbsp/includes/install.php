<?php

/**
 * Install plugin actions
 */
if (!defined('ABSPATH'))
    exit;

/**
 * Install
 */
function dgwt_nbsp_install() {
    global $dgwt_nbsp_settings;

    $options = array();

    // Default options values
    foreach (dgwt_nbsp_get_registered_settings() as $tab => $settings) {

        foreach ($settings as $option) {

            if ('checkbox' == $option['type'] && !empty($option['std'])) {
                $options[$option['id']] = '1';
            }

            if ('text' == $option['type'] && !empty($option['std'])) {
                $options[$option['id']] = $option['std'];
            }

            if ('textarea' == $option['type'] && !empty($option['std'])) {
                $options[$option['id']] = $option['std'];
            }
        }
    }

    // Copy old options values to new option system
    $old_options = get_option('dg_automatic_nbsp');
    if ($old_options) {

        $new_option = '';

        $old_words = explode(',', $old_options);
        if (is_array($old_words) && count($old_words) > 1) {

            foreach ($old_words as $old_word) {
                $new_option .= $old_word . "\r\n";
            }
        }

        $options['words'] = $new_option;

        delete_option('dg_automatic_nbsp');
    }

    // Load the default list of phrases by language 
    if (!isset($dgwt_nbsp_settings['words']) || empty($dgwt_nbsp_settings['words'])) {
        
        $options['words'] = '';
        
        $locale = get_locale();
        $current_lang = substr($locale, 0, 2);

        $langs = dgwt_nbsp_get_phrases_by_lang();


        foreach ($langs as $lang) {

            if ($lang['code'] === $current_lang) {

                foreach ($lang['phrases'] as $phrase) {
                    $options['words'] .= $phrase . "\r\n";
                }
            }
        }
    }

    update_option('dgwt_automatic_nbsp', array_merge($dgwt_nbsp_settings, $options));

    // Last plugin version
    update_option('dgwt_nbsp_version', DGWT_NBSP_VERSION);
}

register_activation_hook(DGWT_NBSP_FILE, 'dgwt_nbsp_install');