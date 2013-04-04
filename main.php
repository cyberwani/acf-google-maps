<?php
 
class acf_google_maps extends acf_Field
{

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
    
    function __construct()
    {
        $this->name = 'acf_google_maps';
        $this->label = 'Google Map Address Lookup';

        // do not delete!
        parent::__construct($parent);

        add_action('save_post', array($this, 'save_lat_lng'));
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
    
    
    /*
    *  input_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
    *  Use this action to add css + javascript to assist your create_field() action.
    *
    *  $info    http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
    *  @type    action
    *  @since    3.6
    *  @date    23/01/13
    */

    function input_admin_enqueue_scripts()
    {
        wp_enqueue_style('acf_google_maps_style', get_template_directory_uri() . '/library/php/acf-addons/acf-google-maps/css/main.css'); 
        wp_enqueue_script('acf_google_maps_script', get_template_directory_uri() . '/library/php/acf-addons/acf-google-maps/js/main.js', array('jquery', 'google_maps'), '1'); 
        wp_enqueue_script('google_maps', '//maps.google.com/maps/api/js?sensor=false', null, null); 
    }

}

new acf_google_maps();
