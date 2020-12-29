<div class="menu">
	<a href="home.php"><img id="logo" src="icons/logo.svg" alt="logo.svg" title="Home"></a>
	<?php
	//Sökruta
	if ($show_search == 1) {
		print ("<div class='search' title='Search for parts or minifigures'>
					<form action='home.php?filter=Show+all&' method='get'>
						<input type='text' placeholder='search' name='search'>
					</form>
				</div>
				<div class='filter'>
					<form method='GET' action='home.php'>
						<input name='filter' type='submit' value='Parts' title='Show all parts'>
						<input name='filter' type='submit' value='Minifigs' title='Show all minifigures'>
						<input name='filter' type='submit' value='Show all' title='Show all parts and minifigures'>
					</form>
				</div>");
		}?>
	<!-- Light/Darkmode knapp -->
	<div id="theme" title="Change color theme">
		<img id="sun" src="icons/sun.svg" alt="sun.svg">
		<label class="switch">
				<input onclick="themeswitch()" type="checkbox" id="themetoggle" >
				<span class="slider round"></span>
		</label>
		<img id="moon" src="icons/moon.svg" alt="moon.svg">
	</div>
</div>