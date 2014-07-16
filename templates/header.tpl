<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="nl">
	<head>
		<title>ASV Karpe Noktem - KN-Album - <?= htmlentities($title); ?></title>
		<base href="<?= $absolute_url_path ?>">
		<!--[if lte IE 7]>
			<link rel="stylesheet" type="text/css" href="https://www.karpenoktem.nl/djmedia/base/iehacks.css" />
		<![endif]-->
		<link type="text/css" rel="stylesheet" href="style.css" />
<?PHP if($sliding) { ?>
		<script type="text/javascript">
			function slider_next() {
				location.href="view.php?slide&foto=<?= urlencode($next) ?>";
			}
<?PHP if($foto_slider_preload) { ?>
			function slider_preload() {
				var el=document.createElement('img');
				el.src='foto.php?foto=<?= urlencode($next) ?>';
				el.style.display='none';
				el.alt='Fotoslider Preloader';
				document.body.appendChild(el);
			}
<?PHP } ?>
			setTimeout('slider_next()', <?= $foto_slider_timeout*1000 ?>);
		</script>
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
