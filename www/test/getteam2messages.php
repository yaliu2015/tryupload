<?php

/* require the user as the parameter */
if(isset($_GET['uid']) && intval($_GET['uid'])) {
  $user_id = intval($_GET['uid']); //no default
}

ELSE 

/* user_id 0 means to retrieve all users  */
/* it is the default  */
{$user_id = 0;}


  /* connect to the db */
  $link = mysql_connect('websys3.stern.nyu.edu','websysS15GB7','websysS15GB7!!') or die('Cannot connect to the DB');
  mysql_select_db('websysS15GB7',$link) or die('Cannot select the DB');

/* if is uid 0, the retrieve all users */


 $query = "SELECT * from messages";  


  $result = mysql_query($query,$link) or die('Errant query:  '.$query);

  /* create one master array of the records */
  /*  it should create an array called 'user' that has one or more rows depending on whether the uid was specified */


  if(mysql_num_rows($result)) {
    while($user = mysql_fetch_assoc($result)) {
      $users[] = array('user'=>$user);
                                              }
                              }

  /* output in necessary format */
 {
    header('Content-type: application/json'); 
    echo json_encode(array('users'=>$users));
 }

  /* disconnect from the db */
  @mysql_close($link);

?>
