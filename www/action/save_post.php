<?php

session_start();

/*! \file save_post.php
 *  \brief This script is used to save a post to the database and save the movie file
 *  \details Pass data to this script via POST
 *  Expected parameters are:
 *      1) 'lon' - longitude of the device posting
 *      2) 'lat' - latitude of the device posting
 *      3) 'title' [optional] - title for the post
 *      4) the movie file to upload
 */

require_once '../util/validators.php';
require_once '../util/json.php';
require_once '../db/posts.php';
require_once '../db/users.php';
require_once 'constants.php';

// require some variables
mandatory(array(UILatitude, UILongitude));

mandatory_file(UIFile);

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

// Validate the title
// Title is optional
$l_title = NULL;
if (isset($_POST[UITitle])) {
    $l_title_val = new TitleValidator;
    $l_title_opt = array('options' => array($l_title_val, 'title'));
    $l_title = filter_input(INPUT_POST, UITitle, FILTER_CALLBACK, $l_title_opt);
    unset($l_title_val);
}

// Validate the file size/type
$l_allowed_types = array("quicktime", "mp4", "mov", "m4v");
$l_file_name = $_FILES[UIFile]["name"];
$l_extension = strtolower(pathinfo($l_file_name, PATHINFO_EXTENSION));
$l_type = strtolower(str_replace("video/", "", $_FILES[UIFile]["type"]));

// Check file extension
if (!in_array($l_extension, $l_allowed_types) ||
    !in_array($l_type, $l_allowed_types)) {
    \MyJSON\sendError("Unsupported file type: " . $l_extension
                      . " Type: " . $l_type );
    die();
}

// Check file size -- Kept for 500Mb
if ($_FILES[UIFile]["size"] > 500000000) {
    \MyJSON\sendError("File is too large");
    die();
}

// Generate a random name for file 
$l_rnd = chr(mt_rand(97, 122)).substr(md5(time()), 1);

date_default_timezone_set("UTC");
      
// Where to save the video
$l_location = ServerVideoFolder . date('Y-m-d');
if (!file_exists($l_location)) {
    mkdir($l_location, 0755, true);
}
$l_location = $l_location. "/" . $l_rnd . date('u') . "." . $l_extension;

// Save file from temporary to permanent
if (!move_uploaded_file($_FILES[UIFile]["tmp_name"], $l_location)) {
    \MyJSON\sendError("Unable to save file");
    die();
}

// Save metadata to DB
$l_post_id = \Buzzs\post_video($l_latitude, $l_longitude,
                               $l_title, $l_location, session_id());

// if post successful, save user info if any
// Its ok for this call to fail since user may exist already
\Bumblrs\add_user(session_id());

?>
