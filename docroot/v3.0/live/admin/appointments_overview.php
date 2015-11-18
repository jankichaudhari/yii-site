<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
</head>

<body>
<p>There are various different types of appointments, each to be handled slightly differently</p>
<p>Each requires different data, some require link tables and so on. Each will need rendering diffrerently on the calendar, and the add and edit page will also need to be customsed for each too. </p>
<hr>
<p>Standard fields for all appointments are:</p>
<ul>
  <li>date (start, end, duration, created)</li>
  <li>user (lead neg on viewings, sole user on others)</li>
  <li>bookedby </li>
  <li>private/public </li>
</ul>
<hr>
<p>Viewing</p>
<ul>
  <li>one of more deals (link_deal_to_appointment link table)</li>
  <li> one or more viewers/clients (cli2app)</li>
  <li> attendees (use2app) </li>
</ul>
<p>Display: </p>
<hr>
<p>Valuation</p>
<ul>
  <li>one deal (link_deal_to_appointment)</li>
  <li>one or more vendors/clients (cli2app) - vendor is usually the same as the vendor linked to the deal, but perhaps not always </li>
  <li>attendees (use2app) </li>
</ul>
<hr>
<p>Production</p>
<ul>
  <li>one or more deals</li>
  <li>one or more vendors/clients (cli2app) - vendor is usually the same as the vendor linked to the deal, but perhaps not always  (house-sitter?)</li>
  <li>attendees (use2app) </li>
</ul>
<hr>
<p>Survey</p>
<ul>
  <li>one deal</li>
  <li>one or more vendors/clients (cli2app) - vendor is usually the same as the vendor linked to the deal, but perhaps not always (house-sitter?) </li>
  <li>one surveyor/contact (con2app) </li>
  <li>survey type (mortgage val, homebuyers, structural) - enum built into table </li>
  <li>attendees (use2app) </li>
</ul>
<hr>
<p>Meeting (encompassing interviews and staff reviews) </p>
<ul>
  <li>subject</li>
  <li>location (notes?) </li>
  <li>outsiders i.e. non-employed attendees (notes?) </li>
  <li>attendees (use2app) </li>
</ul>
<hr>
<p>Lunch</p>
<hr>
<p>Holiday</p>
</body>
</html>
