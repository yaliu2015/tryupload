<?php

/*! \namespace MyDB 
 *  \brief Basic functionality to access database
 *  \author Raj Appama
 *  \date \htmlonly &copy \endhtmlonly 2015
 */

namespace MyDB;

// ================ CONSTANTS ===================

define("DBHost", "127.0.0.1");
define("DBUser", "websysS15GB7");
define("DBPassword", "websysS15GB7!!");
define("DBName", "websysS15GB7");

// ============= GLOBAL OBJECTS =================

//! This is going to be the connection that we will use
$g_connection = NULL;

// ================ FUNCTIONS ===================

/*! \brief Checks the connection status
 * \return TRUE if connected
 */
function is_connected() {
    GLOBAL $g_connection;
    return isset($g_connection) && $g_connection->ping();
}

// ----------------------------------------------

/*! \brief Open up a connection to the database if not connected
 * \return The connection object
 */
function &open() {
    GLOBAL $g_connection;
    if (!is_connected()) {
        $g_connection = new \mysqli(DBHost, DBUser, DBPassword, DBName);
    }
    return $g_connection;
}

// ----------------------------------------------

/*! \brief get the last connection error 
 * \return error, if any, during last connection
 */
function last_connect_error() {
    GLOBAL $g_connection;
    if ($g_connection->connect_errno) {
        return "Failed to connect to database: "
            . $g_connection->connect_errno . " "
            . $g_connection->connect_error;
    }
    return "";
}

// ----------------------------------------------

/*! \brief get the last error 
 * \return error, if any, during last query
 */
function last_error() {
    GLOBAL $g_connection;
    return $g_connection->error;
}

?>
