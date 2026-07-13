<?php if ( ! defined( 'ABSPATH' ) ) exit;


final class WPHTMLMail_Webfonts
{
    private static $instance;
    
    public static function instance(){
        if (!isset(self::$instance) && !(self::$instance instanceof WPHTMLMail_Webfonts)) {
            self::$instance = new WPHTMLMail_Webfonts();
        }

        return self::$instance;
    }


    public function __construct(){
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ] );
        add_action( 'haet_mail_rest_api_init', [ $this, 'rest_api_init' ], 10, 1 );
        add_filter( 'haet_mail_fonts', [$this, 'add_webfonts_to_editor'] );

        // load the stylesheet everywhere we need it
        add_action( 'haet_mail_before_settings_tab_template', [ $this, 'add_stylesheet_to_template_designer' ] );
        add_filter( 'haet_mail_modify_styled_mail', [ $this, 'add_stylesheet_to_email' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'add_stylesheet_to_mailbuilder' ] );

        add_filter( 'haet_mail_react_components', function( $react_components ){
            $react_components['webfonts'] = [
                'url'      => trailingslashit( HAET_MAIL_WEBFONTS_URL  ) . 'js/build/remoteEntry.js',
                'version'  => filemtime( trailingslashit( HAET_MAIL_WEBFONTS_PATH  ) . 'js/build/remoteEntry.js' ),
                'component'=> 'WebfontsSettings'
            ];
            return $react_components;
        } );

        // uncomment this line to refresh the list of google fonts
        // $this->refreshListOfGoogleFonts();
    }


    public function enqueue_scripts_and_styles($page){
        if( strpos($page, 'wp-html-mail') ){
            
            wp_enqueue_style( 'wp-html-mail-webfonts-style',  HAET_MAIL_WEBFONTS_URL.'/css/style.css', [], '1.2' );
		}
    }



    /**
     * filter the getFonts() output so the webfonts are listed in every font dropdown list
     * Either in the template designer or in WooCommerce, EDD and other extensions
     */
    public function add_webfonts_to_editor( $fonts ){
        $settings = get_option('haet_mail_webfont_options');;

        if( $settings && is_array( $settings['fontsets'] ) && count( $settings['fontsets'] ) ){
            foreach( $settings['fontsets'] as $fontset ){
                if( $fontset['cssvalue'] && $fontset['name'] ){
                    $fonts[str_replace('"','',$fontset['cssvalue'])] = str_replace('"','',$fontset['name']);
                }
            }
        }
        return $fonts;
    }


    /**
     * load the webfonts stylesheet in template designer
     */
    public function add_stylesheet_to_template_designer(){
        $settings = get_option('haet_mail_webfont_options');;
        if( $settings && $settings['googleFontsStylesheetURL'] ):
            ?>
            <link
                rel="stylesheet"
                type="text/css"
                href="<?php echo $settings['googleFontsStylesheetURL']; ?>"
            />
            <?php
        endif;
    }


    /**
     * load the webfonts stylesheet in preview iframe and the actually sent email
     */
    public function add_stylesheet_to_email( $template ){
        $settings = get_option('haet_mail_webfont_options');
        if( $settings && $settings['googleFontsStylesheetURL'] ){
            $embed_code = '<link
                    rel="stylesheet"
                    type="text/css"
                    href="'. $settings['googleFontsStylesheetURL'] . '"
                />';
            $template = str_ireplace( '</head>', $embed_code . '</head>', $template );
        }

        return $template;
    }


    /**
     * load the webfonts stylesheet in mailbuilder for WooCommerce
     */
    public function add_stylesheet_to_mailbuilder($page){
        if( false !== strpos($page, 'post.php')){
            global $post;
            $post_type = get_post_type( $post->ID );
            if ( $post_type == 'wphtmlmail_mail' ){
                $settings = get_option('haet_mail_webfont_options');;
                if( $settings['googleFontsStylesheetURL'] ){
                    wp_enqueue_style('haet_mail_googlefonts_css',  $settings['googleFontsStylesheetURL'], array());
                }
            }
        }
    }


    /**
     * Show action links on the plugin screen
     */
    public function plugin_action_links( $links ) {
        return array_merge( array(
            '<a href="' . get_admin_url(null,'options-general.php?page=wp-html-mail&tab=webfonts') . '">' . __( 'Settings' ) . '</a>'
        ), $links );
    }


    /**
     * register REST endpoints to get and save settings
     */
    public function rest_api_init( $api_base ) {
		register_rest_route( $api_base, '/webfontsettings', array(
            'methods' => 'GET',
            'callback' => [ $this, 'getSettings' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
		));
		
		register_rest_route( $api_base, '/webfontsettings', array(
            'methods' => 'POST',
            'callback' => [ $this, 'saveSettings' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
		));
    }
    
    /**
     * Fallback fonts are websafe fonts which will work on every device
     */
    private function getFallbackFonts(){
		$fonts = Haet_Mail()->get_default_fonts();
		$fonts_select_options = [];
		foreach( $fonts as $value => $label ){
			$fonts_select_options[] = [
				'value'	=> $value,
				'label' => $label
			];
		}
        return $fonts_select_options;
    }



    private function getGoogleFonts(){
        $fonts = file_get_contents( HAET_MAIL_WEBFONTS_PATH . 'includes/google-fonts.json' );
        return json_decode( $fonts, true );
    }
    


    public function getSettings(){
		$default_settings = [
            'googleFontsStylesheetURL' => '',
            'fontsets' => [
                [
                    'googleFont'    => '',
                    'fallbackFont'  => '',
                    'name'          => '',
                    'cssvalue'      => ''
                ],
                [
                    'googleFont'    => '',
                    'fallbackFont'  => '',
                    'name'          => '',
                    'cssvalue'      => ''
                ],
                [
                    'googleFont'    => '',
                    'fallbackFont'  => '',
                    'name'          => '',
                    'cssvalue'      => ''
                ]
            ]
        ];
		 
		$settings = get_option('haet_mail_webfont_options');
        $settings = wp_parse_args( $settings, $default_settings );
        $settings['fallbackfonts'] = $this->getFallbackFonts();
        $settings['googlefonts'] = $this->getGoogleFonts();
        return new \WP_REST_Response( $settings );
	}
    
    
	public function saveSettings( $request ){
		if( $request->get_params() ){
			$theme_options = $request->get_params();
			update_option('haet_mail_webfont_options', $theme_options);
		}
		
		return new \WP_REST_Response(  );
    }
    

    public function isScriptDebug() {
        return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG === true;
    }
    

    /**
     * this function does only run locally in our test environment (08) to avoid abuse of our API key and related costs
     * If you have your own google fonts API key you can define it in you wp-config.php
     */
    public function refreshListOfGoogleFonts(){
        if( defined('HAET_MAIL_WEBFONTS_API_KEY') ){
            try{
                $request = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . HAET_MAIL_WEBFONTS_API_KEY . '&sort=alpha' );
                if( is_wp_error( $request ) ) {
                    return false;
                }

                $body = wp_remote_retrieve_body( $request );
                $data = json_decode( $body, true );
                if( !empty( $data ) ){
                    $fonts = [];

                    foreach( $data['items'] as $font ){
                        $fonts[] = [
                                'family'    =>  $font['family'],
                                'category'  =>  $font['category'],
                                'variants'  =>  $font['variants']
                            ];
                    }

                    file_put_contents( HAET_MAIL_WEBFONTS_PATH . 'includes/google-fonts.json', json_encode( $fonts ) );
                }
            }catch( Exception $e ){
                error_log('Could not fetch Google Fonts from API');
            }
        }
    }
}



function WPHTMLMail_Webfonts()
{
    return WPHTMLMail_Webfonts::instance();
}

WPHTMLMail_Webfonts();