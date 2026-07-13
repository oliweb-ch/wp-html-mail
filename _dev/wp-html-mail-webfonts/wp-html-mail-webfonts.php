<?php
/*
Plugin Name: WP HTML Mail - Webfonts addon
Plugin URI: https://codemiq.com/en/plugins/wp-html-mail-webfonts/
Description: Use hundreds of Google Fonts in your email templates
Version: 3.1.3
Author: Hannes Etzelstorfer // codemiq
Author URI: https://codemiq.com
Text Domain: wp-html-mail-webfonts
License: GPLv2 or later
*/

/*  Copyright 2023 codemiq (email : support@codemiq.com) */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

define('HAET_MAIL_WEBFONTS_PATH', plugin_dir_path(__FILE__));
define('HAET_MAIL_WEBFONTS_URL', plugin_dir_url(__FILE__));


function wphtmlmail_webfonts_core_notice()
{
?>
    <div class="notice notice-warning">
        <p><?php printf(
                __('<strong>Notice:</strong> To use the WP HTML Mail - webfonts integration please install the free WP HTML Mail plugin first. <a href="%s">Install Plugin</a>', 'haet_mail'),
                wp_nonce_url(network_admin_url('update.php?action=install-plugin&plugin=wp-html-mail'), 'install-plugin_wp-html-mail')
            ); ?></p>
    </div>
    <?php
}




function wphtmlmail_webfonts_init()
{
    load_plugin_textdomain('wp-html-mail-webfonts', false, dirname(plugin_basename(__FILE__)) . '/translations');
    if (!is_plugin_active('wp-html-mail/wp-html-mail.php')) {
        add_action('admin_notices', 'wphtmlmail_webfonts_core_notice');
    } else {
        $min_core_version = '3.3';
        $core_plugin_data = get_plugin_data(HAET_MAIL_PATH . '/wp-html-mail.php');
        if (version_compare($core_plugin_data['Version'], $min_core_version, '<')) {
            add_action('admin_notices', function () {
                $min_core_version = '3.3';
    ?>
                <div class="notice notice-warning">
                    <p>
                        <?php
                        printf(
                            __('<strong>Notice:</strong> Please update WP HTML Mail to version %s before using the webfonts extension.', 'wp-html-mail-webfonts'),
                            $min_core_version
                        );
                        ?>
                    </p>
                </div>
<?php
            });
        } else {
            require HAET_MAIL_WEBFONTS_PATH . 'includes/class-wphtmlmail-webfonts.php';

            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(WPHTMLMail_Webfonts(), 'plugin_action_links'));
        }
    }

    if (file_exists(dirname(__FILE__) . '/cq-autoupdater/autoupdater.php')) {
        if (!class_exists('CQ_Plugin_Updater')) {
            // load our custom updater if not loaded by any other plugin
            include(dirname(__FILE__) . '/cq-autoupdater/autoupdater.php');
        }

        add_action('admin_init', function () {
            $edd_updater = new CQ_Plugin_Updater(
                __FILE__,
                array(
                    'version'               => '3.1.3',   // current version number
                    'item_id'               => 16250,            // post ID of the product
                    'alternative_item_id'   => 16256,            // translated post ID of the product
                    'author'                => 'Hannes Etzelstorfer // codemiq',    // author of this plugin
                    'beta'                  => false,
                )
            );
        }, 0);
    }
}
add_action('plugins_loaded', 'wphtmlmail_webfonts_init', 25);

register_activation_hook(__FILE__, 'flush_rewrite_rules');
