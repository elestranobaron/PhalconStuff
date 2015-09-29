<meta charset="utf-8"> <!-- pas besoin de ca normalement -->
<!DOCTYPE html>
<html>
	<head>
		<title>Welcome to Vökuró</title>
		<link href="//netdna.bootstrapcdn.com/bootswatch/2.3.1/united/bootstrap.min.css" rel="stylesheet">
		<?php echo $this->tag->stylesheetLink('css/style.css'); ?>
	</head>
	<body>

		<?php echo $this->getContent(); ?>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
	</body>
</html>
