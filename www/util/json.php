<?php

/*! \namespace MyJSON
 * \brief This script helps does all the JSON conversion and dispatching
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */

namespace MyJSON;

const ERR_TEXT = "error";
const RESULT_TEXT = "results";

// ------------------------------------------------------------

/*! \brief Sends a JSON message with just an error
 */
function sendError($a_text) {
    sendJSON(JSONMessageWithError($a_text));
}

// ------------------------------------------------------------

/*! \brief Creates the typical message
 * \author Raj Appama
 * \param a_text [optional] The text describing the error if any
 * \details The structure of the message is always the same:
 * {"error":"(error text)", "results":[ ,, ,]}
 */
function JSONMessageWithError($a_text = "") {
    $l_map = array();
    $l_map[ERR_TEXT] = (empty($a_text) ? NULL : $a_text);
    $l_map[RESULT_TEXT] = array();
    return $l_map;
}

// ------------------------------------------------------------

/*! \brief "Sends" an HTTP JSON response
 * The structure of the JSON is as above
 */
function sendJSON($a_obj) {
    header('Content-type: application/json'); 
    echo json_encode($a_obj);
}

?>
