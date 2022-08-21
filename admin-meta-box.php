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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */


class SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_METABOX {
  protected static $_instance = null;
	public $id = null;
	public $base = null;
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
    $this->version = YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION;
  }
  public function init() {
    /**
     * Fire a hook on ajax post.
     */
    add_action( 'elementor/widgets/register', [ $this, 'register' ], 10, 1 );
    /**
     * All filter functions
     */
    add_filter( 'elementor/widgets/youtube_playlist/include/select', [ $this, 'playlist' ], 10, 1 );
    add_filter( 'elementor/widgets/youtube_playlist/exclude/select', [ $this, 'playlist' ], 10, 1 );
    add_filter( 'elementor/widgets/youtube_playlist/publiclist', [ $this, 'publiclist' ], 10, 1 );
  }
  public function playlist( $args ) {
    // global $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS;
    // $playlists = \SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN->playlists();
    $playlists = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'playlistId' ], false );
    if( ! $playlists ) {return $args;}
    $items = ( isset( $playlists[ 'items' ] ) && is_array( $playlists[ 'items' ] ) ) ? $playlists[ 'items' ] : [];
    foreach( $items as $i => $item ) {
      $item[ 'id' ] = isset( $item[ 'id' ] ) ? $item[ 'id' ] : false;
      if( $item[ 'id' ] && isset( $item[ 'snippet' ] ) ) {
        $args[ $item[ 'id' ] ] = esc_html( isset( $item[ 'snippet' ][ 'title' ] ) ? $item[ 'snippet' ][ 'title' ] : $i );
      }
    }
    return $args;
  }
  public function publiclist( $args ) {
    // global $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS;
    // $playlists = \SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN->playlists();
    $playlists = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'playlistId' ], false );
    if( ! $playlists || ! $playlists[ 'is_Public' ] ) {return $args;}
    $playlists[ 'items' ] = ( isset( $playlists[ 'items' ] ) && is_array( $playlists[ 'items' ] ) ) ? $playlists[ 'items' ] : [];
    foreach( $playlists[ 'items' ] as $i => $item ) {
      if( $item[ 'is_Public' ] ) {
        $args[ 'items' ][] = [
          'id' => $item[ 'id' ],
          'url' => $this->url( $item[ 'id' ] ),
          'title' => $item[ 'snippet' ][ 'title' ],
          'localized' => $item[ 'snippet' ][ 'localized' ],
          // 'description' => $item[ 'snippet' ][ 'description' ],
          'thumbnail' => $this->thumb( $item[ 'snippet' ][ 'thumbnails' ] ),
          'channelTitle' => $item[ 'snippet' ][ 'channelTitle' ],
          'channelId' => $item[ 'snippet' ][ 'channelId' ]
        ];
      }
    }
    return $args;
  }
  public function thumb( $images ) {
    return isset( $images[ 'medium' ] ) ? $images[ 'medium' ][ 'url' ] : $images[ 'default' ][ 'url' ];
  }
  public function url( $id ) {
    return 'https://www.youtube.com/embed/videoseries?list=' . $id;
  }
  public function register( $widgets_manager ) {
    include_once YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/elementor/widgets/widget-youtube-playlists.php';
    $widgets_manager->register( new \Elementor_Widget_videotyplaylist() );
  }
};
// global $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_METABOX;
$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_METABOX = SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_METABOX::instance();
$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_METABOX->init();

