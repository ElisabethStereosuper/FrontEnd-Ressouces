<?php

/*
  Plugin Name: Automatic NBSP
  Description: Automatically adds a non-breaking space (&nbsp) in the content.
  Version: 1.5.3
  Author: Damian Góra
  Author URI: http://damiangora.com
  License: GPLv2 or later
 */

/*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */


if ( !defined( 'ABSPATH' ) )
	exit;

class Webtroter_Automatic_NBSP {

	private static $instance;

	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof Webtroter_Automatic_NBSP ) ) {
			self::$instance = new Webtroter_Automatic_NBSP;
			self::$instance->constants();
			self::$instance->includes();
			self::$instance->load_textdomain();
		}
		return self::$instance;
	}

	/**
	 * Constants
	 */
	private function constants() {

		// Wersja wtyczki
		define( 'DGWT_NBSP_VERSION', '1.5.3' );


		// Nazwa wtyczki
		define( 'DGWT_NBSP_NAME', 'Automatic NBSP' );


		// Ścieżka do wtyczki ( serwer )
		define( 'DGWT_NBSP_DIR', plugin_dir_path( __FILE__ ) );


		// Ścieżka do template ( serwer )
		define( 'DGWT_NBSP_INCLUDES', plugin_dir_path( __FILE__ ) . '/includes' );


		// Ścieżka do wtyczki ( URL )
		define( 'DGWT_NBSP_URL', plugin_dir_url( __FILE__ ) );


		// Główny plik wtyczki
		define( 'DGWT_NBSP_FILE', __FILE__ );
	}

	/*
	 * Joins the necessary files
	 */

	private function includes() {

		global $dgwt_nbsp_settings;



		require_once DGWT_NBSP_DIR . 'includes/settings/register-settings.php';

		$dgwt_nbsp_settings = get_option( 'dgwt_automatic_nbsp' );

		if ( empty( $dgwt_nbsp_settings ) ) {
			$dgwt_nbsp_settings = array();
		}

		require_once DGWT_NBSP_DIR . 'includes/functions.php';

		if ( is_admin() ) {

			require_once DGWT_NBSP_DIR . 'includes/install.php';
			require_once DGWT_NBSP_DIR . 'includes/admin-menu.php';
			require_once DGWT_NBSP_DIR . 'includes/settings/settings-view.php';
		}

		require_once DGWT_NBSP_DIR . 'includes/filters.php';
		require_once DGWT_NBSP_DIR . 'includes/integrations/acf.php';
		require_once DGWT_NBSP_DIR . 'includes/scripts.php';
	}

	/*
	 * Languages textdomain
	 */

	public function load_textdomain() {

		$lang_dir = dirname( plugin_basename( DGWT_NBSP_FILE ) ) . '/languages';
		load_plugin_textdomain( 'automatic-nbsp', false, $lang_dir );
	}

}

// Init
Webtroter_Automatic_NBSP::instance();
?>