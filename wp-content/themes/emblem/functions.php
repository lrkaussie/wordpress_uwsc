<?php
/**
 * Emblem functions and definitions
 *
 * @package Emblem
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 620; /* pixels */
}

if ( ! isset( $full_content_width ) ) {
	$full_content_width = 960; /* pixels */
}

if ( ! class_exists( 'ThemeBoy_Emblem' ) ) :

class ThemeBoy_Emblem {

	/**
	 * @var string
	 */
	public $version = '1.1';

	/**
	 * @var string
	 */
	public $slug = 'emblem';

	/**
	 * @var string
	 */
	public $name = 'Emblem';

	public function __construct() {
		// Define constants
		$this->define_constants();
		
		// Include plugins
		$this->include_plugins();

		// Hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 11 );
		add_action( 'rookie_customize_register', array( $this, 'customize_register' ) );
		add_filter( 'rookie_footer_copyright', array( $this, 'footer_copyright' ), 20 );
		add_filter( 'rookie_footer_credit', array( $this, 'footer_credit' ), 20 );
		add_filter( 'rookie_header_image_style_options', array( $this, 'header_image_style_options' ) );
		add_filter( 'rookie_header_area_sections', array( $this, 'header_area_sections' ) );
		add_filter( 'rookie_customizer_vars', array( $this, 'customizer_vars' ) );
		add_filter( 'sportspress_color_schemes', array( $this, 'color_schemes' ) );
		add_action( 'after_setup_theme', array( $this, 'updater' ) );

		// Default colors
		add_filter( 'rookie_custom_background_args', array( $this, 'background' ) );
		add_filter( 'rookie_default_header_text_color', array( $this, 'default_header_text_color' ) );
		add_filter( 'rookie_default_content_color', array( $this, 'default_content_color' ) );
		add_filter( 'rookie_default_content_background_color', array( $this, 'default_content_background_color' ) );
		add_filter( 'rookie_default_primary_color', array( $this, 'default_primary_color' ) );
		add_filter( 'rookie_default_link_color', array( $this, 'default_link_color' ) );
		add_filter( 'rookie_default_text_color', array( $this, 'default_text_color' ) );
		add_filter( 'rookie_default_background_color', array( $this, 'default_background_color' ) );
		add_filter( 'rookie_default_heading_color', array( $this, 'default_heading_color' ) );
	}

	/**
	 * Define ThemeBoy Constants.
	 */
	private function define_constants() {
		define( 'THEMEBOY_FILE', __FILE__ );
		define( 'THEMEBOY_VERSION', $this->version );
		define( 'THEMEBOY_SLUG', $this->slug );
		define( 'THEMEBOY_NAME', $this->name );
	}

	/**
	 * Include plugins.
	 */
	private function include_plugins() {
		include_once get_template_directory() . '/plugins/mega-slider/mega-slider.php';
		include_once get_template_directory() . '/plugins/news-widget/news-widget.php';
		include_once get_template_directory() . '/plugins/social-sidebar/social-sidebar.php';
	}

	public function customize_register( $wp_customize ) {
	    /*
	     * Footer Section
	     */
	    $wp_customize->add_section( 'rookie_footer' , array(
	        'title'      => __( 'Footer', 'rookie' ),
	    ) );

	    /**
	     * Copyright
	     */
	    $wp_customize->add_setting( 'themeboy[footer_copyright]', array(
	        'default'       => '',
	        'sanitize_callback' => 'sanitize_text_field',
	        'capability'    => 'edit_theme_options',
	        'type'          => 'option',
	    ) );

	    $wp_customize->add_control( 'themeboy_footer_copyright', array(
	        'label'     => __('Copyright', 'rookie'),
	        'section'   => 'rookie_footer',
	        'settings'  => 'themeboy[footer_copyright]',
	        'input_attrs' => array(
	        	'placeholder' => sprintf( _x( '&copy; %1$s %2$s', 'copyright info', 'rookie' ), date( 'Y' ), get_bloginfo( 'name' ) ),
	        ),
	    ) );

	    /**
	     * Credit
	     */
	    $wp_customize->add_setting( 'themeboy[footer_credit]', array(
	        'default'       => '',
	        'sanitize_callback' => 'sanitize_text_field',
	        'capability'    => 'edit_theme_options',
	        'type'          => 'option',
	    ) );

	    $wp_customize->add_control( 'themeboy_footer_credit', array(
	        'label'     => __('Credit', 'rookie'),
	        'section'   => 'rookie_footer',
	        'settings'  => 'themeboy[footer_credit]',
	        'input_attrs' => array(
	        	'placeholder' => sprintf( __( 'Designed by %s', 'rookie' ), 'ThemeBoy' ),
	        ),
	    ) );

	    /**
	     * Link URL
	     */
	    $wp_customize->add_setting( 'themeboy[footer_link_url]', array(
	        'default'       => '',
	        'sanitize_callback' => 'esc_url',
	        'capability'    => 'edit_theme_options',
	        'type'          => 'option',
	    ) );

	    $wp_customize->add_control( 'themeboy_footer_link_url', array(
	        'label'     => __('Link URL', 'rookie'),
	        'section'   => 'rookie_footer',
	        'settings'  => 'themeboy[footer_link_url]',
	        'input_attrs' => array(
	        	'placeholder' => 'http://themeboy.com/',
	        ),
	    ) );
	}
	
	public function footer_copyright( $copyright ) {
		$options = (array) get_option( 'themeboy', array() );

		// Return if not customized
		if ( ! isset( $options['footer_copyright'] ) || '' == $options['footer_copyright'] ) {
			return $copyright;
		} else {
			return $options['footer_copyright'];
		}
	}
	
	public function footer_credit( $credit ) {
		$options = (array) get_option( 'themeboy', array() );

		// Return if not customized
		if ( ( ! isset( $options['footer_credit'] ) || '' == $options['footer_credit'] ) && ( ! isset( $options['footer_link_url'] ) || '' == $options['footer_link_url'] ) ) {
			return $credit;
		} else {
			$text = sprintf( __( 'Designed by %s', 'rookie' ), 'ThemeBoy' );
			$url = 'http://themeboy.com/';
			
			if ( isset( $options['footer_credit'] ) && '' !== $options['footer_credit'] ) {
				$text = $options['footer_credit'];
			}
			
			if ( isset( $options['footer_link_url'] ) && '' !== $options['footer_link_url'] ) {
				$url = $options['footer_link_url'];
			}
			
			return '<a href="' . $url . '">' . $text . '</a>';
		}
	}

	public function header_image_style_options( $options ) {
		$options = array(
			'image' => __( 'Image', 'rookie' ),
		);
		return $options;
	}

	public function header_area_sections() {
		return array( 'widgets', 'banner', 'branding', 'menu' );
	}

	public function scripts() {
		// Remove default Rookie styles
		remove_action( 'wp_print_scripts', 'rookie_custom_colors', 30 );
		wp_dequeue_style( 'rookie-oswald' );
		wp_dequeue_style( 'rookie-lato' );

		// Load Titillium Web font
		wp_enqueue_style( 'emblem-titillium-web', add_query_arg( 'family', 'Titillium+Web:300,300italic,600,600italic', "//fonts.googleapis.com/css", array(), null ) );

		// Apply custom colors
		add_action( 'wp_print_scripts', array( $this, 'custom_colors' ), 30 );
	}

	public function customizer_vars( $vars ) {	
		$vars['content_width_selector'] = '.site-header, .site-content, .site-footer, .site-info';
		return $vars;
	}

	public function custom_colors() {
		$colors = (array) get_option( 'themeboy', array() );
		$colors = array_map( 'esc_attr', $colors );
		
		// Get layout options
		if ( empty( $colors['content_width'] ) ) {
			$width = 1000;
		} else {
			$width = rookie_sanitize_content_width( $colors['content_width'] );
		}

		global $content_width;

		if ( empty( $colors['sidebar'] ) ) {
			$sidebar = '';
		} else {
			$sidebar = $colors['sidebar'];
		}

		if ( 'no' == $sidebar || is_page_template( 'template-fullwidth.php' ) ) {
			$content_width = $width - 40;
		} elseif ( 'double' === $sidebar )  {
			$content_width = $width * .50 - 40;
		} else {
			$content_width = $width * .66 - 40;
		}

		?>
		<style type="text/css"> /* Emblem Custom Layout */
		@media screen and (min-width: 1025px) {
			.site-header, .site-content, .site-footer, .site-info {
				width: <?php echo $width; ?>px; }
		}
		</style>
		<?php

		// Return if not customized
		if ( ! isset( $colors['customize'] ) ) {
			$enabled = get_option( 'sportspress_enable_frontend_css', 'no' );
			if ( 'yes' !== $enabled ) return;
		} elseif ( ! $colors['customize'] ) {
			return;
		}

		$colors['sponsors_background'] = get_option( 'sportspress_footer_sponsors_css_background', '#f4f4f4' );

		// Defaults
		if ( empty( $colors['primary'] ) ) $colors['primary'] = '#2b353e';
		if ( empty( $colors['background'] ) ) $colors['background'] = '#f4f4f4';
		if ( empty( $colors['content'] ) ) $colors['content'] = '#434446';
		if ( empty( $colors['text'] ) ) $colors['text'] = '#222222';
		if ( empty( $colors['heading'] ) ) $colors['heading'] = '#ffffff';
		if ( empty( $colors['link'] ) ) $colors['link'] = '#00a69c';
		if ( empty( $colors['content_background'] ) ) $colors['content_background'] = '#f2f2f2';

		// Calculate colors
		$colors['primary_darker'] = rookie_hex_darker( $colors['primary'], 10, true );
		$colors['highlight'] = rookie_hex_lighter( $colors['background'], 30, true );
		$colors['border'] = rookie_hex_darker( $colors['background'], 40, true );
		$colors['background_alt'] = rookie_hex_darker( $colors['background'], 5, true );
		$colors['text_lighter'] = rookie_hex_mix( $colors['text'], $colors['background'] );
		$colors['text_light'] = rookie_hex_mix( $colors['text'], $colors['text_lighter'] );
		$colors['heading_alpha'] = 'rgba(' . implode( ', ', rookie_rgb_from_hex( $colors['heading'] ) ) . ', 0.5)';
		$colors['sponsors_border'] = rookie_hex_darker( $colors['sponsors_background'], 20, true );
		$colors['content_alt'] = rookie_hex_darker( $colors['content'], 30, true );
		$colors['content_background_alt'] = rookie_hex_darker( $colors['content_background'], 5, true );
		$colors['content_border'] = rookie_hex_darker( $colors['content_background'], 31, true );
		$colors['content_background_from'] = rookie_hex_darker( $colors['content_background'], 13, true );
		$colors['content_background_to'] = rookie_hex_darker( $colors['content_background'], 50, true );
		$colors['content_background_active'] = rookie_hex_darker( $colors['content_background'], 46, true );

		$colors['primary_rgb'] = rookie_rgb_from_hex( $colors['primary'] );

		$colors['header'] = '#' . get_header_textcolor();

		?>
		<style type="text/css"> /* Frontend CSS */
		body,
		.widget_calendar thead,
		.sp-data-table thead,
		.sp-data-table a,
		.sp-template-countdown h5,
		.sp-template-countdown h5 a,
		.sp-template-countdown .event-name,
		.sp-template-countdown .event-name a,
		.sp-template-countdown time span,
		.sp-template-countdown time span small,
		.sp-template-event-calendar thead,
		.sp-event-blocks td,
		.sp-event-blocks a,
		.sp-event-blocks .sp-event-date,
		.sp-template .gallery-caption,
		.sp-template .gallery-caption a,
		.sp-tournament-bracket .sp-event .sp-event-main,
		.sp-template-tournament-bracket .sp-result {
			color: <?php echo $colors['text']; ?>; }
		a {
			color: <?php echo $colors['link']; ?>; }	
		.sp-tab-menu-item-active a {
			border-color: <?php echo $colors['link']; ?>; }
		.main-navigation li a,
		.type-post .entry-title,
		.type-post .entry-content,
		.mega-slider {
			color: <?php echo $colors['content']; ?>; }
		.main-navigation li a:hover {
			color: <?php echo $colors['content_alt']; ?>; }
		.widget_calendar tbody .pad,
		.sp-template-event-calendar tbody .pad,
		.mega-slider__row {
			background: <?php echo $colors['content_background_alt']; ?>; }
		.type-post .entry-header,
		.type-post .article-header,
		.type-post .entry-content,
		.comment-content,
		.widget_calendar tbody,
		.widget_calendar #today,
		.sp-template-event-calendar tbody,
		.sp-template-event-calendar #today,
		.mega-slider__row:hover {
			background: <?php echo $colors['content_background']; ?>; }
		.comment-content:after {
			border-right-color: <?php echo $colors['content_background']; ?>; }
		.widget_calendar tbody td,
		.sp-template-event-calendar tbody td {
			border-color: <?php echo $colors['content_border']; ?>; }
		.main-navigation,
		.main-navigation li {
			background: <?php echo $colors['content_background_from']; ?>;
			background: -moz-linear-gradient(top, <?php echo $colors['content_background_from']; ?> 0%, <?php echo $colors['content_background_to']; ?> 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $colors['content_background_from']; ?>), color-stop(100%,<?php echo $colors['content_background_to']; ?>));
			background: -webkit-linear-gradient(top, <?php echo $colors['content_background_from']; ?> 0%,<?php echo $colors['content_background_to']; ?> 100%);
			background: -o-linear-gradient(top, <?php echo $colors['content_background_from']; ?> 0%,<?php echo $colors['content_background_to']; ?> 100%);
			background: -ms-linear-gradient(top, <?php echo $colors['content_background_from']; ?> 0%,<?php echo $colors['content_background_to']; ?> 100%);
			background: linear-gradient(to bottom, <?php echo $colors['content_background_from']; ?> 0%,<?php echo $colors['content_background_to']; ?> 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $colors['content_background_from']; ?>', endColorstr='<?php echo $colors['content_background_to']; ?>',GradientType=0 ); }
		.main-navigation .current-menu-item,
		.main-navigation .current-menu-parent,
		.main-navigation .current-menu-ancestor,
		.main-navigation .current_page_item,
		.main-navigation .current_page_parent,
		.main-navigation .current_page_ancestor {
			background: <?php echo $colors['content_background_active']; ?>; }
		caption,
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.gallery dd,
		.gallery dd strong,
		.main-navigation li.menu-item-has-children:hover a,
		.main-navigation li.menu-item-has-children ul,
		.main-navigation li.menu-item-has-children a:after,
		.main-navigation.toggled .menu-toggle,
		.entry-details a,
		.article-details a,
		.entry-footer a,
		.widget_calendar caption,
		.widget_calendar tbody td a,
		.widget_calendar #prev a:before,
		.widget_calendar #next a:before,
		.sp-template-event-calendar #prev a:before,
		.sp-template-event-calendar #next a:before,
		.sp-template-event-calendar tbody td a,
		.sp-template-countdown time span small,
		.sp-event-blocks .sp-event-results .sp-result,
		.sp-heading,
		.sp-data-table .sp-heading,
		.sp-data-table tbody tr.highlighted,
		.sp-heading:hover,
		.sp-heading a:hover,
		.sp-table-caption,
		.single-sp_player .entry-header .entry-title strong,
		.mega-slider__row--active,
		.mega-slider__row--active:hover {
			color: <?php echo $colors['heading']; ?>; }
		.sp-data-table tbody tr.odd,
		.sp-data-table tbody tr.alternate,
		.sp-data-table .sp-event-venue-address-row td,
		.sp-event-blocks tr,
		.sp-template-event-logos .sp-team-result,
		.sp-template-countdown h5,
		.sp-template-countdown time span,
		.sp-template-details dl,
		.sp-tournament-bracket .sp-team-name,
		.site .sp-footer-sponsors,
		.sp-statistic-bar {
			background: rgba(<?php echo implode( ', ', $colors['primary_rgb'] ); ?>, 0.25); }
		.footer-area,
		.sp-view-all-link,
		.sp-tab-menu,
		.sp-data-table .sp-total-row td {
			border-color: rgba(<?php echo implode( ', ', $colors['primary_rgb'] ); ?>, 0.5); }
		.sp-tournament-bracket .sp-team .sp-team-name:before {
			border-left-color: rgba(<?php echo implode( ', ', $colors['primary_rgb'] ); ?>, 0.25);
			border-right-color: rgba(<?php echo implode( ', ', $colors['primary_rgb'] ); ?>, 0.25); }
		.sp-tournament-bracket .sp-event {
			border-color: rgba(<?php echo implode( ', ', $colors['primary_rgb'] ); ?>, 0.25) !important; }
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.main-navigation li.menu-item-has-children:hover a,
		.main-navigation li.menu-item-has-children ul,
		.entry-details,
		.article-details,
		.entry-footer,
		.widget_calendar caption,
		.widget_calendar tbody td a,
		.sp-data-table tbody tr.highlighted,
		.sp-table-caption,
		.sp-heading,
		.sp-template-event-calendar tbody td a,
		.sp-event-blocks .sp-event-results .sp-result,
		.single-sp_player .entry-header .entry-title strong,
		.sp-tournament-bracket .sp-heading,
		.mega-slider__row--active,
		.mega-slider__row--active:hover,
		.gallery dd strong,
		.sp-template-countdown time span small,
		.sp-event-blocks .sp-event-results .sp-result,
		.sp-statistic-bar-fill {
			background: <?php echo $colors['primary']; ?>; }
		blockquote:before,
		q:before,
		.type-post .entry-content a,
		.comment-metadata a,
		.comment-content a,
		.widget_recent_entries ul li:before,
		.widget_pages ul li:before,
		.widget_categories ul li:before,
		.widget_archive ul li:before,
		.widget_recent_comments ul li:before,
		.widget_nav_menu ul li:before,
		.widget_links ul li:before,
		.widget_meta ul li:before {
			color: <?php echo $colors['primary']; ?>; }
		.widget_calendar tbody td a:hover,
		.sp-template-event-calendar tbody td a:hover {
			background: <?php echo $colors['primary_darker']; ?>; }
		.gallery dd,
		.widget_calendar thead,
		.sp-data-table thead,
		.sp-template-event-calendar thead,
		.sp-template-countdown .event-name,
		.sp-event-blocks .sp-event-date,
		.single-sp_player .entry-header .entry-title,
		.sp-statistic-value {
			background: <?php echo $colors['background']; ?>; }
		.widget-title,
		.site-info {
			color: <?php echo $colors['header']; ?>; }

		@media screen and (max-width: 600px) {
			.main-navigation li.menu-item-has-children a {
				background: <?php echo $colors['primary']; ?>;
				color: <?php echo $colors['heading']; ?>; }
			.sp-header-sponsors { background: rgba(<?php echo implode( ', ', $colors['primary_rgb'] ); ?>, 0.25); }
		}

		<?php do_action( 'sportspress_frontend_css', $colors ); ?>

		</style>
		<?php
	}

	public function color_schemes( $color_schemes ) {
		$color_schemes['Emblem'] = array( '41a62a', '0e4d15', 'ffffff', 'ffffff', '57ec11' );
		return $color_schemes;
	}

	public function updater() {
		require_once( 'updater/themeboy-updater.php' );
	}

	public function background( $args ) {
		$args['default-color'] = '1e7613';
		return $args;
	}

	public function default_header_text_color() {
		return '#ffffff';
	}

	public function default_content_color() {
		return '#434446';
	}

	public function default_content_background_color() {
		return '#ffffff';
	}

	public function default_primary_color() {
		return '#41a62a';
	}

	public function default_link_color() {
		return '#57ec11';
	}

	public function default_text_color() {
		return '#ffffff';
	}

	public function default_background_color() {
		return '#0e4d15';
	}

	public function default_heading_color() {
		return '#ffffff';
	}
}

new ThemeBoy_Emblem();

endif;

require_once( 'framework.php' );
