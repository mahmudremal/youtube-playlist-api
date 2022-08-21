( function ( $, document, window ) {
  // 'use strict';
	/**
	 * YOUTUBE_PLAYLIST_FRONTEND_SCRIPT Class.
	 */
	class YOUTUBE_PLAYLIST_FRONTEND_SCRIPT {
    settings;
		/**
		 * Constructor
		 */
		constructor() {
      this.settings = {
        key              : siteConfig?.youtubeApi ?? "AIzaSyAk_sfoVdJ6CbBIAezTlh-1AtWgsVCErCs",
        id               : "GoogleDevelopers",  // 'PLIbMQVUKxl0Q_2xwvKZZEf2gc4swtv7Wn', // youtube user id is case sensitive
        max_results      : 50,
        autoplay         : 0,
        theme            :'dark',
        paging           : 'loadmore',
        scroll_duration  : 500,
        first_load       : true,
        complete: function( e ) {
          console.log( 'Completation fired' );
        }
      };
      this.init();
    }
		/**
		 * initialize script
		 */
    init() {
      this.popup();
      // this.click();
		}
		popup() {
			const thisClass = this;
			// const $ = jQuery;
			$( '.imagehvr-link' ).on( 'click', function( e ) {
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
              <ul id="youtube-playlists"></ul>\
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
		popevent( elem ) {
      const thisClass = this;
			$( elem ).addClass( 'show' );
      $( window ).resize(thisClass.height());
			$( document ).on( 'keyup', function( e ) {
				if ( e.key === "Escape" ) {
					$( '.yt-popup' ).remove();
				}
			} );
			$( elem ).on( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				$( elem ).remove();
			} );
			$( elem ).children( '.popup-center-content' ).on( 'click', function( e ) {
				e.stopPropagation();
			} );
		}
    fwp_iframe_src( id, autoplay, theme ){
      // Build and return the youtube iframe src
      var src = 'https://www.youtube.com/embed/' + id + '?version=3&loop=1&autoplay=' + autoplay + '&rel=0&showsearch=0&showinfo=0&theme=' + theme;
      return src;
    }
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
      // if(settings.key === ""){console.log( 'api key not defined' );return;}
      
      next_page_data = $( '#fwpspidochetube_loadmore' ).attr( 'data-next' );
      next_page = ( next_page_data !== undefined ) ? next_page_data : next_page;
      url ="https://www.googleapis.com/youtube/v3/playlistItems?playlistId=" + playlist + "&orderby=reversedPosition&pageToken=" + next_page + "&maxResults=" + max_results + "&key=" + settings.key + "&part=snippet,status,contentDetails";
 
      thisClass.getJSON( url );
    }
    getJSON( url ) {
      const thisClass = this;
      $.getJSON( url, function( data ) {
        thisClass.rander( data );
        console.log( data );
      } ).then( function() {
        thisClass.autoplay();
      }, function( error ) {
        console.log( error );
      } );
    }
    rander( data ) {
      var total_results = data.pageInfo.totalResults;
      var next_page = data.nextPageToken;

      $.each( data.items, function( index, item ) {
        var snippet = item.snippet, title = snippet.title, status = item.status.privacyStatus, video_id  = "", thumb_url = "", video_url = "";
        if( status !== "public" ) {return;}
        if( snippet.thumbnails === undefined ){return;}
        video_id  = snippet.resourceId.videoId;
        thumb_url = snippet.thumbnails.medium.url;
        video_url = "https://www.youtube.com/embed/"+video_id;
        /*
          <li>
            <a>
                <img src="" alt="">
                <span>Titlte here</span>
            </a>
          </li>
        */

        html  = '<li>';
        html += '<a title="' + title + '" href="' + video_url + '" data-youtubeID="' + video_id + '">';
        html += '<img src="' + thumb_url + '" alt="' + title + '" />';
        html += '<span>' + title +'</span>';
        html += '</a>';
        html += '</li>';

        $( '#youtube-playlists' ).append( html );
        $( '#youtube-playlists' ).addClass( 'show' );

        // Update the page token tracker or hide the load more button
        if( data.nextPageToken !== undefined ) {
          $( '#fwpspidochetube_loadmore' ).attr( 'data-next', next_page );
        } else {
          $( '#fwpspidochetube_loadmore' ).css( 'visibility', 'hidden' );
        }
      } );
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
        console.log( 'Request to Load more' );
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
