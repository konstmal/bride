<?php
include_once('db.inc.php');
include_once('classes.inc.php');
	
$db = db_connect($db_options);	

print '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
		<script src="jquery.lazyload.js" type="text/javascript"></script>
		<script type="text/javascript" charset="utf-8">
		  $(function() {
			 $("img.lazy").lazyload({
			 });
		  });
		</script>		
	</head>
	<body>';

	
	
$content = new ImageContent($db);	

$content->page = isset($_GET['page']) ? $_GET['page'] : 0;
$content->tags = isset($_GET['tags']) ? $_GET['tags'] : '';
$content->minus = isset($_GET['minus']) ? $_GET['minus'] : '';
$content->sorting = isset($_GET['sorting']) ? $_GET['sorting'] : 'date';

print $content->Filters();	
print $content->GetImages();
print $content->Pager();


print '</body>
</html>';
?>