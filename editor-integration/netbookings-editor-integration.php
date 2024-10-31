<?php
/**
 *  Handles adding Netbookings buttons into tineMCE and/or gutenberg editor to insert shortcodes
 */

// Make sure to not redeclare the class
if (!class_exists('Netbookings_Editor_Integration')) :
class Netbookings_Editor_Integration {

    private $nb_xml_data;

    function __construct($nb_xml_data)
    {
        $this->nb_xml_data = $nb_xml_data;

        add_action('admin_head', array($this, 'admin_head'));
        
        add_action('admin_init', array($this, 'tinymce_hooks'));

        //add_action('enqueue_block_editor_assets', array($this, 'gutenberg_hooks'), 9 );
    }

    /**
     * Enqueues gutenberg-plugin js
     */
	public function gutenberg_hooks() {
	    wp_enqueue_script(
	        'netbookings-gutenberg-custom-button',
            plugin_dir_url(__FILE__) . 'gutenberg-plugin.js',
            [ 'wp-editor', 'wp-element'],
            '1.0.0',
            true
        );
    }

    /**
     * Load the plugin and register the buttons
     */
    public function tinymce_hooks() {
        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', array($this, 'add_tinymce_plugin'));
            add_filter('mce_buttons', array($this, 'register_tinymce_buttons'));
        }
    }

    public function add_tinymce_plugin($plugin_array) {
        $plugin_array['netbookingsShortcodesExtensions'] = plugins_url('tinymce-plugin.js', __FILE__);

        return $plugin_array;
    }

    /**
     * Register the accordion shortcode buttons
     */
    public function register_tinymce_buttons($buttons) {
        $newButtons = array(
            'NetbookingsItemShortcode',
            'NetbookingsCategoryShortcode'
        );

        // Place the buttons before the "insert more" button
        array_splice($buttons, 12, 0, $newButtons);

        return $buttons;
    }

    /**
     * Localize xml data into js to use for editor buttons
     */
    public function admin_head() {
        if (defined('AS_COMPATIBILITY') && AS_COMPATIBILITY) {
            $prefix = 'as-';
        }
        else {
            $prefix = '';
        }

        $replace = array(",","'",'"');

        //Get package categories
        $filename = get_home_path() . 'xml/package categories export.xml';
        $itemXML = $this->nb_xml_data->get('package_categories');
        $rowdata = $itemXML->ROWDATA;
        $package_categories = array();

        foreach($rowdata->ROW as $row)
        {
            $package_categories[str_replace($replace,'',$row->DISPCAT_NAME)] = (string)$row->DISPCAT_DISPCAT;
        }

        //Get packages
        $filename = get_home_path() . 'xml/package packages export.xml';
        $itemXML = $this->nb_xml_data->get('package_packages');
        $rowdata = $itemXML->ROWDATA;
        $packages = array();

        foreach($rowdata->ROW as $row)
        {
            $packages[str_replace($replace,'',$row->PACKAGES_NAME)] = (string)$row->PACKAGES_PACKAGE;
        }

        //Get service categories
        $filename = get_home_path() . 'xml/service categories export.xml';
        $itemXML = $this->nb_xml_data->get('service_categories');
        $rowdata = $itemXML->ROWDATA;
        $service_categories = array();

        foreach($rowdata->ROW as $row)
        {
            $service_categories[str_replace($replace,'',$row->DISPCAT_NAME)] = (string)$row->DISPCAT_DISPCAT;
        }

        //Get services
        $filename = get_home_path() . 'xml/service services export.xml';
        $itemXML = $this->nb_xml_data->get('service_services');
        $rowdata = $itemXML->ROWDATA;
        $services = array();

        foreach($rowdata->ROW as $row)
        {
            $services[str_replace($replace,'',$row->SERVICES_NAME)] = (string)$row->SERVICES_SERVICE;
        }

        ?>

        <script type="text/javascript">
            var netbookingsShortcodesPrefix = '<?php echo $prefix; ?>';

            var netbookingsPackageCategories = [
                {text: '', value: '-1'},
                <?php
                foreach($package_categories as $k => $v) {
                    echo "{text: '" . $k . "', value: '" . $v . "'},";
                }
                ?>
            ];
            var netbookingsPackages = [
                {text: '', value: '-1'},
                <?php
                foreach($packages as $k => $v) {
                    echo "{text: '" . $k . "', value: '" . $v . "'},";
                }
                ?>
            ];

            var netbookingsServiceCategories = [
                {text: '', value: '-1'},
                <?php
                foreach($service_categories as $k => $v) {
                    echo "{text: '" . $k . "', value: '" . $v . "'},";
                }
                ?>
            ];

            var netbookingsServices = [
                {text: '', value: '-1'},
                <?php
                foreach($services as $k => $v) {
                    echo "{text: '" . $k . "', value: '" . $v . "'},";
                }
                ?>
            ];
        </script>
    <?php }

}

endif;