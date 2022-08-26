<?php
/**
 * Admin FindShortCode page page here
 *
 * @category	Youtube WordPress
 * @package		youtube-playlist-api-integration
 * @author		FutureWordPress.com <info@futurewordpress.com/>
 * @copyright	Copyright (c) 2022-23
 * @link		https://futurewordpress.com/
 * @version		1.3.6
 */

$settings = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_settings', [] );
$playlists = isset( $settings[ 'playlists' ] ) ? $settings[ 'playlists' ] : [];
$youtubeapi = isset( $settings[ 'youtubeapi' ] ) ? $settings[ 'youtubeapi' ] : '';
$lists = [];
foreach( $playlists as $i => $list ) {
  $lists[] = $i;
}
?>
<div class="template__wrapper background__greyBg px30 py50">
	<div class="fwp-admin-setting-tabs">
		<div id="shortcode" class="fwp-admin-setting-tab active">
      <div class="fwp-container fwp-block">
        
        <div class="p30">
          <div class="fwp-grid">
            <div class="fwp-col-md-12">
              <div class="fwp-tool__card fwp-tool__card--flex">
                <div class="content fwp-col-sm-12">
                  <h3><?php esc_html_e( 'Youtube PlayLists Shortcodes.', 'domain' ); ?></h3>
                  <p>
                    <?php echo sprintf( __( 'Shortcodes are used like a hook, or if I explain it simply, shortcode are like a function. Just use it anywhere anytime. How you can use it?%sFor this you\'ve use shortcode widget. Please see this three screenshot below.', 'domain' ), '<br>' ); ?>
                  </p>
                  <div class="line"></div>
                  <div class="fwp-flex shortcodes-tutorial-img">
                    <div class="fwp-col-sm-4">
                      <img src="<?php echo esc_url( YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/img/1.PNG' ); ?>" alt="Findout ShortCode">
                    </div>
                    <div class="fwp-col-sm-4">
                      <img src="<?php echo esc_url( YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/img/2.PNG' ); ?>" alt="ShortCode">
                    </div>
                    <div class="fwp-col-sm-4">
                      <img src="<?php echo esc_url( YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/img/3.PNG' ); ?>" alt="Use your code here">
                    </div>
                  </div>
                  <!-- <div class="line"></div>
                  <p>
                    <?php echo sprintf( __( 'If you use WordPress default editor, so follow these screenshots.', 'domain' ), '<br>' ); ?>
                  </p>
                  <div class="fwp-flex shortcodes-tutorial-img">
                    <div class="fwp-col-sm-4">
                      <img src="<?php echo esc_url( YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/img/4.PNG' ); ?>" alt="Findout ShortCode">
                    </div>
                    <div class="fwp-col-sm-4">
                      <img src="<?php echo esc_url( YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/img/5.PNG' ); ?>" alt="ShortCode">
                    </div>
                    <div class="fwp-col-sm-4">
                      <img src="<?php echo esc_url( YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/img/6.PNG' ); ?>" alt="Use your code here">
                    </div>
                  </div> -->
                  
                  <div class="line"></div>
                  <p>
                    <?php echo sprintf( esc_html( 'Youtube Gallery shortcode name is %syoutube_gallery%s. and it is used under a bracket( third ).%sE.G. [youtube_gallery]', 'domain' ), '<strong>', '</strong>', '<br />' ); ?><br />
                    <?php echo sprintf( esc_html( 'options and paramiters:%s', 'domain' ), '<br />' ); ?><br />
                    <?php echo sprintf( esc_html( '%schannel%s: is used to identify specific channel ( required ). If you given it blank, it will return your first playlist.', 'domain' ), '<strong>', '</strong>' ); ?><br />
                    <?php echo sprintf( esc_html( '%scolumns%s: is used to declare how many columns will be there on large screen. It is decreasing autometically on different breakpoing. In mobile view, it is full width (optional). Default value is 4.', 'domain' ), '<strong>', '</strong>' ); ?><br />
                    <?php echo sprintf( esc_html( '%sinclude%s: is to identify which playlist you want to be there. Only those items will be there, for which you\'ve given. If you leave it blank, it will return all list enabled ( optional ).', 'domain' ), '<strong>', '</strong>' ); ?><br />
                    <?php echo sprintf( esc_html( '%sexclude%s: is to block specific playlists on there. Is comma-seperate value( optional ).', 'domain' ), '<strong>', '</strong>' ); ?><br />
                  </p>

                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="line"></div>

        <?php foreach( $playlists as $id => $title ) : ?>
        <div class="p30">
          <div class="fwp-grid">
            <div class="fwp-col-md-5">
              <div class="fwp-tool__card fwp-tool__card--flex">
                <div class="content">
                  <h4><?php echo esc_html( $title ); ?></h4>
                </div>
              </div>
            </div>
            <div class="fwp-col-md-7">
                <div class="fwp-tool__card">
                  <div class="content mt30">
                    <div class="align__center mb20">
                      <input type="text" name="shortcode[<?php echo esc_attr( $id ); ?>]" class="settings-youtubeapi" placeholder="<?php esc_attr_e( "Paste this code anywhere", 'domain' ); ?>" value="<?php echo esc_attr( '[youtube_gallery channel="' . $id . '" columns="4" include="" exclude=""]' ); ?>" required>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        
      </div>
    </div>
  </div>
</div>

