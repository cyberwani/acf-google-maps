# Google Maps Address Locator

Addon for Advanced Custom Fields to show addresses.

## Dependencies

1. [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) must be installed.

## Installation

* Find the location of your theme folder
* Make a directory for the addon inside your theme files called `library/php/acf-addons/`
* Copy the contents of this repo into the directory

```
cd `~/sites/wp-content/themes/twentytwelve/` 
mkdir -p library/php/acf-addons
cp -r ~/Downloads/acf-google-maps/* library/php/acf-addons/
```

* Edit `functions.php` to include the ACF addon

```
if(function_exists("register_field")) {
  register_field('acf_google_maps', dirname(__FILE__) . '/library/php/acf-addons/acf-google-maps/main.php');
}
```

## Usage

The Google map addon stores data in an array.

```
Array
(
    [search_string] => 1600 penselvania ave washington dc
    [name] => The White House
    [street] => 1600 Pennsylvania Avenue Northwest
    [city] => Washington
    [state] => DC
    [zip] => 20500
    [address] => 1600 Pennsylvania Avenue Northwest, Washington, DC 20500
    [google_address] => 1600 Pennsylvania Avenue Northwest, Washington, DC 20500, USA
    [lat] => 38.8976777
    [lng] => -77.036517
)
```

The plugin also store the `lat` and `lng` coordinates in `wp_post_meta` table with the meta keys `_address_lat` and `_address_lng`. This is useful if you need any kind of store or event locator functionality.
