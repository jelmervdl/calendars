<?php include '../lib/helpers.php' ?>
<html>
	<head>
		<title>Cover activiteiten Agenda</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style type="text/css" media="screen">
			body {
				text-align: center;
			}
			
			body p {
				font: 24px Tahoma;
			}
			
			h1 a {
				font: 148px Times;
				color: black;
				text-decoration: none;
			}
			
			.footnote {
				font-size: 11px;
			}
		</style>
	</head>
	<body>
		
		<!-- Merk op dat deze site op geen enkele manier verbonden is aan SV Cover
			 Het is slechts een hulpmiddel gemaakt door een fan -->
		
		<h1><a href="http://www.svcover.nl">Cover</a></h1>
		
		<p>
			<a href="sv-cover.php">Importeer</a> of 
			<a href="<?php echo calendar_url('sv-cover.php') ?>">Abonneer</a>
		</p>
		
		<p class="footnote">
			Momenteel worden gebeurtenissen waarvoor ingelogd moet worden nog niet weergegeven. Een fix is onderweg.
		</p>
		
		<?php echo google_analytics() ?>
	</body>
</html>