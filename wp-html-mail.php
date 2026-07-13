<?php
/*
Plugin Name: WP HTML Mail - Email Template Designer
Plugin URI: https://github.com/oliweb-ch/wp-html-mail
Description: Create your own professional email design for all your outgoing WordPress emails
Version: 4.0.1
Text Domain: wp-html-mail
Domain Path: /translations
Author: Hannes Etzelstorfer // codemiq, oliweb-ch
Author URI: https://github.com/oliweb-ch
License: GPLv2 or later
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 8.3
*/

/*
 * Copyright 2025 codemiq (email: support@codemiq.com) — original author
 * Copyright 2025 oliweb-ch — fork maintainer
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HAET_MAIL_PATH', plugin_dir_path( __FILE__ ) );
define( 'HAET_MAIL_URL', plugin_dir_url( __FILE__ ) );

require_once HAET_MAIL_PATH . 'vendor/autoload.php';
require HAET_MAIL_PATH . 'includes/class-haet-mail.php';
require HAET_MAIL_PATH . 'includes/class-mailbuilder.php';
require HAET_MAIL_PATH . 'includes/class-haet-sender-plugin.php';
require HAET_MAIL_PATH . 'includes/class-webfonts.php';

// Plugin Update Checker — surveille les GitHub Releases de oliweb-ch/wp-html-mail.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$haet_mail_update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/oliweb-ch/wp-html-mail/',
	__FILE__,
	'wp-html-mail'
);
$haet_mail_update_checker->getVcsApi()->enableReleaseAssets();

add_action( 'init', function () {
	load_plugin_textdomain( 'wp-html-mail', false, dirname( plugin_basename( __FILE__ ) ) . '/translations' );
} );

if ( class_exists( 'Haet_Mail' ) ) {
	if ( version_compare( PHP_VERSION, '8.3' ) < 0 ) {
		add_action( 'admin_notices', 'haet_mail_php_update_notice' );
	} else {
		add_action( 'plugins_loaded', function () {
			if ( is_plugin_active( 'wpmandrill/wpmandrill.php' ) ) {
				add_filter( 'mandrill_payload', array( Haet_Mail(), 'style_mail_wpmandrill' ) );
			} else {
				add_filter( 'wp_mail', array( Haet_Mail(), 'style_mail' ), 12, 1 );
			}

			add_action( 'admin_menu', array( Haet_Mail(), 'admin_page' ), 20 );
			add_action( 'admin_enqueue_scripts', array( Haet_Mail(), 'admin_page_scripts_and_styles' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( Haet_Mail(), 'plugin_action_links' ) );

			Haet_MB_ContentType_Text();
			Haet_MB_ContentType_TwoCol();
			Haet_Mail_Builder();
			Haet_Webfonts();
		}, 5 );
	}
}

function haet_mail_php_update_notice() {
	?>
	<div class="notice notice-warning">
		<p><?php _e( '<strong>Warning:</strong> To use WP HTML Mail please update your PHP version to 8.3 or higher in your hosting admin panel.', 'wp-html-mail' ); ?></p>
	</div>
	<?php
}
