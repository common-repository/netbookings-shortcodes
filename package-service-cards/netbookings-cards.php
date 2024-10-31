<?php

// Make sure to not redeclare the class
if (!class_exists('Netbookings_Cards')) :

class Netbookings_Cards {

    /**
     * Reference to NB_XML_Data object
     * @var
     */
    private $nb_xml_data;

    function __construct($nb_xml_data) {
        $basename = plugin_basename(__FILE__);
        $this->nb_xml_data = $nb_xml_data;

        // Add shortcodes
        $prefix = $this->get_compatibility_prefix();
        add_shortcode($prefix . 'netbookings-packages', array($this, 'netbookings_packages_shortcode'));
        add_shortcode($prefix . 'netbookings-services', array($this, 'netbookings_services_shortcode'));

        // Add scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'add_scripts']);
    }

    /**
     * Get the compatibility mode prefix
     *
     * return string
     */
    private function get_compatibility_prefix() {
        return defined('AS_COMPATIBILITY') && AS_COMPATIBILITY ? 'as-' : '';
    }

    /**
     *
     * @param $atts
     * @param null $content
     * @return null|string|string[]
     */
    public function netbookings_packages_shortcode($atts, $content = null) {
        extract(shortcode_atts(array(
            'item' => '',
            'category' => '',
            'perrow' => 4,
            'truncate' => 0
        ), $atts, 'netbookings-packages'));

        $nb_button_style = get_option('nb_button_style');

        $home_path = get_home_path();
        $site_url = get_site_url();

        $found = array();

        $output = '';
        $output .= '<div class="row nb-items per-row-' . $perrow . '">';
        $itemXML = $this->nb_xml_data->get('package_packages');
        $packages = $itemXML->ROWDATA;

        if ($item){
            $item = explode(",",$item);
            foreach($packages->ROW as $package)
            {
                if(in_array((string)$package->PACKAGES_PACKAGE, $item))
                {
                    if (!in_array((string)$package->PACKAGES_PACKAGE, $found)) {
                        ob_start();
                        require('templates/packages.php');
                        $output .= ob_get_clean();
                        array_push($found, (string)$package->PACKAGES_PACKAGE);
                    }
                }
            }
        }
        else {
            $category = explode(",",$category);
            foreach($packages->ROW as $package)
            {
                if(in_array((string)$package->PACKAGESDISPCAT_DISPCAT, $category))
                {
                    $tag = '';

                    if (in_array((string)$package->PACKAGES_PACKAGE, $found)) {
                        $tag = ' - ' . $package->GROUPS_NAME;
                    }

                    ob_start();
                    require('templates/packages.php');
                    $output .= ob_get_clean();

                    array_push($found, (string)$package->PACKAGES_PACKAGE);
                }
            }
        }

        $output .= '</div>';


        return preg_replace('/\v(?:[\v\h]+)/', '', $output);
    }

    /**
     *
     * @param $atts
     * @param null $content
     * @return null|string|string[]
     */
    public function netbookings_services_shortcode($atts, $content = null) {
        extract(shortcode_atts(array(
            'item' => '',
            'category' => '',
            'perrow' => 4,
            'truncate' => 0
        ), $atts, 'netbookings-services'));

        $nb_button_style = get_option('nb_button_style');

        $home_path = get_home_path();
        $site_url = get_site_url();

        $found = array();

        $output = '<!-- Netbookings services Item:' . $item .' Category:' . $category . '  -->';
        $output .= '<div class="row nb-items per-row-' . $perrow . '">';

        $itemXML = $this->nb_xml_data->get('service_services');
        $services = $itemXML->ROWDATA;

        if ($item){
            $item = explode(",",$item);
            foreach($services->ROW as $service)
            {
                if(in_array((string)$service->SERVICES_SERVICE, $item))
                {
                    if (!in_array((string)$service->SERVICES_SERVICE, $found)) {
                        ob_start();
                        require('templates/services.php');
                        $output .= ob_get_clean();
                        array_push($found, (string)$service->SERVICES_SERVICE);
                    }
                }
            }
        }
        else {
            $category = explode(",",$category);
            foreach($services->ROW as $service)
            {
                if(in_array((string)$service->SERVICEDISPCAT_DISPCAT, $category))
                {
                    if (!in_array((string)$service->SERVICES_SERVICE, $found)) {
                        ob_start();
                        require('templates/services.php');
                        $output .= ob_get_clean();
                        array_push($found, (string)$service->SERVICES_SERVICE);
                    }
                }
            }
        }

        $output .= '</div>';

        return preg_replace('/\v(?:[\v\h]+)/', '', $output);
    }

    /**
     * Registers and enqueues init script
     */
    public function add_scripts(){

        //Enqueues styling chosen in shortcodes settings menu
        $style_name = get_option('nb_styling');
        if($style_name !== ''){
            wp_register_style('nb_stylesheet', plugins_url('/assets/css/' . $style_name . '.css', __FILE__));
            wp_register_style('nb_stylesheet_base', plugins_url('/assets/css/base.min.css', __FILE__));
            wp_enqueue_style('nb_stylesheet');
            wp_enqueue_style('nb_stylesheet_base');
        }

        //Font awesome
        wp_register_style('nb_fontawesome_solid', 'https://use.fontawesome.com/releases/v5.3.1/css/solid.css');
        wp_register_style('nb_fontawesome', 'https://use.fontawesome.com/releases/v5.3.1/css/fontawesome.css');
        wp_enqueue_style('nb_fontawesome_solid');
        wp_enqueue_style('nb_fontawesome');

        wp_register_script('netbookings-cards-init', plugins_url('/assets/js/netbookings-init.min.js', __FILE__), array(), null, true);
        wp_enqueue_script('netbookings-cards-init');

        $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8'); //If using IE, enqueue modernizr compat js for object-fit
        if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0') !== false && strpos($ua, 'rv:11.0') !== false)) {
            wp_register_script('netbookings-cards-compat', plugins_url('/assets/js/netbookings-cards-compat.min.js', __FILE__), array(), null, true);
            wp_enqueue_script('netbookings-cards-compat');
        }

    }

}

endif;