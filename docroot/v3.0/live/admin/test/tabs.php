<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
<script  src="js/tabbar/js/dhtmlXCommon.js"></script>
<script  src="js/tabbar/js/dhtmlXTabbar.js"></script>
<script  src="js/tabbar/js/dhtmlXTabBar_start.js"></script>
<link rel="STYLESHEET" type="text/css" href="js/tabbar/css/dhtmlXTabbar.css">
</head>

<body>



<div id="a_tabbar" style="width:400;height:100">
<div id='html_1'>
<p>Content one</p>
<p>Content one</p>
<p>Content one</p>
<p>Content one</p>
<p>Content one</p>
<p>Content one</p>
<p>Content one</p>
</div>
<div id='html_2'>Copntent 2</div>
</div>
<script>
            tabbar=new dhtmlXTabBar("a_tabbar","top");
            tabbar.setImagePath("js/tabbar/imgs/");
            tabbar.addTab("a1","Tab 1-1","100px");
            tabbar.addTab("a2","Tab 1-2","100px");
            tabbar.setContent("a1","html_1");			
            tabbar.setContent("a2","html_2");
</script>



</body>
</html>
