(function($){

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

})(this.jQuery);

