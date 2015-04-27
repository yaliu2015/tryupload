<?php
session_start();
?>
<html><body>
    <form action="../action/save_post.php" method="post" name="Post Video" enctype="multipart/form-data">
      <input type="number" name="lon" min="-180" max="180"/>
      <input type="number" name="lat" min="-90" max="90"/>
      <input type="text" name="title" pattern="[A-Za-z0-9 _~!@#$%^&*()_+=-<>,?:;\[\]{}]{0-63}"/>
      <input type="file" name="fileToUpload" id="fileToUpload" accept="video/mp4,video/mov,video/m4v">
      <input type="submit" value="Next"/>
    </form>
    
</body></html>
