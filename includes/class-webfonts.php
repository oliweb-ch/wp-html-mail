<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Webfonts integration — formerly a standalone addon by codemiq.
 * Merged into the core plugin by oliweb-ch.
 *
 * Copyright 2023 codemiq (email: support@codemiq.com)
 * Copyright 2025 oliweb-ch
 */
final class Haet_Webfonts {

	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Haet_Webfonts();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'haet_mail_rest_api_init', array( $this, 'rest_api_init' ), 10, 1 );
		add_filter( 'haet_mail_fonts', array( $this, 'add_webfonts_to_editor' ) );

		add_action( 'haet_mail_before_settings_tab_template', array( $this, 'add_stylesheet_to_template_designer' ) );
		add_filter( 'haet_mail_modify_styled_mail', array( $this, 'add_stylesheet_to_email' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_stylesheet_to_mailbuilder' ) );

		add_filter( 'haet_mail_react_components', function ( $react_components ) {
			$react_components['webfonts'] = array(
				'url'       => HAET_MAIL_URL . 'js/webfonts/build/remoteEntry.js',
				'version'   => filemtime( HAET_MAIL_PATH . 'js/webfonts/build/remoteEntry.js' ),
				'component' => 'WebfontsSettings',
			);
			return $react_components;
		} );
	}


	public function enqueue_scripts_and_styles( $page ) {
		if ( strpos( $page, 'wp-html-mail' ) ) {
			wp_enqueue_style(
				'wp-html-mail-webfonts-style',
				HAET_MAIL_URL . 'css/webfonts.css',
				array(),
				'1.2'
			);
		}
	}


	/**
	 * Ajoute les Google Fonts configurées à la liste des polices de l'éditeur.
	 */
	public function add_webfonts_to_editor( $fonts ) {
		$settings = get_option( 'haet_mail_webfont_options' );

		if ( is_array( $settings ) && ! empty( $settings['fontsets'] ) && is_array( $settings['fontsets'] ) ) {
			foreach ( $settings['fontsets'] as $fontset ) {
				if ( ! empty( $fontset['cssvalue'] ) && ! empty( $fontset['name'] ) ) {
					$fonts[ str_replace( '"', '', $fontset['cssvalue'] ) ] = str_replace( '"', '', $fontset['name'] );
				}
			}
		}
		return $fonts;
	}


	/**
	 * Charge la feuille de style Google Fonts dans le template designer.
	 */
	public function add_stylesheet_to_template_designer() {
		$settings = get_option( 'haet_mail_webfont_options' );

		if ( is_array( $settings ) && ! empty( $settings['googleFontsStylesheetURL'] ) ) {
			?>
			<link rel="stylesheet" type="text/css" href="<?php echo esc_url( $settings['googleFontsStylesheetURL'] ); ?>">
			<?php
		}
	}


	/**
	 * Injecte la feuille de style Google Fonts dans le HTML de l'email envoyé.
	 */
	public function add_stylesheet_to_email( $template ) {
		$settings = get_option( 'haet_mail_webfont_options' );

		if ( is_array( $settings ) && ! empty( $settings['googleFontsStylesheetURL'] ) ) {
			$embed = '<link rel="stylesheet" type="text/css" href="' . esc_url( $settings['googleFontsStylesheetURL'] ) . '">';
			$template = str_ireplace( '</head>', $embed . '</head>', $template );
		}
		return $template;
	}


	/**
	 * Charge la feuille de style Google Fonts dans le mailbuilder.
	 */
	public function add_stylesheet_to_mailbuilder( $page ) {
		if ( false === strpos( $page, 'post.php' ) ) {
			return;
		}

		global $post;
		if ( ! $post || get_post_type( $post->ID ) !== 'wphtmlmail_mail' ) {
			return;
		}

		$settings = get_option( 'haet_mail_webfont_options' );
		if ( is_array( $settings ) && ! empty( $settings['googleFontsStylesheetURL'] ) ) {
			wp_enqueue_style( 'haet_mail_googlefonts_css', $settings['googleFontsStylesheetURL'], array() );
		}
	}


	/**
	 * Enregistre les endpoints REST pour la gestion des webfonts.
	 *
	 * @param string $api_base Namespace de l'API REST du plugin principal.
	 */
	public function rest_api_init( $api_base ) {
		register_rest_route(
			$api_base,
			'/webfontsettings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_settings' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		register_rest_route(
			$api_base,
			'/webfontsettings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_settings' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}


	private function get_fallback_fonts() {
		$fonts               = Haet_Mail()->get_default_fonts();
		$fonts_select_options = array();
		foreach ( $fonts as $value => $label ) {
			$fonts_select_options[] = array(
				'value' => $value,
				'label' => $label,
			);
		}
		return $fonts_select_options;
	}


	private function get_google_fonts() {
		$fonts = file_get_contents( HAET_MAIL_PATH . 'includes/google-fonts.json' );
		return json_decode( $fonts, true );
	}


	public function get_settings() {
		$default_settings = array(
			'googleFontsStylesheetURL' => '',
			'fontsets'                 => array(
				array( 'googleFont' => '', 'fallbackFont' => '', 'name' => '', 'cssvalue' => '' ),
				array( 'googleFont' => '', 'fallbackFont' => '', 'name' => '', 'cssvalue' => '' ),
				array( 'googleFont' => '', 'fallbackFont' => '', 'name' => '', 'cssvalue' => '' ),
			),
		);

		$settings                    = get_option( 'haet_mail_webfont_options' );
		$settings                    = wp_parse_args( $settings, $default_settings );
		$settings['fallbackfonts']   = $this->get_fallback_fonts();
		$settings['googlefonts']     = $this->get_google_fonts();

		return new \WP_REST_Response( $settings );
	}


	public function save_settings( $request ) {
		$params = $request->get_params();

		if ( ! $params ) {
			return new \WP_REST_Response( array() );
		}

		$options = array();

		if ( isset( $params['googleFontsStylesheetURL'] ) ) {
			$options['googleFontsStylesheetURL'] = esc_url_raw( $params['googleFontsStylesheetURL'] );
		}

		if ( isset( $params['fontsets'] ) && is_array( $params['fontsets'] ) ) {
			$options['fontsets'] = array();
			foreach ( $params['fontsets'] as $fontset ) {
				$options['fontsets'][] = array(
					'googleFont'   => sanitize_text_field( $fontset['googleFont'] ?? '' ),
					'fallbackFont' => sanitize_text_field( $fontset['fallbackFont'] ?? '' ),
					'name'         => sanitize_text_field( $fontset['name'] ?? '' ),
					'cssvalue'     => sanitize_text_field( $fontset['cssvalue'] ?? '' ),
				);
			}
		}

		update_option( 'haet_mail_webfont_options', $options );

		return new \WP_REST_Response( $options );
	}


	/**
	 * Rafraîchit la liste des Google Fonts depuis l'API.
	 * Nécessite HAET_MAIL_WEBFONTS_API_KEY défini dans wp-config.php.
	 * À n'exécuter qu'en environnement de développement.
	 */
	public function refresh_google_fonts_list() {
		if ( ! defined( 'HAET_MAIL_WEBFONTS_API_KEY' ) ) {
			return;
		}

		$request = wp_remote_get(
			'https://www.googleapis.com/webfonts/v1/webfonts?key=' . HAET_MAIL_WEBFONTS_API_KEY . '&sort=alpha'
		);

		if ( is_wp_error( $request ) ) {
			return;
		}

		$data = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( empty( $data['items'] ) ) {
			return;
		}

		$fonts = array();
		foreach ( $data['items'] as $font ) {
			$fonts[] = array(
				'family'   => $font['family'],
				'category' => $font['category'],
				'variants' => $font['variants'],
			);
		}

		file_put_contents( HAET_MAIL_PATH . 'includes/google-fonts.json', wp_json_encode( $fonts ) );
	}
}


function Haet_Webfonts() {
	return Haet_Webfonts::instance();
}
