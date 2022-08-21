<?php
/**
 * Elementor widget for youtube playlist displaying
 *
 * @category	Youtube WordPress
 * @package		youtube-playlist-api-integration
 * @author		FutureWordPress.com <info@futurewordpress.com/>
 * @copyright	Copyright (c) 2022-23
 * @link		https://futurewordpress.com/
 * @version		1.3.6
 */


if( ! defined( 'ABSPATH' ) ) {exit;} // Exit if accessed directly.


class Elementor_Widget_videotyplaylist extends \Elementor\Widget_Base {
  
	/**
	 * Get widget name.
	 *
	 * Retrieve list widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
  public function get_name() {
    return 'youtube_playlist';
  }
	/**
	 * Get widget title.
	 *
	 * Retrieve list widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
  public function get_title() {
    return esc_html__( 'YouTube Playlists', 'plugin-name' );
  }
	/**
	 * Get widget icon.
	 *
	 * Retrieve list widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
  public function get_icon() {
    return 'eicon-code';
  }
	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
  public function get_custom_help_url() {
    return 'https://futurewordpress.com/wordpress/';
  }
	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
  public function get_categories() {
    return [ 'basic' ];
  }
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
  public function get_keywords() {
    return [ 'youtube', 'playlist', 'api', 'grid', 'gallery' ];
  }
  public function get_script_depends() {

    wp_register_script( 'youtube-playlist-frontend-script', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/js/frontend.js', [], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/js/frontend.js' ), true );
    wp_register_script( 'youtube-player-frontend-script', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/js/youtube-player.js', [], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/js/youtube-player.js' ), true );
		return [];
    return [ 'youtube-playlist-frontend-script' ]; // , 'youtube-player-frontend-script'
  }
  public function get_style_depends() {
		return [];
    wp_register_style( 'youtube-playlist-frontend-style', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/css/frontend.css', [], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/css/frontend.css' ), 'all' );
    wp_register_style( 'youtube-player-frontend-style', YOUTUBE_PLAYLIST_API_INTEGRATION_URL . '/assets/css/youtube-player.css', [], $this->filemtime( YOUTUBE_PLAYLIST_API_INTEGRATION_PATH . '/assets/css/youtube-player.css' ), 'all' );

    return [ 'youtube-playlist-frontend-style', 'youtube-player-frontend-style' ];
  }
	/**
	 * CHeck if file exist and then get file modification time.
	 */
	public function filemtime( $file ) {
		return ( file_exists( $file ) && ! is_dir( $file ) ) ? filemtime( $file ) : YOUTUBE_PLAYLIST_API_INTEGRATION_VERSION;
	}
	/**
	 * Register list widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
    
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'PlayList Content', 'domain' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'channel',
			[
				'label' => esc_html__( 'Channel' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => apply_filters( 'elementor/widgets/youtube_playlist/channel/select', [ 'all' => __( 'All', 'domain' ) ] ),
				'description' => esc_html__( 'Select channel to show here. Playlists, you\'ve muted on dash board, will disapead here.', 'domain' ),
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'width: {{CHANNEL}}%;',
				],
				'separator' => 'before'
			]
		);

		/*
      $this->add_control(
        'include',
        [
          'label' => esc_html__( 'Include', 'domain' ),
          'type' => \Elementor\Controls_Manager::SELECT2,
          'multiple' => true,
          'options' => apply_filters( 'elementor/widgets/youtube_playlist/include/select', [] ),
          'label_block' => true,
          // 'default' => '',
          'description' => esc_html__( 'Select playlist only those should be included here. If you willling to display all except some specific item, just leave blank here, and select from excluded list to exclude.', 'domain' ),
        ]
      );
      $this->add_control(
			'exclude',
			[
				'label' => esc_html__( 'Exclude', 'domain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => apply_filters( 'elementor/widgets/youtube_playlist/exclude/select', [] ),
				'label_block' => true,
				// 'default' => '',
				'description' => esc_html__( 'Select playlist only those should be excluded here.', 'domain' ),
			]
		  );
			$this->add_control(
				'marker_type',
				[
					'label' => esc_html__( 'Marker Type', 'elementor-list-widget' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'ordered' => [
							'title' => esc_html__( 'Ordered List', 'elementor-list-widget' ),
							'icon' => 'eicon-editor-list-ol',
						],
						'unordered' => [
							'title' => esc_html__( 'Unordered List', 'elementor-list-widget' ),
							'icon' => 'eicon-editor-list-ul',
						],
						'other' => [
							'title' => esc_html__( 'Custom List', 'elementor-list-widget' ),
							'icon' => 'eicon-edit',
						],
					],
					'default' => 'ordered',
					'toggle' => false,
				]
			);

			$this->add_control(
				'marker_content',
				[
					'label' => esc_html__( 'Custom Marker', 'elementor-list-widget' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter custom marker', 'elementor-list-widget' ),
					'default' => '🧡',
					'condition' => [
						'marker_type[value]' => 'other',
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-list-widget-text::marker' => 'content: "{{VALUE}}";',
					],
				]
			);
		*/
		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'default' => 4,
				'options' => [
					__( 'none' ), 1, 2, 3, 4, 5, 6
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'width: {{COLUMNS}}%;',
				],
				'separator' => 'before'
			]
		);

		/*
			$this->add_responsive_control(
				'size',
				[
					'label' => esc_html__( 'Size' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 300,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
		
      $this->end_controls_section();

      // For color tab
      $this->start_controls_section(
        'style_content_section',
        [
          'label' => esc_html__( 'List Style', 'elementor-list-widget' ),
          'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
      );

      $this->add_control(
        'title_color',
        [
          'label' => esc_html__( 'Color', 'elementor-list-widget' ),
          'type' => \Elementor\Controls_Manager::COLOR,
          'selectors' => [
            '{{WRAPPER}} .elementor-list-widget-text' => 'color: {{VALUE}};',
            '{{WRAPPER}} .elementor-list-widget-text > a' => 'color: {{VALUE}};',
          ],
        ]
      );

      $this->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
          'name' => 'icon_typography',
          'selector' => '{{WRAPPER}} .elementor-list-widget-text, {{WRAPPER}} .elementor-list-widget-text > a',
        ]
      );

      $this->add_group_control(
        \Elementor\Group_Control_Text_Shadow::get_type(),
        [
          'name' => 'text_shadow',
          'selector' => '{{WRAPPER}} .elementor-list-widget-text',
        ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
        'style_marker_section',
        [
          'label' => esc_html__( 'Marker Style', 'elementor-list-widget' ),
          'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
      );

      $this->add_control(
        'marker_color',
        [
          'label' => esc_html__( 'Color', 'elementor-list-widget' ),
          'type' => \Elementor\Controls_Manager::COLOR,
          'selectors' => [
            '{{WRAPPER}} .elementor-list-widget-text::marker' => 'color: {{VALUE}};',
          ],
        ]
      );

      $this->add_control(
        'marker_spacing',
        [
          'label' => esc_html__( 'Spacing', 'elementor-list-widget' ),
          'type' => \Elementor\Controls_Manager::SLIDER,
          'size_units' => [ 'px', 'em', 'rem' ],
          'range' => [
            'px' => [
              'min' => 0,
              'max' => 100,
            ],
            'em' => [
              'min' => 0,
              'max' => 10,
            ],
            'rem' => [
              'min' => 0,
              'max' => 10,
            ],
          ],
          'default' => [
            'unit' => 'px',
            'size' => 40,
          ],
          'selectors' => [
            // '{{WRAPPER}} .elementor-list-widget' => 'padding-left: {{SIZE}}{{UNIT}};',
            '{{WRAPPER}} .elementor-list-widget' => 'padding-inline-start: {{SIZE}}{{UNIT}};',
          ],
        ]
      );

    */

		$this->end_controls_section();

	}
	/**
	 * Render list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$settings[ 'channel' ] = isset( $settings[ 'channel' ] ) ? $settings[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ];
		$playlists = apply_filters( 'elementor/widgets/youtube_playlist/publiclist', [ 'include' => isset( $settings[ 'include' ] ) ? $settings[ 'include' ] : '', 'exclude' => isset( $settings[ 'exclude' ] ) ? $settings[ 'exclude' ] : '', 'channel' => ( isset( $settings[ 'channel' ] ) && ! empty( $settings[ 'channel' ] ) ) ? $settings[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'playlistId' ], 'items' => [] ] );
		$this->add_render_attribute( 'row', 'class', [
			'elementor-container',
			'elementor-column-gap-default',
			'elementor-container-flex-wrap'
		] );

		$this->add_render_attribute( 'imagehvr_wrapper', 'class', apply_filters( 'fwp_columns', [ $settings[ 'columns' ], 'imagehvr-wrapper elementor-element elementor-column elementor-inner-column' ] ) );
		// $this->add_render_attribute( 'imagehvr_wrapper', 'data-element_type', [ 'column' ] );
		// $this->add_render_attribute( 'imagehvr_wrapper', 'data-col', $this->fwp_columns( $settings[ 'columns' ], '' ) );
		
		$this->add_render_attribute( 'imagehvr_content_wrapper', 'class', [
			'imagehvr-content-wrapper',
			'imagehvr-content-center',
			'imagehvr-anim-zoom-in-alt'
		] );

		$this->add_render_attribute( 'list', 'class', 'elementor-list-widget' );
		?>
		<pre style="display: none;"><?php // print_r( $playlists ); ?></pre>
		<div <?php $this->print_render_attribute_string( 'row' ); ?>>
		 <?php
			foreach ( $playlists['items'] as $index => $item ) {
				$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'list_items', $index );
				$this->add_render_attribute( $repeater_setting_key, 'class', 'elementor-list-widget-text' );
				$this->add_inline_editing_attributes( $repeater_setting_key );
				?>
				<div <?php $this->print_render_attribute_string( 'imagehvr_wrapper' ); ?> <?php $this->print_render_attribute_string( $repeater_setting_key ); ?> >
					<div class="imagehvr">
						<div <?php $this->print_render_attribute_string( 'imagehvr_content_wrapper' ); ?>>
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
	/**
	 * Render list widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		$settings = [ 'include' => '', 'exclude' => '', 'channel' => '' ];
		$settings[ 'channel' ] = isset( $settings[ 'channel' ] ) ? $settings[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'channelId' ];
		?>
		<#
		channellist = <?php echo json_encode( apply_filters( 'elementor/widgets/youtube_playlist/publiclist', [ 'all' => true, 'include' => $settings[ 'include' ], 'exclude' => $settings[ 'exclude' ], 'channel' => ( isset( $settings[ 'channel' ] ) && ! empty( $settings[ 'channel' ] ) ) ? $settings[ 'channel' ] : YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'playlistId' ], 'items' => [] ] ) ); ?>;
		playlists = ( typeof channellist.items[ settings.channel ] === "undefined" ) ? channellist.items.<?php echo esc_attr( YOUTUBE_API_SPECIAL_PLAYLIST_CONTROL[ 'playlistId' ] ); ?> : channellist.items[ settings.channel ];
		view.addRenderAttribute( 'row', 'class', [
			'elementor-container',
			'elementor-container-flex-wrap'
		] );
		view.addRenderAttribute( 'imagehvr_wrapper', 'class', [
			'imagehvr-wrapper',
			'col-lg-2',
			'col-md-3',
			'col-sm-6'
		] );
		view.addRenderAttribute( 'imagehvr_content_wrapper', 'class', [
			'imagehvr-content-wrapper',
			'imagehvr-content-center',
			'imagehvr-anim-zoom-in-alt'
		] );
		
		#>
		<div class="{{{ view.getRenderAttributeString( 'row' ) }}}">
			<# _.each( playlists.items, function( item, index ) {
				var repeater_setting_key = view.getRepeaterSettingKey( 'text', 'list_items', index );
				view.addRenderAttribute( repeater_setting_key, 'class', 'elementor-list-widget-text' );
				view.addInlineEditingAttributes( repeater_setting_key );
				#>
				<div class="{{{ view.getRenderAttributeString( 'imagehvr_wrapper' ) }}}" {{{ view.getRenderAttributeString( repeater_setting_key ) }}} >
					<div class="imagehvr">
						<div class="{{{ view.getRenderAttributeString( 'imagehvr_content_wrapper' ) }}}">
							<a href="{{ item.url.playlists }}" class="imagehvr-link" data-embed="{{ item.url.playlistembed }}">
								<span class="imagehvr-icon ih-delay-zero imagehvr-anim-none">
									<i class="fas fa-play-circle"></i>
								</span>
							</a>
							<picture>
								<source sizes="162px" type="image/webp" data-srcset="{{ item.thumbnail }}" srcset="{{ item.thumbnail }}" >
								<img width="480" height="270" src="{{ item.thumbnail }}" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full lazyautosizes lazyloaded" alt="" data-eio="p" data-src="{{ item.thumbnail }}" decoding="async" data-srcset="{{ item.thumbnail }}" data-sizes="auto" sizes="162px" srcset="{{ item.thumbnail }}" >
								<noscript>
									<img width="480" height="270" src="{{ item.thumbnail }}" class="imagehvr-anim-none imagehvr-anim-zoom-in-out attachment-full size-full" alt="" srcset="{{ item.thumbnail }}" sizes="(max-width: 480px) 100vw, 480px" data-eio="l" />
								</noscript>
							</picture>
						</div>
						<div class="imagecaption">
							<span class="captiontext">
								{{ item.title }}
							</span>
						</div>
					</div>
				</div>
			<# } ); #>
		</div>
		<?php
	}
}

// end of line

?>