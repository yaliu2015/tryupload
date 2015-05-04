var g_base = "http://websys3.stern.nyu.edu/~websysS15GB7/bumblr/";
    //"http://192.168.103.238/~Raj/bumbleapp/";
    //"http://172.16.192.223/~Raj/bumbleapp/";
    //"http://172.27.221.246/~Raj/bumbleapp/";
//

var g_js_base = g_base + "js/";
var g_css_base = g_base + "css/";

// ================ CSS ============

$('head').append('<link rel="stylesheet" type="text/css" href="'
		 + g_css_base + 'bumblr.css">');

// ================ Scripts =============

$('head').append("<script src=\"" + g_js_base + "core.js\"></script>");
$('head').append("<script src=\"" + g_js_base + "templates.js\"></script>");
$('head').append("<script src=\"" + g_js_base + "linkr.js\"></script>");
$('head').append("<script src=\"" + g_js_base + "comments.js\"></script>");

// keep last. This will load the first page
$('head').append("<script src=\"" + g_js_base + "pages.js\"></script>");
