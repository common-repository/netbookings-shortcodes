<?php

/**
 * Plugin Name: Netbookings Shortcodes
 * Description: Shortcodes for inserting Netbookings packages, services and widgets.
 * Version: 2.0.4
 * Author: Netbookings
 * Author URI: http://www.netbookings.com.au
 */

require_once(ABSPATH . 'wp-includes/option.php');
require_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once(ABSPATH . 'wp-admin/includes/template.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once('general/netbookings-settings-page.php');
require_once('general/netbookings-xml-data.php');
require_once('editor-integration/netbookings-editor-integration.php');
require_once('package-service-cards/netbookings-cards.php');
require_once('bathing-availability/netbookings-bathing-availability.php');

// Make sure to not redeclare the class
if (!class_exists('Netbookings_Shortcodes')) :

	class Netbookings_Shortcodes
	{
		public $nb_xml_data;

		/**
		 * Instance of self to for singleton pattern
		 */
		private static $instance = null;

		/**
		 *  Link to plugin documentation
		 *  @var string
		 */
		private $documentation_link = 'http://help.netbookings.com.au/category/integrations/';

		/**
		 * Link to description on Netbookings site
		 * @var string
		 */
		private $description_link = 'http://netbookings.com.au/features/integrations/wordpress/';

		/**
		 * Current plugin version number
		 */
		//private $plugin_version = '2.0';

		public static function get_instance()
		{
			if (null == self::$instance) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Class constructor
		 * Sets up the plugin, adding shortcodes, and adding buttons.
		 * Also adds settings page if in admin area
		 */
		function __construct()
		{
			$basename = plugin_basename(__FILE__);

			//Creates object that handles reading of xml data
			$this->nb_xml_data = new Netbookings_XML_Data;
			$this->nb_xml_data->load();

			if (get_option('nb_cards_active') == 1) {
				new Netbookings_Cards($this->nb_xml_data);
			}

			if (get_option('nb_bathing_availability_active') == 1) {
				new Netbookings_Bathing_Availability();
			}


			if (is_admin()) {
				// Add link to documentation on plugin page
				add_filter("plugin_action_links_" . $basename, array($this, 'add_documentation_link'));

				// Adds netbookings settings page, sets timezone so file modified time is correct
				if (get_option('timezone_strong') != '') {
					date_default_timezone_set(get_option('timezone_string'));
				}

				new Netbookings_Settings_Page($this->nb_xml_data);
				new Netbookings_Editor_Integration($this->nb_xml_data);
			}
		}

		/**
		 * Adds documentation links on plugins page
		 * @param $links
		 * @return mixed
		 */
		public function add_documentation_link($links)
		{
			array_push($links, sprintf('<a href="%s">%s</a>', $this->documentation_link, 'Documentation'));
			array_push($links,  sprintf('<a href="%s">%s</a>', $this->description_link, 'Description'));

			return $links;
		}
	}

	Netbookings_Shortcodes::get_instance();

endif;
