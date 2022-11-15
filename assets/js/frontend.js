( function ( $, document, window ) {
  // 'use strict';
	/**
	 * YOUTUBE_PLAYLIST_FRONTEND_SCRIPT Class.
	 */
	class YOUTUBE_PLAYLIST_FRONTEND_SCRIPT {
    i18n;
    settings;
    denaid;
    videoPlayer;
    videoGallery;
		/**
		 * Constructor
		 */
		constructor() {
      this.settings = {
        key              : siteConfig?.ajax_nonce ?? "",
        url              : siteConfig?.ajaxUrl ?? "/wp-admin/admin-ajax.php",
        listOrder        : siteConfig?.listOrder ?? false,
        singleCategory   : siteConfig?.singleCategory ?? false,
        PlayItemsDesign  : true,
        id               : "GoogleDevelopers", // youtube user id is case sensitive
        token            : "special_youtube_playlist_api_integration_plugin_playlist",
        local            : true,
        max_results      : 50,
        autoplay         : 0,
        theme            :'dark',
        paging           : 'loadmore',
        scroll_duration  : 500,
        first_load       : true,
        errorImage       : siteConfig?.errorImage ?? "",
        complete: function( e ) {
          // console.log( 'Completation fired' );
        }
      };
      this.denaid = false;
      this.videoPlayer = false;
      this.videoGallery = false;
      this.i18n = {
        serverError: siteConfig?.i18n.serverError ?? 'Error while tring to connect with server. Maybe server internal problem.',
        apiError: siteConfig?.i18n.apiError ?? 'We\'re facing some problem while trying to get playlist from Google server.',
        emptyError: siteConfig?.i18n.emptyError ?? 'There is nothing to show. Maybe List is empty or isn\'t publicly visible.',
      };
      this.init();
      if( this.settings.listOrder ) {
        this.sideMenu();
      }
    }
		/**
		 * initialize script
		 */
    init() {
      this.popup();
      // this.click();
		}
    sideMenu() {
      const thisClass = this;
      if( ! thisClass.settings.listOrder ) {return;}
      var nav = document.querySelector( '.elementor-widget.elementor-widget-global.elementor-widget-nav-menu .elementor-widget-container nav ul' ), menus = nav.querySelectorAll( 'li' ), li, ul, h3, a, items, item;
      items = document.querySelectorAll( 'body .elementor-container.fwp-elementor-container > .imagehvr-wrapper' );
      ul = document.createElement( 'ul' );
      if( items.length >= 1 ) {
        items.forEach( function( item, i ) {
          li = document.createElement( 'li' );li.classList.add( 'menu-item', 'menu-item-type-post_type', 'menu-item-object-page' );h3 = document.createElement( 'h3' );
          a = document.createElement( 'a' );a.classList.add( 'elementor-item', 'fwp-link-click' );a.href = 'javascript:void(0)';a.innerText = item.querySelector( '.imagecaption' ).dataset.title;a.dataset.order = i;a.dataset.embed = item.querySelector( '.imagehvr .imagehvr-content-wrapper .imagehvr-link' ).dataset.embed;a.dataset.id = item.querySelector( '.imagehvr .imagehvr-content-wrapper .imagehvr-link' ).dataset.id;
          h3.appendChild( a );li.appendChild( h3 );
          ul.appendChild( li );
        } );
        
        if( ul.querySelectorAll( 'li' ).length >= 1 ) {
          nav.innerHTML = ul.innerHTML;
          thisClass.navClickEvent();
        }
      }
    }
    navClickEvent() {
      const thisClass = this;
      var root = 'body .elementor-container.fwp-elementor-container';
      const playlist = document.querySelectorAll( root + ' > .imagehvr-wrapper' );
      const container = document.querySelectorAll( root );
      var nav = document.querySelector( '.elementor-widget.elementor-widget-global.elementor-widget-nav-menu .elementor-widget-container nav ul' ), list = nav.querySelectorAll( 'li' ), grid;
      list.forEach( function( e ) {
        e.addEventListener( 'click', function( event ) {
          event.preventDefault();
          if( this.classList.contains( 'fwp-active' ) ) {
            var that = this;
            document.querySelectorAll( root ).forEach( function( elem, ielem ) {
              elem.classList.add( 'fwp-hidden' );
            } );
            document.querySelector( root + '.fwp-elementor-container-playlists' ).classList.remove( 'fwp-hidden' );
            that.classList.remove( 'fwp-active' );
          } else {
            var order = e.querySelector( '.fwp-link-click' ).dataset.order;
            if( order && playlist[ order ] ) {
              grid = playlist[ order ].querySelector( '.imagehvr .imagehvr-content-wrapper .imagehvr-link' );
              if( thisClass.settings.singleCategory ) {
                thisClass.handlePlaylist( this, grid );
                container.forEach( function( elem, ielem ) {
                  elem.classList.add( 'fwp-hidden' );
                } );
                document.querySelector( root + '.fwp-elementor-container-gallery' ).classList.remove( 'fwp-hidden' );
              } else if( true ) {
                thisClass.handleIframe( this, grid );
                container.forEach( function( elem, ielem ) {
                  elem.classList.add( 'fwp-hidden' );
                } );
                document.querySelector( root + '.fwp-elementor-container-iframe' ).classList.remove( 'fwp-hidden' );
              } else {
                grid.click();
              }
            } else {
              console.log( 'Playlist index error', order );
            }
          }
        } );
      } );
    }
    handlePlaylist( list, grid ) {
      const thisClass = this;
      var root, iframe, container, div, close, i, url, items, html;
      thisClass.removeActives( list );
      list.classList.add( 'fwp-active' );
      root = 'body .elementor-container.fwp-elementor-container';
      if( ! thisClass.videoGallery ) {
        container = document.querySelectorAll( root );
        if( container[0] ) {
          div = document.createElement( 'div' );div.classList.add( 'elementor-container', 'elementor-column-gap-default', 'elementor-container-flex-wrap', 'fwp-elementor-container', 'fwp-elementor-container-gallery' );

          container[0].parentNode.insertBefore( div, container[0] );
          thisClass.videoGallery = true;
          container[0].classList.add( 'fwp-hidden' );
        }
      }
      root = document.querySelectorAll( root + '.fwp-elementor-container-gallery' );
      root =( root[0] ) ? root[0] : root;root.innerHTML = '';
      root.classList.add( 'fwp-ghost-container' );
      for( i = 0; i < 12; i++ ) {
        div = document.createElement( 'div' );
        div.classList.add( 'fwp-ghost-card' );
        root.appendChild( div );
      }
      url = thisClass.settings.url + '?action=' + thisClass.settings.token + '&playlist=' + grid.dataset.id + '&key=' + thisClass.settings.key;
      $.getJSON( url, function( data ) {
        if( data.error ) {
          thisClass.denaid = true;
          // console.log( data.error.code, data.error.message );
          root.classList.remove( 'fwp-ghost-container' );
          thisClass.nothingFound( root, { list: list, grid: grid, type: 'api', error: data.error } );
        } else {
          data = ( data.data ) ? data.data : data;
          items = ( data.items) ? data.items : [];
          html = '';
          if( items.length >= 1 ) {
            items.forEach( function( e, i ) {
              e.contentDetails = ( e.contentDetails ) ? e.contentDetails : {};
              e.contentDetails.videoId = ( e.contentDetails.videoId ) ? e.contentDetails.videoId : '';
              e.snippet = ( e.snippet ) ? e.snippet : {};
              e.snippet.title = ( e.snippet.title ) ? e.snippet.title : '';
              e.snippet.titleSrt = ( e.snippet.title.length > 30 ) ? e.snippet.title.substr( 0, 30 ) + '..' : e.snippet.title.substr( 0, 30 );
              e.thumbnails = ( e.thumbnails ) ? e.thumbnails : {};
              e.thumbnails.medium = ( e.thumbnails.medium ) ? e.thumbnails.medium : 'https://i.ytimg.com/vi/' + e.contentDetails.videoId + '/sddefault.jpg';
              html += '\
                <div class="imagehvr-wrapper elementor-element elementor-column elementor-inner-column elementor-col-3 elementor-col-md-4 elementor-col-sm-6">\
                  <div class="imagehvr">\
                    <div class="imagehvr-content-wrapper imagehvr-content-center imagehvr-anim-zoom-in-alt">\
                        <a href="https://www.youtube.com/watch?v=' + e.contentDetails.videoId + '&amp;list=' + e.snippet.playlistId + '" class="imagehvr-link" data-embed="https://www.youtube.com/embed/videoseries?v=' + e.contentDetails.videoId + '&amp;list=' + e.snippet.playlistId + '" data-id="' + e.snippet.playlistId + '">\
                          <span class="imagehvr-icon ih-delay-zero imagehvr-anim-none">\
                            <i class="fas fa-play-circle"></i>\
                          </span>\
                        </a>\
                        <picture>\
                          <source sizes="162px" type="image/webp" data-srcset="' + e.thumbnails.medium + '" srcset="' + e.thumbnails.medium + '">\
                          <img width="480" height="270" src="' + e.thumbnails.medium + '" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full lazyautosizes lazyloaded" alt="" data-eio="p" data-src="' + e.thumbnails.medium + '" decoding="async" data-srcset="' + e.thumbnails.medium + '" data-sizes="auto" sizes="162px" srcset="' + e.thumbnails.medium + '">\
                          <noscript>\
                            <img loading="lazy" width="480" height="270" src="' + e.thumbnails.medium + '" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full" alt="" srcset="' + e.thumbnails.medium + '" sizes="(max-width: 480px) 100vw, 480px" data-eio="l" />\
                          </noscript>\
                        </picture>\
                    </div>\
                    <div class="imagecaption" data-title="' + e.snippet.title + '">\
                      <span class="captiontext">' + e.snippet.titleSrt + '</span>\
                    </div>\
                  </div>\
                </div>';
            } );
            root.innerHTML = html;
            thisClass.popup( root.querySelectorAll( '.imagehvr-link' ) );
          } else {
            thisClass.nothingFound( root, { list: list, grid: grid, type: 'zero' } );
          }
        }
      } ).then( function() {
        root.classList.remove( 'fwp-ghost-container' );
      }, function( error ) {
        // console.log( error );
        root.classList.remove( 'fwp-ghost-container' );
        thisClass.nothingFound( root, { list: list, grid: grid, type: 'request', error: error } );
      } );
    }
    nothingFound( root, args ) {
      const thisClass = this;
      var root, container, h3, div, title, img, url, items, html;
      switch( args.type ) {
        case 'request' :
          title = thisClass.i18n.serverError;
          break;
        case 'api' :
          title = thisClass.i18n.apiError;
          break;
        case 'zero' :
          title = thisClass.i18n.emptyError;
          break;
        default :
          title = thisClass.i18n.emptyError;
          break;
      }
      container = document.createElement( 'div' );container.classList.add( 'elementor-element', 'elementor-column', 'elementor-inner-column', 'elementor-col-100' );
      img = document.createElement( 'img' );img.src = thisClass.settings.errorImage;
      h3 = document.createElement( 'h3' );container.classList.add( 'fwp-error-image-caption' );h3.innerHTML = title;
      container.appendChild( img );container.appendChild( div );
      root.innerHTML = '';root.appendChild( container );
    }
    handleIframe( list, grid ) {
      const thisClass = this;
      var root, iframe, container, div, close, i;
      thisClass.removeActives( list );
      list.classList.add( 'fwp-active' );
      root = 'body .elementor-container.fwp-elementor-container.fwp-elementor-container';
      if( ! thisClass.videoPlayer ) {
        container = document.querySelectorAll( root );
        if( container[0] ) {
          div = document.createElement( 'div' );div.classList.add( 'elementor-container', 'elementor-column-gap-default', 'elementor-container-flex-wrap', 'fwp-elementor-container', 'fwp-elementor-container-iframe' );
          
          iframe = document.createElement( 'iframe' );iframe.classList.add( 'elementor-element', 'elementor-column', 'elementor-inner-column', 'elementor-col-100' );
          iframe.src = grid.dataset.embed;iframe.width = '100%';
          iframe.allowFullscreen = true;iframe.frameborder = 0;iframe.type = 'text/html';

          close = document.createElement( 'div' );close.classList.add( 'fwp-elementor-container-iframe-close' );
          i = document.createElement( 'i' );i.classList.add( 'fa', 'fa-times' );close.appendChild( i );
          close.addEventListener( 'click', function( e ) {
            this.parentNode.classList.add( 'fwp-hidden' );
            this.parentNode.nextSibling.classList.remove( 'fwp-hidden' );
          } );
          div.appendChild( close );div.appendChild( iframe );
          container[0].parentNode.insertBefore( div, container[0] );
          thisClass.videoPlayer = true;
          // document.querySelector( root + '.fwp-elementor-container-iframe' ).nextSibling
          container[0].classList.add( 'fwp-hidden' );
        }
      } else {
        container = document.querySelector( 'body .elementor-container.fwp-elementor-container.fwp-elementor-container-iframe' );
        if( container ) {
          container.querySelector( 'iframe' ).src = grid.dataset.embed;
          container.classList.remove( 'fwp-hidden' );
          container.nextSibling.classList.add( 'fwp-hidden' );
        }
      }
    }
    removeActives( elem ) {
      elem = elem.parentNode.firstChild;
      do {
          if (elem.nodeType === 3) {
            continue;
          } else {
            // if( elem.classList.contains( '.fwp-active' ) ) {
              elem.classList.remove( 'fwp-active' );
            // }
          }
      } while ( elem = elem.nextSibling )
    }
    /**
     * Function to execute popup
     */
		popup( tr = '.imagehvr-link' ) {
			const thisClass = this;
			// const $ = jQuery;
			$( tr ).on( 'click', function( e ) {
				e.preventDefault();
				var videoUrl = $( this ).data( 'embed' );
				var videoId = $( this ).data( 'id' );
				if( typeof videoUrl === undefined || ! videoUrl ) {return;}
				if( typeof videoId === undefined || ! videoId ) {return;}
        if( 1 == 1 ) {
          var popup = '\
          <div class="yt-popup" id="media-youtube-popup" title="Press ESC to close.">\
            <div class="popup-center-content">\
              <iframe id="fwp-player-iframe" width="560" height="315" src="' + videoUrl + '" frameborder="0" allowfullscreen></iframe>\
              <ul id="youtube-playlists" class=""></ul>\
              <i class="fas fa-times yt-popup-closer"></i>\
            </div>\
          </div>';
          $( 'body' ).append( popup );
          $( '#media-youtube-popup' ).addClass( 'show' );
          thisClass.player( videoId );
          var elem = $( '.yt-popup' );
          thisClass.popevent( elem );
        }
			} );
		}
    /**
     * returns Control if it is active or default poppup
     */
    active() {
      return true;
    }
    /**
     * After popup called event
     */
		popevent( elem ) {
      const thisClass = this;
			$( elem ).addClass( 'show' );
			$( elem ).find( '.yt-popup-closer' ).addClass( 'show' );
      $( window ).resize( thisClass.height() );
			$( document ).on( 'keyup', function( e ) {
				if ( e.key === "Escape" ) {
					$( '.yt-popup' ).remove();
				}
			} );
			$( elem ).on( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				// $( elem ).remove();
			} );
			$( elem ).children( '.popup-center-content' ).on( 'click', function( e ) {
				e.stopPropagation();
			} );
			$( elem ).find( '.yt-popup-closer' ).on( 'click', function( e ) {
				$( elem ).remove();
			} );
		}
    fwp_iframe_src( id, autoplay, theme ){
      // Build and return the youtube iframe src
      var src = 'https://www.youtube.com/embed/' + id + '?version=3&loop=1&autoplay=' + autoplay + '&rel=0&showsearch=0&showinfo=0&theme=' + theme;
      return src;
    }
    /**
     * Is used to refresh sized on control.
     */
    height(){
      var ratio = 1.7777777777777777 // ratio for 640*360 video 
      var player_width = $( '.yt-popup .popup-center-content' ).width();
      var player_height = player_width / ratio;
      $( '.yt-popup .popup-center-content iframe, .yt-popup .popup-center-content #youtube-playlists' ).height(player_height);
    }
    fwp_set_player_height(){
    // Keep player height ratio on resize
      var ratio = 1.7777777777777777; // ratio for 640*360 video
      var player_width = $( '#fwp-player' ).width();
      var player_height = player_width / ratio;
      $( '#fwp-player' ).height(player_height);
    }
    fwp_init_item_click($el, scroll_duration){
      const thisClass = this;
    // Add delegate click event
      // Update the video on click, scroll to the player and toggle the current class
      $el.on( 'click', 'li a',function(e){
        e.preventDefault();
        var next_video_id = $(this).attr( 'data-youtubeID' );
        var next_video_url = thisClass.fwp_iframe_src(next_video_id, settings.autoplay, settings.theme);

        $( '#fwpspidochetube_list li' ).removeClass( 'fwpspidochetube_current' );
        $(this).parent().addClass( 'fwpspidochetube_current' );

        $( 'html, body' ).animate({
          scrollTop: $( '#fwpspidochetube_player' ).offset().top
        }, scroll_duration, function(){
          $( '#fwpspidochetube_player iframe' ).attr( 'src' , next_video_url);
        });

      });
    }
    player( playlist ) {
			const thisClass = this;var settings = thisClass.settings, max_results = 50, next_page = "", next_page_data = "", url = "", first_load = settings.first_load;
      if(settings.key === ""){console.log( 'Security key not defined' );return;}
      
      next_page_data = $( '#fwpspidochetube_loadmore' ).attr( 'data-next' );
      next_page = ( next_page_data !== undefined ) ? next_page_data : next_page;
      url ="https://www.googleapis.com/youtube/v3/playlistItems?playlistId=" + playlist + "&orderby=reversedPosition&pageToken=" + next_page + "&maxResults=" + max_results + "&key=" + settings.key + "&part=snippet,status,contentDetails";
      if( settings.local ) {
        url = settings.url + '?action=' + thisClass.settings.token + '&playlist=' + playlist + '&key=' + settings.key;
      }
      thisClass.getJSON( url );
    }
    getJSON( url ) {
      const thisClass = this;
      $.getJSON( url, function( data ) {
        if( data.error ) {thisClass.denaid = true;console.log( data.error.code, data.error.message );}
        else {thisClass.rander( ( data.data ) ? data.data : data );}
        // console.log( data );
      } ).then( function() {
        // thisClass.autoplay();
      }, function( error ) {
        // console.log( error );
      } );
    }
    rander( data ) {
      var total_results = ( ( data.pageInfo ) && ( data.pageInfo.totalResults ) ) ? data.pageInfo.totalResults : 50;
      var next_page = ( data.nextPageToken ) ? data.nextPageToken : '';var html = '';
      $.each( data.items, function( index, item ) {
        var snippet = item.snippet, title = snippet.title, status = ( item.status && item.status.privacyStatus ) ? item.status.privacyStatus : 'public', video_id  = "", thumb_url = "", video_url = "";
        if( status !== "public" ) {return;}
        if( snippet.thumbnails === undefined ){return;}
        video_id  = snippet.resourceId.videoId;
        thumb_url = snippet.thumbnails.medium.url;
        video_url = "https://www.youtube.com/embed/"+video_id;

        html += '<li>';
        html += '<a title="' + title + '" href="' + video_url + '" data-youtubeID="' + video_id + '" target="_blank">';
        html += '<img src="' + thumb_url + '" alt="' + title + '" />';
        html += '<span>' + title +'</span>';
        html += '</a>';
        html += '</li>';

      } );
      if( html != '' ) {
        $( '#youtube-playlists' ).html( html );
        $( '#youtube-playlists' ).addClass( 'show' );
        $( '#youtube-playlists li a' ).off( 'click' ).on( 'click',function( e ) {
          e.preventDefault();
          $( '#fwp-player-iframe' ).attr( 'src', $( this ).attr( 'href' ) );
        } );
      } else {
        console.log( 'Something went wrong while generating Playlist. Maybe there is no Items available to show.' );
      }
      // Update the page token tracker or hide the load more button
      // if( data.nextPageToken !== undefined ) {
      //   $( '#fwpspidochetube_loadmore' ).attr( 'data-next', next_page );
      // } else {
      //   $( '#fwpspidochetube_loadmore' ).css( 'visibility', 'hidden' );
      // }
    }
    autoplay() {
      const thisClass = this;var settings = thisClass.settings;
      var first_video_id = $( '#fwpspidochetube_list li:first-child a' ).attr( 'data-youtubeID' );
      var first_video_url = thisClass.fwp_iframe_src( first_video_id, settings.autoplay, settings.theme );
      // var iframe_html = '<iframe id="fwp-player" src="' + first_video_url + '" width="640" height="360" frameborder="0" allowfullscreen></iframe>';
      $( '#fwpspidochetube_list li:first-child' ).addClass( 'fwpspidochetube_current' );
      // $( 'body' ).append( iframe_html );
      $( '#fwpspidochetube_loadmore' ).off( 'click' ).on( 'click',function( e ) {
        e.preventDefault();
        // console.log( 'Request to Load more' );
      } );
      $( '#youtube-playlists li a' ).off( 'click' ).on( 'click',function( e ) {
        e.preventDefault();
        $( '#fwp-player-iframe' ).attr( 'src', $( this ).attr( 'href' ) );
      } );
      // $( '#fwpspidochetube_loadmore' ).off( 'click' ).on( 'click',function( e ) {
      //   e.preventDefault();
      //   console.log( 'Load more' );
      // } );
      // thisClass.fwp_set_player_height();
      // $(window).resize(thisClass.fwp_set_player_height);
      // if( $.isFunction( settings.complete ) ) {
      //   settings.complete.apply( [] );
      // }
    }
	}

	new YOUTUBE_PLAYLIST_FRONTEND_SCRIPT();
} )( jQuery, document, window );
