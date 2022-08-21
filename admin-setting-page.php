<?php
/**
 * Admin Setting page here
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
		<div id="tools" class="fwp-admin-setting-tab active">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="youtube-setup-playlists">
        <input type="hidden" name="action" value="syplapiint_setup">
        <?php wp_nonce_field( 'syplapiint_setup', 'syplapiint_setup_nonce' ); ?>
        <div class="fwp-container fwp-block">
          
          <div class="p30">
            <div class="fwp-grid">
              <div class="fwp-col-md-5">
                <div class="fwp-tool__card fwp-tool__card--flex">
                  <div class="content">
                    <h3><?php esc_html_e( 'Google API KEY', 'domain' ); ?></h3>
                    <p>
                      <?php esc_html_e( 'If you knew nothing about google API\s, please read about it and follow steps to get "YouTube Data API" key.', 'domain' ); ?>
                      <a href="https://console.cloud.google.com/apis/library/youtube.googleapis.com" data-href="https://console.developers.google.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Google Console', 'domain' ); ?></a>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[youtubeapi]" id="settings-youtubeapi" placeholder="<?php esc_attr_e( "Google API key here.", 'domain' ); ?>" value="<?php echo esc_html( $youtubeapi ); ?>" required>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <div class="p30">
              <div class="fwp-grid">
                <div class="fwp-col-md-5">
                    <div class="fwp-tool__card fwp-tool__card--flex">
                      <div class="content">
                          <h3><?php esc_html_e( 'Channel ID\'s', 'domain' ); ?></h3>
                          <p><?php esc_html_e( 'Insert each channel ID in a single line. Do not use any comma, full-stop or anyother sign. Just paste channel ID and hit enter.', 'domain' ); ?></p>
                          <p><?php echo sprintf( __( 'Please follow this %slink%s to know how to use shortcode.', 'domain' ), '<a href="' . esc_url( admin_url( 'admin.php?page=youtube-shortcode-playlists' ) ) . '" target="_blank">', '</a>' ); ?></p>
                      </div>
                    </div>
                </div>
                <div class="fwp-col-md-7">
                    <div class="fwp-tool__card">
                      <div class="content">
                          <textarea name="settings[playlists]" id="settings-playlists" cols="30" rows="10" placeholder="<?php esc_attr_e( "Channel-1\nChannel-2\nChannel-3", 'domain' ); ?>"><?php echo esc_html( implode( "\n", $lists ) ); ?></textarea>
                      </div>
                    </div>
                </div>
              </div>
          </div>
          
          <div class="line"></div>
          <div class="p30">
            <div class="fwp-grid">
              <div class="fwp-col-md-12">
                  <div class="fwp-tool__card">
                    <div class="content fwp-float-right">
                      <button type="submit" class="fwp-button button__themeColor mb20"><?php esc_html_e( 'Save changes', 'domain' ); ?></button>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          
        </div>
      </form>
    </div>
  </div>
</div>

