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
-->
</style>
</head>

<body>
<p>Branch &gt; Department </p>
<p>2 branches, camberwell and sydenham</p>
<p>2 departments in each, sales and lettings</p>
<p>each deals, clients, etc get assigned to the department, not the branch.</p>
<p>assign areas to the branch? no as some departments may wish to vary their coverage</p>
<p>so stil need to use link table to link areas to branches or departments if employed</p>
<p>so areas can be assigned to more than one branch, but need to prevent the same area being selected by two lettings departments? </p>
<p>benefits: when assigning areas to clients, separate selections can be made for sales and lettings, and covered areas can be shown per department. giving client more flexabilty.</p>
<p>&nbsp;</p>
<hr>
<p>&nbsp;</p>
<p>Thoughts for revised form.inc</p>
<p>Adding items to the beggining and end of options array </p>
<p>Adding blank options more gracefully </p>
<p>Sort out checkboxes being wrong way round</p>
<p>Extend classes to make system spercific elements (select_branch, user, etc) </p>
<p>getting rid of any unnessecary divs, more flexible design allowing placement anywhere on page (wrap other tags? assign classes?) </p>
<p>Obviuolsy ajax it all up</p>
<p>Ability to create tabs? </p>
<p>Accept an array of forms, so differnet bits can be processed differently</p>
<p>Validate and process fields from multiple tables and update using improved db_query</p>
<p>&nbsp;</p>
</body>
</html>
