<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Allows plugins to use their own update API.
 *
 * @author Easy Digital Downloads
 * @version 1.6.18
 */
class CQ_Plugin_Updater
{

	private $api_url     = 'https://codemiq.com/';
	private $api_data    = array();
	private $name        = '';
	private $slug        = '';
	private $version     = '';
	private $wp_override = false;
	private $cache_key   = '';

	private $health_check_timeout = 5;

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string  $_api_url     The URL pointing to the custom API endpoint.
	 * @param string  $_plugin_file Path to the plugin file.
	 * @param array   $_api_data    Optional data to send with API calls.
	 */
	public function __construct($_plugin_file, $_api_data = null)
	{

		global $cq_plugin_data;

		$this->api_data    = $_api_data;
		$this->name        = plugin_basename($_plugin_file);
		$this->item_id     = isset($_api_data['item_id']) ? $_api_data['item_id'] : false;
		// translated post id of item on our store
		$this->alternative_item_id     = isset($_api_data['alternative_item_id']) ? $_api_data['alternative_item_id'] : false;
		$this->slug        = basename($_plugin_file, '.php');
		$this->version     = $_api_data['version'];
		$this->wp_override = isset($_api_data['wp_override']) ? (bool) $_api_data['wp_override'] : false;
		$this->beta        = !empty($this->api_data['beta']) ? true : false;

		$this->api_data['license'] = get_option('cq_license_key_' . $this->slug);

		$this->cache_key   = 'edd_sl_' . md5(serialize($this->slug . $this->api_data['license'] . $this->beta));


		$cq_plugin_data[$this->slug] = $this->api_data;

		/**
		 * Fires after the $cq_plugin_data is setup.
		 *
		 * @since x.x.x
		 *
		 * @param array $cq_plugin_data Array of EDD SL plugin data.
		 */
		do_action('post_edd_sl_plugin_updater_setup', $cq_plugin_data);


		// Set up hooks.
		$this->init();
	}




	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init()
	{
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
		add_filter('plugins_api', array($this, 'plugins_api_filter'), 10, 3);
		remove_action('after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10);
		add_action('after_plugin_row_' . $this->name, array($this, 'show_update_notification'), 10, 2);
		add_action('admin_init', array($this, 'show_changelog'));

		add_action('after_plugin_row_' . $this->name, array($this, 'license_row'), 12, 2);

		// add autoupdate css and js
		add_action('admin_print_styles-plugins.php', array($this, 'print_styles_and_scripts'));

		add_action('wp_ajax_cq_activate_license', array($this, 'ajax_activate_license'));
		add_action('wp_ajax_cq_deactivate_license', array($this, 'ajax_deactivate_license'));
		add_action('wp_ajax_cq_check_license', array($this, 'ajax_check_license'));
	}


	/**
	 * Added the Fields for licensing this after Plugin-Row.
	 *
	 * @since	0.1
	 * @uses	is_network_admin, current_user_can, network_admin_url, get_site_option
	 * @return	bool
	 */
	public function license_row($plugin_file, $plugin_data)
	{

		// Security Check
		if (function_exists('is_network_admin') && is_network_admin()) {
			if (!current_user_can('manage_network_plugins'))
				return FALSE;
		} else if (function_exists('current_user_can') && !current_user_can('activate_plugins')) {
			return FALSE;
		}


		$license = get_option('cq_license_key_' . $this->slug);
		$status  = get_option('cq_license_status_' . $this->slug);

?>
		<tr class="cq-license active" id="<?php echo $this->slug; ?>-license">
			<td scope="row" colspan="4" class="cq-license-status <?php echo $this->slug; ?>-license-status">
				<span class="description"><?php _e('License key:'); ?></span>
				<input type="password" class="cq-license-key regular-text" value="<?php esc_attr_e($license); ?>" data-pluginslug="<?php echo $this->slug; ?>" />
				<span class="license-status"></span>
				<?php if ($status !== false && $status == 'valid') : ?>
					<button type="button" class="button-secondary cq-license-deactivate"><?php _e('Deactivate License'); ?></button>
				<?php else : ?>
					<button type="button" class="button-secondary cq-license-activate"><?php _e('Activate License'); ?></button>
				<?php endif; ?>
				<?php //var_dump( $this->check_update( null ) ); 
				?>
			</td>
		</tr>
<?php
	}


	public function print_styles_and_scripts()
	{
		wp_enqueue_style('cq-autoupdate', untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/css/autoupdater.css');
		wp_enqueue_script('cq-autoupdater-script', untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/js/autoupdater.js', 'jquery', '1.0');
		wp_localize_script(
			'cq-autoupdater-script',
			'cq_ajax_object',
			array('ajax_url' => admin_url('admin-ajax.php'))
		);
	}



	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array   $_transient_data Update array build by WordPress.
	 * @return array Modified update array with custom plugin data.
	 */
	public function check_update($_transient_data)
	{
		global $pagenow;

		if (!is_object($_transient_data)) {
			$_transient_data = new stdClass;
		}

		if ('plugins.php' == $pagenow && is_multisite()) {
			return $_transient_data;
		}

		if (!empty($_transient_data->response) && !empty($_transient_data->response[$this->name]) && false === $this->wp_override) {
			return $_transient_data;
		}

		$version_info = $this->get_cached_version_info();

		if (false === $version_info) {
			$version_info = $this->api_request('plugin_latest_version', array('slug' => $this->slug, 'item_id' => $this->item_id, 'beta' => $this->beta));

			$this->set_version_info_cache($version_info);
		}

		if (false !== $version_info && is_object($version_info) && isset($version_info->new_version)) {

			if (version_compare($this->version, $version_info->new_version, '<')) {

				$_transient_data->response[$this->name] = $version_info;

				// Make sure the plugin property is set to the plugin's name/location. See issue 1463 on Software Licensing's GitHub repo.
				$_transient_data->response[$this->name]->plugin = $this->name;
			}

			$_transient_data->last_checked           = time();
			$_transient_data->checked[$this->name] = $this->version;
		}

		return $_transient_data;
	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string  $file
	 * @param array   $plugin
	 */
	public function show_update_notification($file, $plugin)
	{

		if (is_network_admin()) {
			return;
		}

		if (!current_user_can('update_plugins')) {
			return;
		}

		if (!is_multisite()) {
			return;
		}

		if ($this->name != $file) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'), 10);

		$update_cache = get_site_transient('update_plugins');

		$update_cache = is_object($update_cache) ? $update_cache : new stdClass();

		if (empty($update_cache->response) || empty($update_cache->response[$this->name])) {

			$version_info = $this->get_cached_version_info();

			if (false === $version_info) {
				$version_info = $this->api_request('plugin_latest_version', array('slug' => $this->slug, 'beta' => $this->beta));

				// Since we disabled our filter for the transient, we aren't running our object conversion on banners, sections, or icons. Do this now:
				if (isset($version_info->banners) && !is_array($version_info->banners)) {
					$version_info->banners = $this->convert_object_to_array($version_info->banners);
				}

				if (isset($version_info->sections) && !is_array($version_info->sections)) {
					$version_info->sections = $this->convert_object_to_array($version_info->sections);
				}

				if (isset($version_info->icons) && !is_array($version_info->icons)) {
					$version_info->icons = $this->convert_object_to_array($version_info->icons);
				}

				$this->set_version_info_cache($version_info);
			}

			if (!is_object($version_info)) {
				return;
			}

			if (version_compare($this->version, $version_info->new_version, '<')) {

				$update_cache->response[$this->name] = $version_info;
			}

			$update_cache->last_checked = time();
			$update_cache->checked[$this->name] = $this->version;

			set_site_transient('update_plugins', $update_cache);
		} else {

			$version_info = $update_cache->response[$this->name];
		}

		// Restore our filter
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));

		if (!empty($update_cache->response[$this->name]) && version_compare($this->version, $version_info->new_version, '<')) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table('WP_Plugins_List_Table');
			# <tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
			echo '<tr class="plugin-update-tr" id="' . $this->slug . '-update" data-slug="' . $this->slug . '" data-plugin="' . $this->slug . '/' . $file . '">';
			echo '<td colspan="3" class="plugin-update colspanchange">';
			echo '<div class="update-message notice inline notice-warning notice-alt">';

			$changelog_link = self_admin_url('index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911');

			if (empty($version_info->download_link)) {
				printf(
					__('There is a new version of %1$s available. %2$sView version %3$s details%4$s.', 'easy-digital-downloads'),
					esc_html($version_info->name),
					'<a target="_blank" class="thickbox" href="' . esc_url($changelog_link) . '">',
					esc_html($version_info->new_version),
					'</a>'
				);
			} else {
				printf(
					__('There is a new version of %1$s available. %2$sView version %3$s details%4$s or %5$supdate now%6$s.', 'easy-digital-downloads'),
					esc_html($version_info->name),
					'<a target="_blank" class="thickbox" href="' . esc_url($changelog_link) . '">',
					esc_html($version_info->new_version),
					'</a>',
					'<a href="' . esc_url(wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $this->name, 'upgrade-plugin_' . $this->name)) . '">',
					'</a>'
				);
			}

			do_action("in_plugin_update_message-{$file}", $plugin, $version_info);

			echo '</div></td></tr>';
		}
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed   $_data
	 * @param string  $_action
	 * @param object  $_args
	 * @return object $_data
	 */
	public function plugins_api_filter($_data, $_action = '', $_args = null)
	{

		if ($_action != 'plugin_information') {

			return $_data;
		}

		if (!isset($_args->slug) || ($_args->slug != $this->slug)) {

			return $_data;
		}

		$to_send = array(
			'slug'   => $this->slug,
			'is_ssl' => is_ssl(),
			'fields' => array(
				'banners' => array(),
				'reviews' => false,
				'icons'   => array(),
			)
		);

		$cache_key = 'edd_api_request_' . md5(serialize($this->slug . $this->api_data['license'] . $this->beta));

		// Get the transient where we store the api request for this plugin for 24 hours
		$edd_api_request_transient = $this->get_cached_version_info($cache_key);

		//If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
		if (empty($edd_api_request_transient)) {

			$api_response = $this->api_request('plugin_information', $to_send);

			// Expires in 3 hours
			$this->set_version_info_cache($api_response, $cache_key);

			if (false !== $api_response) {
				$_data = $api_response;
			}
		} else {
			$_data = $edd_api_request_transient;
		}

		// Convert sections into an associative array, since we're getting an object, but Core expects an array.
		if (isset($_data->sections) && !is_array($_data->sections)) {
			$_data->sections = $this->convert_object_to_array($_data->sections);
		}

		// Convert banners into an associative array, since we're getting an object, but Core expects an array.
		if (isset($_data->banners) && !is_array($_data->banners)) {
			$_data->banners = $this->convert_object_to_array($_data->banners);
		}

		// Convert icons into an associative array, since we're getting an object, but Core expects an array.
		if (isset($_data->icons) && !is_array($_data->icons)) {
			$_data->icons = $this->convert_object_to_array($_data->icons);
		}

		if (!isset($_data->plugin)) {
			$_data->plugin = $this->name;
		}

		return $_data;
	}

	/**
	 * Convert some objects to arrays when injecting data into the update API
	 *
	 * Some data like sections, banners, and icons are expected to be an associative array, however due to the JSON
	 * decoding, they are objects. This method allows us to pass in the object and return an associative array.
	 *
	 * @since 3.6.5
	 *
	 * @param stdClass $data
	 *
	 * @return array
	 */
	private function convert_object_to_array($data)
	{
		$new_data = array();
		foreach ($data as $key => $value) {
			$new_data[$key] = $value;
		}

		return $new_data;
	}

	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array   $args
	 * @param string  $url
	 * @return object $array
	 */
	public function http_request_args($args, $url)
	{

		$verify_ssl = $this->verify_ssl();
		if (strpos($url, 'https://') !== false && strpos($url, 'edd_action=package_download')) {
			$args['sslverify'] = $verify_ssl;
		}
		return $args;
	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 * @param string  $_action The requested action.
	 * @param array   $_data   Parameters for the API action.
	 * @return false|object
	 */
	private function api_request($_action, $_data)
	{

		global $wp_version, $edd_plugin_url_available;

		$verify_ssl = $this->verify_ssl();

		// Do a quick status check on this domain if we haven't already checked it.
		$store_hash = md5($this->api_url);
		if (!is_array($edd_plugin_url_available) || !isset($edd_plugin_url_available[$store_hash])) {
			$test_url_parts = parse_url($this->api_url);

			$scheme = !empty($test_url_parts['scheme']) ? $test_url_parts['scheme']     : 'http';
			$host   = !empty($test_url_parts['host'])   ? $test_url_parts['host']       : '';
			$port   = !empty($test_url_parts['port'])   ? ':' . $test_url_parts['port'] : '';

			if (empty($host)) {
				$edd_plugin_url_available[$store_hash] = false;
			} else {
				$test_url = $scheme . '://' . $host . $port;
				$response = wp_remote_get($test_url, array('timeout' => $this->health_check_timeout, 'sslverify' => $verify_ssl));
				$edd_plugin_url_available[$store_hash] = is_wp_error($response) ? false : true;
			}
		}

		if (false === $edd_plugin_url_available[$store_hash]) {
			return;
		}

		$data = array_merge($this->api_data, $_data);

		if ($data['slug'] != $this->slug) {
			return;
		}

		if ($this->api_url == trailingslashit(home_url())) {
			return false; // Don't allow a plugin to ping itself
		}

		$api_params = array(
			'edd_action' => 'get_version',
			'license'    => !empty($data['license']) ? $data['license'] : '',
			'item_name'  => isset($data['item_name']) ? $data['item_name'] : false,
			'item_id'    => isset($data['item_id']) ? $data['item_id'] : false,
			'version'    => isset($data['version']) ? $data['version'] : false,
			'slug'       => $data['slug'],
			'author'     => $data['author'],
			'url'        => home_url(),
			'beta'       => !empty($data['beta']),
		);

		$request    = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => $verify_ssl, 'body' => $api_params));

		if (!is_wp_error($request)) {
			$request = json_decode(wp_remote_retrieve_body($request));
		}

		if ($request && isset($request->sections)) {
			$request->sections = maybe_unserialize($request->sections);
		} else {
			$request = false;
		}

		if ($request && isset($request->banners)) {
			$request->banners = maybe_unserialize($request->banners);
		}

		if ($request && isset($request->icons)) {
			$request->icons = maybe_unserialize($request->icons);
		}

		if (!empty($request->sections)) {
			foreach ($request->sections as $key => $section) {
				$request->$key = (array) $section;
			}
		}

		// each plugin exists twice on our website with different IDs for english an german
		// so we check once again, maybe the license has been purchased for other language
		if (
			empty($request->new_version)
			&& isset($request->msg)
			&& strpos($request->msg, 'License key is not valid') !== false
			&& $this->alternative_item_id
			&& $this->alternative_item_id != $data['item_id']
		) {

			$data['item_id'] = $this->alternative_item_id;
			return $this->api_request($_action, $data);
		}

		return $request;
	}

	public function show_changelog()
	{

		global $cq_plugin_data;

		if (empty($_REQUEST['edd_sl_action']) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action']) {
			return;
		}

		if (empty($_REQUEST['plugin'])) {
			return;
		}

		if (empty($_REQUEST['slug'])) {
			return;
		}

		if (!current_user_can('update_plugins')) {
			wp_die(__('You do not have permission to install plugin updates', 'easy-digital-downloads'), __('Error', 'easy-digital-downloads'), array('response' => 403));
		}

		$data         = $cq_plugin_data[$_REQUEST['slug']];
		$beta         = !empty($data['beta']) ? true : false;
		$cache_key    = md5('edd_plugin_' . sanitize_key($_REQUEST['plugin']) . '_' . $beta . '_version_info');
		$version_info = $this->get_cached_version_info($cache_key);

		if (false === $version_info) {

			$api_params = array(
				'edd_action' => 'get_version',
				'item_name'  => isset($data['item_name']) ? $data['item_name'] : false,
				'item_id'    => isset($data['item_id']) ? $data['item_id'] : false,
				'slug'       => $_REQUEST['slug'],
				'author'     => $data['author'],
				'url'        => home_url(),
				'beta'       => !empty($data['beta'])
			);

			$verify_ssl = $this->verify_ssl();
			$request    = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => $verify_ssl, 'body' => $api_params));

			if (!is_wp_error($request)) {
				$version_info = json_decode(wp_remote_retrieve_body($request));
			}


			if (!empty($version_info) && isset($version_info->sections)) {
				$version_info->sections = maybe_unserialize($version_info->sections);
			} else {
				$version_info = false;
			}

			if (!empty($version_info)) {
				foreach ($version_info->sections as $key => $section) {
					$version_info->$key = (array) $section;
				}
			}

			$this->set_version_info_cache($version_info, $cache_key);
		}

		if (!empty($version_info) && isset($version_info->sections['changelog'])) {
			echo '<div style="background:#fff;padding:10px;">' . $version_info->sections['changelog'] . '</div>';
		}

		exit;
	}

	public function get_cached_version_info($cache_key = '')
	{
		return false;
		if (empty($cache_key)) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option($cache_key);

		if (empty($cache['timeout']) || time() > $cache['timeout']) {
			return false; // Cache is expired
		}

		// We need to turn the icons into an array, thanks to WP Core forcing these into an object at some point.
		$cache['value'] = json_decode($cache['value']);
		if (!empty($cache['value']->icons)) {
			$cache['value']->icons = (array) $cache['value']->icons;
		}

		return $cache['value'];
	}

	public function set_version_info_cache($value = '', $cache_key = '')
	{

		if (empty($cache_key)) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime('+3 hours', time()),
			'value'   => json_encode($value)
		);

		update_option($cache_key, $data, 'no');
	}

	/**
	 * Returns if the SSL of the store should be verified.
	 *
	 * @since  1.6.13
	 * @return bool
	 */
	private function verify_ssl()
	{
		return (bool) apply_filters('edd_sl_api_request_verify_ssl', true, $this);
	}


	public function ajax_activate_license($use_alternative_item_id = false)
	{
		$result = array(
			'success'	=> false,
			'message'	=> 'Please enter a license key'
		);

		if (isset($_POST['key']) && isset($_POST['slug'])) {
			// retrieve the license from the database
			$license = trim($_POST['key']);
			$slug = trim($_POST['slug']);

			update_option('cq_license_key_' . $slug, $license);

			global $cq_plugin_data;
			$data = $cq_plugin_data[$slug];
			if ($use_alternative_item_id)
				$item_id = isset($data['alternative_item_id']) ? $data['alternative_item_id'] : false;
			else
				$item_id = isset($data['item_id']) ? $data['item_id'] : false;


			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_id'    => $item_id,
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

			// make sure the response came back okay
			if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

				if (is_wp_error($response)) {
					$result['message'] = $response->get_error_message();
				} else {
					$result['message'] = __('An error occurred, please try again.');
				}
			} else {

				$license_data = json_decode(wp_remote_retrieve_body($response));

				if (false === $license_data->success) {

					switch ($license_data->error) {

						case 'expired':

							$result['message'] = sprintf(
								__('Your license key expired on %s.'),
								date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
							);
							break;

						case 'disabled':
						case 'revoked':

							$result['message'] = __('Your license key has been disabled.');
							break;

						case 'missing':

							$result['message'] = __('Invalid license.');
							break;

						case 'invalid':
						case 'site_inactive':

							$result['message'] = __('Your license is not active for this URL.');
							break;

						case 'item_name_mismatch':

							$result['message'] = sprintf(__('This appears to be an invalid license key for %s.'), $this->slug);
							break;

						case 'no_activations_left':

							$result['message'] = __('Your license key has reached its activation limit.');
							break;
						case 'invalid_item_id':
							// try ONCE again with item_id in different language
							if (!$use_alternative_item_id)
								return $this->ajax_activate_license(true);
							else
								$result['message'] = __('An error occurred, please try again.');
							break;
						default:

							$result['message'] = __('An error occurred, please try again.');
							break;
					}
				} else {
					$result['message'] = __('Your license has been activated sucessfully.');
					$result['success'] = true;
				}

				update_option('cq_license_status_' . $slug, $license_data->license);
			}
		}

		echo json_encode($result);
		wp_die();
	}




	public function ajax_deactivate_license($use_alternative_item_id = false)
	{
		$result = array(
			'success'	=> false,
			'message'	=> ''
		);

		if (isset($_POST['slug'])) {
			$slug = trim($_POST['slug']);
			$license = get_option('cq_license_key_' . $slug);
			update_option('cq_license_key_' . $slug, '');
			delete_option('cq_license_status_' . $slug);

			global $cq_plugin_data;
			$data = $cq_plugin_data[$slug];
			if ($use_alternative_item_id)
				$item_id = isset($data['alternative_item_id']) ? $data['alternative_item_id'] : false;
			else
				$item_id = isset($data['item_id']) ? $data['item_id'] : false;


			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_id'    => $item_id,
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

			// make sure the response came back okay
			if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

				if (is_wp_error($response)) {
					$result['message'] = $response->get_error_message();
				} else {
					$result['message'] = __('An error occurred, please try again.');
				}
			} else {
				// decode the license data
				$license_data = json_decode(wp_remote_retrieve_body($response));

				// $license_data->license will be either "deactivated" or "failed"
				if ($license_data->license == 'deactivated') {
					$result['message'] = __('Your license has been deactivated.');
					$result['success'] = true;
				} elseif (
					$license_data->license == 'invalid_item_id'
					&& !$use_alternative_item_id
				)
					// try ONCE again with item_id in different language
					return $this->ajax_check_license(true);
			}
		}

		echo json_encode($result);
		wp_die();
	}

	function ajax_check_license($use_alternative_item_id = false)
	{

		global $wp_version;

		if (isset($_POST['slug'])) {
			$slug = trim($_POST['slug']);
			$license = get_option('cq_license_key_' . $slug);

			global $cq_plugin_data;
			$data = $cq_plugin_data[$slug];
			if ($use_alternative_item_id)
				$item_id = isset($data['alternative_item_id']) ? $data['alternative_item_id'] : false;
			else
				$item_id = isset($data['item_id']) ? $data['item_id'] : false;

			$api_params = array(
				'edd_action' => 'check_license',
				'license' => $license,
				'item_id' => $item_id,
				'url'       => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

			if (is_wp_error($response)) {
				echo 0;
				wp_die();
			}

			$license_data = json_decode(wp_remote_retrieve_body($response));

			if ($license_data->license == 'valid') {
				echo 1;
			} else {
				// try ONCE again with item_id in different language
				if (
					$license_data->license == 'invalid_item_id'
					&& !$use_alternative_item_id
				)
					return $this->ajax_check_license(true);

				echo 0;
			}
		}
		wp_die();
	}
}
