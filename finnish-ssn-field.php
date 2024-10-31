<?php
/**
 * Plugin Name: Finnish SSN Field for NinjaForms
 * Version: 1.0
 * Description: Extends the Ninja Forms plugin with a Finnish SSN field. This plugin saves SSNs and you are responsible for their security.
 * Author: henri
 * License: GPLv3
*/

function finssn_add_fields($actions) {
    class FI_SSN_Field extends NF_Fields_Textbox {
        protected $_name = 'Finnish SSN';
        protected $_section = 'userinfo';
        protected $_type = 'textbox';
        protected $_templates = 'textbox';
    
        public function __construct() {
            parent::__construct();
    
            $this->_nicename = __( 'Finnish SSN', 'ninja-forms' );
        }
    }

	$actions['Finnish SSN'] = new FI_SSN_Field();

	return $actions;
}

function finssn_validate_input() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('validate', plugins_url('/validate.js', __FILE__));
}

function finssn_get_t($val) {
    $LUT = [
        0,
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'H',
        'J',
        'K',
        'L',
        'M',
        'N',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y'
    ];
    $t = $LUT[intval($val)%31];
    return $t;
}

function finssn_is_valid($field_value) {
    if (strlen($field_value) != 11) {
        return False;
    }
    $y = $field_value[6];
    $t = $field_value[10];
    $birthdate = substr($field_value, 0, 6);
    $nnn = substr($field_value, 7, 3);
    $birthyear = substr($birthdate, 4, 2);
    $is_y_valid = False;
    
    if ($birthyear > 1999) {
        if ($y == 'A') {
            $is_y_valid = True;
        }
    } else {
        if ($y == '-' || $y == '−') {
            $is_y_valid = True;
        }
    }

    return $is_y_valid && $t == finssn_get_t($birthdate . $nnn);
}

function finssn_on_submit( $form_data ) {
  foreach( $form_data[ 'fields' ] as $field ) {
      $field_key = $field['key'];
      $field_value = $field['value'];
      $field_id = $field['id'];

      if ( str_contains($field_key, 'finnish_ssn_') ) {
          if (! finssn_is_valid($field_value)) {
            error_log("Invalid");
            $errors[ 'fields' ][ $field_id ] = 'Field Error';
            wp_die();
          }
      }
  }

  return $form_data;
}

add_filter( 'ninja_forms_submit_data', 'finssn_on_submit' );
add_filter('ninja_forms_register_fields', 'finssn_add_fields');
add_action('wp_enqueue_scripts', 'finssn_validate_input');