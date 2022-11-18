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
 */

defined( 'ABSPATH' ) || exit;


class SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_ADMIN {
  protected static $_instance = null;
	public $id = null;
	public $version = null;
  public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	private function __construct() {
    $this->id = YOUTUBE_PLAYLIST_API_INTEGRATION_ID;
  }
  public function init() {
    add_action( 'admin_init', [ $this, 'enqueue' ], 10, 0 );
    add_action( 'admin_notices', [ $this, 'notice' ] );
    // add_action( 'admin_init', [ $this, 'metabox' ], 10, 0 );
    add_action( 'admin_menu', [ $this, 'menu' ], 10, 0 );

    add_action( 'wp_ajax_' . $this->id, [ $this, 'ajax' ] );
    add_action( 'wp_ajax_nopriv_' . $this->id, [ $this, 'ajax' ] );

    add_action( 'admin_post_syplapiint_setup', [ $this, 'post' ] );

    add_action( 'admin_init', [ $this, 'updates' ], 10, 0 );

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
  public function filemtime( $url ) {
    return file_exists( $url ) ? filemtime( $url ) : YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION;
  }
  public function notice() {
    $transiant = get_transient( $this->id );
    if( $transiant === false ) {return;}
    $html_message = sprintf( '<div class="updated %s">%s</div>', esc_attr( ( isset( $transiant[ 'type' ] ) && in_array( $transiant[ 'type' ], [ 'success', 'error', 'failed', 'updated' ] ) ) ? $transiant[ 'type' ] : 'error' ), wpautop( esc_html( isset( $transiant[ 'message' ] ) ? $transiant[ 'message' ] : $transiant ) ) );
    echo wp_kses_post( $html_message );
    delete_transient( $this->id );
  }
  public function metabox( $url = false ) {
    if( file_exists( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/admin-meta-box.php' ) ) {
      include_once YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/admin-meta-box.php';
    }
  }
  public function menu() {
    add_menu_page( __( 'Youtube playlists', 'youtube-playlist-api-integration' ), __( 'Playlists', 'youtube-playlist-api-integration' ), 'manage_options', 'youtube-playlists', [ $this, 'page'], 'dashicons-youtube', 10 );
    add_submenu_page( 'youtube-playlists', __( 'Youtube integration API Setup', 'youtube-playlist-api-integration' ), __( 'Settings', 'youtube-playlist-api-integration' ), 'manage_options', 'youtube-setup-playlists', [ $this, 'setting' ] );
    add_submenu_page( 'youtube-playlists', __( 'Playlists shortcode', 'youtube-playlist-api-integration' ), __( 'Shortcodes', 'youtube-playlist-api-integration' ), 'manage_options', 'youtube-shortcode-playlists', [ $this, 'shortcode' ] );
    // Hide menu from admin link for custom post type. Currently hidden by default.
    // remove_menu_page( 'edit.php?post_type=playlist' );
  }
  public function page( $args = [] ) {
    $args = wp_parse_args( $args, YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL );
    $file = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/admin-option-page.php';
    if( ! file_exists( $file ) ) {return;} else {include $file;}
  }
  public function setting( $args = [] ) {
    $args = wp_parse_args( $args, YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL );
    $file = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/admin-setting-page.php';
    if( ! file_exists( $file ) ) {return;} else {include $file;}
  }
  public function shortcode( $args = [] ) {
    $args = wp_parse_args( $args, YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL );
    $file = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/admin-shortcode-page.php';
    if( ! file_exists( $file ) ) {return;} else {include $file;}
  }
  public function playlists( $expect = false ) {
    $expect = ( $expect ) ? $expect : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ];
    $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . $expect;
    $get = get_option( $option, false );
    if( $get ) {
      return $get;
    } else {
      $get = $this->rget( 'playlists', [ 'channelId' => $expect ] );
      add_option( $option, $get, 'Youtube playload auto save as draft with status', true );
      return $get;
    }
  }
  public function channels() {
    $channelList = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelList' ];
    return $channelList;
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
  protected function allowpush() {
    return true;
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
    ] );
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
  public function ajax() {
    /**
     * Can be filter using nonce: youtube_api_playlist_toggle
     * $_POST[ 'nonce' ]
     */
    if( ! isset( $_POST[ 'toggle' ] ) || empty( $_POST[ 'toggle' ] ) ) {
      wp_send_json_error( __( 'Failed to fetch request!', 'youtube-playlist-api-integration' ) );
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
              wp_send_json_success( __( 'Successfully Submitted this playlist action :)', 'youtube-playlist-api-integration' ), 200 );
            } else {
              wp_send_json_error( __( 'Failed to findout targeted playlist :(', 'youtube-playlist-api-integration' ) );
            }
          }
          break;
        case 'channelId' :
          $row = $this->playlists( $expect );
          if( isset( $row[ 'items' ] ) && is_array( $row[ 'items' ] ) ) {
            $row[ 'is_Public' ] = ( isset( $_POST[ 'status' ] ) && $_POST[ 'status' ] == 'on' ) ? true : false;
            update_option( $option, $row, true );
            wp_send_json_success( __( 'Successfully Submitted this channel action :)', 'youtube-playlist-api-integration' ), 200 );
          } else {
            wp_send_json_error( __( 'Failed to findout your channel :(', 'youtube-playlist-api-integration' ) );
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
              wp_send_json_success( __( 'Successfully changed category :)', 'youtube-playlist-api-integration' ), 200 );
            } else {
              wp_send_json_error( __( 'Failed to findout category :(', 'youtube-playlist-api-integration' ) );
            }
          }
          break;
      }
    }
  }
  public function post() {
    if( ! isset( $_POST['syplapiint_setup_nonce'] ) || ! wp_verify_nonce( $_POST['syplapiint_setup_nonce'], 'syplapiint_setup' ) ) {
    //  wp_nonce_ays( __( 'Are you Sure you want to confirming this action?', 'youtube-playlist-api-integration' ) );
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
      $lists = explode( "\n", $_POST[ 'settings' ][ 'playlists' ] );
      $playlists = $this->getchannel( $lists, $_POST[ 'settings' ][ 'youtubeapi' ] );
      $channel = [];$def = false;
      if( count( $playlists ) < 1 ) {foreach( $lists as $list ) {$channel[ $list ] = $list;$def = true;}}
      $_POST[ 'settings' ][ 'playlists' ] = ( count( $playlists ) >= 1 ) ? $playlists : $channel;
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
      set_transient( $this->id, [ 'type' => 'success', 'message' => __( 'Settings Saved successfully! If your playlists are correct, you can see their on playlist gallery\'s channel name.', 'youtube-playlist-api-integration' ) . ' ' . ( ( $def ) ? __( "Channel title can't be placed because of youtube API doesn't supply proper data.", 'youtube-playlist-api-integration' ) : '' ) ], 45 );
    } else {
      set_transient( $this->id, [ 'type' => 'error', 'message' => __( 'We\'re facing soome trouble identifing your request. Please contact to this plugin developer.', 'youtube-playlist-api-integration' ) ], 45 );
    }

    if( isset( $_POST[ '_wp_http_referer' ] ) && ! empty( $_POST[ '_wp_http_referer' ] ) ) {
      wp_redirect( $_POST[ '_wp_http_referer' ] );
    } else {
      wp_redirect( admin_url( 'admin.php?page=youtube-setup-playlists' ) );
    }
    
  }
  private function getchannel( $id = [], $api = false ) {
    if( ! $api ) {$api = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[
      'youtubeAPI' ];}
    $return = wp_remote_get( $this->apis( 'channels', [ 'id' => $id, 'youtubeAPI' => $api ] ), [] );
    $channelLists = json_decode( $return[ 'body' ], true );$channelList = [];
    
    // wp_die( print_r( [ $channelLists, $this->apis( 'channels', [ 'id' => $id, 'youtubeAPI' => $api ] ) ]) );
    if( isset( $channelLists[ 'items' ] ) ) {
      foreach( $channelLists[ 'items' ] as $i => $item ) {
        $channelList[ $item[ 'id' ] ] = ( isset( $item[ 'snippet' ] ) && isset( $item[ 'snippet' ][ 'title' ] ) ) ? $item[ 'snippet' ][ 'title' ] : $item[ 'id' ];
      }
    }
    return $channelList;
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
      wp_send_json_success( $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->ajaxSort( $remote ), 200 );
    }
  }
  public function toPost( $args = [] ) {
    if( ! isset( $_GET[ 'ondev' ] ) ) {return;}
    // $this->playListPost( $args );
    $this->playlistItems( $args );
    wp_die();
    wp_safe_redirect( admin_url( 'admin.php?page=youtube-playlists' ) );
  }
  private function playListPost( $args = [] ) {
    $items = isset( $args[ 'items' ] ) ? $args[ 'items' ] : [];
    foreach( $items as $item ) {
      $itemType = isset( $item[ 'kind' ] ) ? explode( '#', $item[ 'kind' ] ) : [];
      $itemType = isset( $itemType[ 1 ] ) ? $itemType[ 1 ] : false;
      if( $itemType == 'playlist' ) {
        $posts = get_posts( [
          'numberposts'   => 1,
          'post_type'     => 'playlist',
          'meta_key'      => 'fwp_meta-playlistId',
          'meta_value'    => $item[ 'id' ],
        ] );
        if( count( $posts ) <= 0 ) {
          $post_args = [
            'post_type'  => 'playlist',
            'post_status'   => 'publish',
            'post_title'    => wp_strip_all_tags( $item[ 'snippet' ][ 'title' ] ),
            'post_content'  => $item[ 'snippet' ][ 'description' ],
            'post_excerpt'  => substr( $item[ 'snippet' ][ 'description' ], 0, 450 ),
            // 'post_category' => [],
            'post_parent'   => 0,
            'meta_input'    => [
              'fwp_meta-playlistId'     => $item[ 'id' ],
              'fwp_meta-channelId'      => $item[ 'snippet' ][ 'channelId' ],
              'fwp_meta-channelTitle'   => $item[ 'snippet' ][ 'channelTitle' ],
              'fwp_meta-localized'      => $item[ 'snippet' ][ 'localized' ],
              'fwp_meta-thumbnails'     => $item[ 'snippet' ][ 'thumbnails' ],
            ],
            'post_date'     => date( 'Y-m-d H:i:s', strtotime( $item[ 'snippet' ][ 'publishedAt' ] ) )
          ];
          if( isset( $posts[0] ) && isset( $posts[0]->ID ) ) {
            $post_args[ 'ID' ] = $posts[0]->ID;
          }
          $post_id = wp_insert_post( $post_args );
          $this->featuredImage( $post_id, $item );
        } else {
          // Already exists
        }
      }
    }
  }
  private function playlistItems( $args = [] ) {
    $items = isset( $args[ 'items' ] ) ? $args[ 'items' ] : [];
    foreach( $items as $item ) {
      $itemType = isset( $item[ 'kind' ] ) ? explode( '#', $item[ 'kind' ] ) : [];
      $itemType = isset( $itemType[ 1 ] ) ? $itemType[ 1 ] : false;
      if( $itemType == 'playlistItem' ) {
        $parent = get_posts( [
          'numberposts'   => 1,
          'post_type'     => 'playlist',
          'meta_key'      => 'fwp_meta-playlistId',
          'meta_value'    => $item[ 'snippet' ][ 'playlistId' ],
        ] );
        $posts = get_posts( [
          'numberposts'   => 1,
          'post_type'     => 'playlist',
          'meta_key'      => 'fwp_meta-playlistItemId',
          'meta_value'    => $item[ 'id' ],
        ] );
        if( count( $posts ) <= 0 ) {
          $post_args = [
            'post_type'  => 'playlist',
            'post_status'   => 'publish',
            'post_title'    => wp_strip_all_tags( $item[ 'snippet' ][ 'title' ] ),
            'post_content'  => $item[ 'snippet' ][ 'description' ],
            'post_excerpt'  => substr( $item[ 'snippet' ][ 'description' ], 0, 450 ),
            // 'post_category' => [],
            'post_parent'   => ( isset( $parent[0] ) && isset( $parent[0]->ID ) ) ? $parent[0]->ID : 0,
            'meta_input'    => [
              'fwp_meta-playlistItemId'   => $item[ 'id' ],
              'fwp_meta-privacyStatus'    => $item[ 'status' ][ 'privacyStatus' ],
              'fwp_meta-parentPlaylistId' => $item[ 'snippet' ][ 'playlistId' ],
              'fwp_meta-channelId'        => $item[ 'snippet' ][ 'channelId' ],
              'fwp_meta-channelTitle'     => $item[ 'snippet' ][ 'channelTitle' ],
              'fwp_meta-videoId'          => $item[ 'contentDetails' ][ 'videoId' ],
              'fwp_meta-thumbnails'       => $item[ 'snippet' ][ 'thumbnails' ],
            ],
            'post_date'     => date( 'Y-m-d H:i:s', strtotime( $item[ 'snippet' ][ 'publishedAt' ] ) )
          ];
          if( isset( $posts[0] ) && isset( $posts[0]->ID ) ) {
            $post_args[ 'ID' ] = $posts[0]->ID;
          }
          $post_id = wp_insert_post( $post_args );
          $this->featuredImage( $post_id, $item );
        }
      }
    }
  }
  private function featuredImage( $post_id, $item ) {
    if( true && $post_id ) { // Auto enabled attachment ot save local directory
      $thumb = isset( $item[ 'snippet' ][ 'thumbnails' ][ 'maxres' ] ) ? $item[ 'snippet' ][ 'thumbnails' ][ 'maxres' ] : (
        isset( $item[ 'snippet' ][ 'thumbnails' ][ 'standard' ] ) ? $item[ 'snippet' ][ 'thumbnails' ][ 'standard' ] : (
          isset( $item[ 'snippet' ][ 'thumbnails' ][ 'high' ] ) ? $item[ 'snippet' ][ 'thumbnails' ][ 'high' ] : (
            isset( $item[ 'snippet' ][ 'thumbnails' ][ 'medium' ] ) ? $item[ 'snippet' ][ 'thumbnails' ][ 'medium' ] : (
              isset( $item[ 'snippet' ][ 'thumbnails' ][ 'default' ] ) ? $item[ 'snippet' ][ 'thumbnails' ][ 'default' ] : [
                'url' => ''
              ]
            )
          )
        )
      );
      $attach_id = $this->insertUrlAttach( $thumb[ 'url' ], $post_id );
      if( $attach_id ) {
        set_post_thumbnail( $post_id, $attach_id );
      }
    }
  }
  private function insertUrlAttach( $url, $post_id = null ) {
    if ( ! class_exists( 'WP_Http' ) ) {
      require_once ABSPATH . WPINC . '/class-http.php';
    }
    $http     = new WP_Http();
    $response = $http->request( $url );
    if ( 200 !== $response['response']['code'] ) {
      return false;
    }
    $upload = wp_upload_bits( basename( $url ), null, $response['body'] );
    if ( ! empty( $upload['error'] ) ) {
      return false;
    }
    $file_path        = $upload['file'];
    $file_name        = basename( $file_path );
    $file_type        = wp_check_filetype( $file_name, null );
    $attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
    $wp_upload_dir    = wp_upload_dir();
    $post_info = array(
      'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
      'post_mime_type' => $file_type['type'],
      'post_title'     => $attachment_title,
      'post_content'   => '',
      'post_status'    => 'inherit',
    );
    // Create the attachment.
    $attach_id = wp_insert_attachment( $post_info, $file_path, $post_id );
    // Include image.php.
    require_once ABSPATH . 'wp-admin/includes/image.php';
    // Generate the attachment metadata.
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
    // Assign metadata to attachment.
    wp_update_attachment_metadata( $attach_id, $attach_data );
    return $attach_id;
  }
}