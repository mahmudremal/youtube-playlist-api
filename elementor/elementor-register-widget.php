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


class SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_WIDGETS {
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
    // add_action( 'elementor/widgets/register', [ $this, 'register' ], 10, 1 );
    /**
     * All filter functions
     */
    add_filter( 'elementor/widgets/youtube_playlist/include/select', [ $this, 'playlist' ], 10, 1 );
    add_filter( 'elementor/widgets/youtube_playlist/exclude/select', [ $this, 'playlist' ], 10, 1 );
    add_filter( 'elementor/widgets/youtube_playlist/channel/select', [ $this, 'channel' ], 10, 1 );
    add_filter( 'elementor/widgets/youtube_playlist/publiclist', [ $this, 'publiclist' ], 10, 1 );
    add_filter( 'fwp_columns', [ $this, 'columns_class' ], 10, 2 );

    add_shortcode( 'youtube-gallery', [ $this, 'gallery' ] );

    // if( isset( $_GET[ 'post' ] ) && $_GET[ 'post' ] == '7385' && isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'elementor' && isset( $_GET[ 'myprint' ] ) ) {
    //   print_r( apply_filters( 'elementor/widgets/youtube_playlist/channel/select', [ 'all' => __( 'All', 'domain' ) ] ) );
    //   print_r( apply_filters( 'elementor/widgets/youtube_playlist/publiclist', [ 'all' => true, 'channel' => 'UCXs8nFqUPQaJZxAt1b3Wblw' ] ) );
    //   wp_die();
    // }
  }
  public function gallery( $args ) {
    $args = wp_parse_args( $args, [
      'channel' => YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ],
      'columns' => 4,
      'include' => '',
      'exclude' => ''
    ] );
    $args[ 'include' ] = explode( ',', $args[ 'include' ] );
    $args[ 'exclude' ] = explode( ',', $args[ 'exclude' ] );
    $playlists = $this->publiclist( [ 'include' => $args[ 'include' ], 'exclude' => $args[ 'exclude' ], 'channel' => $args[ 'channel' ], 'items' => [] ] );
    ?>
    <div class="elementor-container elementor-column-gap-default elementor-container-flex-wrap">
		 <?php
			foreach( $playlists['items'] as $index => $item ) {
				?>
				<div class="<?php echo esc_attr( impolde( ' ', $this->columns_class( $args[ 'columns' ], 'imagehvr-wrapper elementor-element elementor-column elementor-inner-column' ) ) ); ?>">
					<div class="imagehvr">
						<div class="imagehvr-content-wrapper imagehvr-content-center imagehvr-anim-zoom-in-alt">
								<a href="<?php echo esc_url( $item['url'][ 'playlists' ] ); ?>" class="imagehvr-link" data-embed="<?php echo esc_url( $item['url'][ 'playlistembed' ] ); ?>" data-id="<?php echo esc_attr( $item['id' ] ); ?>">
									<span class="imagehvr-icon ih-delay-zero imagehvr-anim-none">
										<i class="fas fa-play-circle"></i>
									</span>
								</a>
								<picture>
									<source sizes="162px" type="image/webp" data-srcset="<?php echo esc_url( $item['thumbnail'] ); ?>" srcset="<?php echo esc_url( $item['thumbnail'] ); ?>" >
									<img width="480" height="270" src="<?php echo esc_url( $item['thumbnail'] ); ?>" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full lazyautosizes lazyloaded" alt="" data-eio="p" data-src="<?php echo esc_url( $item['thumbnail'] ); ?>" decoding="async" data-srcset="<?php echo esc_url( $item['thumbnail'] ); ?>" data-sizes="auto" sizes="162px" srcset="<?php echo esc_url( $item['thumbnail'] ); ?>" >
									<noscript>
										<img width="480" height="270" src="<?php echo esc_url( $item['thumbnail'] ); ?>" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full" alt="" srcset="<?php echo esc_url( $item['thumbnail'] ); ?>" sizes="(max-width: 480px) 100vw, 480px" data-eio="l" />
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
  }
  public function columns_class( $columns, $extra = false ) {
		if( $extra ) {$class = explode( ' ', $extra );} else {$class = [];}
		$class[] = 'elementor-col-' . number_format( ( 12 / $columns ), 0, '', '' );
		$class[] = 'elementor-col-md-' . number_format( ( ( 12 / $columns ) * 2 ), 0, '', '' );
		$class[] = 'elementor-col-sm-' . number_format( ( ( 12 / $columns ) * 3 ), 0, '', '' );
		$class[] = 'elementor-col-xs-' . number_format( ( ( 12 / $columns ) * 4 ), 0, '', '' );
		return $class;
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
  public function channel( $args ) {
    // global $wpdb;$prefix = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_';
    // option_name option_value option_id
    // $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE '%{$prefix}%' AND option_name != '{$prefix}settings' AND option_name != '{$prefix}backup';", ARRAY_A );

    // $args = wp_parse_args( $args, [ 'channel' => '' ] );
    $playlists = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelList' ];
    if( ! $playlists ) {return $args;}
    return $playlists;
  }
  public function publiclist( $args = [] ) {
    if( isset( $args[ 'all' ] ) && $args[ 'all' ] ) {
      $opt = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_settings', [ 'playlists' => [] ] );
      foreach( $opt[ 'playlists' ] as $id => $title ) {
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
                'thumbnail' => $this->thumb( $item[ 'snippet' ][ 'thumbnails' ] ),
                'channelTitle' => $item[ 'snippet' ][ 'channelTitle' ],
                'channelId' => $item[ 'snippet' ][ 'channelId' ]
              ];
            }
          }
        }
      }
      
      return $args;
    } else {
      $playlists = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_' . ( isset( $args[ 'channel' ] ) ? $args[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'playlistId' ] ), false );
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
              'thumbnail' => $this->thumb( $item[ 'snippet' ][ 'thumbnails' ] ),
              'channelTitle' => $item[ 'snippet' ][ 'channelTitle' ],
              'channelId' => $item[ 'snippet' ][ 'channelId' ]
            ];
          }
        }
        return $args;
      } else {return $args;}
    }
  }
  public function thumb( $images ) {
    return isset( $images[ 'medium' ] ) ? $images[ 'medium' ][ 'url' ] : $images[ 'default' ][ 'url' ];
  }
  public function yturl( $expect = 'watch', $args = [] ) {
    // $args = wp_parse_args( $args, [ 'id' => '', 'c' => '', 'p' => '' ] );
    $urls = [
      'watch' > 'https://www.youtube.com/watch?' . ( isset( $args[ 'id' ] ) ? 'v=' . $args[ 'id' ] : '' ),
      'watch-embed' > 'https://www.youtube.com/embed/watch?' . ( isset( $args[ 'id' ] ) ? 'v=' . $args[ 'id' ] : '' ),
      'channel' => 'https://www.youtube.com/channel/' . ( isset( $args[ 'c' ] ) ? $args[ 'c' ] : '' ),
      'playlist' => 'https://www.youtube.com/watch?' . ( isset( $args[ 'id' ] ) ? 'v=' . $args[ 'id' ] : '' ) . '' . ( isset( $args[ 'p' ] ) ? '&list=' . $args[ 'p' ] : '' ),
      'playlist-embed' => 'https://www.youtube.com/embed/videoseries?' . ( isset( $args[ 'p' ] ) ? 'list=' . $args[ 'p' ] : '' )
    ];
    return isset( $urls[ $expect ] ) ? $urls[ $expect ] : $urls[ 'playlist' ];
  }
  public function url( $id ) {
    return $this->yturl( 'playlist-embed', [ 'p' => $id ] );
  }
  public function register( $widgets_manager ) {
    $file = YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/elementor/widgets/widget-youtube-playlists.php';
    if( ! file_exists( $file ) ) {return;}
    include_once $file;
    $widgets_manager->register( new \Elementor_Widget_videotyplaylist() );
  }
};
// global $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_WIDGETS;
$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_WIDGETS = SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_WIDGETS::instance();
$SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_WIDGETS->init();

