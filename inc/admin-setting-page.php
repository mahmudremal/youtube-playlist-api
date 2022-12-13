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
defined( 'ABSPATH' ) || exit;

$settings = get_option( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'option_prefix' ] . '_settings', [] );
$playlists = isset( $settings[ 'playlists' ] ) ? $settings[ 'playlists' ] : [];
$youtubeapi = isset( $settings[ 'youtubeapi' ] ) ? $settings[ 'youtubeapi' ] : '';
$facebook_userName = isset( $settings[ 'facebook-username' ] ) ? $settings[ 'facebook-username' ] : 'hayalhanemmersin';
$facebook_pageID = isset( $settings[ 'facebook-page' ] ) ? $settings[ 'facebook-page' ] : '';
$facebook_appID = isset( $settings[ 'facebook-id' ] ) ? $settings[ 'facebook-id' ] : '';
$facebook_appSecrete = isset( $settings[ 'facebook-secrete' ] ) ? $settings[ 'facebook-secrete' ] : '';
$twitter_userName = isset( $settings[ 'twitter-username' ] ) ? $settings[ 'twitter-username' ] : 'hayalhanemersin';
$instagram_userName = isset( $settings[ 'instagram-username' ] ) ? $settings[ 'instagram-username' ] : 'hayalhanemmersin';
$youtube_userName = isset( $settings[ 'youtube-username' ] ) ? $settings[ 'youtube-username' ] : 'hayalhanem';
$telegram_userName = isset( $settings[ 'telegram-username' ] ) ? $settings[ 'telegram-username' ] : 'hayalhanemmersin';
$shortcodeTemplate = isset( $settings[ 'shortcode-template' ] ) ? $settings[ 'shortcode-template' ] : '
<div class="fss-col">
  <div class="fss-icon">
    <a class="elementor-icon" href="{url}" target="_blank">
      <i aria-hidden="true" class="{logo}"></i>
    </a>
  </div>
  <div class="fss-counter-wrapper">
    <span class="fss-counter-prefix"></span>
    <span class="fss-counter" data-duration="2000" data-to-value="{totals}" data-from-value="0" data-delimiter=".">0</span>
    <span class="fss-counter-suffix"></span>
  </div>
  <div class="fss-counter-title">{title}</div>
</div>';
$shortcodeInlineCSS = isset( $settings[ 'shortcode-inlinecss' ] ) ? $settings[ 'shortcode-inlinecss' ] : '.fwp-elementor-shortcode-row {display: flex;flex-wrap: wrap;justify-content: space-around;width: 100%;margin: auto;}.fwp-elementor-shortcode-row > .fss-col {display: block;text-align: center;width: auto;min-width: 20%;}.fwp-elementor-shortcode-row > .fss-col .fss-icon .elementor-icon {margin-bottom: 10px;font-size: 40px;color: #F2BC03;border-color: #F2BC03;}.fwp-elementor-shortcode-row > .fss-col .fss-counter-wrapper .fss-counter {color: #FAB702;font-family: "Poppins", Sans-serif;font-size: 25px;font-weight: 500;}.fwp-elementor-shortcode-row > .fss-col .fss-counter-title {color: #FFFFFF;font-family: "Poppins", Sans-serif;font-size: 20px;font-weight: 500;}';
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
                    <h3><?php esc_html_e( 'Google API KEY', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php echo wp_kses_post( sprintf( __( 'If you knew nothing about google API\s, please read about it and follow steps to get "YouTube Data API" key on%s Google Console%s.', 'youtube-playlist-api-integration' ), '<a href="https://console.cloud.google.com/apis/library/youtube.googleapis.com" data-href="https://console.developers.google.com/" target="_blank" rel="noopener noreferrer">', '</a>' ) ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[youtubeapi]" id="settings-youtubeapi" placeholder="<?php esc_attr_e( "Google API key here.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $youtubeapi ); ?>" required>
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
                          <h3><?php esc_html_e( 'Channel ID\'s', 'youtube-playlist-api-integration' ); ?></h3>
                          <p><?php esc_html_e( 'Insert each channel ID in a single line. Do not use any comma, full-stop or anyother sign. Just paste channel ID and hit enter.', 'youtube-playlist-api-integration' ); ?></p>
                          <p><?php echo sprintf( __( 'Please follow this %slink%s to know how to use shortcode.', 'youtube-playlist-api-integration' ), '<a href="' . esc_url( admin_url( 'admin.php?page=youtube-shortcode-playlists' ) ) . '" target="_blank">', '</a>' ); ?></p>
                      </div>
                    </div>
                </div>
                <div class="fwp-col-md-7">
                    <div class="fwp-tool__card">
                      <div class="content">
                          <textarea name="settings[playlists]" id="settings-playlists" cols="30" rows="10" placeholder="<?php esc_attr_e( "Channel-1\nChannel-2\nChannel-3", 'youtube-playlist-api-integration' ); ?>"><?php echo esc_html( implode( "\n", $lists ) ); ?></textarea>
                      </div>
                    </div>
                </div>
              </div>
          </div>
          
          <div class="line"></div>
          <div class="p30">
            <div class="fwp-grid">
              <div class="fwp-col-md-5">
                <div class="fwp-tool__card fwp-tool__card--flex">
                  <div class="content">
                    <h3><?php esc_html_e( 'Facebook Page ID', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php esc_html_e( 'Facebook Profile ID, not URL.', 'youtube-playlist-api-integration' ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[facebook-page]" id="facebook-page" placeholder="<?php esc_attr_e( "Facebook Page ID.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $facebook_pageID ); ?>" required>
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
                    <h3><?php esc_html_e( 'Facebook App ID', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php echo wp_kses_post( sprintf( __( 'Facebook App ID is required to sync facebook page followers. You can create an %sApp ID from here%s', 'youtube-playlist-api-integration' ), '<a href="https://support.appmachine.com/support/solutions/articles/80000978442-create-a-facebook-app-id-app-secret#:~:text=Click%20on%20the%20%22Create%20New,create%20a%20new%20Facebook%20app.&text=Step%206%3A%20Your%20App%20ID,to%20reveal%20the%20App%20Secret." target="_blank" rel="noopener noreferrer">', '</a>' ) ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[facebook-id]" id="facebook-id" placeholder="<?php esc_attr_e( "Facebook APP ID.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $facebook_appID ); ?>" required>
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
                    <h3><?php esc_html_e( 'Facebook App Secrete', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php esc_html_e( 'Facebook App ID Secrete to sync data once eachday. Meant API will call everyday for once to update and to store information.', 'youtube-playlist-api-integration' ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[facebook-secrete]" id="facebook-secrete" placeholder="<?php esc_attr_e( "Facebook APP Secrete.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $facebook_appSecrete ); ?>" required>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          
          <div class="line"></div>
          <div class="p30">
            <div class="fwp-grid">
              <div class="fwp-col-md-5">
                <div class="fwp-tool__card fwp-tool__card--flex">
                  <div class="content">
                    <h3><?php esc_html_e( 'Twitter User Name', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php echo wp_kses_post( sprintf( __( 'Twitter %sUser profile name%s. Without @.', 'youtube-playlist-api-integration' ), '<a href="https://twitter.com/." target="_blank" rel="noopener noreferrer">', '</a>' ) ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[twitter-username]" id="twitter-username" placeholder="<?php esc_attr_e( "Twitter User name.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $twitter_userName ); ?>" required>
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
                    <h3><?php esc_html_e( 'Instagram User Name', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php echo wp_kses_post( sprintf( __( 'Instagram %sUser profile name%s. Without @.', 'youtube-playlist-api-integration' ), '<a href="https://instagram.com/." target="_blank" rel="noopener noreferrer">', '</a>' ) ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[instagram-username]" id="instagram-username" placeholder="<?php esc_attr_e( "Instagram User name.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $instagram_userName ); ?>" required>
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
                    <h3><?php esc_html_e( 'Youtube User Name', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php echo wp_kses_post( sprintf( __( 'Youtube %sUser name%s. Without @.', 'youtube-playlist-api-integration' ), '<a href="https://youtube.com/." target="_blank" rel="noopener noreferrer">', '</a>' ) ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[youtube-username]" id="youtube-username" placeholder="<?php esc_attr_e( "Youtube User name.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $youtube_userName ); ?>" required>
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
                    <h3><?php esc_html_e( 'Telegram Profile Name', 'youtube-playlist-api-integration' ); ?></h3>
                    <p>
                      <?php echo wp_kses_post( sprintf( __( 'Telegram %sUser profile name%s. Without @.', 'youtube-playlist-api-integration' ), '<a href="https://telegram.com/." target="_blank" rel="noopener noreferrer">', '</a>' ) ); ?>
                    </p>
                  </div>
                </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content mt30">
                      <div class="align__center mb20">
                        <input type="text" name="settings[telegram-username]" id="telegram-username" placeholder="<?php esc_attr_e( "Telegram User name.", 'youtube-playlist-api-integration' ); ?>" value="<?php echo esc_attr( $telegram_userName ); ?>" required>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>

          <div class="line"></div>
          <div class="p30">
            <div class="fwp-grid">
              <div class="fwp-col-md-5">
                  <div class="fwp-tool__card fwp-tool__card--flex">
                    <div class="content">
                      <h3><?php esc_html_e( 'Shortcode Template', 'youtube-playlist-api-integration' ); ?></h3>
                      <p><?php echo sprintf( __( 'Follower counter shortcode html template content. Use %s for icon, %s for URI. Use %s for Synced data, and %s for title.', 'youtube-playlist-api-integration' ), '<code>{logo}</code>', '<code>{url}</code>', '<code>{totals}</code>', '<code>{title}</code>' ); ?></p>
                    </div>
                  </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content">
                      <textarea name="settings[shortcode-template]" id="shortcode-template" cols="30" rows="10" placeholder="<?php esc_attr_e( "Shortcode HTML template", 'youtube-playlist-api-integration' ); ?>"><?php echo wp_kses_post( $shortcodeTemplate ); ?></textarea>
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
                      <h3><?php esc_html_e( 'Inline CSS', 'youtube-playlist-api-integration' ); ?></h3>
                      <p><?php esc_html_e( 'Custom inline CSS for shortcode template. Make sure you skip style tag from here.', 'youtube-playlist-api-integration' ); ?></p>
                    </div>
                  </div>
              </div>
              <div class="fwp-col-md-7">
                  <div class="fwp-tool__card">
                    <div class="content">
                      <textarea name="settings[shortcode-inlinecss]" id="shortcode-template" cols="30" rows="10" placeholder="<?php esc_attr_e( "Shortcode inline css without <style> tag.", 'youtube-playlist-api-integration' ); ?>"><?php echo $shortcodeInlineCSS; ?></textarea>
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
                      <button type="submit" class="fwp-button button__themeColor mb20"><?php esc_html_e( 'Save changes', 'youtube-playlist-api-integration' ); ?></button>
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

