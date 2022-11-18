<?php
/**
 * Admin Option page class
 *
 * @category	Youtube WordPress
 * @package		youtube-playlist-api-integration
 * @author		FutureWordPress.com <info@futurewordpress.com/>
 * @copyright	Copyright (c) 2022-23
 * @link		https://futurewordpress.com/
 * @version		1.3.6
 */
defined( 'ABSPATH' ) || exit;
global $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS;

$channelList = $this->channels();
$currentChannel = ( isset( $_GET[ 'channel' ] ) && isset( $channelList[ $_GET[ 'channel' ] ] ) ) ? $_GET[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ];
$playlists = $this->playlists( $currentChannel );
$are_Category = $this->are_Category();

$playlists[ 'items' ] = isset( $playlists[ 'items' ] ) ? $playlists[ 'items' ] : [];
$channelTitle = ( isset( $playlists[ 'items' ][ 0 ] ) && isset( $playlists[ 'items' ][ 0 ][ 'snippet' ] ) && isset( $playlists[ 'items' ][ 0 ][ 'snippet' ][ 'channelTitle' ] ) ) ? $playlists[ 'items' ][ 0 ][ 'snippet' ][ 'channelTitle' ] : 'Global Control';
$channelId = ( isset( $playlists[ 'items' ][ 0 ] ) && isset( $playlists[ 'items' ][ 0 ][ 'snippet' ] ) && isset( $playlists[ 'items' ][ 0 ][ 'snippet' ][ 'channelId' ] ) ) ? $playlists[ 'items' ][ 0 ][ 'snippet' ][ 'channelId' ] : false;

$this->toPost( $playlists );
?>

<pre style="display: none;"><?php // $this->toPost( $playlists );// print_r( $playlists ); ?></pre>

<div class="template__wrapper background__greyBg px30 py50">
	<div class="fwp-admin-setting-tabs">
		<div id="elements" class="fwp-admin-setting-tab active">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="youtube-playlists-form">
				<div class="fwp-global__control mb45">
					<div class="global__control__content">
						<h4>
							<select name="switch-channel" id="switch-channel" onchange="location.href = '<?php echo esc_url( admin_url( 'admin.php?page=youtube-playlists&channel=' ) ); ?>' + value">
								<?php foreach( $channelList as $id => $title ) : ?>
									<option value="<?php echo esc_attr( $id ); ?>" <?php echo esc_attr( ( isset( $_GET[ 'channel' ] ) && $_GET[ 'channel' ] == $id ) ? 'selected' : '' ); ?>><?php echo esc_html( $title ); ?></option>
								<?php endforeach; ?>
							</select>
						</h4>
						<!-- <p>Use the Toggle Button to Activate or Deactivate all the Elements of Essential Addons at once.</p> -->
					</div>
					<div class="global__control__switch">
						<label class="fwp-switch fwp-switch--xl">
							<input class="fwp-element-global-switch fwp-toggle-switcher" name="channelId[<?php echo esc_attr( $playlists[ 'etag' ] ); ?>]" type="checkbox" <?php echo esc_attr( ( isset( $playlists[ 'is_Public' ] ) && $playlists[ 'is_Public' ] ) ? 'checked' : '' ); ?> data-channel="<?php echo esc_attr( $currentChannel ); ?>" data-toggle="channelId" data-target="<?php echo esc_attr( $playlists[ 'etag' ] ); ?>">
							<span class="switch__box"></span>
						</label>
						<span class="switch__status enable"><?php _e( 'Visible' ); ?></span>
						<span class="switch__status disable"><?php _e( 'Hide' ); ?></span>
					</div>
					<div class="global__control__button">
						<button type="button" class="fwp-button js-fwp-settings-update" data-channel="<?php echo esc_attr( ( isset( $_GET[ 'channel' ] ) && isset( $channelList[ $_GET[ 'channel' ] ] ) ) ? '' : $currentChannel ); ?>"><?php _e( 'Update' ); ?></button>
					</div>
				</div>


				<?php if( $this->allow( true ) ) : ?>
					<style>
					</style>
					<div class="fwp-section mb50">
						<h3 class="fwp-section__header"><?php echo esc_html( 'Playlists' ); ?></h3>
						<?php
							if( count( $playlists[ 'items' ] ) <= 0 ) {
								?>
								<div class="fwp-tool__card fwp-container fwp-block p30" style="max-width: 100%;width: 100%;">
									<div class="content">
										<h3><?php esc_html_e( 'Nothing found there :(', 'youtube-playlist-api-integration' ); ?></h3>
										<p>
											<?php esc_html_e( 'There is nothing on this channel. If you\'re sure, about your channel, please recheck channel ID you\ve given there. It might be happens by Google API server. Please update your playlist by clicking Update button. If it is again happen, change your youtube API key. Maybe it\'d reached it\'s limit.', 'youtube-playlist-api-integration' ); ?>
										</p>
									</div>
								</div>
								<?php
							}
						?>
						<div class="fwp-element__wrap">
							<?php
							foreach( $playlists[ 'items' ] as $i => $item ) :
							$snippet = $item[ 'snippet' ];
							$thumb = $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->thumb( $snippet[ 'thumbnails' ], 'medium' );
							?>
							<div class="fwp-element__item ">
								<a href="<?php echo esc_url( $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->yturl( 'playlist', [ 'id' => $item[ 'id' ] ] ) ); ?>" class="element-link" target="_blank" data-embed="<?php echo esc_url( $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->yturl( 'playlist-embed', [ 'p' => $item[ 'id' ] ] ) ); ?>">
									<img class="element-image" src="<?php echo esc_url( $thumb[ 'url'] ); ?>" alt="<?php echo esc_attr( 'Bilinmeyen Günahlar' ); ?>" height="<?php echo esc_attr( $thumb[ 'height'] ); ?>" width="<?php echo esc_attr( $thumb[ 'width'] ); ?>">
								</a>
								<div class="element__content">
									<h4><?php echo esc_html( substr( $snippet[ 'title' ], 0, 45 ) ); ?></h4>
									<div class="element__options">
										<?php if( 1 == 1 || isset( $snippet[ 'description' ] ) && ! empty( $snippet[ 'description' ] ) ) : ?>
										<div class="element__icon" href="javascript:void(0)" data-href="<?php echo esc_url( $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->yturl( 'playlist', [ 'p' => $item[ 'id' ] ] ) ); ?>">
											<i class="ea-admin-icon ic on-monitor dashicons-before dashicons-lightbulb"></i>
											<div class="tooltip-text">
												<div class="tooltip-header">
                        	<?php if( 1 == 2 ) : ?>
														<select class="changeCategory" name="changeCategory[<?php echo esc_attr( $item[ 'id' ] ); ?>]" type="checkbox" data-channel="<?php echo esc_attr( $currentChannel ); ?>" data-toggle="changeCategory" data-channel="<?php echo esc_attr( $currentChannel ); ?>" data-target="<?php echo esc_attr( $item[ 'id' ] ); ?>">
															<?php foreach( $are_Category as $catID => $catTitle ) : ?>
																<option value="<?php echo esc_attr( $catID ); ?>" <?php echo esc_attr( ( isset( $item[ 'is_Category' ] ) && $item[ 'is_Category' ] == $catID ) ? 'selected' : '' ); ?>><?php echo esc_html( $catTitle ); ?></option>
															<?php endforeach; ?>
														</select>
													<?php endif; ?>
													<i class="ea-admin-icon dashicons-before dashicons-clipboard" data-clipboard="<?php echo esc_attr( $item[ 'id' ] ); ?>"></i>
													<a href="<?php echo esc_url( $SPECIAL_YOUTUBE_PLAYLIST_API_INTEGRATION_PLUGIN_CLS->yturl( 'watch', [ 'p' => $item[ 'id' ] ] ) ); ?>" class="tootip-link" target="_blank"><i class="dashicons-before dashicons-admin-links"></i></a>
												</div>
												<div class="tooltip-body">
													<?php echo esc_html( ( isset( $snippet[ 'description' ] ) && ! empty( $snippet[ 'description' ] ) && ! is_array( $snippet[ 'description' ] ) ) ? substr( $snippet[ 'description' ], 0, 120 ) : __( 'Descriptions Not available.', 'youtube-playlist-api-integration' ) ); ?>
												</div>
											</div>
										</div>
										<?php endif; ?>
										<?php if( $this->allow( 'main' ) ) : ?>
											<label class="fwp-switch">
												<input class="fwp-widget-item fwp-elements-list fwp-toggle-switcher" name="playlistId[<?php echo esc_attr( $item[ 'id' ] ); ?>]" type="checkbox" <?php echo esc_attr( ( isset( $item[ 'is_Public' ] ) && $item[ 'is_Public' ] ) ? 'checked' : '' ); ?> data-channel="<?php echo esc_attr( $currentChannel ); ?>" data-toggle="playlistId" data-target="<?php echo esc_attr( $item[ 'id' ] ); ?>">
												<span class="switch__box "></span>
											</label>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php endforeach; ?>

						</div>
					</div>
				<?php endif; ?>
			</form>
		</div>
  </div>
</div>