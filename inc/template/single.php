<?php
/**
 * Youtube Playlist and Single Video template
 *
 * @category	Youtube WordPress
 * @package		youtube-playlist-api-integration
 * @author		FutureWordPress.com <info@futurewordpress.com/>
 * @copyright	Copyright (c) 2022-23
 * @link		https://futurewordpress.com/
 * @version		1.3.6
 */

defined( 'ABSPATH' ) || exit;
get_header();
$postMeta = [];$playlistItems = [];
$metaData = get_post_meta( get_the_ID(), false, true );
foreach( $metaData as $key => $meta ) {
  $postMeta[ str_replace( [ 'fwp_meta-' ], [ '' ], $key ) ] = isset( $meta[0] ) ? $meta[0] : $meta;
}
$postMeta[ 'localized' ] = isset( $postMeta[ 'localized' ] ) ? maybe_unserialize( $postMeta[ 'localized' ] ) : [];
$postMeta[ 'thumbnails' ] = isset( $postMeta[ 'thumbnails' ] ) ? maybe_unserialize( $postMeta[ 'thumbnails' ] ) : [];
// if( isset( $postMeta[ 'playlistId' ] ) && ! empty( $postMeta[ 'playlistId' ] ) ) {
//   $option = YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_playlist_' . $postMeta[ 'playlistId' ];
//   wp_die( $option );
//   $playlistItems = $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->ajaxSort( get_option( $option, [] ) );
//   // $playlistItems = $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->publiclist( [ 'playlistId' => $postMeta[ 'playlistId' ], 'items' => [] ] );
//   $playlistItems = isset( $playlistItems[ 'items' ] ) ? $playlistItems[ 'items' ] : [];
// }
?>
<pre style="display: none;"><?php print_r( [ $postMeta, $playlistItems ] ); ?></pre>
<div class="elementor futurewordpress-elementor">


  <section class="elementor-section elementor-top-section elementor-element elementor-element-main-content elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-background-overlay"></div>
    <div class="elementor-container elementor-column-gap-default">
      <div class="elementor-column elementor-col-70 elementor-top-column elementor-element" data-element_type="column">
        <div class="elementor-widget-wrap elementor-element-populated">
          <div class="elementor-element elementor-widget elementor-widget-heading" data-id="786c8ff" data-element_type="widget" data-widget_type="heading.default">
            <div class="elementor-widget-container">
              <iframe id="fwp-player-iframe" width="100%" height="auto" src="https://www.youtube.com/embed/videoseries?list=<?php echo esc_attr( $postMeta[ 'playlistId' ] ); ?>" frameborder="0" allowfullscreen="" style="min-height: 80vh;"></iframe>
            </div>
          </div>
        </div>
      </div>
      <div class="elementor-column elementor-col-30 elementor-top-column elementor-element" data-element_type="column">
        <div class="elementor-widget-wrap">
          <ul class="fwp-playlist-list-unordered">
            <?php foreach( $playlistItems as $item ) : ?>
              <li class="fwp-list-item">
                <a href="" class="fwp-list-item-link">
                  <img src="<?php echo esc_url( $item[ 'thumbnail' ][ 'url' ] ); ?>" alt="">
                  <h3><?php echo esc_html( $item[ 'title' ] ); ?></h3>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </section>
</div>
<style>
  .futurewordpress-elementor .elementor-element.elementor-element-ce138e5:not(.elementor-motion-effects-element-type-background),.futurewordpress-elementor .elementor-element.elementor-element-ce138e5>.elementor-motion-effects-container>.elementor-motion-effects-layer{background-color:#333333;background-image:url("https://www.hayalhanem.com.tr/wp-content/uploads/2022/06/5-7.jpg");background-position:top center;background-size:cover}.futurewordpress-elementor .elementor-element.elementor-element-ce138e5>.elementor-background-overlay{background-color:#222222;opacity:.8;mix-blend-mode:multiply;transition:background .3s,border-radius .3s,opacity .3s}.futurewordpress-elementor .elementor-element.elementor-element-ce138e5{transition:background .3s,border .3s,border-radius .3s,box-shadow .3s;margin-top:-5em;margin-bottom:0;padding:13em 0 9em 0}.futurewordpress-elementor .elementor-element.elementor-element-71fef16>.elementor-widget-wrap>.elementor-widget:not(.elementor-widget__width-auto):not(.elementor-widget__width-initial):not(:last-child):not(.elementor-absolute){margin-bottom:0}.futurewordpress-elementor .elementor-element.elementor-element-a59d103>.elementor-widget-wrap>.elementor-widget:not(.elementor-widget__width-auto):not(.elementor-widget__width-initial):not(:last-child):not(.elementor-absolute){margin-bottom:10px}.futurewordpress-elementor .elementor-element.elementor-element-a59d103>.elementor-element-populated{padding:1em 1em 1em 1em}.futurewordpress-elementor .elementor-element.elementor-element-786c8ff{text-align:center}.futurewordpress-elementor .elementor-element.elementor-element-786c8ff .elementor-heading-title{color:var(--e-global-color-ef5282c);font-family:"Poppins",Sans-serif;font-size:45px;font-weight:bold}.futurewordpress-elementor .elementor-element.elementor-element-b7b9a28>.elementor-widget-wrap>.elementor-widget:not(.elementor-widget__width-auto):not(.elementor-widget__width-initial):not(:last-child):not(.elementor-absolute){margin-bottom:0}.futurewordpress-elementor .elementor-element.elementor-element-6e9e00e>.elementor-container{max-width:1600px}
  .elementor-element-main-content {
    background: #333333;
  }
  .futurewordpress-elementor .elementor-section.elementor-section-boxed > .elementor-container {
    max-width: 1280px;transition:background .3s,border .3s,border-radius .3s,box-shadow .3s;margin-top:-5em;margin-bottom:0;padding: 6em 0 2em 0;
  }
</style>
<?php
get_footer();