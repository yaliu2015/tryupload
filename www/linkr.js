/*! \file linkr.js
 * \brief Scripts that get the geo location and then request
 * the videos for the location.
 * \author Raj Appama
 * \date \htmlonly &copy \endhtmlonly 2015
 */

// -------------------------------------------------

var g_count = 0;

var g_base = "http://websys3.stern.nyu.edu/~websysS15GB7/bumblr";

function init() {
    if (g_count++ < 1) {
	setTimeout(function() {
	    setInterval("getLocation(requestVideos)", 10000); }, 1000);
	setTimeout(function() { getLocation(requestVideos); }, 100);
    }
    if (g_count > 10)
	window.close();
}

// -------------------------------------------------

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
    ajaxRequestByPost(addVideosToHTML, g_base + "/action/load_videos.php", l_msg);
}

// -------------------------------------------------

/*! \brief Request videos using AJAX
 */
function uploadMovie(a_pos) {

    var l_formData = new FormData($('#uploader')[0]);
    l_formData.append('lon', a_pos.coords.longitude);
    l_formData.append('lat', a_pos.coords.latitude);
    
    ajaxRequestByPost(parsePostResponse, g_base + "/action/save_post.php", l_formData);
}

// -------------------------------------------

/*! \brief Request videos using AJAX
 */
function parsePostResponse(a_array) {
    // we are just expecting a post_id here
    // we should request this video and add it to the top

    alert("Successfully posted :)");
}

// -------------------------------------------

/*! \brief Request videos using AJAX
 */
function blockMove(a_event) {
    a_event.preventDefault();
}

// -------------------------------------------

function postComment(a_event) {
    var l_id = a_event.target.id;
    l_id = l_id.replace('icb', '');
    var l_text = $('#ict' + l_id).val();
    if (!l_text || l_text == '') return;
    
    var l_msg='com_text=' + l_text + '&post_id=' + l_id;
    //alert(l_msg);
    ajaxRequestByPost(displayComment, g_base + "/action/save_comment.php", l_msg);
    $('#ict' + l_id).val('');
}

// -------------------------------------------

function voteComment(a_event) {
    var l_id = a_event.target.id;
    var l_array = l_id.split("-");
    var l_uv = l_array[0] == "d" ? -1 : 1;
    var l_params = 'user_vote=' + l_uv + '&comment_id=' + l_array[1];
    ajaxRequestByPost(updateCommentVote, g_base + "/action/vote_comment.php", l_params);
}

// ==============================================================
//                       HTML Edit Section
// ==============================================================

/*! \brief Takes the JSON response and adds list of videos
 * \param a_array An array containing the details of the videos
 */
function addVideosToHTML(a_array) {

    var l_post_id = 1;
    if ($('.feed_item').length)
	l_post_id = $('.feed_item')[0].id + 1;
    
    for (i=a_array.length-1;i>-1;--i) {

	if (a_array[i].post_id < l_post_id) continue;
	
	var l_html = "<div class='feed_item' id='" + a_array[i].post_id + "'>"
	    + HTMLforVideo(a_array[i])
	    + HTMLforTitle(a_array[i])
	    + HTMLforInteract(a_array[i].post_id)
	    + "</div><div class='separator'></div>";
	$("#main_feed").prepend(l_html);
    }

    $(".feed_item").append("<div class='feed_wrap Comment'></div>");

    for (i=0;i<a_array.length;++i)
	ajaxRequestByPost(displayComment, g_base + "/action/load_comment.php",
			  "post_id=" + a_array[i].post_id);
    
};

// -------------------------------------------

function updateCommentVote(a_array) {
    for (i=0;i<a_array.length;++i) {
	var l_id = a_array[i].comment_id;
	var l_uv = a_array[i].user_vote;
	var l_uptype = "textButton up";
	var l_downtype = "textButton down";
	if (l_uv > 0) l_uptype = "textButton upped";
	else if (l_uv < 0) l_downtype = "textButton downed";

	//alert("ID: " + l_id + " UV: " + l_uv + " UpType: " + l_uptype);
	
	// set the vote count
	$('#v-' + l_id).text(a_array[i].votes);

	// set the right color for the buttons
	$('#u-' + l_id).attr('class', l_uptype);
	$('#d-' + l_id).attr('class', l_downtype);
    }
}

// -------------------------------------------

function displayComment(a_array) {
    //alert('Got Comments: ' + a_array.length);
    
    for (i=0;i<a_array.length;++i) {
	var l_id = a_array[i].comment_id;	
	$("#" + a_array[i].post_id + " > .Comment")
	    .append(HTMLforComment(l_id, a_array[i].text));
    }

    updateCommentVote(a_array);
}

// -------------------------------------------------

function HTMLforComment(a_id, a_text) {
    return "<div class='wrap cell one' id='cw-" + a_id + "'>"
	+ "<div class='cell three'><p>" + a_text + "</p></div>"
	+ "<div class='cell one'><button class='textButton down'"
	+ " id='d-" + a_id + "' onclick='voteComment(event)'>Down</button></div>"
	+ "<div class='cell one'><h2 id='v-" + a_id + "'>0</h2></div>"
    	+ "<div class='cell one'><button class='textButton up' id='u-"
	+ a_id + "' onclick='voteComment(event)'>Up</button></div></div>";
}

// -------------------------------------------------

function HTMLforVideo(a_obj) {
    return "<video controls class='movie'>"
	+ "<source src='" + a_obj.media_location + "' type='video/mp4'/>"
        + "<source src='" + a_obj.media_location + "' type='video/mov'/>" 
	+ "<source src='" + a_obj.media_location + "' type='video/m4v'/>"
	+ "</video>";
}

// -------------------------------------------------

function HTMLforTitle(a_obj) {
    var l_title = "";
    if (a_obj.title != "") l_title = a_obj.title;
	
    return "<div class='wrap'>"
	+ "<div class='cell one'><img src='assets/minutes.png'></div>"
	+ "<div class='cell three'><h3>" + l_title + "</h3></div>"
	+ "<div class='cell one'><button class='textButton up'>Up</button></div>"
    	+ "<div class='cell one'><h2 id='vv-'" + a_obj.post_id + ">0</h2></div>"
	+ "<div class='cell one'><button class='textButton down'>Down</button></div>"
	+ "</div>";
}

// -------------------------------------------------

function HTMLforInteract(a_post_id) {
    return "<div class='wrap'>"
	+ "<div class='cell three'>"
	+ "<input type='text' autocomplete='on'"
	+ " id='ict" + a_post_id + "'/></div>"
	+ "<div class='cell one'><button class='textButton up' id='icb"
	+ a_post_id + "' onclick='postComment(event)'>Post</button></div></div>";
}
