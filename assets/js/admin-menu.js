( function ( $, document ) {
	/**
	 * SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_JS Class.
	 */
	class SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_JS {
		/**
		 * Constructor
		 */
		constructor() {this.init();}
		/**
		 * initializeSPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_JS
		 */
     init() {
			// setInterval( () => this.time(), 1000 );
      this.tab();this.toggle();this.popup();this.reload();this.change();
		}
    tab() {
			$( '.tab__menu .tab__list a' ).on( 'click', function( e ) {
        e.preventDefault();
        if( $( $( this ).attr( 'href' ) ).length >= 1 ) {
          $( this ).parent( '.tab__list' ).addClass( 'active' ).siblings().removeClass( 'active' );
          $( '.fwp-admin-setting-tabs ' + $( this ).attr( 'href' ) ).show().siblings().hide();
          $( '.fwp-admin-setting-tabs ' + $( this ).attr( 'href' ) ).show( 600 ).siblings().hide( 400 );
        }
      } );
		}
		submit() {
			let thisClass = this;
			$( '#youtube-playlists' ).on( 'submit', function( e ) {
				e.preventDefault();
				var url = '';
				$.ajax( {
					url: url,
					dataType: "json",
					type: "GET",
					async: true,
					data: { },
					success: function (data) {},
					error: function ( xhr, exception ) {
						var msg = "";
						if (xhr.status === 0) {
							msg = "Not connect.\n Verify Network." + xhr.responseText;
						} else if (xhr.status == 404) {
							msg = "Requested page not found. [404]" + xhr.responseText;
						} else if (xhr.status == 500) {
							msg = "Internal Server Error [500]." +  xhr.responseText;
						} else if (exception === "parsererror") {
							msg = "Requested JSON parse failed.";
						} else if (exception === "timeout") {
							msg = "Time out error." + xhr.responseText;
						} else if (exception === "abort") {
							msg = "Ajax request aborted.";
						} else {
							msg = "Error:" + xhr.status + " " + xhr.responseText;
						}
						thisClass.log( msg );
					}
				} );
			} );
		}
		toggle() {
			let thisClass = this;
			// const $ = jQuery;
			$( '.fwp-toggle-switcher' ).on( 'change', function( e ) {
				var ckb = $( this ), toggle = $( this ).data( 'toggle' ), channel = $( this ).data( 'channel' ), target = $( this ).data( 'target' );
				let status = ckb.prop( "checked" ) ? 'on' : 'off';
				var ajaxUrl = siteConfig?.ajaxUrl ?? 'https://hayalhanem.conceptwebactueel.nl/wp-admin/admin-ajax.php';
				$.ajax( {
					url: ajaxUrl,
					dataType: "json",
					type: "POST",
					async: true,
					data: {
						action: 'special_youtube_playlist_api_integration_plugin',
						toggle: toggle,
						channel: channel,
						id: target,
						status: status,
						nonce: siteConfig?.ajax_nonce ?? ''
					},
					success: function (data) {
						if( data.success ) {
							thisClass.log( 'success', data.data );
						} else {
							thisClass.log( 'error', data.data );
						}
					},
					error: function ( xhr, exception ) {
						var msg = "";
						if( xhr.status === 0 ) {
							msg = "Not connect.\n Verify Network." + xhr.responseText;
						} else if( xhr.status == 404 ) {
							msg = "Requested page not found. [404]" + xhr.responseText;
						} else if( xhr.status == 500 ) {
							msg = "Internal Server Error [500]." +  xhr.responseText;
						} else if( exception === "parsererror" ) {
							msg = "Requested JSON parse failed.";
						} else if( exception === "timeout" ) {
							msg = "Time out error." + xhr.responseText;
						} else if( exception === "abort" ) {
							msg = "Ajax request aborted.";
						} else {
							msg = "Error:" + xhr.status + " " + xhr.responseText;
						}
						thisClass.log( 'error', msg );
					}
				} );
			} );
		}
		change() {
			let thisClass = this;
			// const $ = jQuery;
			$( '.changeCategory' ).on( 'change', function( e ) {
				var select = $( this ), toggle = $( this ).data( 'toggle' ), channel = $( this ).data( 'channel' ), target = $( this ).data( 'target' );
				let status = ( $( this ).val() !== undefined && $( this ).val() != '' ) ? $( this ).val() : ( 
					( $( this ).find(":selected").val() !== undefined && $( this ).find(":selected").val() != '' ) ? $( this ).find(":selected").val() : (
						( this.value ) ? this.value : false
					)
				 );
				 if( ! status ) {thisClass.log( 'error', 'Data fatching ptoblem detected!' );return;}
				var ajaxUrl = siteConfig?.ajaxUrl ?? 'https://hayalhanem.conceptwebactueel.nl/wp-admin/admin-ajax.php';
				$.ajax( {
					url: ajaxUrl,
					dataType: "json",
					type: "POST",
					async: true,
					data: {
						action: 'special_youtube_playlist_api_integration_plugin',
						toggle: toggle,
            channel: channel,
						id: target,
						status: status,
						nonce: siteConfig?.ajax_nonce ?? ''
					},
					success: function (data) {
						if( data.success ) {
							thisClass.log( 'success', data.data );
						} else {
							thisClass.log( 'error', data.data );
						}
					},
					error: function ( xhr, exception ) {
						var msg = "";
						if( xhr.status === 0 ) {
							msg = "Not connect.\n Verify Network." + xhr.responseText;
						} else if( xhr.status == 404 ) {
							msg = "Requested page not found. [404]" + xhr.responseText;
						} else if( xhr.status == 500 ) {
							msg = "Internal Server Error [500]." +  xhr.responseText;
						} else if( exception === "parsererror" ) {
							msg = "Requested JSON parse failed.";
						} else if( exception === "timeout" ) {
							msg = "Time out error." + xhr.responseText;
						} else if( exception === "abort" ) {
							msg = "Ajax request aborted.";
						} else {
							msg = "Error:" + xhr.status + " " + xhr.responseText;
						}
						thisClass.log( 'error', msg );
					}
				} );
			} );
		}
		log( type, msg ) {
			// notify goes here
			var popup = '\
			<label for="one" class="alert-message ' + type + '">\
				' + msg + '\
				<span class="close" onclick="jQuery( this ).parent().remove();">×</span>\
			</label>';
			$( 'body' ).append( popup );
			setTimeout( () => {
				$( '.alert-message' ).remove();
			}, 6000 );
		}
		popup() {
			let thisClass = this;
			// const $ = jQuery
			$( '.element-link' ).on( 'click', function( e ) {
				e.preventDefault();
				var videoUrl = $( this ).data( 'embed' ); // $( this ).attr( 'href' );
				if( typeof videoUrl === undefined || ! videoUrl ) {return;}
				var popup = '\
				<div class="yt-popup" id="media-youtube-popup" title="Press ESC to close.">\
					<iframe width="560" height="315" src="' + videoUrl + '" frameborder="0" allowfullscreen></iframe>\
				</div>';
				$( 'body' ).append( popup );
				thisClass.popevent();
			} );
			$( document ).on( 'keyup', function( e ) {
				if ( e.key === "Escape" ) {
					$( '.yt-popup' ).remove();
				}
			} );
		}
		popevent() {
			$( '.yt-popup' ).addClass( 'show' );
			$( '.yt-popup' ).on( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				$( '.yt-popup' ).remove();
			} );
			$( '.yt-popup > iframe' ).on( 'click', function( e ) {
				e.stopPropagation();
			} );
		}
		reload() {
			$( '.js-fwp-settings-update' ).on( 'click', function( e ) {
				e.preventDefault();
        var channel = $( this ).data( 'channel' ), url = location.href;
        url += ( channel == '' ) ? '' : '&channel=' + channel;
				if( confirm( siteConfig?.confirmUpdate ?? 'Are you sure you want to update this playlist?' ) ) {
					location.href = url + '&update=true';
				}
			} );
		}
	}

	new SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_JS();
} )( jQuery, document );
