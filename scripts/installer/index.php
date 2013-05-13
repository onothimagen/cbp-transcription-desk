<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>Installing sample item</title>
	<script src="/w/resources/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/console.js"></script>
    <link rel="stylesheet" type="text/css" href="css/console.css" />
  </head>
  <body>
	<div id="console"></div>
    <button type="button" style="position:relative; float:left;" onclick="ajaxConnect();StartScroll();">Start Item Import</button>
    <div id="loader" style="display:none;position:relative;left:50px" ><img src="images/loader.gif" style="vertical-align: middle;" /> Loading sample item...</div>
    <?php
    require_once './report/jobs.inc.php';
    ?>
  </body>
</html>