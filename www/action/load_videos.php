<?php

/*! \file load_videos.php
 * \brief This is the script that will receive the request to load nearby videos
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 * \details Tasks by this script:
 *   1) Clean up the input using PHP filters - for security
 *   2) Fetch the video data and send it in JSON
 * \note We assume three doubles will be passed by POST, 'lat', 'lon', 'radius'
 */

require_once '../util/validators.php';
require_once '../util/geo.php';
require_once '../db/posts.php';
require_once 'constants.php';

// let us validate the input data first
mandatory(array(UILatitude, UILongitude, UIRadius));

//echo "<p>Ya you called me yo! Radius: " . $_POST["radius"] . "</p>";

// Validate the longitude
$l_longi_val = new LongitudeValidator;
$l_longi_opt = array('options' => array($l_longi_val, 'longitude'));
$l_longitude = filter_input(INPUT_POST, UILongitude, FILTER_CALLBACK, $l_longi_opt);
unset($l_longi_val);

// Validate the latitude
$l_lati_val = new LatitudeValidator;
$l_lati_opt = array('options' => array($l_lati_val, 'latitude'));
$l_latitude = filter_input(INPUT_POST, UILatitude, FILTER_CALLBACK, $l_lati_opt);
unset($l_lati_val);

// Validate the width
$l_rad_val = new RadiusValidator;
$l_rad_opt = array('options' => array($l_rad_val, 'radius'));
$l_rad = filter_input(INPUT_POST, UIRadius, FILTER_CALLBACK, $l_rad_opt);
unset($l_rad_val);

// split the 'radius' into degrees of latitude and longitude
$l_lat_hw = mile_to_delta_lat($l_rad);
$l_lon_hw = mile_to_delta_lon($l_rad, $l_latitude);

// if there is a post_id, validate it as well
$l_post_id = 0;
if (isset($_POST[UIPostID])) {
    $l_opt = array('options'=>array('default'=>0, 'min_range'=>0));
    $l_fil_id = filter_input(INPUT_POST, UIPostID, FILTER_VALIDATE_INT, $l_opt);
    if ($l_fil_id > 0) $l_post_id = $l_fil_id;
}

// We are now ready to query the database
\Buzzs\load_nearby_videos($l_latitude, $l_longitude,
                          $l_lat_hw, $l_lon_hw, $l_post_id);

?>
