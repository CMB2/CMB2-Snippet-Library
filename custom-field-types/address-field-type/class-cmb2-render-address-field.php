<?php

/**
 * Handles 'address' custom field type.
 */
class CMB2_Render_Address_Field extends CMB2_Type_Base {

	/**
	 * List of states. To translate, pass array of states in the 'state_list' field param.
	 *
	 * @var array
	 */
	protected static $state_list = array( 'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District Of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming' );

	public static function init() {
		add_filter( 'cmb2_render_class_address', array( __CLASS__, 'class_name' ) );
		add_filter( 'cmb2_sanitize_address', array( __CLASS__, 'maybe_save_split_values' ), 12, 4 );

		/**
		 * The following snippets are required for allowing the address field
		 * to work as a repeatable field, or in a repeatable group
		 */
		add_filter( 'cmb2_sanitize_address', array( __CLASS__, 'sanitize' ), 10, 5 );
		add_filter( 'cmb2_types_esc_address', array( __CLASS__, 'escape' ), 10, 4 );
	}

	public static function class_name() { return __CLASS__; }

	/**
	 * Handles outputting the address field.
	 */
	public function render() {

		// make sure we assign each part of the value we need.
		$value = wp_parse_args( $this->field->escaped_value(), array(
			'address-1' => '',
			'address-2' => '',
			'city'      => '',
			'state'     => '',
			'zip'       => '',
			'country'   => '',
		) );

		if ( ! $this->field->args( 'do_country' ) ) {
			$state_list = $this->field->args( 'state_list', array() );
			if ( empty( $state_list ) ) {
				$state_list = self::$state_list;
			}

			// Add the "label" option. Can override via the field text param
			$state_list = array( '' => esc_html( $this->_text( 'address_select_state_text', 'Select a State' ) ) ) + $state_list;

			$state_options = '';
			foreach ( $state_list as $abrev => $state ) {
				$state_options .= '<option value="'. $abrev .'" '. selected( $value['state'], $abrev, false ) .'>'. $state .'</option>';
			}
		}

		$state_label = 'State';
		if ( $this->field->args( 'do_country' ) ) {
			$state_label .= '/Province';
		}

		ob_start();
		// Do html
		?>
		<div><p><label for="<?php echo $this->_id( '_address_1' ); ?>"><?php echo esc_html( $this->_text( 'address_address_1_text', 'Address 1' ) ); ?></label></p>
			<?php echo $this->types->input( array(
				'name'  => $this->_name( '[address-1]' ),
				'id'    => $this->_id( '_address_1' ),
				'value' => $value['address-1'],
				'desc'  => '',
			) ); ?>
		</div>
		<div><p><label for="<?php echo $this->_id( '_address_2' ); ?>'"><?php echo esc_html( $this->_text( 'address_address_2_text', 'Address 2' ) ); ?></label></p>
			<?php echo $this->types->input( array(
				'name'  => $this->_name( '[address-2]' ),
				'id'    => $this->_id( '_address_2' ),
				'value' => $value['address-2'],
				'desc'  => '',
			) ); ?>
		</div>
		<div style="overflow: hidden;">
			<div class="alignleft"><p><label for="<?php echo $this->_id( '_city' ); ?>'"><?php echo esc_html( $this->_text( 'address_city_text', 'City' ) ); ?></label></p>
				<?php echo $this->types->input( array(
					'class' => 'cmb_text_small',
					'name'  => $this->_name( '[city]' ),
					'id'    => $this->_id( '_city' ),
					'value' => $value['city'],
					'desc'  => '',
				) ); ?>
			</div>
			<div class="alignleft"><p><label for="<?php echo $this->_id( '_state' ); ?>'"><?php echo esc_html( $this->_text( 'address_state_text', $state_label ) ); ?></label></p>
				<?php if ( $this->field->args( 'do_country' ) ) : ?>
					<?php echo $this->types->input( array(
						'class' => 'cmb_text_small',
						'name'  => $this->_name( '[state]' ),
						'id'    => $this->_id( '_state' ),
						'value' => $value['state'],
						'desc'  => '',
					) ); ?>
				<?php else: ?>
					<?php echo $this->types->select( array(
						'name'    => $this->_name( '[state]' ),
						'id'      => $this->_id( '_state' ),
						'options' => $state_options,
						'desc'    => '',
					) ); ?>
				<?php endif; ?>
			</div>
			<div class="alignleft"><p><label for="<?php echo $this->_id( '_zip' ); ?>'"><?php echo esc_html( $this->_text( 'address_zip_text', 'Zip' ) ); ?></label></p>
				<?php echo $this->types->input( array(
					'class' => 'cmb_text_small',
					'name'  => $this->_name( '[zip]' ),
					'id'    => $this->_id( '_zip' ),
					'value' => $value['zip'],
					'type'  => 'number',
					'desc'  => '',
				) ); ?>
			</div>
		</div>
		<?php if ( $this->field->args( 'do_country' ) ) : ?>
		<div class="clear"><p><label for="<?php echo $this->_id( '_country' ); ?>'"><?php echo esc_html( $this->_text( 'address_country_text', 'Country' ) ); ?></label></p>
			<?php echo $this->types->input( array(
				'name'  => $this->_name( '[country]' ),
				'id'    => $this->_id( '_country' ),
				'value' => $value['country'],
				'desc'  => '',
			) ); ?>
		</div>
		<?php endif; ?>
		<p class="clear">
			<?php echo $this->_desc();?>
		</p>
		<?php

		// grab the data from the output buffer.
		return $this->rendered( ob_get_clean() );
	}

	/**
	 * Optionally save the Address values into separate fields
	 */
	public static function maybe_save_split_values( $override_value, $value, $object_id, $field_args ) {
		if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
			// Don't do the override
			return $override_value;
		}

		$address_keys = array( 'address-1', 'address-2', 'city', 'state', 'zip' );

		foreach ( $address_keys as $key ) {
			if ( ! empty( $value[ $key ] ) ) {
				update_post_meta( $object_id, $field_args['id'] . 'addr_'. $key, sanitize_text_field( $value[ $key ] ) );
			}
		}

		remove_filter( 'cmb2_sanitize_address', array( __CLASS__, 'sanitize' ), 10, 5 );

		// Tell CMB2 we already did the update
		return true;
	}

	public static function sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}

		return array_filter($meta_value);
	}

	public static function escape( $check, $meta_value, $field_args, $field_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}

		return array_filter($meta_value);
	}

}
