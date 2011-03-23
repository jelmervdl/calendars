<?php include '../lib/helpers.php' ?>
<html>
	<head>
		<title>Noordelijk Film Festival iCal Agenda</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style type="text/css" media="screen">
			body {
				text-align: center;
			}
			
			a img {
				border: 0;
			}
			
			body form {
				width: 210px;
				margin: 20px auto;
				text-align: left;
				
				font: 14px Tahoma;
			}
			
			body form button {
				width: 150px;
				margin-left: 30px;
			}
			
			body p {
				font: 24px Tahoma;
			}
		</style>
	</head>
	<body>
		
		<!-- Merk op dat deze site op geen enkele manier verbonden is aan het Noordelijk Film Festival.
			 Het is slechts een hulpmiddel gemaakt door een fan. -->
		
		<!-- Het script haalt de lijst met alle films, en iedere link op die pagina op en bewaart deze
			 dan voor één uur. Daarna zal het opnieuw deze pagina's ophalen zodra iemand de feed opnieuw
			 download of herlaadt. -->
		
		<a href="http://www.noordelijkfilmfestival.nl"><img src="noordelijk-film-festival.png" alt="Noordelijk Film Festival Logo"></a>
		
		<!--
		<form action="noordelijk_film_festival.php" method="get">
			<p>
				<input  id="voorstellingen-leeuwarden" type="checkbox" name="location_conditions[]" value="leeuwarden" checked disabled>
				<label for="voorstellingen-leeuwarden" title="Toon de voorstellingen in Leeuwarden">Voorstellingen in Leeuwarden</label>
			<p>
				<input  id="off-shore-voorstellingen" type="checkbox" name="location_conditions[]" value="off-shore">
				<label for="off-shore-voorstellingen" title="Toon ook de voorstellingen op Terschelling &amp; Vlieland in de agenda">Voorstellingen op de eilanden</label>
			</p>
			<p>
				<input  id="besloten-voorstellingen" type="checkbox" name="location_conditions[]" value="besloten">
				<label for="besloten-voorstellingen" title="Toon ook de besloten voorstellingen in de agenda">Besloten voorstellingen</label>
			</p>
			<button type="submit" title="Genereer de agenda. Dit duurt soms wel een minuut">Genereer</button>
		</form>
		-->
		
		<p>
			<a href="noordelijk-film-festival.php">Importeer</a> of 
			<a href="<?php echo calendar_url('noordelijk-film-festival.php') ?>">Abonneer</a>
		</p>
		<?php echo google_analytics() ?>
	</body>
</html>