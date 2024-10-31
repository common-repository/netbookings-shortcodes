<?php 
/**
 * Created by Liam on 04/09/2018.
 */

require_once('netbookings-settings-page-sanitiser.php');

// Make sure to not redeclare the class
if (!class_exists('Netbookings_Settings_Page')) :

class Netbookings_Settings_Page {

    /**
     * NB_XML_Data object passed into constructor
     * @var
     */
    private $nb_xml_data;

    /**
     * Tracks new sections for whitelist_custom_options_page()
     * @var array
     */
    private $page_sections = array();

    /**
     * @var string
     */
    protected $page_slug = 'netbookings';

    /**
     * @var string
     */
    protected $option_group_settings = 'nb_settings';

    /**
     * @var string
     */
    protected $option_group_bathing_availability = 'nb_bathing_availability';

    /**
     * @var string
     */
    protected $option_group_cards = 'nb_cards';

    /**
     * @var string
     */
    protected $option_group_xml_status = 'nb_xml_status';

    protected $sanitiser;

    /**
     * Netbookings_Settings_Page constructor.
     * @param $nb_xml_data
     */
    public function __construct($nb_xml_data){
		add_action('admin_menu', [$this, 'add_plugin_page']);
		add_action('admin_init', [$this, 'page_init']);
		
        $this->nb_xml_data = $nb_xml_data;
        $this->sanitiser = new Netbookings_Settings_Page_Sanitiser();
		
		// Must run after wp's `option_update_filter()`, so priority > 10
		add_action( 'whitelist_options', array( $this, 'whitelist_custom_options_page' ),11 );
	}

    /**
     * Callback for admin_menu, creates a menu item
     */
    public function add_plugin_page(){
		add_menu_page('Netbookings Shortcodes', 'Netbookings', 'manage_options', $this->page_slug, [$this, 'plugin_page_html'], plugins_url('general/assets/icon.png', __DIR__));
	}

    /**
     * Outputs html for the plugin page
     */
    public function plugin_page_html(){
		settings_errors(); ?>
		
		<div class="wrap">
			<h1><?= esc_html(get_admin_page_title()); ?></h1>
			<form method="post" action="options.php">
				<?php 
				settings_fields($this->page_slug);
				do_settings_sections($this->page_slug);
				submit_button(); 
				?>
			</form>
		</div>
		<?php
	}

    private function register_settings(){
        register_setting($this->option_group_settings, 'nb_base_url', array(
            'type' => 'string',
            'description' => 'Base URL for desired business.',
            'default' => 'https://secure.netbookings.com.au/tourism/'
        ));

        register_setting($this->option_group_settings, 'nb_business_id', array(
            'type' => 'integer',
            'description' => 'Business ID.',
            'sanitize_callback' =>  array($this->sanitiser, 'business_id')
        ));

        register_setting($this->option_group_cards, 'nb_cards_active', array(
            'type' => 'boolean',
            'descriptions' => 'Enables/disables package/service cards shortcodes',
            'sanitize_callback' => array($this->sanitiser, 'cards_active'),
            'default' => 'false'
        ));

        register_setting($this->option_group_cards, 'nb_styling', array(
            'type' => 'string',
            'description' => 'Styling choice for packages and services.',
            'default' => 'grey',
        ));

	    register_setting($this->option_group_cards, 'nb_button_style', array(
		    'type' => 'boolean',
		    'default' => 'false',
		    'description' => 'Select button styling.'
	    ));

        register_setting($this->option_group_bathing_availability, 'nb_bathing_availability_active', array(
            'type' => 'boolean',
            'descriptions' => 'Enables/disables bathing availability shortcode',
            'sanitize_callback' => array($this->sanitiser, 'bathing_availability_active'),
            'default' => 'false'
        ));

        register_setting($this->option_group_bathing_availability, 'nb_primary_colour', array(
            'type' => 'string',
            'description' => 'Primary colour for bathing availability graph.',
            'default' => '#f26522'
        ));

        register_setting($this->option_group_bathing_availability, 'nb_secondary_colour', array(
            'type' => 'string',
            'description' => 'Secondary colour for bathing availability graph.',
            'default' =>  '#336886'
        ));

        $xml_names = array(
            'package_categories',
            'package_packages',
            'service_categories',
            'service_services'
        );

        foreach($xml_names as $name){
            register_setting($this->option_group_xml_status, 'nb_xml_status_' . $name, array(
                'type' => 'boolean'
            ));
        }
    }

    private function add_settings(){
        add_settings_field('nb_base_url', 'Base URL', function(){

            $current_val = get_option('nb_base_url');
            echo '<input name="nb_base_url" type="url" value="' . (string)$current_val . '">';
            echo '<p class="description">Most users should not need to change this. Include trailing slash.</p>';

        }, $this->page_slug, $this->option_group_settings);

        add_settings_field('nb_business_id', 'Business ID', function(){

            $current_val =  get_option('nb_business_id');
            echo '<input name="nb_business_id" type="number" value="' . (string)$current_val . '">';
            echo '<p class="description">This is your business\'s unique identifier. It is the ID that you enter when logging into Netbookings.</p>';

        }, $this->page_slug, $this->option_group_settings);

        add_settings_field('nb_cards_active', 'Active', function(){
            $current_val = get_option('nb_cards_active');
            $checked = $current_val == 1 ? 'checked' : '';

            ?>
                <label for="nb_cards_active">
                    <input name="nb_cards_active" type="checkbox" id="nb_cards_active" value="1" <?= $checked ?>> 
                    Enable the package/service cards shortcodes
                </label>
            <?php
        }, $this->page_slug, $this->option_group_cards);

        add_settings_field('nb_styling', 'Styling', function(){

            $current_val = get_option('nb_styling');

            $values = array(
                'Grey' => 'grey',
                'Burgundy' => 'burgundy',
                'Dark Blue' => 'dark-blue',
                'Earthy Green' => 'earthy-green',
                'Red' => 'red',
                'None' => ''
            );

            echo '<select name="nb_styling">';

            foreach($values as $key => $value){

                $selected = '';
                if($value == $current_val){
                    $selected = 'selected';
                }
                ?>

                <option value="<?= $value ?>" <?= $selected ?>>
                    <?= $key; ?>
                </option>

                <?php
            }

            ?>

            <p class="description">You may choose a styling option, which will change the look and feel of the showcase.</p>
            <?php

        }, $this->page_slug, $this->option_group_cards);

        add_settings_field('nb_button_style', 'Button Style', function(){
            $current_val = get_option('nb_button_style');
            $checked = $current_val == 1 ? 'checked' : '';

            ?>
                <label for="nb_button_style">
                    <input name="nb_button_style" type="checkbox" id="nb_button_style" value="1" <?= $checked ?>> 
                    Enable expanded button style.
                </label>
            <?php
        }, $this->page_slug, $this->option_group_cards);

        add_settings_field('nb_bathing_availability_active', 'Active', function(){
            $current_val = get_option('nb_bathing_availability_active');
            $checked = $current_val == 1 ? 'checked' : '';

            ?>
                <label for="nb_bathing_availability_active">
                    <input name="nb_bathing_availability_active" type="checkbox" id="nb_bathing_availability_active" value="1" <?= $checked ?>> 
                    Enable the bathing availability shortcode
                </label>
            <?php
        }, $this->page_slug, $this->option_group_bathing_availability);
        
        add_settings_field('nb_primary_colour', 'Primary Colour', function(){
            $current_val = get_option('nb_primary_colour');

            echo '<input name="nb_primary_colour" type="text" value="' . (string)$current_val . '">';
            echo '<p class="description">Primary colour is to be in hexidecimal, including "#". For example: #f26522</p>';
        }, $this->page_slug, $this->option_group_bathing_availability);

        add_settings_field('nb_secondary_colour', 'Secondary Colour', function(){
            $current_val = get_option('nb_secondary_colour');

            echo '<input name="nb_secondary_colour" type="text" value="' . (string)$current_val . '">';
            echo '<p class="description">Secondary colour is to be in hexidecimal, including "#". For example: #336886</p>';
        }, $this->page_slug, $this->option_group_bathing_availability);

        $xml_names = array(
            'package_categories',
            'package_packages',
            'service_categories',
            'service_services'
        );

        $xml_names_sanitised = array(
            'Package Categories',
            'Packages',
            'Service Categories',
            'Services'
        );

        foreach($xml_names_sanitised as $key => $name){
            add_settings_field('nb_xml_status' . $xml_names[$key], $name, function($args){

                $nb_xml_data = $args[0];
                $name = $args[1];

                if($nb_xml_data->get($name) == false){
                    echo '<span class="dashicons dashicons-no"></span><span>&nbsp;&nbsp;Not found </span>';
                }else{
                    echo '<span class="dashicons dashicons-yes"></span>&nbsp;&nbsp;Updated on: ' . $nb_xml_data->get_last_modified($name) . '</span>';
                }

            }, $this->page_slug, $this->option_group_xml_status, array($this->nb_xml_data, $xml_names[$key]));
        }
    }
    /**
     * Registers and adds settings
     */
    public function page_init(){
		$this->add_settings_section(
			$this->option_group_settings,
			'Settings',
			null,
			$this->page_slug
        );

        $this->add_settings_section(
            $this->option_group_cards,
            'Package/Service Cards',
            null,
            $this->page_slug
        );
        
        $this->add_settings_section(
			$this->option_group_bathing_availability,
			'Bathing Availability',
			null,
			$this->page_slug
		);
		
		$this->add_settings_section(
			$this->option_group_xml_status,
			'Imported Data',
			null,
			$this->page_slug
		);
        $this->register_settings();
		$this->add_settings();
	}

    /**
     * White-lists options on custom pages.
     *
     * @param $whitelist_options
     * @return mixed
     */
    public function whitelist_custom_options_page($whitelist_options ){
		// Custom options are mapped by section id; Re-map by page slug.
		foreach($this->page_sections as $page => $sections ){
			
			$whitelist_options[$page] = array();
			foreach( $sections as $section )
			
				if( !empty( $whitelist_options[$section] ) )
					foreach( $whitelist_options[$section] as $option )
				
						$whitelist_options[$page][] = $option;
				}
				
		return $whitelist_options;
	}

    /**
     * Wrapper for wp's `add_settings_section()` that tracks custom sections
     *
     * @param $id
     * @param $title
     * @param $cb
     * @param $page
     */
    private function add_settings_section($id, $title, $cb, $page ){
		
		add_settings_section( $id, $title, $cb, $page );
		
		if( $id != $page ){
			if( !isset($this->page_sections[$page]))
				
				$this->page_sections[$page] = array();
				
			$this->page_sections[$page][$id] = $id;
		}
	}
	
}

endif;