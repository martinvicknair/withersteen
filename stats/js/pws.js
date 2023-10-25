var js_agent = "agent";

// Chromium based browsers
if ( navigator.userAgent.includes("Chrome") && !navigator.userAgent.includes("Samsung"))
 {
  navigator.userAgentData.getHighEntropyValues(["platformVersion"],["architecture"],["bitness"],["model"],["fullVersionList"]).then(ua => {
    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "SCRIPT_DOMAIN/SCRIPT_PATHpws.php?js_bos=" + btoa(JSON.stringify(ua, null, "  "), true) + "&js_resolution=" + screen.width + "x" + screen.height + "&js_agent=" + js_agent + "&js_color=" + screen.colorDepth + "&js_referer=" + document.referrer + "&s=" + window.location.pathname + "&js_url=" + escape(document.URL));
    xhttp.send();
  });
 }
else
 {  
  jsinfo = "SCRIPT_DOMAIN/SCRIPT_PATHpws.php?js_resolution=" + screen.width + "x" + screen.height + "&js_agent=" + js_agent + "&js_color=" + screen.colorDepth + "&js_referer=" + document.referrer + "&s=" + window.location.pathname + "&js_url=" + escape(document.URL);
  
  try {
    var script = document.createElementNS('http://www.w3.org/1999/xhtml','script'); 
    script.setAttribute('type', 'text/javascript'); 
    script.setAttribute('src', jsinfo); 
    document.getElementsByTagName('body')[0].appendChild(script); 
  } 
  catch(e) { 
    str = '<script type="text/javascript" src="'+jsinfo+'"></script>';
    document.write(str+"");
  }
 }