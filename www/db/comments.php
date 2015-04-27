<?php

/*! \namespace Comments
 *  \brief All the functions related to comments
 *  \author Raj Appama
 *  \date \htmlonly &copy \endhtmlonly 2015
 */

// ===========================================================

namespace Comments;

// This will get all our connections to the database
require_once 'tables.php';
require_once 'connect.php';

// --------------------------------------------------

/*! \brief load the comments for a given post
 * \param a_post_id The post_id to which this comment belongs
 */
function comments_by_post($a_post_id) {

    // open connection to db
    $l_db = &\MyDB\open();

    // build the query
    $l_query = "SELECT * FROM " . DBTableComments . " WHERE "
             . DBComPostId . " = '$a_post_id' ORDER BY "
             . DBComRank . " ASC";

    // execute query
    $l_result = $l_db->query($l_query);

    // what to do with results
    if ($l_result->num_rows > 0) {
        // output data of each row
        while($l_row = $l_result->fetch_assoc()) {
            echo "post_id: " . $l_row["post_id"]. " Text: "
                             . $l_row["text"]. " Votes: "
                             . $l_row["votes"] . "<br/>";
        }
    }
}

// --------------------------------------------------

/*! \brief add comment to a post
 * \param a_post_id The post_id to which this comment belongs
 * \param a_text The text of the comment, aka, the actual comment
 * \param a_rank The rank that should go this post
 */
function add_for_post($a_post_id, $a_text, $a_rank) {

    // open connection to db
    $l_db = &\MyDB\open();

    // add the comment
    $l_db->query("INSERT INTO " . DBTableComments . " ("
                 . DBComPostId . ", " . DBComText . ", " . DBComRank
                 . ") VALUES ('$a_post_id', '$a_text', '$a_rank')");

    // check success
    if ($l_db->error) return FALSE;
    
    return TRUE;
}

// --------------------------------------------------

/*! \brief delete a comment given its post id and rank
 * \param a_post_id The post_id to which this comment belongs
 * \param a_rank The rank that should go this post
 */
function delete_for_post($a_post_id, $a_rank) {

    // open connection to db
    $l_db = &\MyDB\open();

    // did we succeed?
    if (!\MyDB\is_connected())
        return FALSE;

    // delete the comment
    $l_db->query("DELETE FROM " . DBTableComments . " WHERE "
                 . DBComPostId . "='$a_post_id' && "
                 . DBComRank . "='$a_rank'");

    // check error
    if ($l_db->error)
        return FALSE;
    
    return TRUE;
}

?>