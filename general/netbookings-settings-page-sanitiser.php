<?php 
// Make sure to not redeclare the class
if (!class_exists('Netbookings_Settings_Page_Sanitiser')) :
    
class Netbookings_Settings_Page_Sanitiser {

    /**
     * Sanitises and adds errors for business id option
     *
     * @param $input
     * @return int|string
     */
    public function business_id($input){

        $current_val = get_option('nb_business_id');
		$message = null;
		$type = null;

		if($input != '' && is_numeric($input) && $input > 0){
			$type = 'updated';
			$message = 'Netbookings business ID updated';
		}else{
			$type = 'error';
			$message = 'Please enter a valid Netbookings business ID';
		}

        if($current_val != $input){
            add_settings_error(
                'nb_business_id',
                esc_attr('settings_updated'),
                $message,
                $type
            );
        }

		return $type == 'error' ? '' : $input;
    }

    public function bathing_availability_active($input){
        $current_val = get_option('nb_bathing_availability_active');
        $message = null;
        $type = null;
        
        if($input == '1' || $input == ''){
            $type = 'updated';
			$message = 'Netbookings bathing availability setting updated.';
        }else{
            $type = 'error';
			$message = 'Please enter a valid bathing availability setting.';
        }

        if($current_val != $input){
            add_settings_error(
                'nb_bathing_availability_active',
                esc_attr('settings_updated'),
                $message,
                $type
            );
        }

        if($input == '') $input = 0;

        return $type == 'error' ? 0 : $input;
    }

    public function cards_active($input){
        $current_val = get_option('nb_cards_active');
        $message = null;
        $type = null;
        
        if($input == '1' || $input == ''){
            $type = 'updated';
			$message = 'Netbookings package/service card setting updated.';
        }else{
            $type = 'error';
			$message = 'Please enter a valid package/service card setting.';
        }

        if($current_val != $input){
            add_settings_error(
                'nb_card_active',
                esc_attr('settings_updated'),
                $message,
                $type
            );
        }

        if($input == '') $input = 0;

        return $type == 'error' ? 0 : $input;
    }

}    

endif;