/*
 overlibmws_modal.js plug-in module - Copyright Foteos Macrides and Seth Banks
   For support of the MODAL feature.
   Initial: November 15, 2006 - Last Revised: November 16, 2006
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

OLloaded=0;
var OLmodalCmds='modal';
OLregCmds(OLmodalCmds);

// DEFAULT CONFIGURATION
if(OLud('modal'))var ol_modal=0;
// END CONFIGURATION

var o3_modal=0;

function OLloadModal(){
OLload(OLmodalCmds);
}

function OLparseModal(pf,i,ar){
var k=i,t=OLtoggle;
if(k<ar.length){
if(Math.abs(ar[k])==MODAL){t(ar[k],pf+'modal');return k;}}
return -1;
}

var OLmMask=null,OLmMaskIsShown=false,
OLmRoot=(document.compatMode&&document.compatMode=='BackCompat')?'body':'html',
OLmGotSc=false,OLmScLeft=0,OLmScTop=0,OLmKDH=null,OLmTabIndexes=new Array(),
OLmTabbableTags=new Array("a","button","textarea","input","iframe");	

function OLchkModal(){
if(o3_modal){if(o3_sticky)OLmInitMask();else o3_modal=0;}
}

function OLclearModal(){
if(OLmMaskIsShown)OLmHideMask();
}

function OLmInitMask(){
if(!OLmMask){OLmMask=OLmkLyr('modalMask',self,'200');var o=OLmMask.style;
o.display='none';o.top='0px';o.left='0px';o.width='100%';o.height='100%';
o.visibility='visible';o.backgroundColor='#bbbbbb';
if(OLie4&&!OLieM&&typeof o.filter=='string'){o.filter='Alpha(opacity=40)';
if(OLie55)OLmMask.filters.alpha.enabled=1;}else{
if(typeof o.opacity!='undefined')o.opacity=0.4;
else if(typeof o.MozOpacity!='undefined')o.MozOpacity=0.4;
else if(typeof o.KhtmlOpacity!='undefined')o.KhtmlOpacity=0.4;}}
OLmAddEvent(window,"resize",OLmHandleMask);OLmShowMask();
}

function OLmShowMask(){
OLmMaskIsShown=true;if(!OLie4||OLop7){
OLmKDH=document.onkeypress?document.onkeypress.toString():null;
document.onkeypress=OLmKeyDownHandler;}else OLmDisableTabIndexes();
OLmMask.style.display="block";OLmHandleMask();OLmSetMaskSize();
if(OLie4&&!OLie7&&!OLop7)OLmHideSelectBoxes();
}

function OLmHandleMask(){
var scLeft=0,scTop=0;if(OLmMaskIsShown){if(!OLmGotSc){
OLmScLeft=parseInt((OLie4&&!OLop7?OLfd(self).scrollLeft:self.pageXOffset),10);
OLmScTop=parseInt((OLie4&&!OLop7?OLfd(self).scrollTop:self.pageYOffset),10);OLmGotSc=true;}
var root=document.getElementsByTagName(OLmRoot)[0];
if(root.style.overflow!='hidden')root.style.overflow='hidden';
var scLeft=parseInt((OLie4&&!OLop7?OLfd(self).scrollLeft:self.pageXOffset),10),
scTop=parseInt((OLie4&&!OLop7?OLfd(self).scrollTop:self.pageYOffset),10),o=OLmMask.style;
o.top=scTop+"px";o.left=scLeft+"px";o.top=scTop+"px";o.left=scLeft+"px";OLmSetMaskSize();}
}

function OLmSetMaskSize(){
var root=document.getElementsByTagName(OLmRoot)[0],mHt,fullHt=OLmGetViewportHeight(),
fullWd=OLmGetViewportWidth();if(fullHt>root.scrollHeight)mHt=fullHt;
else mHt=root.scrollHeight;OLmMask.style.height=mHt+'px';
OLmMask.style.width=root.scrollWidth+'px';
}

function OLmHideMask(){
OLmMaskIsShown=false;var root=document.getElementsByTagName(OLmRoot)[0];
root.style.overflow=(OLop7?'auto':'');if(!OLie4||OLop7){document.onkeypress=OLmKDH;
OLmKDH=null;}else OLmRestoreTabIndexes();if(OLie4&&!OLie7&&!OLop7)OLmShowSelectBoxes();
OLmRemoveEvent(window,"resize",OLmHandleMask);if(self.scrollTo&&OLmGotSc){
self.scrollTo(OLmScLeft,OLmScTop);OLmGotSc=false;}
if(OLmMask)OLmMask.style.display='none';
}

function OLmKeyDownHandler(e) {
if(OLmMaskIsShown&&e.keyCode==9)return false;
}

function OLmAddEvent(obj,evType,fn){
if(obj.addEventListener){obj.addEventListener(evType,fn,false);return true;}
else if(obj.attachEvent){var r=obj.attachEvent("on"+evType,fn);return r;}
else return false;
}

function OLmRemoveEvent(obj,evType,fn){
if(obj.removeEventListener){obj.removeEventListener(evType,fn,false);return true;}
else if(obj.detachEvent){var r=obj.detachEvent("on"+evType,fn);return r;}
else return false;
}

function OLmGetViewportHeight(){
if(window.innerHeight!=window.undefined)return window.innerHeight;
if(document.compatMode=='CSS1Compat')return document.documentElement.clientHeight;
if(document.body)return document.body.clientHeight; 
return window.undefined; 
}

function OLmGetViewportWidth(){
if(window.innerWidth!=window.undefined)return window.innerWidth; 
if(document.compatMode=='CSS1Compat')return document.documentElement.clientWidth; 
if(document.body)return document.body.clientWidth; 
return window.undefined; 
}

function OLmHideSelectBoxes(){
for(var i=0;i<document.forms.length;i++){
for(var e=0;e <document.forms[i].length;e++){
if(document.forms[i].elements[e].tagName=="select"){
document.forms[i].elements[e].style.visibility="hidden";}}}
}

function OLmShowSelectBoxes(){
for(var i=0;i<document.forms.length;i++){
for(var e=0;e<document.forms[i].length;e++){
if(document.forms[i].elements[e].tagName=="select"){
document.forms[i].elements[e].style.visibility="visible";}}}
}

function OLmDisableTabIndexes(){
if(OLie4&&!OLop7){var i=0;for(var j=0;j<OLmTabbableTags.length;j++){
var tagElements=document.getElementsByTagName(OLmTabbableTags[j]);
for(var k=0;k<tagElements.length; k++){OLmTabIndexes[i]=tagElements[k].tabIndex;
tagElements[k].tabIndex="-1";i++;}}}
}

function OLmRestoreTabIndexes(){
if(OLie4&&!OLop7){var i=0;for(var j=0;j<OLmTabbableTags.length;j++){
var tagElements=document.getElementsByTagName(OLmTabbableTags[j]);
for(var k=0;k<tagElements.length;k++){tagElements[k].tabIndex=OLmTabIndexes[i];
tagElements[k].tabEnabled=true;i++;}}}
}

OLregRunTimeFunc(OLloadModal);
OLregCmdLineFunc(OLparseModal);

OLmodalPI=1;
OLloaded=1;
