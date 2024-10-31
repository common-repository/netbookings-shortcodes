<?php

// Make sure to not redeclare the class
if (!class_exists('Netbookings_Bathing_Availability')) :

class Netbookings_Bathing_Availability {

    function __construct(){
        // Add scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'add_scripts']);

        add_action('wp_head', array($this, 'wp_head'));

        // Add shortcodes
        $prefix = $this->get_compatibility_prefix();
        add_shortcode($prefix . 'netbookings-bathing-availability', array($this, 'netbookings_bathing_availability'));

        //Add server side api call to hook
        add_action('wp_ajax_nb_get_availability', array($this, 'nb_get_availability'));
        add_action('wp_ajax_nopriv_nb_get_availability', array($this, 'nb_get_availability'));
    }

    public function nb_get_availability(){
        $date = preg_replace('/[^\/0-9]/', '', $_GET['date']);
        $room = (int) $_GET['room'];

        if($date == '') $date = date('Y/m/d'); 
        if(!is_integer($room)){
            return;
        };

        $curl = curl_init();

        $business = get_option('nb_business_id');
        $url = get_option('nb_base_url') . 'nbjsonserver.dll?action=getbathing&business=' . $business . '&room=' . $room . '&date=' . $date;

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 2
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        echo $response;

        exit('');
    }

    public function netbookings_bathing_availability($atts, $content = null){
        extract(shortcode_atts(array(
            'roomid' => '3',
            'pricinggroup' => '3'
        ), $atts, 'netbookings-bathing-availability'));

        return '<div class="nb-bathing-availability-chart" roomid="' . $roomid . '" pricinggroup="' . $pricinggroup . '"></div>';
    }

    public function wp_head(){
        $nb_base_url = get_option('nb_base_url');
        $nb_business_id = get_option('nb_business_id');
        $nb_primary_colour = get_option('nb_primary_colour');
        $nb_secondary_colour = get_option('nb_secondary_colour');

        if(isset($nb_base_url) && isset($nb_business_id)){
            ?>
                <script type="text/javascript">
                    var nbBaseURL = '<?= $nb_base_url ?>';
                    var nbBusinessID = <?=$nb_business_id ?>;
                    var nbPrimaryColour = '<?= $nb_primary_colour ?>';
                    var nbSecondaryColour = '<?= $nb_secondary_colour ?>';
                </script>
            <?php
        }
    }

    public function add_scripts(){
        wp_register_script('netbookings-bathing-availability-js', plugins_url('dist/index.js', __FILE__), array(), null, true);
        wp_enqueue_script('netbookings-bathing-availability-js');

        wp_register_style('netbookings-bathing-availability-css',  plugins_url('dist/style.css', __FILE__));
        wp_enqueue_style('netbookings-bathing-availability-css');
    }

    /**
     * Get the compatibility mode prefix
     *
     * return string
     */
    private function get_compatibility_prefix() {
        return defined('AS_COMPATIBILITY') && AS_COMPATIBILITY ? 'as-' : '';
    }

}

endif;