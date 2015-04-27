<?php

/*! \namespace Bumblrs
 *  \brief All action related directly to the users table in the DB
 *  \date \htmlonly &copy \endhtmlonly 2015
 */

namespace Bumblrs;

require_once 'connect.php';
require_once 'tables.php';

// ------------------------------------------------------------

/*! \brief Adds a user with a session id
 * \param a_session The sesssion id that identifies this session
 * \return TRUE if successful
 */
function add_user($a_session) {
    
    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected()) return FALSE;

    // Query
    $l_db->query("INSERT INTO " . DBTableUsers
                 . " (" . DBUserSession . ") VALUES ('$a_session')");

    // Check for errors
    if ($l_db->error) return FALSE;

    return TRUE;
}


// ------------------------------------------------------------

/*! \brief Deletes the user with a session id
 * \param a_session The sesssion id that identifies this session
 * \return TRUE if successful
 */
function delete_user($a_session) {
    
    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected()) return FALSE;

    // query
    $l_db->query("DELETE FROM " . DBTableUsers
                 . " WHERE " . DBUserSession . "='$a_session'");

    // error?
    if ($l_db->error) return FALSE;

    return TRUE;
}

?>