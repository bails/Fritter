function setMsg(qry) {
	var thePage = 'hoot.php?' + qry + '&d=' + Date().getTime();
	
	
	myReq.open("GET", thePage, true);
	myReq.onreadystatechange = HTTPMsgResponse;
	myReq.send(null);
}

function HTTPMsgResponse() {
	if (myReq.readyState == 4) {
		if(myReq.status == 200){
			var response = myReq.responseText;
			document.getElementById('msg').innerHTML = response;
		}
	}
}



//*********************************************************************************************
//*******************JS code for Pagination Effect*********************************************
//*********************************************************************************************
function OnDivScroll()
{  
  var el = document.getElementById('posts');
  if(el.scrollTop < el.scrollHeight - 800)
    return;
 
 
  var loading = document.getElementById('loadingDiv');
  if(loading.style.display == '')
    return; //already loading
   
  loading.style.display = '';
 
  LoadMoreElements();
}

function LoadMoreElements()
{
  //Do a server callback to load
  //more elements
 
  setTimeout(loadPosts, 350);
}

/* function LoadCallback()
{
  var el = document.getElementById('posts');
  var loading = document.getElementById('loadingDiv');
 
  loading.style.display = 'none';
 
  for(var i=1; i<=6; i++)
    el.innerHTML += "<div class='entry'>New Entry " + i
        + " of Set " + NumberOfNewEntrySets + "</div>";
  NumberOfNewEntrySets++;
} */



function loadPosts() {
	var loading = document.getElementById('loadingDiv');
	var thePage = 'posts.php?';
	
	
	myReq.open("GET", thePage, true);
	myReq.onreadystatechange = loadHTTPResponse;
	myReq.send(null);
	loading.style.display = 'none';
}

function loadHTTPResponse() {
	if (myReq.readyState == 4) {
		if(myReq.status == 200){
			var response = myReq.responseText;
			document.getElementById('posts').innerHTML += response;
		}
	}
}


//*********************************************************************************************
//*************JS code for AJAX and Fading Effects*********************************************
//*********************************************************************************************

function getXMLHTTPRequest() {
	var req = false;
	try{
		/* firefox */
		req = new XMLHttpRequest();
	} catch (err) {
		try {
			/* some versions of IE*/
			req = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (err) {
			try {
				/*Some other versions of IE*/
				req = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (err) {
				req = false;
			}
		}
	}
	return req;
}



function getPosts(querystring) {
	var thePage = 'posts.php?' + querystring;
	myReq.open("GET", thePage, true);
	myReq.onreadystatechange = theHTTPResponse;
	fadeIn();
	myReq.send(null);
}

function theHTTPResponse() {
	if (myReq.readyState == 4) {
		if(myReq.status == 200){
			var response = myReq.responseText;
			document.getElementById('posts').innerHTML = response;
		}
	} 
}

function setOpacity(level) {
	document.getElementById('posts').style.opacity = level;
	document.getElementById('posts').style.MozOpacity = level;
	document.getElementById('posts').style.KhtmlOpacity = level;
	document.getElementById('posts').style.filter = "alpha(opacity=" + (level * 100) + ");";
	}

	function fadeIn(){
	  for (i = 0; i <= 1; i += (1 / 100)) {
	    setTimeout("setOpacity(" + i + ")", i * 500);
	  }
	  
	}
	
function toggleOn(obj) {
	var hoot = document.getElementById('hoot_'+obj);
	//if ( lol.style.visibility == 'visible' ) {
		//lol.style.visibility = 'hidden';
	//}
	//else {
		hoot.style.visibility = 'visible';
	//}
}

function toggleOff(obj) {
	var hoot = document.getElementById('hoot_'+obj);
	//if ( lol.style.visibility == 'visible' ) {
		hoot.style.visibility = 'hidden';
	//}
	//else {
		//lol.style.visibility = 'visible';
	//}
}
