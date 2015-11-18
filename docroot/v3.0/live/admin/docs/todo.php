<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
<style type="text/css">
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight:bold;
}
.done {
	color:#666666;
	font-weight:normal;
	}
.declined {
	color:#666666;
	font-weight:normal;
	text-decoration:line-through;
	}
-->
</style>
</head>

<body>
<p class="done">Client lookup: </p>
<p class="done">match first part only of entered strings? i.e. mar would match mark and margaret, but not umar or omar....?</p>
<p class="done">improve results to show:</p>
<ol>
  <li class="done"> full match on first and surname</li>
  <li class="done">full match on first name soundex AND surname soundex</li>
  <li class="done"> match on first or surname</li>
  <li class="done">match on soundex&nbsp;first or surname</li>
</ol>
<p class="done">11/05/07 Yeah, done... and removed email from equation (superfluous)</p>
<p class="done">with&nbsp;7 levels of checking, all done with php not mysql...</p>
<ol>
  <li class="done">exact match, fname AND sname</li>
  <li class="done">match to first part of fname AND sname</li>
  <li class="done">match soundex of fname AND exact surname</li>
  <li class="done">match exact fname AND soundex of surname</li>
  <li class="done">match exact fname OR exact surname</li>
  <li class="done">match to first part of fname OR to first part of surname</li>
  <li class="done">match to soundex of fname OR soundex of surname</li>
  <li class="done">(all the rest)</li>
</ol>
<p class="done">maybe need to think about stripping out some words (Mr., Mrs., &amp;, and,) etc - sias</p>
<p class="done">add above funcitonalty to client search page too </p>
<hr>
<p class="done">Cancel appointment, with notes field for reason and advice to inform all vendors/landlords </p>
<p class="done">All day event tickbox to disable time </p>
<p class="done">Calendar sumary, showing cancelled and delete appointment counts, link to search results page</p>
<p class="done"><span class="done">Feature suggest / Bug reporting system - </span>need view/edit page </p>

  <p class="done">Keybook - temporary solution, simple text field</p>

<p class="done">Inspections: need to expand list of contacts and write search/add system for contacts<br>
  contacts can be added during appointment creation process, but currenty company cannot
</p>
<p><span class="done">Contacts system, contact/company</span> </p>
<p class="done">Edit notes</p>
<p class="done">Board status and log </p>
<p class="done">Client Summary page:<br>
registered since, referer, property viewed, offers made, deals (sales and lettings), requirements summary<br>
NOT DOING THIS, instead improved client edit page, added viewing history</p>
<p class="done">Lettings: <br>
available date&nbsp;</p>
<p class="done">Sales: <br>
service charge, ground rent, other, lease details(for now, fields in deal; in future, link to charges table?)&nbsp;</p>
<p><span class="done">Print property details page layout</span> - little bit more to do </p>
<p class="done">Print appointments (ones not done: inspection,meeting,lunch etc) - enough, not much info for notes and meetings </p>
<p class="done">Print calendar filtered - new window, print calendar_print.php (calendar_day with a few minor adjustments) </p>
<p class="done">Calendar appointment type: sickie/holiday (maybe note_type? - 'General','Holiday','Sick','Private') </p>
<p class="done">Extend client search</p>
<p>Client ptype and area prefs to be stored in link table </p>
<p class="done">Extend property search </p>
<p><span class="done">General property notes</span>, applicant notes </p>
<p>Paginate the changeLog </p>
<p class="done">Proofing: advise on missing items, do not allow release without required fields - unnessecary, only editors can make live; to follow later </p>
<p><span class="done">Pre-release properties not available to negs (basic details only)</span>  - little bit more to do </p>
<p class="declined">Mailing list&nbsp;- to use prefs to generate list, use existing mail sending script</p>
<p class="done">Mailing list - fork to command line php script looping through mail() </p>
<p class="done">Advertising - continue to use old database</p>

<p class="done">File sending - continue to use old database</p>
<p>Datafeeds - propertyfinder, rightmove( zoomf, findaproperty)</p>
<p>Documents (upload form)</p>
<p class="done">When booking viewing, if viewer has no address force entry </p>
<p class="done">Sort out the source table, it is shit - sorted, with abilty to add new! and will accept unlimited new master types with no re-programming NICE </p>
<p class="done">More improvements to client_lookup.... now using levenshtein as well as soundex and exact matches </p>
<p class="done">When booking viewing, prompt to enter/review requirements - only if client has not been reviewed in the past 2 months </p>
<p class="done">using 
    <LABEL for=checkbox_row_7>cli_reviewed</LABEL> 
field which gets updated each time the property requirements are updated either by client or user. </p>
<p><span class="done">Hide offers from negs, except accepted and rejected (show all to owner, manager etc) -</span> perhaps not for lettings? </p>
<p class="done">Include inspectors(contacts) when searching calendar - also included company names </p>
<p class="done">Show cancelled appointments  on home page for reference - in grey with strike-through </p>
<p>Show appointments where invited as attendee, notify attendee when invitied </p>
<p>Name fields cannot contain special characters except hyphens, apostrophes and umlats etc </p>
<p class="done">Copy deal from sales to lettings and vv </p>
<p>Make adding, search contacts more user friendly</p>
<p class="done">consider removing vendor phone numbers from print app</p>
<p class="done">Add full vendor details to deal page / not quite full, but added vendor and tenants to separate tab </p>
<p class="done">Move inspection subtype out of appointment table, and have separate for sales and lettings </p>
<p>Ideally, move app_type into separate table</p>
<p>&nbsp;</p>
<p>DATA</p>
<p>Add all contacts, surveyors, everyone - MANUALLY</p>
<p>Add current (for sale, under offer, exchanged(sold) properties with vendors - MANUALLY</p>
<p>Add applicants  </p>
<hr>
<p>Probably for v3.0.1 </p>
<p>Tabbed layout on large pages - maybe for v3.0.1, but I dont want users to get used to verticle tabs only to change them over to horizontal in a month <br>
  This requires a major re-write of style sheets, and form constructor </p>
<p>Persistent login, retaining login info in database until logged out. Auto logout after n mins of inactivity. Show logged in users. Only allow login on one computer at a time. </p>
<p>Calendars side by side</p>
<p>Week and Month view </p>
<p>Recurring Appointments </p>
<p>Proofing: advise on missing items, do not allow release without required fields</p>
<p>Deal progression page: allowing exchage, completion dates, surveys, buyer and seller solicitor, creation of memos and notes </p>
<p>&nbsp;</p>
<hr>
<p>Further in future</p>
<p>PGP signed emails </p>
<p>Document creating, storage and management </p>
<p>&nbsp; </p>
</body>
</html>
