// thanks to http://stackoverflow.com/questions/8567114/how-to-make-an-ajax-call-without-jquery

function GET(url, callback) {
    AJAX("GET", url, callback);
}

function POST(url, callback) {
    AJAX("POST", url, callback);
}


function AJAX(method, url, callback) {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
            if (xmlhttp.status == 200) {
                // return the object to the callback
                console.log("res |" + xmlhttp.responseText+"|");
                callback(JSON.parse(xmlhttp.responseText));
            }
            else
                console.log('xhrClient: There was an error ' + xmlhttp.status + ' ('+url+')')
            
        }
    }
    xmlhttp.open(method, url, true);
    xmlhttp.send();
}