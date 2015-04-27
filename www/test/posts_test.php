<html><body>
<?php

/*! \brief This script will be used to test posts related actions
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */

require_once '../db/posts.php';
  
delete_posts(100);
  
/*! \brief add a number of posts in the DB 
 * \param a_number The total number of posts to add
 */
function add_posts($a_number) {
    // get the total number of movies in db
    $l_posts = num_of_posts();
    
    for ($i=$l_posts+1;$i<=$l_posts+$a_number;++$i) {
        $l_title = "Title number " . $i;
        $l_loc = "Location /the/magical/location/movie" . $i;
        $l_lat = mt_rand(-90,90);
        $l_lon = mt_rand(-180,180);

        if (post_video($l_lat, $l_lon, $l_title, $l_loc) < 1) {
            echo "Could not post video number " . $i;
        }
    }
};

function delete_posts($a_number) {

    // get the total number of movies in db
    $l_posts = num_of_posts();

    for ($i=1;$i<=$a_number;++$i) {
        $l_id = mt_rand(1,$l_posts);

        if (!delete_post_by_id($l_id)) {
            echo "Could not delete post id: " . $l_id;
        }
    }
}

?>
</body></html>
