<?php

/*! \namespace Buzzs 
 *  \brief All the functions related to a post
 *  \author Raj Appama
 *  \date \htmlonly &copy \endhtmlonly 2015
 */

namespace Buzzs;

// ===========================================================

// This will get all our connections to the database
require_once 'connect.php';
require_once 'tables.php';
require_once '../util/geo.php';
require_once '../util/json.php';

// ------------------------------------------------------------

/*! \brief delete a post with certain id
 * \param a_post_id The post id of the post to delete
 * \return TRUE if successful
 */
function delete_post_by_id($a_id) {
    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected()) return FALSE;

    // build the query
    $l_query = "DELETE FROM " . DBTablePosts . " WHERE " . DBPostID
             . "='$a_id'";

    if ($l_db->query($l_query)) return TRUE;

    return FALSE;
}

// ------------------------------------------------------------

/*! \brief loads recent videos within a geofence
 * \param a_latitude the latitude of the device currently
 * \param a_longitude the longitude of the device currently
 * \param a_lat_hw half the region width in degrees of latitude
 * \param a_lon_hw half the region width in degrees of longitude
 * \param a_post_id Will return all videos ABOVE this post_id
 * \returns a JSON structure returning the path of the videos
 * as well as the associated comments and votes
 */
function load_nearby_videos($a_latitude, $a_longitude,
                            $a_lat_hw, $a_lon_hw, $a_post_id=0) {

    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected()) {
        \MyJSON\sendError("Not connected!");
        return;
    }
    
    // get the geo fence
    $l_geos = get_subqueries_for_location($a_latitude,
                                          $a_longitude, $a_lat_hw, $a_lon_hw);

    // build the queries
    $l_base = "SELECT * FROM " . DBTablePosts . " WHERE "
            . DBPostID . " > '$a_post_id' && ";
    // sort by timestamp then votes
    $l_postfix = " ORDER BY " . DBPostTime . " DESC, " . DBPostVotes . " DESC";

    // in most cases there will be only one element in the array
    $l_json_res = NULL;
    foreach ($l_geos as $l_val) {
        $l_query = $l_base . $l_val . $l_postfix;

        $l_result = $l_db->query($l_query);

        if ($l_result->num_rows > 0) {
            if (!isset($l_json_res))
                $l_json_res = \MyJSON\JSONMessageWithError();
            // output data of each row
            while($l_row = $l_result->fetch_assoc()) {
                $l_json_res[\MyJSON\RESULT_TEXT][] = $l_row;
            }
        }
    }

    // send JSON
    if (isset($l_json_res))
        \MyJSON\sendJSON($l_json_res);
}

// ------------------------------------------------------------

/*! \brief get the correct grid for the location
 * \param a_latitude the latitude of the device currently
 * \param a_longitude the longitude of the device currently
 * \param a_lat_hw half the region width in degrees of latitude
 * \param a_lon_hw half the region width in degrees of longitude
 * \returns an array of the partial queries
 * \details Latitude and longitudes are very different. Longitudes
 * loop around while latitudes don't. Latitudes 'reflect' at the edges.
 */
function get_subqueries_for_location($a_latitude,
                                     $a_longitude, $a_lat_hw, $a_lon_hw) {

    $l_bounds = get_region($a_latitude, $a_longitude, $a_lat_hw, $a_lon_hw);

    // we need an array as we may need to break the
    // region into subsets, even though they are
    // physically contiguous
    $l_array = array();

    // international date line area
    if ($l_bounds[2] > $l_bounds[3]) {
        array_push($l_array, DBPostLon . " > '$l_bounds[2]'");
        array_push($l_array, DBPostLon . " < '$l_bounds[3]'");
    }
    else {
        array_push($l_array, DBPostLon . " > '$l_bounds[2]' && "
                   . DBPostLon . " < '$l_bounds[3]'");
    }

    // polar regions
    if (abs($l_bounds[0] - $l_bounds[1]) < 2 * $a_lat_hw - 0.00001) {
        foreach ($l_array as &$l_val) {
            $l_limit = ($l_bounds[0] > 0 ?
                        min($l_bounds[0], $l_bounds[1]) :
                        max($l_bounds[0], $l_bounds[1]));
            $l_val = $l_val . " && " . DBPostLat .
                   ($l_bounds[0] > 0 ? " > " : " < ") . "'$l_limit'";
        }
    }
    else {
        foreach ($l_array as &$l_val) {
            $l_val = $l_val . " && "
                   . DBPostLat . " > '$l_bounds[0]' && "
                   . DBPostLat . " < '$l_bounds[1]'";
        }
    }

    /*foreach ($l_array as $l_value) {
        echo "<h3>" . $l_value . "</h3>";
        }*/
        
    return $l_array;
}

// ------------------------------------------------------------

/*! \brief save a new post to the database 
 * \param a_latitude the latitude of the device posting
 * \param a_longitude the longitude of the device posting
 * \param a_title the title of the video
 * \param a_file_location the url of the video
 * \param a_session a token that identifies this session
 * \return returns the post_id if successful, -1 otherwise
 */
function post_video($a_latitude, $a_longitude,
                    $a_title, $a_file_location, $a_session = "") {

    // create query string
    $l_query = "INSERT INTO " . DBTablePosts . " (" . DBPostLat . ", "
             . DBPostLon . ", " . DBPostTitle . ", " . DBPostFileURL;
    $l_values = " VALUES ('$a_latitude', '$a_longitude', '$a_title', '$a_file_location'";

    // do we have a session?
    if (!empty($a_session)) {
        $l_query = $l_query . ", " . DBPostSession;
        $l_values = $l_values . ", '$a_session'";
    }

    // complete the query
    $l_query = $l_query . ")" . $l_values . ")";

    // execute query
    $l_db = &\MyDB\open();
    
    if ($l_db->query($l_query)) {

        /*! \todo We need a way to push this video to all
         * live connections in this zone */

        // for now send JSON with post_id to poster
        $l_res = \MyJSON\JSONMessageWithError();
        $l_res[\MyJSON\RESULT_TEXT][] = array();
        $l_res[\MyJSON\RESULT_TEXT][0][DBPostID] = $l_db->insert_id;

        \MyJSON\sendJSON($l_res);
        
        return $l_db->insert_id;
    }
    else {
        \MyJSON\sendError("Could not write to DB: " . $l_db->error);
    }
    
    return -1;
}

// ------------------------------------------------------------

/*! \brief Gets the number of comments for a post
 * \param a_post_id The post_id of the post
 * \return the number of comments, -1 if post does not exist
 */
function num_of_comments($a_post_id) {
    
    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected())
        return -1;

    // check whether this post exist and get number of comments
    $l_query = "SELECT " . DBPostNumCom . " FROM "
                  . DBTablePosts . " WHERE " . DBPostID
                  . " = '$a_post_id'";
    
    // execute query
    $l_result = $l_db->query($l_query);

    // we have a post
    if ($l_result->num_rows > 0) {
        $l_row = $l_result->fetch_assoc();
        return $l_row[DBPostNumCom];
    }

    return -1;
}

// ------------------------------------------------------------

/*! \brief Sets the number of comments for a post
 * \param a_post_id The post_id of the post
 * \param a_num The number of comments to set it to
 * \return TRUE if successful, FALSE otherwise
 */
function set_num_of_comments($a_post_id, $a_num) {
    
    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected())
        return FALSE;

    // check whether this post exist and get number of comments
    $l_query = "UPDATE " . DBTablePosts
             . " SET " . DBPostNumCom . "='$a_num'"
             . " WHERE " . DBPostID . "='$a_post_id'";

    // execute query
    if ($l_db->query($l_query))
        return TRUE;

    return FALSE;
}

// ------------------------------------------------------------

/*! \brief Counts number of posts in DB
 * \return The number of posts
 */
function num_of_posts() {
    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected())
        return FALSE;

    $l_query = "SELECT COUNT(*) AS Count FROM " . DBTablePosts;

    $l_result = $l_db->query($l_query);
    
    // we have a post
    if ($l_result->num_rows > 0) {
        $l_row = $l_result->fetch_assoc();
        return $l_row['Count'];
    }
    
    return 0;
}

?>