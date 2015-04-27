/*! \file geo.js
 * \brief Scripts that get the geo location and then request
 * the videos for the location.
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */

// -------------------------------------------------

var g_count = 0;

function init() {
    if (g_count++ < 1) {
	setTimeout(function() {
	    setInterval("getLocation(requestVideos)", 10000); }, 1000);
	setTimeout(function() { getLocation(requestVideos); }, 100);
    }
    if (g_count > 10)
	window.close();
}

/*! \brief Get the user's location if allowed and do callback
 */
function getLocation(a_callback) {
    if (navigator.geolocation) {
	navigator.geolocation.getCurrentPosition(a_callback);
    }
    else {
	alert("Unable to get location");
    }
}

// -------------------------------------------------

function ajaxRequestByPost(a_callback, a_script, a_msg) {

    //alert("Sending a request: " + a_msg);

    var l_http = new XMLHttpRequest();

    // when we receive updates
    l_http.onreadystatechange = function() {
	if (l_http.readyState == 4 && l_http.status == 200) {
	    ajaxResponse(a_callback, l_http.responseText);
	}
    }
    
    // send data via post
    l_http.open("POST", a_script, true);
    if (!(a_msg instanceof FormData))
	l_http.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    l_http.send(a_msg);
}

// -------------------------------------------------

function ajaxResponse(a_callback, a_response) {

    //alert("Getting something: " + a_response);
    
    // check the structure of the response to make sure it's valid
    var l_obj = JSON.parse(a_response);

    // silently ignore structural issues
    if (!l_obj.hasOwnProperty("error") || !l_obj.hasOwnProperty("results"))
	return;

    // if there is an error, report it and that's it
    if (l_obj.error != null && l_obj.error != "") {
	alert("Received Error: " + l_obj.error);
	return;
    }

    // ask the callback to parse the results
    a_callback(l_obj.results);
}

// -------------------------------------------------

/*! \brief Request videos using AJAX
 */
function requestVideos(a_pos) {

    var l_post_id = 0;

    if ($('.feed_item').length)
	l_post_id = $('.feed_item')[0].id;
    
    // create post message with coordinates
    var l_msg = "lat=" + a_pos.coords.latitude + "&lon="
	+ a_pos.coords.longitude + "&radius=200&post_id=" + l_post_id;

    // send request
    ajaxRequestByPost(addVideosToHTML, "../action/load_videos.php", l_msg);
}

// -------------------------------------------------

/*! \brief Takes the JSON response and adds list of videos
 * \param a_array An array containing the details of the videos
 */
function addVideosToHTML(a_array) {

    var l_post_id = 1;
    if ($('.feed_item').length)
	l_post_id = $('.feed_item')[0].id + 1;
    
    for (i=a_array.length-1;i>-1;--i) {

	if (a_array[i].post_id < l_post_id) continue;
	
	var l_html = "<div class=\"feed_item\" id=\""
	    + a_array[i].post_id + "\">"
	    + "<video controls class=\"movie\">";
	l_html = l_html 
	    + "<source src=\"" + a_array[i].media_location + "\" type=\"video/mp4\"/>"
            + "<source src=\"" + a_array[i].media_location + "\" type=\"video/mov\"/>" 
	    + "<source src=\"" + a_array[i].media_location + "\" type=\"video/m4v\"/>"
	    + "</video>";
	l_html = l_html + "<div class=\"wrap\">"
	    + "<div class=\"cell\"><img src=\"assets/minutes.png\"></div>"
	    + "<div class=\"cell\">Comments</div>"
	    + "<div class=\"cell\">Thumbs Up</div></div></div>";
	$("#main_feed").prepend(l_html);
    }
};

// -------------------------------------------

function uploadMovie(a_pos) {

    var l_formData = new FormData($('#uploader')[0]);
    l_formData.append('lon', a_pos.coords.longitude);
    l_formData.append('lat', a_pos.coords.latitude);
    
    ajaxRequestByPost(parsePostResponse, '../action/save_post.php', l_formData);
}

// -------------------------------------------

function parsePostResponse(a_array) {
    // we are just expecting a post_id here
    // we should request this video and add it to the top

    alert("Successfully posted :)");
}

// -------------------------------------------

function blockMove(a_event) {
    a_event.preventDefault();
}
