<?php

/*! \brief All tables and columns that are needed by the scripts are defined here
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */

// Tables
define("DBTablePosts", "posts");
define("DBTableComments", "comments");
define("DBTableUsers", "users");

// Users
define("DBUserSession", "session");
define("DBUserScore", "score");

// Columns for posts
define("DBPostID", "post_id");
define("DBPostLat", "lat");
define("DBPostLon", "lon");
define("DBPostTitle", "title");
define("DBPostSession", "session");
define("DBPostFileURL", "media_location");
define("DBPostVotes", "votes");
define("DBPostTime", "ts");
define("DBPostNumCom", "num_comments");

// Columns for comments
define("DBComPostId", "post_id");
define("DBComVotes", "votes");
define("DBComText", "text");
define("DBComRank", "rank");

?>