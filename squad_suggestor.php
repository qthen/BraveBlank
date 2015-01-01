<?php
//Squad Suggester App
/*
v.0.01
A few ideas is to create a formula or created a score for every single unit based on the table vote or some other system. 
Then this PHP script (or Python, I'll communicate the scripts through JSON decode and encode) will calculate the submitted units the user already has and suggest the squad wiht the highest score
I think treating the squad as a single "unit" will be the easiest to do. 

DECIDED TO FOCUS THIS ON MAXWELL AND LATER EXPAND IT FOR SIMPLICTY

Not sure how we're going to assign leader skill or synergy values. I'm thinking of just doing something like:
max_possible_damage = This will be the maximum possible damage the units entered can do in a squad of 6
max_possible_resistance = Minimum possible damage Maxwell can do to this squad
 */
if (isset($_POST['submit'])) {
	require_once('connectvars.php');
	$dbc = mysqli_connect(HOST, USER, PASS, 'kuhaku_voting')
	or die ("Error connecting to database");
	
	$units_array = explode(", ", $_POST['units']);


}
?>
<html>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Units (please seperate by comma)
<textarea name="units" value="<?php echo $_POST['units'];?>">
</textarea>
<input type="submit" value="Suggest Squad" name="submit">
</form>