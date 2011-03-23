<?php include '../lib/helpers.php' ?>
<html>
	<head>
		<title>Noordelijk Film Festival iCal Agenda</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style type="text/css" media="screen">
			body {
				text-align: center;
			}
			
			body p {
				font: 24px Tahoma;
			}
			
			a img {
				border: 0;
			}
		</style>
	</head>
	<body>
		
		<!-- Merk op dat deze site op geen enkele manier verbonden is aan het Filmhuis Leeuwarden
			 Het is slechts een hulpmiddel gemaakt door een fan -->
		
		<a href="http://www.filmhuisleeuwarden.nl"><img src="filmhuis-leeuwarden.png" alt="Filmhuis Leeuwarden Logo"></a>
		
		<p>
			<a href="filmhuis-leeuwarden.php">Importeer</a> of 
			<a href="<?php echo calendar_url('filmhuis-leeuwarden.php') ?>">Abonneer</a>
		</p>
		<?php echo google_analytics() ?>
	</body>
</html>