<?php

/*! \file validators.php
 * \brief This script implements classes that can be used to filter inputs
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */

// -------------------------------------------

require_once 'json.php';

// -------------------------------------------

function mandatory($a_array) {
    $l_err = NULL;
    foreach ($a_array as $l_arg) {
        if (!isset($_POST[$l_arg]))
            $l_err = $l_err . " " . $l_arg;
    }
    if (!empty($l_err)) {
        \MyJSON\sendError("Missing: " . $l_err);
        die();
    }
}

function mandatory_file($a_file) {
    if (!isset($_FILES[$a_file])) {
        \MyJSON\sendError("Missing file: " . $a_file);
        die();
    }
}

// -------------------------------------------

/*! \class LongitudeValidator
 * \brief Validator to take an input and check if the value is acceptable
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */
class LongitudeValidator {

    const m_min = -180;
    const m_max = 180;
    const m_err_val = 360;
    
    public function longitude($a_value) {
        $l_longi = filter_var($a_value, FILTER_VALIDATE_FLOAT,
                              array('options' => array('default'=>self::m_err_val),
                                    'flags' => FILTER_FLAG_ALLOW_FRACTION));
        if ($l_longi === $m_err_val ||
            $l_longi < self::m_min ||
            $l_longi > self::m_max) {
            \MyJSON\sendError("Invalid Longitude");
            die();
        }
        return $l_longi;
    }
}

// -------------------------------------------

/*! \class LatitudeValidator
 * \brief Validator to take an input and check if the value is acceptable
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */
class LatitudeValidator {

    const m_min = -90;
    const m_max = 90;
    const m_err_val = 180;
    
    public function latitude($a_value) {
        $l_lati = filter_var($a_value, FILTER_VALIDATE_FLOAT,
                             array('options' => array('default'=>self::m_err_val),
                                    'flags' => FILTER_FLAG_ALLOW_FRACTION));
        if ($l_lati === $m_err_val ||
            $l_lati < self::m_min ||
            $l_lati > self::m_max) {
            \MyJSON\sendError("Invalid Latitude");
            die();
        }
        return $l_lati;
    }
}

// -------------------------------------------

/*! \class RadiusValidator
 * \brief Validator to take the radius and check if the value is acceptable
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */
class RadiusValidator {

    const m_min = 0.1;
    const m_max = 500;
    const m_err_val = 0;
    
    public function radius($a_value) {
        $l_rad = filter_var($a_value, FILTER_VALIDATE_FLOAT,
                            array('options' => array('default'=>self::m_err_val),
                                    'flags' => FILTER_FLAG_ALLOW_FRACTION));
        if ($l_rad === $m_err_val ||
            $l_rad < self::m_min ||
            $l_rad > self::m_max) {
            \MyJSON\sendError("Invalid Radius");
            die();
        }
        return $l_rad;
    }
}

// -------------------------------------------

/*! \class TitleValidator
 * \brief Validator to take the title and check if the value is acceptable
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */
class TitleValidator {
    
    public function title($a_value) {
        $l_title = filter_var($a_value, FILTER_VALIDATE_REGEXP,
                              array("options"=>array("regexp"=>"/^[A-Za-z0-9_ !.@#$%^&*+=-~`<>?,|{}\[\]:;\"\']+$/")));
        if ($l_title === FALSE) {
            \MyJSON\sendError("Invalid Title");
            die();
        }
        return $l_title;
    }
}

?>
