/* globals jQuery: false, ajaxurl: false, wp: false, SecuPressi18nModules: false, secupressIsSpaceOrEnterKey: false, swal2: false */
// Global vars =====================================================================================
var SecuPress = {
	doingAjax:           {},
	deletedRowColor:     "#FF9966",
	addedRowColor:       "#CCEEBB",
	swal2Defaults:        {
		title:             SecuPressi18nModules.confirmTitle,
		confirmButtonText: SecuPressi18nModules.confirmText,
		cancelButtonText:  SecuPressi18nModules.cancelText,
		type:              "warning",
		allowOutsideClick: true,
		customClass:       "wpmedia-swal2 secupress-swal2"
	},
	swal2ConfirmDefaults: {
		showCancelButton:  true,
		closeOnConfirm:    false
	},
	expandButton: '<p class="secupress-expand-trigger-container"><button class="secupress-expand-trigger" type="button"><span class="secupress-expand-txt">' + SecuPressi18nModules.expandTextOpen + '</span><i class="secupress-icon-angle-down" aria-hidden="true"></i></button></p>'
};

/**
 * Show swal2 message if no scan done yet
 */
if ( SecuPressi18nModules.alreadyScanned === 0 ) {
	var modal_title = '<span class="secupress-swal-header-imaged"><img src="' + SecuPressi18nModules.firstScanImage + '" alt="" width="113" height="113"></span>',
		modal_content;

	modal_content  = '<p class="secupress-text-baseup secupress-mt1 secupress-mb1 secupress-primary secupress-bold">';
		modal_content += SecuPressi18nModules.firstScanTitle;
	modal_content += '</p>';
	modal_content += '<p class="secupress-text-base secupress-mt2 secupress-mb2">';
		modal_content += SecuPressi18nModules.firstScanText;
	modal_content += '</p>';
	modal_content += '<p class="secupress-mt1 secupress-mb1">';
		modal_content += '<a class="secupress-button secupress-button-primary secupress-button-scan shadow" href="' + SecuPressi18nModules.firstScanURL + '">';
			modal_content += '<span class="icon">';
				modal_content += '<i class="secupress-icon-radar" aria-hidden="true"></i>';
			modal_content += '</span>';
			modal_content += '<span class="text">';
				modal_content += SecuPressi18nModules.firstScanButton;
			modal_content += '</span>';
		modal_content += '</a>';
	modal_content += '</p>';

	swal2( jQuery.extend( {}, SecuPress.swal2Defaults, {
		title: modal_title,
		html: modal_content,
		type:  null,
		width: 400,
		showConfirmButton: false,
		showCloseButton: true,
		showCancelButton: false,
		customClass: 'wpmedia-swal2 secupress-swal2 secupress-swal-dark-header secupress-text-center'
	} ) );
}


// Tools ===========================================================================================
/**
 * Disable a button that calls an ajax action.
 * - Add a "working" class, so that the spinner can be displayed.
 * - Add a "aria-disabled" attribute.
 * - If it's a link: add a "disabled" attribute. If it's a button or input: add a "disabled" attribute.
 * - Change the button text if a "data-loading-i18n" attribute is present.
 * - Use `wp.a11y.speak` if a text is provided.
 * - Set a `SecuPress.doingAjax` attribute to `true`.
 *
 * @since 1.0
 *
 * @param (object) $button jQuery object of the button.
 * @param (string) speak   Text for `wp.a11y.speak`.
 * @param (string) ajaxID  An identifier used for `SecuPress.doingAjax`. Default: "global".
 */
function secupressDisableAjaxButton( $button, speak, ajaxID ) {
	var text     = $button.attr( "data-loading-i18n" ),
		isButton = $button.get( 0 ).nodeName.toLowerCase(),
		value;

	ajaxID = undefined !== ajaxID ? ajaxID : "global";
	SecuPress.doingAjax[ ajaxID ] = true;
	isButton = isButton === "button" || isButton === "input";

	if ( undefined !== text && text ) {
		if ( isButton ) {
			value = $button.val();
			if ( undefined !== value && value ) {
				$button.val( text );
			} else {
				if ( $button.find('.text').length ) {
					$button.find('.text').text( text );
				} else {
					$button.text( text );
				}
			}
		} else {
			if ( $button.find('.text').length ) {
				$button.find('.text').text( text );
			} else {
				$button.text( text );
			}
		}

		if ( undefined === speak || ! speak ) {
			speak = text;
		}
	}

	if ( isButton ) {
		$button.addClass( "working" ).attr( { "disabled": "disabled", "aria-disabled": "true" } );
	} else {
		$button.addClass( "disabled working" ).attr( "aria-disabled", "true" );
	}

	if ( wp.a11y && wp.a11y.speak && undefined !== speak && speak ) {
		wp.a11y.speak( speak );
	}
}

/**
 * Enable a button that calls an ajax action.
 * - Remove the "working" class, so that the spinner can be hidden again.
 * - Remove the "aria-disabled" attribute.
 * - If it's a link: remove the "disabled" attribute. If it's a button or input: remove the "disabled" attribute.
 * - Change the button text if a "data-original-i18n" attribute is present.
 * - Use `wp.a11y.speak` if a text is provided.
 * - Set a `SecuPress.doingAjax` attribute to `false`.
 *
 * @since 1.0
 *
 * @param (object) $button jQuery object of the button.
 * @param (string) speak   Text for `wp.a11y.speak`.
 * @param (string) ajaxID  An identifier used for `SecuPress.doingAjax`. Default: "global".
 */
function secupressEnableAjaxButton( $button, speak, ajaxID ) {
	var text, isButton, value;

	if ( undefined !== $button && $button && $button.length ) {
		text     = $button.attr( "data-original-i18n" );
		isButton = $button.get( 0 ).nodeName.toLowerCase();
		isButton = "button" === isButton || "input" === isButton;

		if ( undefined !== text && text ) {
			if ( isButton ) {
				value = $button.val();
				if ( undefined !== value && value ) {
					$button.val( text );
				} else {
					if ( $button.find('.text').length ) {
						$button.find('.text').text( text );
					} else {
						$button.text( text );
					}
				}
			} else {
				if ( $button.find('.text').length ) {
					$button.find('.text').text( text );
				} else {
					$button.text( text );
				}
			}
		}

		if ( isButton ) {
			$button.removeClass( "working" ).removeAttr( "disabled aria-disabled" );
		} else {
			$button.removeClass( "disabled working" ).removeAttr( "aria-disabled" );
		}
	}

	if ( wp.a11y && wp.a11y.speak && undefined !== speak && speak ) {
		wp.a11y.speak( speak );
	}

	ajaxID = undefined !== ajaxID ? ajaxID : "global";
	SecuPress.doingAjax[ ajaxID ] = false;
}

/**
 * Before doing an ajax call, do some tests:
 * - test if we have an URL.
 * - if the event is "keyup", test if the key is the Space bar or Enter.
 * - test another ajax call is not running.
 * Also prevent default event.
 *
 * @since 1.0
 *
 * @param (string) href The URL.
 * @param (object) e    The jQuery event object.
 * @param (string) ajaxID  An identifier used for `SecuPress.doingAjax`. Default: "global".
 *
 * @return (bool|string) False on failure, the ajax URL on success.
 */
function secupressPreAjaxCall( href, e, ajaxID ) {
	e.preventDefault();

	if ( undefined === href || ! href ) {
		return false;
	}

	if ( "keyup" === e.type && ! secupressIsSpaceOrEnterKey( e ) ) {
		return false;
	}

	ajaxID = undefined !== ajaxID ? ajaxID : "global";

	if ( typeof SecuPress.doingAjax[ ajaxID ] === "undefined" ) {
		SecuPress.doingAjax[ ajaxID ] = false;
	}

	if ( SecuPress.doingAjax[ ajaxID ] ) {
		return false;
	}

	return href.replace( "admin-post.php", "admin-ajax.php" );
}

/**
 * Display an error message via Sweet Alert and re-enable the button.
 *
 * @since 1.0
 *
 * @param (object) $button jQuery object of the button.
 * @param (string) text    Text for swal2 + `wp.a11y.speak`.
 * @param (string) ajaxID  An identifier used for `SecuPress.doingAjax`. Default: "global".
 */
function secupressDisplayAjaxError( $button, text, ajaxID ) {
	if ( undefined === text ) {
		text = SecuPressi18nModules.unknownError;
	}

	swal2( jQuery.extend( {}, SecuPress.swal2Defaults, {
		title: SecuPressi18nModules.error,
		html:  text,
		type:  "error"
	} ) );

	ajaxID = undefined !== ajaxID ? ajaxID : "global";
	secupressEnableAjaxButton( $button, text, ajaxID );
}

/**
 * Display a success message via Sweet Alert and re-enable the button.
 *
 * @since 1.0
 *
 * @param (object) $button jQuery object of the button.
 * @param (string) text    Text for swal2 + `wp.a11y.speak`.
 * @param (string) ajaxID  An identifier used for `SecuPress.doingAjax`. Default: "global".
 */
function secupressDisplayAjaxSuccess( $button, text, ajaxID ) {
	if ( undefined === text ) {
		text = null;
	}

	swal2( jQuery.extend( {}, SecuPress.swal2Defaults, {
		title: SecuPressi18nModules.done,
		html:  text,
		type:  "success",
		timer: 4000
	} ) );

	ajaxID = undefined !== ajaxID ? ajaxID : "global";
	secupressEnableAjaxButton( $button, text, ajaxID );
}


// Roles: at least one role must be chosen. ========================================================
(function($, d, w, undefined) {

	if ( "function" === typeof document.createElement( "input" ).checkValidity ) {
		$( ".affected-role-row :checkbox" ).on( "click.secupress", function() {
			this.setCustomValidity( '' );

			if ( 0 === $( '[name="' + this.name + '"]:checked' ).length ) {
				this.setCustomValidity( SecuPressi18nModules.selectOneRoleMinimum );
				$( "#secupress-module-form-settings [type='submit']" ).first().trigger( "click.secupress" );
			}
		} );
	} else {
		$( ".affected-role-row p.warning" ).removeClass( "hide-if-js" );
	}

} )(jQuery, document, window);


// Radioboxes: 1 checked at most. ==================================================================
(function($, d, w, undefined) {

	$( ".radiobox" ).on( "click.secupress", function() {
		$( '[name="' + this.name + '"]:checked' ).not( this ).removeAttr( "checked" ).trigger( "change" );
	} );

} )(jQuery, document, window);


// Show/Hide panels, depending on some other field value. ==========================================
(function($, d, w, undefined) {

	var $depends   = $( "#wpbody-content" ).find( '[class*="depends-"]' ), // Rows that will open/close.
		dependsIds = {}, // IDs of the checkboxes, radios, etc that will trigger a panel open/close.
		dependsRadioNames = {}; // names of the radios.

	$depends.each( function() {
		var classes = $( this ).attr( "class" ).replace( /^\s+|\s+$/g, "" ).replace( /\s+/, " " ).split( " " );

		$.each( classes, function( i, id ) {
			var $input,        // input element
				inputTagName,  // input tag name
				inputTypeAttr, // input type
				inputNameAttr, // input name
				inputIsValid = false;

			// If the class is not a "depends-XXXXXX", bail out.
			if ( 0 !== id.indexOf( "depends-" ) ) {
				return true;
			}

			id = id.substr( 8 );

			// If the ID was previously delt with, bail out.
			if ( "undefined" !== typeof dependsIds[ id ] ) {
				return true;
			}
			dependsIds[ id ] = 1;

			$input = $( "#" + id );

			// Uh? The input doesn't exist?
			if ( ! $input.length ) {
				return true;
			}

			// We need to know which type of input we deal with, the way we deal with it is not the same.
			inputTagName = $input.get( 0 ).nodeName.toLowerCase();

			if ( "input" === inputTagName ) {
				inputTypeAttr = $input.attr( "type" ).toLowerCase();

				if ( "checkbox" === inputTypeAttr || "radio" === inputTypeAttr ) {
					inputIsValid = true;
				}

			} else if ( "button" === inputTagName ) {
				inputIsValid = true;
			}

			// Only checkboxes, radios groups and buttons so far.
			if ( ! inputIsValid ) {
				return true;
			}

			// Attach the events.
			// Buttons
			if ( "button" === inputTagName ) {

				$input.on( "click.secupress", function() {
					var id = $( this ).attr( "id" );
					$( this ).toggleClass( 'open' );
					$( ".depends-" + id ).toggle( 250 );
				} );

			}
			// Radios
			else if ( "radio" === inputTypeAttr ) {

				inputNameAttr = $input.attr( "name" );

				// If the name was previously delt with, bail out.
				if ( "undefined" !== typeof dependsRadioNames[ inputNameAttr ] ) {
					return true;
				}
				dependsRadioNames[ inputNameAttr ] = 1;

				$( '[name="' + inputNameAttr + '"]' ).on( "change init.secupress", function( e ) {
					var $this   = $( this ),
						$toShow = $( ".depends-" + $this.attr( "id" ) ), // Elements to show.
						toHide  = [], // Elements to hide.
						tempo   = "init" === e.type && "secupress" === e.namespace ? 0 : 250; // On page load, no animation.

					// The radio is checked: open the desired boxes if not visible.
					$toShow.not( ":visible" ).trigger( "secupressbeforeshow" ).show( tempo, function() {
						$( this ).trigger( "secupressaftershow" );
					} );

					// Find boxes to hide.
					$( '[name="' + $this.attr( "name" ) + '"]' ).not( $this ).each( function() {
						toHide.push( ".depends-" + $( this ).attr( "id" ).replace( /^\s+|\s+$/g, "" ) );
					} );

					$( toHide.join( "," ) ).not( $toShow ).filter( ":visible" ).trigger( "secupressbeforehide" ).hide( tempo, function() {
						$( this ).trigger( "secupressafterhide" );
					} );
				} ).filter( ":checked" ).trigger( "init.secupress" );

			}
			// Checkboxes
			else if ( "checkbox" === inputTypeAttr ) {

				$input.on( "change init.secupress", function( e ) {
					var $this  = $( this ),
						id     = $this.attr( "id" ),
						$elems = $( ".depends-" + id ), // Elements to hide or show.
						tempo  = "init" === e.type && "secupress" === e.namespace ? 0 : 250; // On page load, no animation.

					// Uh? No rows?
					if ( ! $elems.length ) {
						return true;
					}

					// The checkbox is checked: open if not visible.
					if ( $this.is( ":checked" ) ) {
						$elems.not( ":visible" ).trigger( "secupressbeforeshow" ).show( tempo, function() {
							$( this ).trigger( "secupressaftershow" );
						} );
					}
					// The checkbox is not checked: close if visible and no other checkboxes that want this row to be open is checked.
					else {
						$elems.filter( ":visible" ).each( function() {
							var $this   = $( this ),
								classes = $this.attr( "class" ).replace( /^\s+|\s+$/g, "" ).replace( /\s+/, " " ).split( " " ),
								others  = []; // Other checkboxes

							$.each( classes, function( i, v ) {
								if ( "depends-" + id !== v && 0 === v.indexOf( "depends-" ) ) {
									others.push( "#" + v.substr( 8 ) + ":checked" );
								}
							} );

							others = others.join( "," );

							if ( ! $( others ).length ) {
								$this.trigger( "secupressbeforehide" ).hide( tempo, function() {
									$( this ).trigger( "secupressafterhide" );
								} );
							}
						} );
					}
				} ).filter( ":checked" ).trigger( "init.secupress" );

			}
		} );
	} );

} )(jQuery, document, window);


// Backups =========================================================================================
(function($, d, w, undefined) {

	function secupressUpdateAvailableBackupCounter( r ) {
		$( "#secupress-available-backups" ).text( r.data.countText );
	}

	function secupressUpdateBackupVisibility() {
		if ( 0 === $( "#form-delete-backups" ).find( ".secupress-large-row" ).length ) {
			$( "#form-delete-backups" ).hide();
			$( "#secupress-no-backups" ).show();
		} else {
			$( "#secupress-no-backups" ).hide();
			$( "#form-delete-backups" ).show();
		}
	}

	// Delete all backups.
	function secupressDeleteAllBackups( $button, href ) {
		secupressDisableAjaxButton( $button, SecuPressi18nModules.deletingAllText, "backup" );

		$.getJSON( href )
		.done( function( r ) {
			var $fieldset, $legend;

			if ( $.isPlainObject( r ) && r.success ) {
				swal2.close();
				$fieldset = $button.closest( "form" ).find( "fieldset" );
				$legend   = $fieldset.children( "legend" );
				$fieldset.text( "" ).prepend( $legend );

				secupressUpdateBackupVisibility();
				secupressEnableAjaxButton( $button, SecuPressi18nModules.deletedAllText, "backup" );
			} else {
				secupressDisplayAjaxError( $button, SecuPressi18nModules.deleteAllImpossible, "backup" );
			}
		} )
		.fail( function() {
			secupressDisplayAjaxError( $button, null, "backup" );
		} );
	}

	// Delete one backup.
	function secupressDeleteOneBackup( $button, href ) {
		secupressDisableAjaxButton( $button, SecuPressi18nModules.deletingOneText, "backup" );

		$.getJSON( href )
		.done( function( r ) {
			if ( $.isPlainObject( r ) && r.success ) {
				swal2.close();

				$button.closest( ".secupress-large-row" ).css( "backgroundColor", SecuPress.deletedRowColor ).hide( "normal", function() {
					$( this ).remove();

					secupressUpdateAvailableBackupCounter( r );
					secupressUpdateBackupVisibility();
					SecuPress.doingAjax.backup = false;
				} );

				if ( wp.a11y && wp.a11y.speak ) {
					wp.a11y.speak( SecuPressi18nModules.deletedOneText );
				}
			} else {
				secupressDisplayAjaxError( $button, SecuPressi18nModules.deleteOneImpossible, "backup" );
			}
		} )
		.fail( function() {
			secupressDisplayAjaxError( $button, null, "backup" );
		} );
	}

	// Do a DB backup.
	function secupressDoDbBackup( $button, href ) {
		secupressDisableAjaxButton( $button, SecuPressi18nModules.backupingText, 'backup' );

		$.post( href, $button.closest( "form" ).serializeArray() )
		.done( function( r ) {
			if ( $.isPlainObject( r ) && r.success ) {
				$( r.data.elemRow ).addClass( "hidden" ).css( "backgroundColor", SecuPress.addedRowColor ).insertAfter( "#form-delete-backups legend" ).show( "normal", function() {
					$( this ).css( "backgroundColor", "" );
				} );

				secupressUpdateAvailableBackupCounter( r );
				secupressUpdateBackupVisibility();
				secupressEnableAjaxButton( $button, SecuPressi18nModules.backupedText, "backup" );
			} else {
				secupressDisplayAjaxError( $button, SecuPressi18nModules.backupImpossible, "backup" );
			}
		} )
		.fail( function() {
			secupressDisplayAjaxError( $button, null, "backup" );
		} );
	}

	// Do a files backup.
	function secupressDoFilesBackup( $button, href ) {
		secupressDisableAjaxButton( $button, SecuPressi18nModules.backupingText, 'backup' );

		$.post( href, $button.closest( "form" ).serializeArray() )
		.done( function( r ) {
			if ( $.isPlainObject( r ) && r.success ) {
				$( r.data.elemRow ).addClass( "hidden" ).css( "backgroundColor", SecuPress.addedRowColor ).insertAfter( "#form-delete-backups legend" ).show( "normal", function() {
					$( this ).css( "backgroundColor", "" );
				} );

				secupressUpdateAvailableBackupCounter( r );
				secupressUpdateBackupVisibility();
				$( "#ignored_directories" ).val( r.data.ignoredFiles );
				secupressEnableAjaxButton( $button, SecuPressi18nModules.backupedText, "backup" );
			} else {
				secupressDisplayAjaxError( $button, SecuPressi18nModules.backupImpossible, "backup" );
			}
		} )
		.fail( function() {
			secupressDisplayAjaxError( $button, null, "backup" );
		} );
	}

	// Ajax call that delete all backups.
	$( "#submit-delete-backups" ).on( "click.secupress", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( $this.closest( "form" ).attr( "action" ), e );

		if ( ! href ) {
			return;
		}

		if ( "function" === typeof w.swal2 ) {
			swal2( $.extend( {}, SecuPress.swal2Defaults, SecuPress.swal2ConfirmDefaults, {
				text:              SecuPressi18nModules.confirmDeleteBackups,
				confirmButtonText: SecuPressi18nModules.yesDeleteAll,
				type:              "question",
				reverseButtons:    true
			} ) ).then( function ( isConfirm ) {
				if ( isConfirm ) {
					swal2.enableLoading();
					secupressDeleteAllBackups( $this, href );
				}
			} );
		} else if ( w.confirm( SecuPressi18nModules.confirmTitle + "\n" + SecuPressi18nModules.confirmDeleteBackups ) ) {
			secupressDeleteAllBackups( $this, href );
		}
	} ).removeAttr( "disabled aria-disabled" );


	// Ajax call that delete one Backup.
	$( "body" ).on( "click.secupress keyup", ".a-delete-backup", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( $this.attr( "href" ), e );

		if ( ! href ) {
			return;
		}

		if ( "function" === typeof w.swal2 ) {
			swal2( $.extend( {}, SecuPress.swal2Defaults, SecuPress.swal2ConfirmDefaults, {
				text:              SecuPressi18nModules.confirmDeleteBackup,
				confirmButtonText: SecuPressi18nModules.yesDeleteOne,
				type:              "question",
				reverseButtons:    true
			} ) ).then( function ( isConfirm ) {
				if ( isConfirm ) {
					swal2.enableLoading();
					secupressDeleteOneBackup( $this, href );
				}
			} );
		} else if ( w.confirm( SecuPressi18nModules.confirmTitle + "\n" + SecuPressi18nModules.confirmDeleteBackup ) ) {
			secupressDeleteOneBackup( $this, href );
		}
	} );

	// Ajax call that does a DB Backup.
	$( "#submit-backup-db" ).on( "click.secupress", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( $this.closest( "form" ).attr( "action" ), e );

		if ( href ) {
			secupressDoDbBackup( $this, href );
		}
	} ).removeAttr( 'disabled aria-disabled' );

	// Ajax call that does a files Backup.
	$( "#submit-backup-files" ).on( "click.secupress", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( $this.closest( "form" ).attr( "action" ), e );

		if ( href ) {
			secupressDoFilesBackup( $this, href );
		}
	} ).removeAttr( 'disabled aria-disabled' );

} )(jQuery, document, window);


// Countries =======================================================================================
(function($, d, w, undefined) {

	function secupress_set_indeterminate_state( code ) {
		var all_boxes     = $( "[data-code-country='" + code + "']" ).length,
			checked_boxes = $( "[data-code-country='" + code + "']:checked" ).length;

		if ( checked_boxes === all_boxes ) {
			$( "[value='continent-" + code + "']" ).prop( { "checked": true, "indeterminate": false } ).css( "-webkit-appearance", "none" );
		} else if ( 0 === checked_boxes ) {
			$( "[value='continent-" + code + "']" ).prop( { "checked": false, "indeterminate": false } ).css( "-webkit-appearance", "none" );
		} else {
			$( "[value='continent-" + code + "']" ).prop( { "checked": false, "indeterminate": true } ).css( "-webkit-appearance", "checkbox" );
		}
	}

	$( ".continent input" ).on( "click.secupress", function( e ) {
		var $this = $( this ),
			val   = $this.css( "-webkit-appearance", "none" ).val().replace( "continent-", "" );

		$( ".depends-geoip-system_type_blacklist.depends-geoip-system_type_whitelist [data-code-country='" + val + "']" ).prop( "checked", $this.is( ":checked" ) );
	} );

	$( "[data-code-country]" ).on( "click.secupress", function( e ) {
		var code = $( this ).data( "code-country" );
		secupress_set_indeterminate_state( code );
	} );

	$( ".continent input" ).each( function( i ) {
		var code = $( this ).val().replace( "continent-", "" );
		secupress_set_indeterminate_state( code );
	} );

	$( ".expand_country" ).on( "click.secupress", function( e ) {
		$( this ).next( "fieldset" ).toggleClass( "hide-if-js" );
	} );

} )(jQuery, document, window);


// Banned IPs ======================================================================================
(function($, d, w, undefined) {

	var $row = $( "#banned-ips-row" ),
		$banForm, banUrl;

	if ( ! $row.length ) {
		return;
	}

	// Empty the list, display a message in a placeholder (a row in the list), and maybe hide the search form and the "Clear all IPs" button.
	function secupressBannedIPsEmptyList( message, resetSearch ) {
		var $form;

		if ( undefined === message || ! message ) {
			message = SecuPressi18nModules.noBannedIPs;
		}
		// Remove all rows from the list and display the placeholder.
		$row.find( "#secupress-banned-ips-list" ).html( '<li id="no-ips">' + message + "</li>" );

		if ( undefined !== resetSearch && ! resetSearch ) {
			return;
		}
		// Hide the "Clear all IPs" button and spinner.
		$row.find( "#secupress-clear-ips-button" ).next().addBack().addClass( "hidden" );
		// Hide and reset the search form.
		$form = $row.find( "#form-search-ip" ).addClass( "hidden" );
		// Reset the form.
		$form.find( "#reset-banned-ips-list" ).next().addBack().addClass( "hidden" );
		$form.find( "#secupress-search-banned-ip" ).val( "" );
	}

	// Fill in the list.
	function secupressBannedIPsFillList( data, replace ) {
		var $list    = $row.find( "#secupress-banned-ips-list" ),
			template = '<li class="secupress-large-row"><strong>%ip%</strong> <em>(%time%)</em><span><a class="a-unban-ip" href="%unban_url%">' + SecuPressi18nModules.delete + '</a> <span class="spinner secupress-inline-spinner"></span></span></li>',
			isSearch = ! $( "#reset-banned-ips-list" ).hasClass( "hidden" ),
			out      = "";

		if ( undefined === replace || replace ) {
			// We will replace the list content.
			replace = true;
			$list.html( "" );
		}

		if ( undefined === data || ! data.length ) {
			// No data.
			if ( replace ) {
				// We must display a placeholder with a message.
				if ( typeof replace === "string" ) {
					secupressBannedIPsEmptyList( replace );
				} else if ( isSearch ) {
					secupressBannedIPsEmptyList( SecuPressi18nModules.IPnotFound, false );
				} else {
					secupressBannedIPsEmptyList();
				}
			}
			return;
		}

		// Build the rows html.
		$.each( data, function( i, v ) {
			out += template.replace( /%ip%/g, v.ip ).replace( /%time%/g, v.time ).replace( /%unban_url%/g, v.unban_url );
		} );

		// Insert the rows.
		$list.append( out );
	}

	// Perform an ajax call to ban an IP address.
	function secupressBanIP( $button, href ) {
		var params = { "ip": $( "#secupress-ban-ip" ).val() };

		if ( ! params.ip ) {
			secupressBanIPswal2( $button, href );
			return;
		}

		secupressDisableAjaxButton( $button, null, "ban-ip" );

		$.getJSON( href, params )
		.done( function( r ) {
			var message;

			if ( ! $.isPlainObject( r ) || ! r.data || ! $.isPlainObject( r.data ) ) {
				secupressDisplayAjaxError( $button, SecuPressi18nModules.error, "ban-ip" );
				return;
			}

			if ( ! r.success ) {
				message = r.data.message ? r.data.message : null;
				secupressDisplayAjaxError( $button, message, "ban-ip" );
				return;
			}

			// Remove the placeholder if it exists.
			$row.find( "#secupress-banned-ips-list" ).children( "#no-ips" ).remove();

			// Add a new row in the list.
			secupressBannedIPsFillList( r.data.tmplValues, false );

			// Display the search form.
			$row.find( "#form-search-ip" ).removeClass( "hidden" );

			// Display the "Clear all IPs" button.
			$row.find( "#secupress-clear-ips-button" ).next().addBack().removeClass( "hidden" );

			secupressDisplayAjaxSuccess( $button, r.data.message, "ban-ip" );
		} )
		.fail( function() {
			secupressDisplayAjaxError( $button, null, "ban-ip" );
		} );
	}

	// swal2 that displays the form to ban an IP address.
	function secupressBanIPswal2( $button, href ) { // jshint ignore:line
		swal2( $.extend( {}, SecuPress.swal2Defaults, SecuPress.swal2ConfirmDefaults, {
			title:             $banForm.find( '[for="secupress-ban-ip"]' ).text(),
			confirmButtonText: $button.data( "original-i18n" ),
			html:              $banForm,
			type:              "info"
		} ) ).then( function ( isConfirm ) {
			if ( isConfirm ) {
				swal2.enableLoading();
				secupressBanIP( $button, href );
			}
		} );
	}

	// Reset buttons on page load.
	$row.find( "button" ).removeAttr( "disabled aria-disabled" );

	// The form to ban an IP address.
	$banForm = $row.find( "#form-ban-ip" ).remove();
	banUrl   = $banForm.attr( "action" );
	$banForm = $banForm.children().wrapAll( "<div id='secupress-ban-ip-fields' />" ).parent();
	$banForm.find( "[type='submit']" ).remove();

	// Search an IP address.
	$row.on( "submit", "#form-search-ip", function( e ) {
		var $this  = $( this ),
			$field = $this.find( "#secupress-search-banned-ip" ),
			href   = secupressPreAjaxCall( d.location.href, e, "ban-ip" ),
			ip     = $field.val(),
			$button, params;

		if ( ! href || ! ip ) {
			return;
		}

		$button = $field.next();
		params  = $this.serializeArray();

		secupressDisableAjaxButton( $button, null, "ban-ip" );

		$row.load( href + " #banned-ips-row > th, #banned-ips-row > td", params, function() {
			$row.find( "#form-ban-ip" ).remove();
			$row.find( "#secupress-search-banned-ip" ).focus();
			secupressEnableAjaxButton( $row.find( "#secupress-search-banned-ip" ), SecuPressi18nModules.searchResults, "ban-ip" );
		} );
	} );

	// Reset search.
	$row.on( "click.secupress keyup", "#reset-banned-ips-list", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( d.location.href, e, "ban-ip" );

		if ( ! href ) {
			return;
		}

		secupressDisableAjaxButton( $this, null, "ban-ip" );

		$row.load( href + " #banned-ips-row > th, #banned-ips-row > td", function() {
			$row.find( "#form-ban-ip" ).remove();
			$row.find( "#secupress-search-banned-ip" ).focus();
			secupressEnableAjaxButton( $this, SecuPressi18nModules.searchReset, "ban-ip" );
		} );
	} );

	// Ban an IP address.
	$row.on( "click.secupress keyup", "#secupress-ban-ip-button", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( banUrl, e, "ban-ip" );

		if ( href ) {
			secupressBanIPswal2( $this, href );
		}
	} );

	// Unban an IP address.
	$row.on( "click.secupress keyup", ".a-unban-ip", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( $this.attr( "href" ), e, "ban-ip" );

		if ( ! href ) {
			return;
		}

		secupressDisableAjaxButton( $this, null, "ban-ip" );

		$.getJSON( href )
		.done( function( r ) {
			var $list, $li, message;

			if ( ! $.isPlainObject( r ) || ! r.data || ! $.isPlainObject( r.data ) ) {
				secupressDisplayAjaxError( $this, SecuPressi18nModules.error, "ban-ip" );
				return;
			}

			if ( ! r.success ) {
				message = r.data.message ? r.data.message : null;
				secupressDisplayAjaxError( $this, message, "ban-ip" );
				return;
			}

			// Remove the row from the list.
			$li   = $this.closest( ".secupress-large-row" );
			$list = $li.parent();
			$li.remove();

			// The list is empty.
			if ( ! $list.children().length ) {
				if ( $( "#reset-banned-ips-list" ).hasClass( "hidden" ) ) {
					// It's not a search.
					secupressBannedIPsEmptyList();
				} else {
					// It's a search.
					secupressBannedIPsEmptyList( SecuPressi18nModules.IPremoved, false );
				}
			}

			secupressDisplayAjaxSuccess( $this, r.data.message, "ban-ip" );
		} )
		.fail( function() {
			secupressDisplayAjaxError( $this, null, "ban-ip" );
		} );
	} );

	// Unban all IP addresses.
	$row.on( "click.secupress keyup", "#secupress-clear-ips-button", function( e ) {
		var $this = $( this ),
			href  = secupressPreAjaxCall( $this.attr( "href" ), e, "ban-ip" );

		if ( ! href ) {
			return;
		}

		secupressDisableAjaxButton( $this, null, "ban-ip" );

		$.getJSON( href )
		.done( function( r ) {
			var $list = $this.siblings( "#secupress-banned-ips-list" ),
				message;

			if ( ! $.isPlainObject( r ) || ! r.data || ! $.isPlainObject( r.data ) ) {
				secupressDisplayAjaxError( $this, SecuPressi18nModules.error, "ban-ip" );
				return;
			}

			if ( ! r.success ) {
				message = r.data.message ? r.data.message : null;
				secupressDisplayAjaxError( $this, message, "ban-ip" );
				return;
			}

			// Remove all rows from the list, display the placeholder, etc.
			secupressBannedIPsEmptyList();

			secupressDisplayAjaxSuccess( $this, r.data.message, "ban-ip" );
		} )
		.fail( function() {
			secupressDisplayAjaxError( $this, null, "ban-ip" );
		} );
	} );

} )(jQuery, document, window);

// Auto Expand Textarea ============================================================================
(function($) {

	var SPautoSized = {},
		browsers = {},
		// Expandable items.
		$expandables = $('.secupress-textarea-container'),
		hCheck;

	browsers.msie  = /msie/.test( navigator.userAgent.toLowerCase() );
	browsers.opera = /opera/.test( navigator.userAgent.toLowerCase() );
	hCheck = ! ( browsers.msie || browsers.opera );

	// Function to resize textarea.
	SPautoSized.resize = function( e, init ) {
		var vlen, ewidth;

		// e = event or element?
		e = e.target || e;
		// Content length and box width.
		vlen   = e.value.length;
		ewidth = e.offsetWidth;

		if ( vlen !== e.valLength || ewidth !== e.boxWidth ) {
			if ( hCheck && ( vlen < e.valLength || ewidth !== e.boxWidth ) ) {
				e.style.height = '0px';
			}
			var h          = Math.max( e.expandMin, Math.min( e.scrollHeight, e.expandMax ) ),
				$textarea  = $( e ),
				$container = $textarea.closest('.secupress-textarea-container');

			e.style.overflow = 'hidden';
			e.style.height = h + 'px';

			e.valLength = vlen;
			e.boxWidth = ewidth;

			// Resize the container.
			SPautoSized.textareaParentHeight( $container, $textarea );

			// Add (+) button.
			if ( e.scrollHeight > $container.height() && $container.next() ) {
				SPautoSized.handleExpandButton( $container, true ); // Create.
			}
			else if ( e.scrollHeight <= $container.height() && $container.next('.secupress-expand-trigger-container').find('.open').length === 0 ) {
				SPautoSized.handleExpandButton( $container, false ); // Remove.
			}
		}

		return true;
	};

	// Function to resize textarea parent.
	SPautoSized.textareaParentHeight = function( $container, $textarea ) {
		$container.css(
			'height',
			$textarea.outerHeight() + $container.outerHeight() - $container.height()
		);
		return true;
	};

	// Function to create (+) button.
	SPautoSized.handleExpandButton = function( $container, create ) {
		// If creation needed, check if exists, or create it.
		if ( ! $container.next( '.secupress-expand-trigger-container' ).length && create ) {
			$container.after( SecuPress.expandButton ).attr( 'spellcheck', false );
		} else if ( $container.next( '.secupress-expand-trigger-container' ).length && false === create ) {
			$container.next( '.secupress-expand-trigger-container' ).remove();
		}
	};

	// jQuery definition.
	$.fn.AutoSized = function( minHeight, maxHeight ) {
		this.each( function() {
			// Is a textarea?
			if ( this.nodeName.toLowerCase() !== 'textarea' ) {
				return;
			}

			// Height restriction.
			this.expandMin = minHeight || 0;
			this.expandMax = maxHeight || 99999;

			// Initial resize
			SPautoSized.resize( this, this.Initialized );

			// Resize on write.
			if ( ! this.Initialized ) {
				this.Initialized = true;
				$( this ).css( {
					'padding-top'    : '0',
					'padding-bottom' : '0'
				} )
				.on('keyup.secupress focus.secupress', SPautoSized.resize );
			}
		} );

		return this;
	};

	// Move to end.
	$.fn.focusToEnd = function() {
		return this.each( function() {
			var v = $( this ).val();
			$( this ).focus().val( '' ).val( v );
		} );
	};

	// Change class on parent textarea on focus/blur.
	// Applied only on visible elements (see below for others).
	$expandables.filter( ':visible' ).find( 'textarea' ).AutoSized()
		.on( 'focus.secupress', function() {
			$( this ).parent().addClass( 'textarea-focused' );
		} )
		.on( 'blur.secupress', function() {
			$( this ).parent().removeClass( 'textarea-focused' );
		} );

	// Same action as previous for textarea depending on other actions to be displayed.
	$( '#wpbody-content' ).find( '.secupress-setting-row' ).on( 'secupressaftershow', function() {
		$( this ).find( '.secupress-textarea-container' ).find( 'textarea' ).AutoSized()
			.on('focus.secupress', function() {
				$( this ).parent().addClass( 'textarea-focused' );
			} )
			.on( 'blur.secupress', function() {
				$( this ).parent().removeClass( 'textarea-focused' );
			} );
	} );

	// On click on (+) button.
	$expandables.closest( 'label, .secupress-setting-content-col' ).on( 'click.secupress', '.secupress-expand-trigger', function() {
		var $_this     = $( this ),
			$container = $_this.closest( '.secupress-expand-trigger-container' ).prev( '.secupress-textarea-container' ),
			$textarea  = $container.find( 'textarea' );

		if ( $_this.hasClass( 'open' ) ) {
			$container.css( 'height', '200px' );
			$_this.removeClass( 'open' );
			$_this.find( '.secupress-expand-txt' ).text( SecuPressi18nModules.expandTextOpen );
			$_this.find( 'i' ).removeClass( 'secupress-icon-angle-up' ).addClass( 'secupress-icon-angle-down' );
			// Scroll to the top of the box on close.
			$( 'html, body' ).animate( {
				scrollTop: $container.offset().top - 80 // 80 to be slightly over the box.
			}, 275 );
		} else {
			SPautoSized.textareaParentHeight( $container, $textarea );
			$textarea.focusToEnd();
			$_this.addClass( 'open' );
			$_this.find( '.secupress-expand-txt' ).text( SecuPressi18nModules.expandTextClose );
			$_this.find( 'i' ).removeClass( 'secupress-icon-angle-down' ).addClass( 'secupress-icon-angle-up' );
		}
		return false;
	} );

} )(jQuery);

// Auto Expand Boxed Groups ======================================================================
(function($) {
	$( 'fieldset.secupress-boxed-group' ).each( function() {
		var $box      = $( this ),
			maxHeight = 200;

		$box.data( 'height', $box.outerHeight() )
			.css( 'height', maxHeight )
			.after( SecuPress.expandButton );

		$box.next( '.secupress-expand-trigger-container' ).on( 'click.secupress', function() {
			var $this = $( this );

			if ( $this.hasClass( 'open' ) ) {
				$box.css( 'height', maxHeight );
				$this.removeClass( 'open' );
				$this.find( '.secupress-expand-txt' ).text( SecuPressi18nModules.expandTextOpen );
				$this.find( 'i' ).removeClass( 'secupress-icon-angle-up' ).addClass( 'secupress-icon-angle-down' );
				// scroll to the top of the box on close.
				$('html, body').animate({
					scrollTop: $box.offset().top - 80 // 80 to be slightly over the box.
				}, 275 );
			} else {
				$box.css( 'height', $box.data( 'height' ) );
				$this.addClass( 'open' );
				$this.find( '.secupress-expand-txt' ).text( SecuPressi18nModules.expandTextClose );
				$this.find( 'i' ).removeClass( 'secupress-icon-angle-down' ).addClass( 'secupress-icon-angle-up' );
			}
			return false;
		} );
	} );
} )(jQuery);

// Pricing switching ============================================================================
(function($) {
	var $buttons = $( '.secupress-tab-content-get-pro .secupress-inline-options button' );

	$buttons.on( 'click.secupress', function() {
		var $_this  = $( this ),
			current = 'secupress-current';

		if ( $_this.hasClass( current ) ) {
			return false;
		}

		$buttons.removeClass( current );
		$_this.addClass( current );

		$_this.closest( '.secupress-inline-options' ).removeClass( 'secupress-type-yearly secupress-type-monthly' ).addClass( 'secupress-type-' + $_this.data( 'type' ) );
	} );
} )(jQuery);

// Malware Scan Status ============================================================================
(function($, d, w, undefined) {
	if ( undefined !== SecuPressi18nModules && 'on' === SecuPressi18nModules.malwareScanStatus ) {

		$( d ).on( 'heartbeat-send', function( e, data ) {
			data.secupress_heartbeat_malware_scan = 'malwareScanStatus';
		} )
		// Listen for the custom event "heartbeat-tick" on $(document).
		.on( 'heartbeat-tick', function( e, data ) {
			// Only proceed if our data is present.
			if ( data.malwareScanStatus ) {
				w.location.href = SecuPressi18nModules.MalwareScanURI;
			}
		} );
	}
} )(jQuery, document, window);

// Checked checkbox class ========================================================================
(function($, d, w, undefined) {
	$( '.secupress-fieldset-item-checkboxes' ).each( function() {
		var $checkbox = $(this).find('input'),
			$label    = $checkbox.closest( 'label' );

		if ( $checkbox.filter(':checked').length ) {
			$label.addClass( 'is-checked' );
		}

		$checkbox.on( 'change', function() {
			if ( $checkbox.filter(':checked').length ) {
				$label.addClass( 'is-checked' );
			} else {
				$label.removeClass( 'is-checked' );
			}
		} );
	} );
} )(jQuery, document, window);
