
var baseUrl = '/js/';

/**
 * extends an object with a mixin.
 * @param obj object to be extended
 * @param extension extension object's name
 * @param noAutoload if noAutoload is set to false and there is no extension object in current context it will try to load id.
 * @return {Boolean}
 */
function extend(obj, extension, noAutoload)
{
	if(!this[extension]) {
		if(!noAutoload) {
			loadFile(extension+'.js', function(){
				extend(obj, extension, true);
			})
		}
		return false;
	}
	this[extension].call(obj);
	return true;
}

function strpos( haystack, needle, offset){ // Find position of first occurrence of a string
   var i = haystack.indexOf( needle, offset ); // returns -1
   return i >= 0 ? i : false;
}


var loadFile = function (url, callback)
{
	var script = document.createElement("script");
	script.type = "text/javascript";
	if (script.readyState) {  //IE
		script.onreadystatechange = function ()
		{
			if (script.readyState == "loaded" ||
				script.readyState == "complete") {
				script.onreadystatechange = null;
				callback();
			}
		};
	} else {  //Others
		script.onload = function ()
		{
			callback();
		};
	}
	script.src = baseUrl + url;
	document.getElementsByTagName("head")[0].appendChild(script);
}


