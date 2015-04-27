<html><body>
<?php

/*! \brief This script will be used to test comment related actions
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */

require_once '../db/comments.php';
require_once '../db/posts.php';

add_comments(1000);
get_comments(100);
delete_comments(10);

// ----------------------------------
  
/*! \brief add comments to certain posts in the DB 
 * \param a_number The total number of comments to add across all posts
 */
function add_comments($a_number) {
    
    for ($i=1;$i<$a_number;++$i) {
        // pick a random post
        $l_id = mt_rand(1, 8000); // pick a random post
        $l_rank = num_of_comments($l_id); // how many comments it has
        //echo "Id: " . $l_id . " rank: " . $l_rank . " <br/>";

        // does post even exist?
        if ($l_rank > -1) {
            if (add_comment($l_id, "Comment number " . $l_rank, $l_rank)) {
                if (!set_num_of_comments($l_id, $l_rank + 1))
                    echo "Failed to set number of post comments: " . last_db_error();
            }
            else
                echo "Add Failed<br/>";
        }
    }
}

// ----------------------------------
  
  /*! \brief load comments across a number of posts
   * \param a_number The total number of posts to retrieve comments for
   */
function get_comments($a_number) {
    // get comments for a_number posts
    for ($j=0;$j<$a_number;++$j)
        load_comments_for_post(mt_rand(1, 8000));
}

// ----------------------------------
  
  /*! \brief delete a number of comments across different posts
   * \param a_number The total number of comments to delete
   */
function delete_comments($a_number) {
    for ($j=0;$j<$a_number;++$j) {
        // find a post
        $l_id = mt_rand(1, 8000);

        // get the num_comments
        $l_max_rank = num_of_comments($l_id);

        // pick a random one
        $l_rank = mt_rand(0, $l_max_rank);

        // try to remove it. This may legitimately fail since
        // record may not be there
        if (!delete_comment($l_id, $l_rank)) {
            echo "<p>Failed to delete $l_id: $l_rank</p>";
        }
    }
}
  
?>
   </body></html>