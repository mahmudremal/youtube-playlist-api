<?php
/**
 * Plugin Name: YouTube playlist integration
 * Description: This plugin is used for colllecting data from YouTube using youtube official API. It is collecting data from a channel, by private API key, and then proposed to publish here with category selecting.
 * Plugin URI: https://github.com/mahmudremal/youtube-playlist-api/
 * Author: Future WordPress
 * Version: 1.3.6
 * Author URI: https://futurewordpress.com/
 * Text Domain: youtube-playlist-api-integration
 * Domain Path: /languages
 * @category	WordPress Development
 * @package		youtube-playlist-api-integration
 * @author		FutureWordPress.com <info@futurewordpress.com/>
 * @copyright	Copyright (c) 2022-23
 * @link		https://futurewordpress.com/
 * @version		1.3.6
 */
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_FILE' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_FILE', __FILE__ );
defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION', '1.3.6' );
defined( 'YOUTUBE_PLAYLIST_API_INTEGRATION_ID' ) || define( 'YOUTUBE_PLAYLIST_API_INTEGRATION_ID', 'special_youtube_playlist_api_integration_plugin' );
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
  'youtubeAPI' => isset( $settings[ 'youtubeapi' ] ) ? $settings[ 'youtubeapi' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL_PREV[ 'youtubeAPI' ],
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

include_once YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/class-youtube-project.php';
global $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS;
$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS = SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN::instance();
$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->init();
if( is_admin() ) {
  include_once YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/inc/class-project-admin.php';
  $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_ADMIN = SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_ADMIN::instance();$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_ADMIN->init();
}

// include_once YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/elementor/elementor-register-widget.php';
?>