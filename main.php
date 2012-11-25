<?php
/*
 *	Advanced Custom Fields - Google Maps Address Lookup
 */
 
 
class google_maps_address_lookup extends acf_Field
{

	var $localizationDomain = 'google_maps_address_lookup';

	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- This function is called when the field class is initalized on each page.
	*	- Here you can add filters / actions and setup any other functionality for your field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
		// do not delete!
    	parent::__construct($parent);

		$locale = get_locale();	
		load_textdomain($this->localizationDomain, sprintf("/%s/lang/%s-%s.mo",dirname( plugin_basename( __FILE__ ) ),$this->localizationDomain, $locale));
   	
    	// set name / title
    	$this->name = 'google_maps_address_lookup'; // variable name (no spaces / special characters / etc)
		  $this->title = __("Google Map Address Lookup",$this->localizationDomain); // field label (Displayed in edit screens)
   	}

	
	/*--------------------------------------------------------------------------------------
	*
	* Builds the field options
	* 
	* @see acf_Field::create_options()
	* @param string $key
	* @param array $field
	*
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
  {

  }
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	pre_save_field
	*	- this function is called when saving your acf object. Here you can manipulate the
	*	field object and it's options before it gets saved to the database.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function pre_save_field($field)
	{
		// do stuff with field (mostly format options data)
		
		return parent::pre_save_field($field);
	}

  /**
   * try and get a nested array value
   */
  function try_get_value($field, $key) 
  {
    return isset($field[$key]) ? $field[$key] : false;

  }
	
	
	/*--------------------------------------------------------------------------------------
	*
	* Creates the time picker field for inside post metaboxes
	* 
	* @see acf_Field::create_field()
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{


  global $post;

  ?>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script type="text/javascript">
    jQuery(document).ready(function($){

      /**
       * Validation
       * -------------------------------------------- */
      
      $('form#post').live('submit', function(){
        var count = 0;

        $('.ve_map .required').each(function(){

          if( $(this).val() == false ) {
            count = 1;
            console.log( $(this) );
          }

        });

        if(count) {

          // hide ajax stuff on submit button
          $('#publish').removeClass('button-primary-disabled');
          $('#ajax-loading').attr('style','');

          $('.error-message').show();

          return false;

        }

      });

      $('#get-coordinates').live('click', function(){

        ve_get_location( $('#property-address').val() );
        
      });
      var inputs = [
      '#property-street',
      '#property-city',
      '#property-state',
      '#property-zip'
      ].join(',')

      $(inputs).bind('keyup', update_address);

      var $google_address = $('#google-address');

      if($google_address.length)
        load_iframe($google_address.text());

      function ve_get_location(zip) {

        var geocoder = new google.maps.Geocoder(),
            data = { address: '' + zip + '' };

        geocoder.geocode( data, ve_update_data );

      }

      function ve_update_data(data, s) {

        var $lat      = $('#property-lat'),
            $lng      = $('#property-lng'),
            $acf_lng  = $('#acf-property-lng'),
            $acf_lat  = $('#acf-property-lat'),
            $google_address  = $('#google-address'),
            $address  = $('.address'),
            $gooAddy  = $('.google-address'),
            $street   = $('#property-street'),
            $city     = $('#property-city'),
            $state    = $('#property-state'),
            $zip      = $('#property-zip'),
            $lat_text = $('#lat'),
            $lng_text = $('#lng'),
            data      = data[0],
            address   = data.formatted_address;
            street    = address.replace(/,.*/, '').trim(),
            city      = address.split(',').slice(1,2)[0].trim(),
            state     = address.replace(/.* ([A-Z]{2}) .*/, '$1').trim(),
            zip       = address.replace(/.* ([0-9]{5}).*/, '$1').trim(),
            lat       = data.geometry.location.lat(),
            lng       = data.geometry.location.lng(),
            user_defined_address = make_address(street, city, state, zip);
            

        $google_address.text(address);
        $address.val(user_defined_address);
        $gooAddy.val(address);
        $street.val(street);
        $city.val(city);
        $state.val(state);
        $zip.val(zip);
        $lat.val(lat);
        $lng.val(lng);
        $acf_lng.val(lng);
        $acf_lat.val(lat);
        $lat_text.text(lat);
        $lng_text.text(lng);
        load_iframe(address);

      }

      function load_iframe( address ) {
        var url    = 'https://maps.google.com/maps?q=' + escape(address) + '&iwloc=0',
            iframe = 'https://maps.google.com/maps?q=' + escape(address) + '&iwloc=0&output=embed',
            $iframe   = $('#property-iframe'),
            $url      = $('#property-url');

        $url.attr('href', url);
        $iframe.attr('src', iframe);
      }

      function make_address(street, city, state, zip) {

        var address = [street, city, state].join(', ') + ' ' + zip;

        return address.replace(/^[, ]+|[, ]+$/, '').trim();

      }

      function update_address() {
        var $address  = $('.address'),
            street   = $('#property-street').val(),
            city     = $('#property-city').val(),
            state    = $('#property-state').val(),
            zip      = $('#property-zip').val();

        $address.val( make_address(street, city, state, zip));

      }

    });
  </script>


  <div class="ve_map">

    <?php 
      
      /**
       * Get the meta
       * -------------------------------------------- */

      $data    = $field['value'];
      $name    = $this->try_get_value($data, 'name');
      $lat     = $this->try_get_value($data, 'lat');
      $lng     = $this->try_get_value($data, 'lng');
      $street  = $this->try_get_value($data, 'street');
      $city    = $this->try_get_value($data, 'city');
      $state   = $this->try_get_value($data, 'state');
      $zip     = $this->try_get_value($data, 'zip');
      $address = $this->try_get_value($data, 'address');
      $google_address = $this->try_get_value($data, 'google_address');
    
    ?>

    <fieldset>

      <p class="error-message">Enter an address for the property.</p>

      <?php /* ?>
      <h4>How it works</h4>
      <ol>
        <li>Enter the property address into the "Address Lookup Tool" (directly below)</li>
        <li>Click "Get Coordinates"</li>
        <li>We fetch the latitude and longitude coordinates from Google, which are required for the search feature to work properly.</li>
        <li>The "Street", "City", "State", and "Zip" are for presentation purposes only and are not used in the search function, so feel free to change "Pl" to "Place", or "Dr" to "Drive"</li>
      </ol>

      <h4>Address Lookup Tool</h4>
      <?php */ ?>
      <p> <input type="text" class="widefat" placeholder="Enter the street address" id="property-address" name="<?php echo $field['name'] ?>[search_string]" value="" / > </p>
      <p> <a id="get-coordinates" class="button-primary" href="javascript:;">Get Coordinates</a> </p>
    </fieldset>

    <div class="map">
        <iframe id="property-iframe" width="655" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $url; ?>"></iframe><br><a id="property-url" target="_blank" href="<?php echo $url ?>">View Larger Map</a>
    </div>

    <fieldset class="address-info">
      <p><b>Name</b> <input type="text" id="property-name" class="widefat" name="<? echo $field['name'] ?>[name]" value="<?php echo $name ?>" / > </p>
      <p><b>Street</b> <input type="text" id="property-street" class="widefat" name="<? echo $field['name'] ?>[street]" value="<?php echo $street ?>" / > </p>
      <p><b>City</b>   <input type="text" id="property-city"   class="widefat" name="<? echo $field['name'] ?>[city]" value="<?php echo $city ?>" / > </p>
      <p><b>State</b>  <input type="text" id="property-state"  class="widefat" name="<? echo $field['name'] ?>[state]" value="<?php echo $state ?>" / > </p>
      <p><b>Zip</b>    <input type="text" id="property-zip"    class="widefat" name="<? echo $field['name'] ?>[zip]" value="<?php echo $zip ?>" / > </p>
      <table>
        <tr>
          <td><b>Google Address:</b></td>
          <td><span id="google-address"><?php echo $google_address ?></span></td>
        </tr>
        <tr>
          <td><b>Latitude:</b></td>
          <td><span id="lat"><?php echo $lat ?></span></td>
        </tr>
        <tr>
          <td><b>Longitude:</b></td>
          <td><span id="lng"><?php echo $lng ?></span></td>
        </tr>
      </table>
      <input class="required address" type="hidden" name="<? echo $field['name'] ?>[address]" value="<?php echo $address ?>" />
      <input class="required google-address" type="hidden" name="<? echo $field['name'] ?>[google_address]" value="<?php echo $google_address ?>" />
      <input class="required" id="acf-property-lat" type="hidden" name="<? echo $field['name'] ?>[lat]" value="<?php echo $lat ?>" />
      <input class="required" id="acf-property-lng" type="hidden" name="<? echo $field['name'] ?>[lng]" value="<?php echo $lng ?>" />
      <input class="required" id="property-lat" type="hidden" name="ve_address_lat" value="<?php echo $lat ?>" />
      <input class="required" id="property-lng" type="hidden" name="ve_address_lng" value="<?php echo $lng ?>" />
    </fieldset>

  </div>

  <div class="clear"> </div>

  <style>
    .map {
      float:right;
      width:60%;
      background:url( <?php bloginfo('stylesheet_directory') ?>/images/gmaps.png ) no-repeat center center; 
      padding:0 1em;
    }
    .map iframe {
      width:100%;
      border:1px solid #eee;
    }
    .address-info, .map {
      margin-top:4em;
    }
    .address-info .widefat {
      width:97%;
    }
    .error-message {
      display:none;
      border:1px solid red;
      background-color:pink;
      padding:1em;
    }
  </style>

  <?php
  
 	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	* admin_print_scripts / admin_print_styles
	* These functions are called in the admin_print_scripts / admin_print_styles where 
	* your field is created. Use this function to register css and javascript to assist 
	* your create_field() function.
	*
	* @see acf_Field::admin_print_scripts()
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
	
		
	}
	
	function strip_array_indices( $ArrayToStrip ) {
		foreach( $ArrayToStrip as $objArrayItem) {
			$NewArray[] =  $objArrayItem;
		}
	
		return( $NewArray );
	}
	
	
	function admin_print_styles()
	{
		wp_enqueue_style('jquery-style', get_stylesheet_directory_uri() . '/library/php/acfAddons/google_maps_address_lookup/css/jquery-ui.css'); 
		wp_enqueue_style('timepicker',  get_stylesheet_directory_uri() . '/library/php/acfAddons/google_maps_address_lookup/css/jquery-ui-timepicker-addon.css',array(
			'jquery-style'
		),'1.0.0');
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*	- this function is called when saving a post object that your field is assigned to.
	*	the function will pass through the 3 parameters for you to use.
	*
	*	@params
	*	- $post_id (int) - usefull if you need to save extra data or manipulate the current
	*	post object
	*	- $field (array) - usefull if you need to manipulate the $value based on a field option
	*	- $value (mixed) - the new value of your field.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{
		// do stuff with value

		parent::update_value($post_id, $field, $value);
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*	- called from the edit page to get the value of your field. This function is useful
	*	if your field needs to collect extra data for your create_field() function.
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		// get value
		$value = parent::get_value($post_id, $field);
		
		// format value
		
		// return value
		return $value;		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc). 
	*	This function is useful if your field needs to format the returned value
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// get value
		$value = $this->get_value($post_id, $field);
		
		// format value
		
		// return value
		return $value;

	}
	
}
