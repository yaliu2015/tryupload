<?php

define("MIN_LATITUDE", -90);
define("LATITUDE_RANGE", 180);
define("MIN_LONGITUDE", -180);
define("LONGITUDE_RANGE", 360); // the range must be 360

// --------------------------------------------------

/*! \brief convert miles to change in latitude
 * \param a_miles Number of miles to convert
 * \return The corresponding degree change in latitude
 */
function mile_to_delta_lat($a_miles) {
    return $a_miles / 69.172;
}

// --------------------------------------------------

/*! \brief convert miles to change in longitude
 * \param a_miles Number of miles to convert
 * \param a_latitude The latitude we are at
 * \return The corresponding degree change in longitude
 */
function mile_to_delta_lon($a_miles, $a_latitude) {
    if (abs($a_latitude) > 89.9) return 10;
    return $a_miles / (69.0976 * cos($a_latitude * pi()/180));
}

// --------------------------------------------------

/*! \brief Get the max possible latitude
 * \returns A double that represents the largest latitude
 */
function max_latitude() {
    return (MIN_LATITUDE + LATITUDE_RANGE);
}

// --------------------------------------------------

/*! \brief Get the max possible longitude
 * \returns A double that represents the largest longitude
 */
function max_longitude() {
    return (MIN_LONGITUDE + LONGITUDE_RANGE);
}

// --------------------------------------------------

/*! \brief Get the latitudes and longitudes centered at the point passed
 * \param a_latitude the latitude of the center of the region
 * \param a_longitude the longitude of the center of the region
 * \param a_lat_hw half the width in degrees latitude of the region
 * \param a_lon_hw half the width in degrees longitude of the region
 * \returns an array of 4 doubles in this order: top latitude,
 * bottom latitude, left longitude, right longitude
 */
function get_region($a_latitude, $a_longitude, $a_lat_hw, $a_lon_hw) {

    // the array for the results
    $l_array = array(0.0, 0.0, 0.0, 0.0);

    // top latitude
    $l_array[0] = $a_latitude - $a_lat_hw;
    if ($l_array[0] < MIN_LATITUDE) $l_array[0] = 2*MIN_LATITUDE - $l_array[0];

    // bottom latitude
    $l_array[1] = $a_latitude + $a_lat_hw;
    if ($l_array[1] > max_latitude()) $l_array[1] = 2*max_latitude() - $l_array[1];

    // left longitude
    $l_array[2] = $a_longitude - $a_lon_hw;
    if ($l_array[2] < MIN_LONGITUDE) $l_array[2] += LONGITUDE_RANGE;

    // right longitude
    $l_array[3] = $a_longitude + $a_lon_hw;
    if ($l_array[3] > max_longitude()) $l_array[3] -= LONGITUDE_RANGE;
     
    return $l_array;
}

?>