<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="nl">
	<head>
		<title>ASV Karpe Noktem - KN-Album - <?= htmlentities($title); ?></title>
		<base href="<?= $absolute_url_path ?>">
		<link href="http://www.karpenoktem.nl/base/styles/bare/" rel="stylesheet" type="text/css" />
		<!--[if lte IE 7]>
			<link rel="stylesheet" type="text/css" href="http://www.karpenoktem.nl/djmedia/base/iehacks.css" />
		<![endif]-->
		<link href="http://www.karpenoktem.nl/base/styles/common/" rel="stylesheet" type="text/css" />
		<link type="text/css" rel="stylesheet" href="style.css" />
		<script type="text/javascript">
			var next    = "view.php?slide&foto=<?= urlencode($next) ?>";
			var preload = 'foto.php?foto=<?= urlencode($next) ?>';
			var sliding = <?= $sliding ? 'true' : 'false' ?>;
			var slider_timeout = <?= $foto_slider_timeout ?>;
			var type    = <?= "'". $type ."'" ?>
		</script>
		<script type="text/javascript" src="script.js"></script>
<?PHP if($sliding) { ?>
		<noscript>
			<meta http-equiv="refresh" content="<?= $foto_slider_timeout ?>; url=view.php?slide&foto=<?= urlencode($next) ?>">
		</noscript>
<?PHP } ?>
	</head>
	<body>
		<div id="wrapper">
			<div id="logo"></div>
			<div id="main">
				<div id="body">
