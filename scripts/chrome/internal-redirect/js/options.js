chrome.webRequest.onBeforeRequest.addListener(function(details) {

    var addParam = function (url, param) {
        if (! url.toString().match(param)) {
            url += (url.split('?')[1] ? '&':'?') + param;
        }
        return url;
    }

    var url = details.url;
    var domains = [
        "grooveshark",
        "google.com",
        "afip.gov",
        "ggpht.com",
        "9gag.com",
        "youtube.com",
        "googleapis.com",
        "prx.im",
        "hideoxy.com",
        "zalmos.com"
    ];
    var regex = '^(https|http)://(192\.|.*'+ domains.join('|.*') +')';
    if (url.toString().match(new RegExp(regex))) {
        return;
    }

    url = addParam(url, 'utm_campaign=PARAM');
    url = addParam(url, 'utm_source=PARAM');
    return {
        redirectUrl: url
    };
}, { urls: [] }, ["blocking"]);
