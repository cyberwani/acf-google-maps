<?php
/*
 *    Advanced Custom Fields - Google Maps Address Lookup
 */
 
 
class acf_google_maps extends acf_Field
{

    var $localizationDomain = 'acf_google_maps';

    /*--------------------------------------------------------------------------------------
    *
    *    Constructor
    *    - This function is called when the field class is initalized on each page.
    *    - Here you can add filters / actions and setup any other functionality for your field
    *
    *    @author Elliot Condon
    *    @since 2.2.0
    * 
    *-------------------------------------------------------------------------------------*/
    
    function __construct($parent)
    {
        // do not delete!
        parent::__construct($parent);

        $locale = get_locale();    
        load_textdomain($this->localizationDomain, sprintf("/%s/lang/%s-%s.mo",dirname( plugin_basename( __FILE__ ) ),$this->localizationDomain, $locale));

        // set name / title
        $this->name = 'acf_google_maps'; // variable name (no spaces / special characters / etc)
        $this->title = __("Google Map Address Lookup",$this->localizationDomain); // field label (Displayed in edit screens)
        add_action('save_post', array($this, 'save_lat_lng'));
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
    *    pre_save_field
    *    - this function is called when saving your acf object. Here you can manipulate the
    *    field object and it's options before it gets saved to the database.
    *
    *    @author Elliot Condon
    *    @since 2.2.0
    * 
    *-------------------------------------------------------------------------------------*/
    
    function pre_save_field($field)
    {
        // do stuff with field (mostly format options data)
        
        return parent::pre_save_field($field);
    }

    /*--------------------------------------------------------------------------------------
    *
    *       try and get a nested array value
    *
    *-------------------------------------------------------------------------------------*/

    function try_get_value($field, $key)
    {
            return isset($field[$key]) ? $field[$key] : false;
    }

    /*--------------------------------------------------------------------------------------
    *
    *       Saves the lat and lng into the post_meta table
    *
    *-------------------------------------------------------------------------------------*/

    function save_lat_lng($post_ID)
    {
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;

      //sanitize user input
      $lat = sanitize_text_field( $_POST['_address_lat'] );
      $lng = sanitize_text_field( $_POST['_address_lng'] );

      // either using 
      update_post_meta($post_ID, '_address_lat', $lat);
      update_post_meta($post_ID, '_address_lng', $lng);
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

          <p> <input type="text" class="widefat" placeholder="Enter the street address" id="property-address" name="<?php echo $field['name'] ?>[search_string]" value="" / > </p>
          <p> <a id="get-coordinates" class="button-primary" href="javascript:;">Get Coordinates</a> </p>
        </fieldset>

        <div class="map">
            <iframe id="property-iframe" width="655" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $url; ?>"></iframe><br><a id="property-url" target="_blank" href="<?php echo $url ?>">View Larger Map</a>
        </div>

        <fieldset class="address-info">
          <p><b>Name</b> <input type="text" id="property-name" class="widefat" name="<?php echo $field['name'] ?>[name]" value="<?php echo $name ?>" / > </p>
          <p><b>Street</b> <input type="text" id="property-street" class="widefat" name="<?php echo $field['name'] ?>[street]" value="<?php echo $street ?>" / > </p>
          <p><b>City</b>   <input type="text" id="property-city"   class="widefat" name="<?php echo $field['name'] ?>[city]" value="<?php echo $city ?>" / > </p>
          <p><b>State</b>  <input type="text" id="property-state"  class="widefat" name="<?php echo $field['name'] ?>[state]" value="<?php echo $state ?>" / > </p>
          <p><b>Zip</b>    <input type="text" id="property-zip"    class="widefat" name="<?php echo $field['name'] ?>[zip]" value="<?php echo $zip ?>" / > </p>
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
          <input class="required address" type="hidden" name="<?php echo $field['name'] ?>[address]" value="<?php echo $address ?>" />
          <input class="required google-address" type="hidden" name="<?php echo $field['name'] ?>[google_address]" value="<?php echo $google_address ?>" />
          <input class="required" id="acf-property-lat" type="hidden" name="<?php echo $field['name'] ?>[lat]" value="<?php echo $lat ?>" />
          <input class="required" id="acf-property-lng" type="hidden" name="<?php echo $field['name'] ?>[lng]" value="<?php echo $lng ?>" />
          <input class="required" id="property-lat" type="hidden" name="_address_lat" value="<?php echo $lat ?>" />
          <input class="required" id="property-lng" type="hidden" name="_address_lng" value="<?php echo $lng ?>" />
        </fieldset>

      </div>

      <div class="clear"> </div>

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
        wp_enqueue_script('acf_google_maps_script', get_template_directory_uri() . '/library/php/acf-addons/acf-google-maps/js/main.js', array('jquery', 'google_maps'), '1'); 
        wp_enqueue_script('google_maps', '//maps.google.com/maps/api/js?sensor=false', null, null); 

    }
    
    function strip_array_indices( $ArrayToStrip ) {
        foreach( $ArrayToStrip as $objArrayItem) {
            $NewArray[] =  $objArrayItem;
        }
    
        return( $NewArray );
    }
    
    
    function admin_print_styles()
    {
        wp_enqueue_style('acf_google_maps_style', get_template_directory_uri() . '/library/php/acf-addons/acf-google-maps/css/main.css'); 
    }

    
    /*--------------------------------------------------------------------------------------
    *
    *    update_value
    *    - this function is called when saving a post object that your field is assigned to.
    *    the function will pass through the 3 parameters for you to use.
    *
    *    @params
    *    - $post_id (int) - usefull if you need to save extra data or manipulate the current
    *    post object
    *    - $field (array) - usefull if you need to manipulate the $value based on a field option
    *    - $value (mixed) - the new value of your field.
    *
    *    @author Elliot Condon
    *    @since 2.2.0
    * 
    *-------------------------------------------------------------------------------------*/
    
    function update_value($post_id, $field, $value)
    {
        // do stuff with value

        parent::update_value($post_id, $field, $value);
    }
    
    /*--------------------------------------------------------------------------------------
    *
    *    get_value
    *    - called from the edit page to get the value of your field. This function is useful
    *    if your field needs to collect extra data for your create_field() function.
    *
    *    @params
    *    - $post_id (int) - the post ID which your value is attached to
    *    - $field (array) - the field object.
    *
    *    @author Elliot Condon
    *    @since 2.2.0
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
    *    get_value_for_api
    *    - called from your template file when using the API functions (get_field, etc). 
    *    This function is useful if your field needs to format the returned value
    *
    *    @params
    *    - $post_id (int) - the post ID which your value is attached to
    *    - $field (array) - the field object.
    *
    *    @author Elliot Condon
    *    @since 3.0.0
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
