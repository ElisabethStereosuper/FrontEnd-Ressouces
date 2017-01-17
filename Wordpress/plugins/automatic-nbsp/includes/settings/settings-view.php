<?php
/**
 * Settings
*/

if ( !defined( 'ABSPATH' ) ) exit;


function dgwt_nbsp_options_page() {
	global $dgwt_nbsp_options;

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], dgwt_nbsp_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';
        
        // Data od predefined  conjunctions
        $langs = dgwt_nbsp_get_phrases_by_lang();
        
	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( dgwt_nbsp_get_settings_tabs() as $id => $name ) {
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab' => $id
				) );

				$active = $active_tab == $id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $name );
				echo '</a>';
			}
			?>
		</h2>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
				settings_fields( 'dgwt_nbsp_settings' );
                                //do_settings_fields( 'dgwt_nbsp_settings_general', 'dgwt_nbsp_settings_general' );
				do_settings_fields( 'dgwt_nbsp_settings_' . $active_tab, 'dgwt_nbsp_settings_' . $active_tab );
				?>
                                    
                                   
				</table>
                            <?php if($active_tab === 'general'): ?>
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th scope="row"><?php _e('Import words', 'automatic-nbsp') ?></th>
                                        <td>
                                            <select id="dgwt-nbsp-language" name="dgwt-nbsp-language">
                                                <option>-</option>
                                                <?php foreach ($langs as $lang){
                                                    echo '<option value="' . $lang['code'] . '">' . $lang['name'] . '</option>';
                                                } ?>
                                            </select>
                                            <?php foreach ($langs as $lang){
                                                echo '<textarea id="dgwt-nbsp-' . $lang['code'] . '" name="dgwt-nbsp-' . $lang['code'] . '" class="dgwt-nbsp-import-textarea">';
                                                
                                            foreach ($lang['phrases'] as $phrase){
                                                echo $phrase . "\r\n";
                                            }
                                                
                                                echo '</textarea>';
                                            } ?>
                                            <button id="dgwt-nbsp-import" class="button"><?php _e('Import', 'automatic-nbsp'); ?></button>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <?php endif; ?>
                                
                            
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}
