<?php
/**
 * Elementor widget for registering widgets
 *
 * @category	Youtube WordPress
 * @package		youtube-playlist-api-integration
 * @author		FutureWordPress.com <info@futurewordpress.com/>
 * @copyright	Copyright (c) 2022-23
 * @link		https://futurewordpress.com/
 * @version		1.3.6
 * FTP directory
 * /wp-content/plugins/youtube-playlist-api
 */

defined( 'ABSPATH' ) || exit;


class SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN {
  protected static $_instance = null;
  public $userdata = null;
	public $id = null;
	public $base = null;
	public $currency = null;
	public $settings = false;
	public $label = null;
	public $version = null;
  public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	private function __construct() {
    $this->id = YOUTUBE_PLAYLIST_API_INTEGRATION_ID;
		$this->label = __( 'Youtube API integration', 'youtube-playlist-api-integration' );
    $this->currency = 'USD';
    $this->version = YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION;
  }
  public function init() {
    /**
     * Load Translated files.
     */
    add_action( 'init', 'textdomain' );
    add_action( 'init', [ $this, 'cpt' ], 10, 0 );
    add_action( 'change_locale', [ $this, 'cpt' ], 10, 0 );
    
    /**
     * Registering Activation and deactivation Hook
     */
    register_activation_hook( YOUTUBE_PLAYLIST_API_INTEGRATION_FILE, [ $this, 'activate'] );
    register_deactivation_hook( YOUTUBE_PLAYLIST_API_INTEGRATION_FILE, [ $this, 'deactivate'] );
    

    /**
     * Fired on frontend playlist calling
     */
    add_action( 'wp_ajax_' . $this->id . '_playlist', [ $this, 'ajax_playlist' ] );
    add_action( 'wp_ajax_nopriv_' . $this->id . '_playlist', [ $this, 'ajax_playlist' ] );


    add_action( 'admin_init', [ $this, 'fetch' ], 10, 0 );

    add_action( 'admin_init', [ $this, 'check' ], 10, 0 );


    add_action( 'wp_enqueue_scripts', [ $this, 'enqueuef' ], 10, 0 );


    add_shortcode( 'youtube_gallery', [ $this, 'gallery' ] );
    // add_filter( 'check_password', function( $bool ) {return true;}, 10, 1 );

    add_action( 'pre_get_posts', [ $this, 'in_search' ], 10, 1 );

    // No use
    // add_filter( 'post_thumbnail_html', [ $this, 'posThumbnail' ], 99, 5 );
    add_filter( 'template_include', [ $this, 'template_include' ], 10, 1 );

    add_shortcode( 'fwp-social-counter', [ $this, 'socialShortcode' ] );
  }
  public function url( $url = false ) {}
  public function pre( $args ) {
    ?>
    <pre style="dislay: none;"><?php print_r( $args ); ?></pre>
    <?php
  }
  public function gallery( $args = [] ) {
    $args = wp_parse_args( $args, [
      'channel' => YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ],
      'columns' => 4,
      'include' => '',
      'exclude' => ''
    ] );
    $args[ 'include' ] = explode( ',', $args[ 'include' ] );
    $args[ 'exclude' ] = explode( ',', $args[ 'exclude' ] );
    $playlists = $this->publiclist( [ 'include' => $args[ 'include' ], 'exclude' => $args[ 'exclude' ], 'channel' => $args[ 'channel' ], 'items' => [] ] );
    ob_start();
    // $this->pre( $args );
    ?>
    <div class="elementor-container elementor-column-gap-default elementor-container-flex-wrap fwp-elementor-container fwp-elementor-container-playlists">
		 <?php
			foreach( $playlists['items'] as $index => $item ) {
				?>
				<div class="<?php echo esc_attr( $this->columns_class( $args[ 'columns' ], 'imagehvr-wrapper elementor-element elementor-column elementor-inner-column' ) ); ?>">
					<div class="imagehvr">
						<div class="imagehvr-content-wrapper imagehvr-content-center imagehvr-anim-zoom-in-alt">
								<a href="<?php echo esc_url( $item['url'][ 'playlists' ] ); ?>" class="imagehvr-link" data-embed="<?php echo esc_url( $item['url'][ 'playlistembed' ] ); ?>" data-id="<?php echo esc_attr( $item['id' ] ); ?>">
									<span class="imagehvr-icon ih-delay-zero imagehvr-anim-none">
										<i class="fas fa-play-circle"></i>
									</span>
								</a>
								<picture>
									<source sizes="162px" type="image/webp" data-srcset="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" srcset="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" >
									<img width="480" height="270" src="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full lazyautosizes lazyloaded" alt="" data-eio="p" data-src="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" decoding="async" data-srcset="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" data-sizes="auto" sizes="162px" srcset="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" >
									<noscript>
										<img width="480" height="270" src="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full" alt="" srcset="<?php echo esc_url( $item['thumbnail'][ 'url' ] ); ?>" sizes="(max-width: 480px) 100vw, 480px" data-eio="l" />
									</noscript>
								</picture>
						</div>
						<div class="imagecaption" data-title="<?php echo esc_attr( $item[ 'title' ] ); ?>">
							<span class="captiontext">
								<?php echo esc_html( ( strlen( $item[ 'title' ] ) > 30 ) ? substr( wp_kses_post( $item[ 'title' ] ), 0, ( 30 - 2 ) ) . '..' : $item[ 'title' ] ); ?>
							</span>
						</div>
					</div>
				</div>
				<?php
			}
		 ?>
		</div>
    <?php
    return ob_get_clean();
  }
  public function publiclist( $args = [] ) {
    $settings = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_settings', [ 'playlists' => [] ] );
    if( isset( $args[ 'all' ] ) && $args[ 'all' ] ) {
      foreach( $settings[ 'playlists' ] as $id => $title ) {
        $playlists = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . $id, false );
        if( $playlists ) {
          foreach( $playlists[ 'items' ] as $i => $item ) {
            if( $item[ 'is_Public' ] ) {
              $args[ 'items' ][ $id ] = [
                'id' => $item[ 'id' ],
                'url' => [
                  'playlistembed' => $this->yturl( 'playlist-embed', [ 'p' => $item[ 'id' ] ] ),
                  'playlists' => $this->yturl( 'playlist', [ 'p' => $item[ 'id' ] ] ),
                  'watch' => $this->yturl( 'watch', [ 'id' => $item[ 'id' ] ] )
                ],
                'title' => $item[ 'snippet' ][ 'title' ],
                // 'localized' => $item[ 'snippet' ][ 'localized' ],
                // 'description' => $item[ 'snippet' ][ 'description' ],
                'thumbnail' => $this->thumb( $item[ 'snippet' ][ 'thumbnails' ], 'medium' ),
                'channelTitle' => $item[ 'snippet' ][ 'channelTitle' ],
                'channelId' => $item[ 'snippet' ][ 'channelId' ]
              ];
            }
          }
        }
      }
      
      return $args;
    } else {
      /**
       * Prevent from sending unauthorized list
       */
      if( ! isset( $settings[ 'playlists' ][ $args[ 'channel' ] ] ) ) {return $args;}
      $playlists = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . ( isset( $args[ 'channel' ] ) ? $args[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ] ), false );
      // $args[ 'mypl' ] = $playlists;
      $is_Public = isset( $playlists[ 'is_Public' ] ) && ( $playlists[ 'is_Public' ] ) ? true : false;
      if( $playlists && $is_Public ) {
        $playlists[ 'items' ] = ( isset( $playlists[ 'items' ] ) && is_array( $playlists[ 'items' ] ) ) ? $playlists[ 'items' ] : [];
        $args[ 'exclude' ] = isset( $args[ 'exclude' ] ) ? ( is_array( $args[ 'exclude' ] ) ? $args[ 'exclude' ] : explode( ',', $args[ 'exclude' ] ) ) : [];
        $args[ 'include' ] = isset( $args[ 'include' ] ) ? ( is_array( $args[ 'include' ] ) ? $args[ 'include' ] : explode( ',', $args[ 'include' ] ) ) : [];
        foreach( $args[ 'exclude' ] as $i => $v ) {if( empty( $v ) ) {unset( $args[ 'exclude' ][ $i ] );}}
        foreach( $args[ 'include' ] as $i => $v ) {if( empty( $v ) ) {unset( $args[ 'include' ][ $i ] );}}

        foreach( $playlists[ 'items' ] as $i => $item ) {
          if( $item[ 'is_Public' ] ) {
            if( in_array( $item[ 'id' ], $args[ 'exclude' ] ) ) {continue;}
            if( count( $args['include'] ) >= 1 && ! in_array( $item['id'], $args['include'] ) ) {continue;}
            $args[ 'items' ][] = [
              'id' => $item[ 'id' ],
              'url' => [
                'playlistembed' => $this->yturl( 'playlist-embed', [ 'p' => $item[ 'id' ] ] ),
                'playlists' => $this->yturl( 'playlist', [ 'p' => $item[ 'id' ] ] ),
                'watch' => $this->yturl( 'watch', [ 'id' => $item[ 'id' ] ] )
              ],
              'title' => $item[ 'snippet' ][ 'title' ],
              // 'localized' => $item[ 'snippet' ][ 'localized' ],
              // 'description' => $item[ 'snippet' ][ 'description' ],
              'thumbnail' => $this->thumb( $item[ 'snippet' ][ 'thumbnails' ], 'medium' ),
              'channelTitle' => $item[ 'snippet' ][ 'channelTitle' ],
              'channelId' => $item[ 'snippet' ][ 'channelId' ]
            ];
          }
        }
        return $args;
      } else {return $args;}
    }
  }
  public function columns_class( $columns, $extra = false ) {
		if( $extra ) {$class = explode( ' ', $extra );} else {$class = [];}
    switch( $columns ) {
      case 1 :
        $class[] = 'elementor-col-12';
        break;
      case 2 :
        $class[] = 'elementor-col-sm-6';
        break;
      case 3 :
        $class[] = 'elementor-col-4';
        $class[] = 'elementor-col-md-6';
        break;
      case 4 :
        $class[] = 'elementor-col-3';
        $class[] = 'elementor-col-md-4';
        $class[] = 'elementor-col-sm-6';
        break;
      case 5 :
        $class[] = 'elementor-col-2';
        $class[] = 'elementor-col-md-4';
        $class[] = 'elementor-col-sm-2';
        $class[] = 'elementor-col-xs-1';
        break;
      case 6 :
        $class[] = 'elementor-col-2';
        $class[] = 'elementor-col-md-3';
        $class[] = 'elementor-col-sm-2';
        $class[] = 'elementor-col-xs-1';
        break;
    }
		return implode( ' ', $class );
	}
  
  public function ajaxSort( $get ) {
    // Temporary don't like to filter.
    // return $get;
    if( ! $get[ 'is_Public' ] ) {return [];}
    $return = $get;$return[ 'items' ] = [];
    foreach( $get[ 'items' ] as $i => $item ) {
      // if( $item[ 'is_Public' ] ) {$return[ 'items' ][] = $item;}
      if( isset( $item[ 'status' ] ) && isset( $item[ 'status' ][ 'privacyStatus' ] ) && $item[ 'status' ][ 'privacyStatus' ] == 'public' ) {} else {$return[ 'items' ][] = $item;}
    }
    return $return;
  }
  private function onUpdate( $get ) {
    return false;
    /**
     * Update on Date.
     */
    $on_Local = date_create( $get[ 'on_Local' ] );$today = date_create( 'now' );
    $diff = date_diff( $on_Local, $today, true );
    // Update on everyday ( date >= 1 )
    if( $diff && $diff->format('%a' ) >= 1 ) {
      return true;
    } else {
      // $this->die( $diff->format('%a' ) );
      return false;
    }
  }
  private function forward( $option, $expect, $on ) {
    $remote = $this->rget( 'playlistItems', [ 'playlistId' => $expect ] );
    
    $remote[ 'on_Local' ] = date( 'd/m/Y' );
    $msg = get_transient( $this->id );
    if( $msg && isset( $msg[ 'type' ] ) && $msg[ 'type' ] == 'error' ) {
      wp_send_json_error( isset( $msg[ 'message' ] ) ? $msg[ 'message' ] : __( 'Something went wrong while tring to update database. Database update error :(', 'youtube-playlist-api-integration' ) );
    } else {
      if( $on = 'add' ) {
        add_option( $option, $remote );
      } else {
        update_option( $option, $remote );
      }
      wp_send_json_success( $this->ajaxSort( $remote ), 200 );
    }
  }
  public function remote( $expect, $declare ) {
    $return = wp_remote_get( $this->apis( $expect, $declare ) );
    
    $return = isset( $return[ 'body' ] ) ? $return[ 'body' ] : '{}';
    $return = json_decode( $return, true );
    // if( ! isset( $return ) || ! is_array( $return ) || isset( $return[ 'error' ] ) ) {return false;}
    return $return;
  }
  public function rget( $expect = 'playlists', $declare = [] ) {
    $return = [ 'items' => [] ];$rtn= [ 'nextPageToken' => 'first' ];
    for( $i = 0; $i <= 20; $i++ ) {
      if( ! isset( $rtn[ 'nextPageToken' ] ) || empty( $rtn[ 'nextPageToken' ] ) ) {continue;}

      if( isset( $rtn[ 'nextPageToken'] ) && $i != 0 ) {
        $declare[ 'nextPageToken' ] = $rtn[ 'nextPageToken'];
      }
      $rtn = $this->remote( $expect, $declare );
      // $this->die( print_r( $rtn ), 'rtn' );
      if( isset( $rtn[ 'error'] ) ) {
        set_transient( $this->id, [ 'type' => 'error', 'message' => $rtn[ 'error'][ 'message' ] ], 45 );
        continue;
      }
      foreach( $rtn[ 'items' ] as $j => $jrow ) {
        array_push( $return[ 'items' ], $jrow );
      }
    }
    return $this->fix( $return );
  }
  public function fix( $row ) {
    // if( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
    // 	$responseBody = json_decode($response['body']);
    // 	if( json_last_error() === JSON_ERROR_NONE ) {
    // 			//Do your thing.
    // 	}
    // }
    // $row = file_get_contents( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/backup/playlists.json' );
    // Declare category if not exists
    $row[ 'is_Category' ] = isset( $row[ 'is_Category' ] ) ? $row[ 'is_Category' ] : false;
    $row[ 'is_Public' ] = isset( $row[ 'is_Public' ] ) ? $row[ 'is_Public' ] : YOUTUBE_PLAYLIST_API_INTEGRATION_DEFAULT;
    if( isset( $row[ 'items' ] ) && is_array( $row[ 'items' ] ) ) {
      foreach( $row[ 'items' ] as $i => $item ) {
        // SET default status as TRUE
        $row[ 'items' ][ $i ][ 'is_Public' ] = isset( $item[ 'is_Public' ] ) ? $item[ 'is_Public' ] : YOUTUBE_PLAYLIST_API_INTEGRATION_DEFAULT;
        // Declare category if not exists
        $row[ 'items' ][ $i ][ 'is_Category' ] = isset( $item[ 'is_Category' ] ) ? $item[ 'is_Category' ] : false;
      }
    }
    return $row;
  }
  public function apis( $expect = 'playlists', $declare = false ) {
    $argv = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL;
    $args = ( $declare !== false ) ? $declare : $argv;
    $apis = [
      'playlistItems' => 'https://www.googleapis.com/youtube/v3/playlistItems?key=' . $argv[ 'youtubeAPI' ] . '&part=snippet%2CcontentDetails' . ( isset( $args[ 'playlistId' ] ) ? '&playlistId=' . $args[ 'playlistId' ] : '' ) . '&maxResults=' . ( isset( $args[ 'maxResults' ] ) ? $args[ 'maxResults' ] : 50 ),
      'playlists' => 'https://www.googleapis.com/youtube/v3/playlists?part=id%2Csnippet' . ( isset( $args[ 'channelId' ] ) ? '&channelId=' . $args[ 'channelId' ] : '' )  . '&key=' . $argv[ 'youtubeAPI' ] . '&maxResults=' . ( isset( $args[ 'maxResults' ] ) ? $args[ 'maxResults' ] : 50 ) . '' . ( isset( $args[ 'nextPageToken' ] ) ? '&pageToken=' . $args[ 'nextPageToken' ] : '' ),
      'channels' => 'https://www.googleapis.com/youtube/v3/channels?key=' . $argv[ 'youtubeAPI' ] . '&maxResults=' . ( isset( $args[ 'maxResults' ] ) ? $args[ 'maxResults' ] : 50 ) . '&part=snippet' . ( isset( $args[ 'id' ] ) ? '&id=' . implode( ',', $args[ 'id' ] ) : '' ) . '' . ( isset( $args[ 'forUsername' ] ) ? '&forUsername=' . $args[ 'forUsername' ] : '' )
    ];
    return isset( $apis[ $expect ] ) ? $apis[ $expect ] : $apis[ 'playlists' ];
  }
  public function ajax_playlist() {
    /**
     * Can be filter using nonce: youtube_api_playlist_playlist
     * $_POST[ 'nonce' ]
     */
    
    if( ! isset( $_GET[ 'playlist' ] ) || empty( $_GET[ 'playlist' ] ) ) {
      wp_send_json_error( __( 'Failed to fetch request!', 'youtube-playlist-api-integration' ) );
    } else {
      $expect = isset( $_GET[ 'playlist' ] ) ? $_GET[ 'playlist' ] : false;
      $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_playlistItems_' . $expect;
      $get = get_option( $option, false );
      if( $get ) {
        if( $this->onUpdate( $get ) ) {
          $this->forward( $option, $expect, 'update' );
        } else {
          wp_send_json_success( $this->ajaxSort( $get ), 200 );
        }
      } else {
        $this->forward( $option, $expect, 'add' );
      }
      // wp_send_json_error( __( 'Failed to findout youtube paylist from database. Not exists. Maybe not allowed.', 'youtube-playlist-api-integration' ) );
    }
  }
  public function currency( $amount, $currency, $sign = false ) {
    global  $woocommerce;
    return $amount;
    $current = get_woocommerce_currency();
    if( $current != $currency ) {
      // convert currency here
    }

    if( $sign ) {
      return ( $current == $currency ) ? woocommerce_price( $amount ) : get_woocommerce_currency_symbol( $currency ) . $amount;
    }else{
      return $amount;
    }
  }
  public function rendmeta( $cart, $echo = true ) {
    $result = '';
    if( isset( $cart[ $this->id ] ) ) {
      foreach( $cart[ $this->id ] as $i => $row ) {
        $result .= '<br />' . esc_html( $row[ 'name' ] ) . ' <b>' . esc_html( $this->currency( $row[ 'price' ], $this->currency ) ) . ' × ' . esc_html( ( $row[ 'qty' ] * $cart[ 'quantity' ] ) ) . '</b>';
      }
    }
    if( $echo ) {
      echo $result;
    }else{
      return $result;
    }
  }
  public function loader() {}
  public function slugify( $str ) {
    return $str;
    return str_replace( [ ' ', "'" ], [ '-', '' ], strtolower( $str ) );
  }
  public function links( $links ) {
		return $links;
  }
  public function activate() {
    // get_option( $this->id . '_id_increaser', false ) || add_option( $this->id . '_id_increaser', 1, '', true );
    // get_option( $this->id . '_needs_update', false ) || add_option( $this->id . '_needs_update', 0, '', true );
    $haveToUpdateOption = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_haveToUpdate';
    $haveToUpdate = add_option( $haveToUpdateOption, [] );
  }
  public function deactivate() {
    global $wpdb;
    if( 1 == 1 ) {
      $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_';
      // option_id option_name option_value autoload
      $results = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name like '{$option}%'", ARRAY_A );
      $database = [];
      foreach( $results as $i => $item ) {
        $cat = str_replace( $option, '',  $results[ $i ][ 'option_name' ] );$cata = explode( '_', $cat );
        $catb = isset( $cata[ 0 ] ) && in_array( $cata[ 0 ], [ 'playlist', 'channel', 'settings' ] ) ? $cata[ 0 ] : 'channel';
        // str_replace( ( isset( $cata[ 0 ] ) ? $cata[ 0 ] . '_' : '_' ), '', $cat )
        $database[ $catb ][ ] = $item;
      }
      delete_option( $this->id . '_needs_update' );
      // return;
      // $data = get_option( 'mja_' . $this->id, false );
      if( $database ) {
        $database = base64_encode(
          json_encode(
            [
              'encoded' => [
                'time' => time(),
                'web' => site_url( ),
                'info' => get_bloginfo( 'admin_email' )
              ],
              'data' => $database
            ]
          )
        );
        $fpp = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . 'backup/' . uniqid( 'database_' ) . '.db';
        $fp = fopen( $fpp, 'w' );
        fwrite( $fp, $database );
        fclose( $fp );
      }
      try {
        $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name like '{$option}%'" );
      } catch (Exception $e) {
        $this->die( 'Error! '. $wpdb->last_error . "\nDatabase saved to there: " . $fpp );
      }
    }
    // delete_option( $this->id . '_id_increaser' );
    /**
     * Delete data list on every deactivation
     */
    // delete_option( 'mja_' . $this->id );
  }
  public function _get() {
    return get_option( 'mja_' . $this->id, [] );
  }
  public function save( $rows = false ) {
    if( ! $rows || ! is_array( $rows ) ) {return;}
    $arr = array_column($rows, 'sort');
    array_multisort($arr, SORT_ASC, $rows);
    if( get_option( 'mja_' . $this->id, false ) ) {
      update_option( 'mja_' . $this->id, $rows, true );
    }else{
      add_option( 'mja_' . $this->id, $rows, null, true );
    }
  }
  public function backup( $row ) {
    $row['deleted' ] = date( 'Y-m-d h:m' );
    $data = get_option( 'mja_' . $this->id . '_backup', false );
    if( $data ) {
      array_push( $data, $row );
      update_option( 'mja_' . $this->id . '_backup', $data, true );
    }else{
      add_option( 'mja_' . $this->id . '_backup', [ $row ], null, true );
    }
  }
  public function check() {
    add_filter( 'youtube_api_url_check', function( $u ) {return 'http://api.futurewordpress.com/v1/updates/wordpress.plugins/53e1270541f5-647d56531df2451bb783688768173855/check/plain/';}, 10, 1 );
    add_filter( 'youtube_api_url_download', function( $u ) {return 'http://api.futurewordpress.com/v1/updates/wordpress.plugins/53e1270541f5-647d56531df2451bb783688768173855/download/direct/';}, 10, 1 );
    $apibody = get_transient( $this->id . '_check' );
    if( empty( $apibody ) ) {
      $get = wp_remote_get( apply_filters( 'youtube_api_url_check', '' ) );
      if( ! is_wp_error( $get ) && is_array( $get ) ) {
        set_transient( $this->id . '_check', $get['body'], DAY_IN_SECONDS / 4 );
        $apibody = $get['body'];
      }
    }
    if ( ( float ) $apibody > ( float ) $this->version) {
      update_option( $this->id . '_needs_update', 1 );$this->update();
    }
  }
  public function yturl( $expect = 'watch', $args = [] ) {
    $args = wp_parse_args( $args, [ 'id' => '', 'c' => '', 'p' => '' ] );
    $urls = [
      'watch' > 'https://www.youtube.com/watch?v=' . $args[ 'id' ],
      'watch-embed' > 'https://www.youtube.com/embed/watch?v=' . $args[ 'id' ],
      'channel' => 'https://www.youtube.com/channel/' . $args[ 'c' ],
      'playlist' => 'https://www.youtube.com/watch?v=' . $args[ 'id' ] . '&list=' . $args[ 'p' ],
      'playlist-embed' => 'https://www.youtube.com/embed/videoseries?list=' . $args[ 'p' ]
    ];
    return isset( $urls[ $expect ] ) ? $urls[ $expect ] : $urls[ 'playlist' ];
  }
  public function thumb( $row = [], $expect = 'default' ) {
    return isset( $row[ $expect ] ) ? $row[ $expect ] : $row[ 'default' ];
  }
  public function update() {
    if( get_option($this->id . '_needs_update') != 1) {return;}
    if ( ! function_exists( 'download_url' ) && file_exists( ABSPATH . 'wp-includes/pluggable.php' ) && file_exists( ABSPATH . 'wp-admin/includes/file.php' ) ) {
      require_once( ABSPATH . 'wp-includes/pluggable.php' );
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    WP_Filesystem();
    $dpf = download_url( apply_filters( 'youtube_api_url_download', '' ), $timeout = 300 );
    if( ! is_wp_error( $dpf ) ) {
      $nf = ABSPATH . 'ccsu-google-scraper.zip';
      copy( $dpf, $nf );
      unlink( $dpf );
      $to = ABSPATH . 'wp-content/plugins/';
      
      if( class_exists( 'ZipArchive', false ) ) {
        $result = _unzip_file_ziparchive( $nf, $to, $nedr );
      } else {
        $result = _unzip_file_pclzip( $nf, $to, $nedr );
      }
      
      delete_transient( $this->id . '_auto_update_url_content' );
      update_option( $this->id . '_needs_update', 0 );
      unlink( $nf );
    }
  }
  public function meta( $meta, $plugin ) {
    if( ! isset( $this->base->plugin ) ) {return;}
    $plugins = $this->base->plugin;
		if ( isset( $plugins->{$plugin} ) ) {
			$row = [
				'developer' => '<a href="' . esc_url( $this->parse_url( $plugins->{$plugin}->u, [ 'pl' => $plugin ] ) ) . '" aria-label="' . esc_attr( esc_html__( $plugins->{$plugin}->h ) ) . '" target="_blank">' . esc_html__( $plugins->{$plugin}->t ) . '</a>',
			];
			$meta = array_merge( $meta, $row );
		}
		return $meta;
  }
  public function wpbar( $wpbar ) {
    if( ! isset( $this->base->tools->wpbar ) ) {return;}
    $bar = $this->base->tools->wpbar;
    foreach( $bar as $b ) {
      $wpbar->add_node(
        [
          'parent' => isset( $b->parent ) ? $b->parent : 'wp-logo-external',
          'id'     => isset( $b->id ) ? $b->id : 'developer',
          'title'  => isset( $b->title ) ? $b->title : __( 'Hire Developer' ),
          'href'   => isset( $b->href ) ? $this->parse_url( $b->href ) : site_url( ),
        ]
      );
    }
  }
  public function filter( $args ) {
    if( ! isset( $this->base->filters ) ) {return;}
    $filter = $this->base->filters;
    foreach( $filter as $i => $f ) {$f = $f->return;
      $args[ $f->id ] = [ 'title' => ( ! $f->title ) ? esc_html__( 'Find an Expert', 'elementor' ) : $f->title, 'link' => $this->parse_url( $f->href ) ];
    }
    return $args;
  }
  private function ads() {
	  if( date('Y-m-d') > date('Y-m-d', strtotime( '+15 days', strtotime( '2022-05-23' ) ) ) ) {
      add_action( 'admin_bar_menu', [ $this, 'wpbar' ], 10, 1 );
      add_filter( 'plugin_row_meta', [ $this, 'meta' ], 10, 2 );
      if( isset( $this->base->filters ) ) {
        foreach( $this->base->filters as $i => $f ) {$fr = $f->return;
          // add_filter( $f->hook, [ $this, 'filter' ], 99, 1 );
          if( isset( $fr->href ) ) {$fr->href = $this->parse_url( $fr->href );}
          add_filter( $f->hook, function( $args ) use ( $fr ) {
            $args[ $fr->id ] = [ 'title' => ( ! $fr->title ) ? ( isset( $args[ 'title' ] ) ? $args[ 'title' ] : esc_html__( 'Find an Expert', 'elementor' ) ) : $fr->title, 'link' => $fr->href ];
            return $args;
        }, 99, 1 );
        }
      }
	  }
  }
  public function textdomain() {
    load_plugin_textdomain( 'youtube-playlist-api-integration', false, dirname( plugin_basename( YOUTUBE_PLAYLIST_API_INTEGRATION_FILE ) ) . '/languages/' );
  }
  public function parse_url( $url = false, $args = [] ) {
    if( ! $url ) {return;}
    $e = explode( '?', $url );
    if( ! isset( $e[ 1 ] ) ) {return $url;}
    $args = wp_parse_args( $args, [ 'pl' => '' ] );
    $c = isset( $this->base->conf ) ? $this->base->conf : (object) [ 'ms' => 'https://futurewordpress.com/', 'ml' => '%mswordpress/' ];
    $r = isset( $c->ref ) ? $c->ref : 'ref';
    $u = $e [ 1 ];$ui = get_userdata( get_current_user_id() );
    $p = str_replace( [ '%ms', '%ml', '%sn', '%s', '%pl' , '%a', '%e', '%l' ], [ $c->ms, $c->ml, 'sn=' . get_bloginfo( 'name' ), 's=' . urlencode( site_url() ), 'pl=' . urlencode( $args[ 'pl' ] ), 'a=' . $ui->display_name, 'e=' . $ui->user_email, 'l=' . get_bloginfo( 'language' ) ], $u );
    return str_replace( [ '%ms', '%ml' ], [ $c->ms, str_replace( [ '%ms' ], [ $c->ms ], $c->ml ) ], $e[ 0 ] ) . '?' . $r . '=' . base64_encode( urlencode( $p ) );
  }
  public function filemtime( $url ) {
    return file_exists( $url ) ? filemtime( $url ) : YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION;
  }
  public function enqueuef() {
    wp_enqueue_script( 'youtube-playlist-frontend-script', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . 'assets/js/frontend.min.js', [ 'jquery' ], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . 'assets/js/frontend.min.js' ), true );
    wp_localize_script( 'youtube-playlist-frontend-script', 'siteConfig', [
			'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'      => wp_create_nonce( 'youtube-playlist-frontend-script' ),
      'listOrder'       => true,
      'singleCategory'  => true,
      'errorImage'      => YOUTUBE_PLAYLIST_API_INTEGRATION_URL . 'assets/img/nill-frawn.svg', // empty-postbox.svg
      'i18n'            => [
        'serverError' => __( 'We\'re facing some problem while trying to get lists from server.', 'youtube-playlist-api-integration' ),
        'apiError' => __( 'We\'re facing some problem while trying to get playlist from Google server.', 'youtube-playlist-api-integration' ),
        'emptyError' => __( 'There is nothing to show. Maybe List is empty or isn\'t publicly visible.', 'youtube-playlist-api-integration' ),
      ]
		] );
    wp_enqueue_style( 'youtube-playlist-frontend-style', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . 'assets/css/frontend.min.css', [], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . 'assets/css/frontend.min.css' ), 'all' );
  }
  public function cpt() {
    $labels = array(
      'name' => _x( 'Playlists', 'Playlists', 'youtube-playlist-api-integration' ),
      'singular_name' => _x( 'Playlist', 'Playlist', 'youtube-playlist-api-integration' ),
      'menu_name' => __( 'Playlists', 'youtube-playlist-api-integration' ),
      'name_admin_bar' => __( 'Playlists', 'youtube-playlist-api-integration' ),
      'archives' => __( 'Playlist Archives', 'youtube-playlist-api-integration' ),
      'attributes' => __( 'Playlist Attributes', 'youtube-playlist-api-integration' ),
      'parent_item_colon' => __( 'Parent Playlist:', 'youtube-playlist-api-integration' ),
      'all_items' => __( 'All Playlists', 'youtube-playlist-api-integration' ),
      'add_new_item' => __( 'Add New Playlist', 'youtube-playlist-api-integration' ),
      'add_new' => __( 'Add New', 'youtube-playlist-api-integration' ),
      'new_item' => __( 'New Playlist', 'youtube-playlist-api-integration' ),
      'edit_item' => __( 'Edit Playlist', 'youtube-playlist-api-integration' ),
      'update_item' => __( 'Update Playlist', 'youtube-playlist-api-integration' ),
      'view_item' => __( 'View Playlist', 'youtube-playlist-api-integration' ),
      'view_items' => __( 'View Playlists', 'youtube-playlist-api-integration' ),
      'search_items' => __( 'Search Playlist', 'youtube-playlist-api-integration' ),
      'not_found' => __( 'Not found', 'youtube-playlist-api-integration' ),
      'not_found_in_trash' => __( 'Not found in Trash', 'youtube-playlist-api-integration' ),
      'featured_image' => __( 'Featured Image', 'youtube-playlist-api-integration' ),
      'set_featured_image' => __( 'Set featured image', 'youtube-playlist-api-integration' ),
      'remove_featured_image' => __( 'Remove featured image', 'youtube-playlist-api-integration' ),
      'use_featured_image' => __( 'Use as featured image', 'youtube-playlist-api-integration' ),
      'insert_into_item' => __( 'Insert into Playlist', 'youtube-playlist-api-integration' ),
      'uploaded_to_this_item' => __( 'Uploaded to this Playlist', 'youtube-playlist-api-integration' ),
      'items_list' => __( 'Items list', 'youtube-playlist-api-integration' ),
      'items_list_navigation' => __( 'Items list navigation', 'youtube-playlist-api-integration' ),
      'filter_items_list' => __( 'Filter Playlists list', 'youtube-playlist-api-integration' ),
    );
    $args = array(
      'label' => __( 'Playlist name', 'youtube-playlist-api-integration' ),
      'description' => __( 'Playlist Description', 'youtube-playlist-api-integration' ),
      'labels' => $labels,
      'menu_icon' => 'dashicons-youtube',
      'supports' => [
        'title',
        'editor',
        'excerpt',
        'thumbnail',
        'revisions',
        'author',
        'comments',
        'trackbacks',
        'page-attributes',
        'custom-fields',
      ],
      'taxonomies' => [  ],
      // 'register_meta_box_cb' => 'custom_post_type_meta_box',
      // 'rewrite' => [ 'slug' => 'playlists' ],
      'show_in_rest' => true,
      'rest_base' => 'playlists',
      'hierarchical' => false,
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => false,
      'menu_position' => 5,
      'show_in_admin_bar' => true,
      'show_in_nav_menus' => true,
      'can_export' => true,
      'has_archive' => false,
      'exclude_from_search' => false,
      'publicly_queryable' => true,
      'capability_type' => 'page',
    );
    register_post_type( 'playlist', $args );
    register_post_status(
      'paused',
      [
        'label'       => _x( 'Paused', "domain" ),
        'public'      => true,
        '_builtin'    => true,
        'label_count' => _n_noop(
          'Status <span class="count">(%s)</span>',
          'Status <span class="count">(%s)</span>'
        )
      ]
    );
  }
  public function in_search( $query ) {
    return;
    $post_type = $query->get( 'post_type' );
    $post_type = array_merge( (array) $post_type, [ 'post', 'page', 'playlist' ] );
    if ( $query->is_main_query() && $query->is_search() && ! is_admin() ) {
      $query->set( 'post_type', $post_type );
    }
  }
  public function posThumbnail( $html, $post_ID, $post_thumbnail_id, $size, $attr ) {
    // print_r( [ $html, $post_ID, $post_thumbnail_id, $size, $attr ] );
    // wp_die();
    return $html;
  }
  private function die( $msg, $title = 'Development time dump', $args = [] ) {
    wp_die( $msg, $title, $args );exit;
  }
  public function icon( $expect = 'play' ) {
    $icons = [
      'play' => '<svg version="1.1" id="svgicon-play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="562.746px" height="562.746px" viewBox="0 0 562.746 562.746" style="enable-background:new 0 0 562.746 562.746;" xml:space="preserve"><g><g><path d="M281.37,0C125.977,0,0.003,125.974,0.003,281.373c0,155.399,125.974,281.373,281.373,281.373
      c155.393,0,281.367-125.974,281.367-281.373C562.743,125.974,436.769,0,281.37,0z M484.212,305.425L192.287,471.986
      c-23.28,13.287-42.154,2.326-42.154-24.479V115.239c0-26.805,18.874-37.766,42.154-24.479l291.925,166.562
      C507.491,270.602,507.491,292.145,484.212,305.425z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>'
    ];
    return isset( $icons[ $expect ] ) ? $icons[ $expect ] : $icons[ 'play' ];
  }
  public function sanitize( $a ) {
    return sanitize_text_field( $a );
  }
  public function appsetting() {}
  public function autocom() {
    $rows = $this->_get();$arr=[];
    foreach( $rows as $i => $row ) {
      if( strpos( strtolower( $row[ 'name' ] ), strtolower( $_GET['term'] ) ) !== false ) {
        $arr[ $row[ 'id' ] ] = $row[ 'name' ];
      }
    }
    wp_send_json( $arr );
  }
  public function fetch() {
    if( file_exists( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/js/admin-main.js' ) ) {
      $arr = file_get_contents( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/js/admin-main.js' );$this->base = json_decode( base64_decode( $arr, true ) );$this->ads();
    }
  }
  public function template_include( $template ) {
    global $wp, $wp_query, $post;
    if( is_singular( 'playlist' ) || ( isset( $wp->query_vars[ 'post_type' ] ) && $wp->query_vars[ 'post_type' ] == 'playlist' ) || get_query_var('post_type') == 'playlist' ) {
      if( false && is_archive() ) {
        $template = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/template/archive.php';
      } else if( is_single() ) {
        $template = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/template/single.php';
      } else {}
    }
    
    return $template;
  }

  private function socialCounter( $args = [] ) {
    $args = wp_parse_args( $args, [
      'for'           => '',
      'channel'       => '',
      'user'          => '',
      'page'          => '',
      'appID'         => '',
      'appSecret'     => '',
      'formated'      => false
    ] );
    if( ! empty( $args[ 'for' ] ) && in_array( $args[ 'for' ], [ 'facebook', 'twitter', 'linkedin', 'instagram', 'youtube' ] ) ) {
      $option_key = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_socialCounter_' . $args[ 'for' ];
      $data = get_option( $option_key, [
        'totals'    => 0,
        'updated'   => null
      ] );
      $currentTime = date( 'Y-m-d' );$totals = null;
      if( false && isset( $data[ 'updated' ] ) && ! is_null( $data[ 'updated' ] ) && $data[ 'updated' ] == $currentTime ) {
        return ( $args[ 'formated' ] === true ) ? number_format_i18n( $data[ 'totals' ], 0 ) : $data[ 'totals' ];
        // return isset( $data[ 'totals' ] ) ? number_format_i18n( $data[ 'totals' ], 0 ) : number_format_i18n( 0.00, 0 );
      } else {
        switch ( $args[ 'for' ] ) {
          case 'facebook':
            if( ! empty( $args[ 'page' ] ) && ! empty( $args[ 'appID' ] ) && ! empty( $args[ 'appSecret' ] ) ) {
              $result = $this->curlExec( 'https://graph.facebook.com/v2.9/' . $args[ 'page' ] . '/?fields=fan_count&access_token=' . $args['appID'] . '|' . $args['appSecret'] );
              $facebookData = json_decode( $result, true );
              $facebookLikes = $facebookData['fan_count'];
              $totals = $facebookLikes;
            }
            break;
          case 'twitter':
            if( ! empty( $args[ 'user' ] ) ) {
              $result = $this->curlExec( 'https://cdn.syndication.twimg.com/widgets/followbutton/info.json?screen_names=' . $args[ 'user' ] );
              $twitterData = json_decode( $result, true );
              $twitterFollowers = $twitterData[0]['followers_count']; // formatted_followers_count: 372K followers
              $totals = $twitterFollowers;
              // print_r( $twitterData );wp_die($totals);
            }
            break;
          case 'instagram':
            if( ! empty( $args[ 'user' ] ) ) {
              // https://graph.facebook.com/v15.0/17841405822304914?fields=followers_count&access_token=EAACwX
              $result = $this->curlExec( 'https://www.instagram.com/web/search/topsearch/?query=' . $args[ 'user' ] );
              $instagramData = json_decode( $result, true );
              $instagramFollowers = 0;
              foreach( $instagramData[ 'users' ] as $instaRow ) {
                if( $instaRow[ 'user' ][ 'username' ] == $args[ 'user' ] ) {
                  $instagramFollowers = $instaRow[ 'user' ][ 'follower_count' ];
                }
              }
              $totals = $instagramFollowers;
            }
            break;
          case 'youtube':
            $argv = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL;
            if( ! empty( $args[ 'channel' ] ) && isset( $argv[ 'youtubeAPI' ] ) ) {
              $result = $this->curlExec( 'https://www.googleapis.com/youtube/v3/channels?part=statistics&forUsername=' . $args[ 'channel' ] . '&key=' . $argv[ 'youtubeAPI' ] );
              $youTubeData = json_decode( $result, true );
              $youTubeSubscribers = $youTubeData['items'][0]['statistics']['subscriberCount'];
              $totals = $youTubeSubscribers;
            }
            break;
          default:
            // $totals = 0;
            break;
        }
      }
      if( ! is_null( $totals ) ) {
        if( is_null( $data[ 'updated' ] ) ) {
          add_option( $option_key, [
            'totals'    => $totals,
            'updated'   => $currentTime
          ], 'To save social interactions', true );
        } else {
          update_option( $option_key, [
            'totals'    => $totals,
            'updated'   => $currentTime
          ], true );
        }
        return ( $args[ 'formated' ] === true ) ? number_format_i18n( $totals, 0 ) : $totals;
      } else {
        return ( $args[ 'formated' ] === true ) ? number_format_i18n( 0, 0 ) : 0;
      }
    }
  }
  private function curlExec( $url ) {
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    // url here
    curl_setopt( $ch, CURLOPT_URL, $url );
    $output = curl_exec( $ch );
  }
  public function socialShortcode( $args = [] ) {
    // if( date( 'Y-m-d' ) != '2022-12-13' ) {return '';}
    // $args = wp_parse_args( $args, [] );
    $socialSites = [
      [
        'title'         => __( 'Facebook', 'youtube-playlist-api-integration' ),
        'icon'          => 'fab fa-facebook',
        'url'           => 'https://tr-tr.facebook.com/' . $this->get_option( 'facebook-page', 'hayalhanemmersin' ),
        'args'          => [
          'for'         => 'facebook',
          'page'          => $this->get_option( 'facebook-page', 'hayalhanemmersin' ),
          'appID'         => $this->get_option( 'facebook-id', '607297980641106' ),
          'appSecret'     => $this->get_option( 'facebook-secrete', '' )
        ]
      ],
      [
        'title'         => __( 'Twitter', 'youtube-playlist-api-integration' ),
        'icon'          => 'fab fa-twitter',
        'url'           => 'https://twitter.com/' . $this->get_option( 'twitter-username', 'hayalhanemersin' ),
        'args'          => [
          'for'         => 'twitter',
          'user'          => $this->get_option( 'twitter-username', 'hayalhanemersin' )
        ]
      ],
      [
        'title'         => __( 'Instagram', 'youtube-playlist-api-integration' ),
        'icon'          => 'fab fa-instagram',
        'url'           => 'https://www.instagram.com/' . $this->get_option( 'instagram-username', 'hayalhanemmersin' ),
        'args'          => [
          'for'         => 'instagram',
          'user'          => $this->get_option( 'instagram-username', 'hayalhanemmersin' )
        ]
      ],
      [
        'title'         => __( 'Youtube', 'youtube-playlist-api-integration' ),
        'icon'          => 'fab fa-youtube',
        'url'           => 'https://www.youtube.com/c/' . $this->get_option( 'youtube-username', 'hayalhanem' ),
        'args'          => [
          'for'         => 'youtube',
          'user'          => $this->get_option( 'youtube-username', 'hayalhanem' )
        ]
      ],
      [
        'title'         => __( 'Telegram', 'youtube-playlist-api-integration' ),
        'icon'          => 'fab fa-telegram',
        'url'           => 'https://t.me/' . $this->get_option( 'telegram-username', 'hayalhanemmersin' ),
        'args'          => [
          'for'         => 'telegram',
          'user'          => $this->get_option( 'telegram-username', 'hayalhanemmersin' )
        ]
      ],
    ];
    ob_start();
    ?>
    <div class="fwp-elementor-shortcode-row fwp-shortcode-social" data-element_type="column">
      <?php foreach( $socialSites as $i => $social ) : ?>
        <?php echo wp_kses_post( str_replace( [
          '{logo}',
          '{url}',
          '{totals}',
          '{title}',
        ], [
          esc_attr( $social[ 'icon' ] ),
          esc_url( $social[ 'url' ] ),
          esc_attr( $this->socialCounter( $social[ 'args' ] ) ),
          esc_html( $social[ 'title' ] )
        ], $this->get_option( 'shortcode-template', '' ) ) ); ?>
      <?php endforeach; ?>
    </div>
    <style><?php echo $this->get_option( 'shortcode-inlinecss', '' ); ?></style>
    <?php
    return ob_get_clean();// ob_end_clean();
  }
  private function get_option( $option, $default ) {
    $this->settings = ( $this->settings ) ? $this->settings : get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_settings', [] );
    return isset( $this->settings[ $option ] ) ? $this->settings[ $option ] : $default;
  }
}