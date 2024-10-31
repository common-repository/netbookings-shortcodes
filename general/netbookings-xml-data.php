<?php
/**
 * Created by Liam on 04/09/2018.
 */
class Netbookings_XML_Data {

    /**
     * @var array
     */
    private $xml_data = array();

    /**
     * @var array
     */
    private $last_modified_data = array();

    /**
     * @var array
     */
    private $file_names = array(
		'package categories export.xml',
		'package packages export.xml',
		'service categories export.xml',
		'service services export.xml'
	);

    /**
     * @var array
     */
    private $lookup = array(
			'package_categories' => 0,
			'package_packages' => 1,
			'service_categories' => 2,
			'service_services' => 3,
		);

    /**
     * Loads xml and last modified data into arrays
     */
    public function load(){
		$home_path = get_home_path();
		
		foreach($this->file_names as $key => $file_name){
			
			try{
				$filepath = $home_path . 'xml/' . $file_name;
				$this->xml_data[$key] = simplexml_load_file($filepath,'SimpleXMLElement', LIBXML_NOCDATA);
				$this->last_modified_data[$key] = filemtime($filepath);
			}catch(Throwable $e){
				$this->xml_data[$key] = null;
			}
			
		}
		
	}

    /**
     * @param $name
     * @return bool|mixed
     */
    public function get($name){
		
		if($this->xml_data[$this->lookup[$name]] == null){
			return false;
		}else{
			return $this->xml_data[$this->lookup[$name]];
		}
		
	}

    /**
     * @param $name
     * @return false|string
     */
    public function get_last_modified($name){
		
		return date("d/n/y h:ia" ,$this->last_modified_data[$this->lookup[$name]]);
		
	}
	
}