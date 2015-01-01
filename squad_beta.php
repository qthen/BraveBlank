<?php
//Squad Suggester App
/*
Some quick intro info:
The connection variables are for another database, not wordpress. Will change that soon

v.0.01 - Jan 1, 2015:
A few ideas is to create a formula or created a score for every single unit based on the table vote or some other system. 
Then this PHP script (or Python, I'll communicate the scripts through JSON decode and encode) will calculate the submitted units the user already has and suggest the squad wiht the highest score
I think treating the squad as a single "unit" will be the easiest to do. 
I also thought of perhaps modelling a function for every single unit. For example, any unit that is classified as a "healer" will have a function similar to sqrt(x) or log(x). Basically, a function that slows down quickly due ot decreasing returns from having more and more healers on a team

DECIDED TO FOCUS THIS ON MAXWELL AND LATER EXPAND IT FOR SIMPLICTY

Not sure how we're going to assign leader skill or synergy values. I'm thinking of just doing something like:
max_possible_damage = This will be the maximum possible damage the units entered can do in a squad of 6
max_possible_resistance = Minimum possible damage Maxwell can do to this squad

0.02 - Added unit informtion data and max possible atk and defence functions
THIS IS REALLY SLOW AND I NEED TO CONVERT THE MYSQL TABLE INTO RAW UNIT DATA (CURRENTLY THE UNIT COLUMN INCLUDES ALL THE HTML CODE). I'VE TRIED LOOKING FOR A WAY TO SEARCH IN SQL BASED ON IF SUBSTRING IN STRING LIKE THIS:
The user types in "Creator Maxwell". Preform SQL search where "Creator Maxwell" in Unit. The unit string for maxwell in the sql database looks like this:
<a href="http://touchandswipe.github.io/bravefrontier/unitsguide.html?unit=Creator%20Maxwell"><img src = "http://braveblank.com/wp-content/uploads/2014/11/creator-maxwell.png"alt = "" width = "50" height ="50" class="alignleft size-full wp-image-45" /><b>Creator Maxwell</b></a>
Another option might be through AngularJS or Ajax, but I have no idea how to do it at my current level. For now, this is what I have:
I'M GOING TO CREATE A MYSQL TABLE FOR THE RAW UNIT DATA SOON. IM VERY LAZY -_-
Still trying to figure out how to get leader skill values... This  may be difficult...

 */
error_reporting(-1);
ini_set('display_errors', 'On');
require_once('connectvars.php');
$dbc = mysqli_connect(SHUFFLR_DATA_HOST, SHUFFLR_DATA_USER, SHUFFLR_DATA_PASS, SHUFFLR_DATA_DATABASE)
or die ("Error connecting to database");
//Function declaration begins now --------------------------------------------------------------------------------------------------
function fetch_unitname($unitstr) {
	return explode("</b>", explode("<b>", $unitstr)[1])[0];
}
function fetch_raw_value($hp, $atk, $def, $rec) {
	$statsarray = array(); //Array to hold the stats for after commas have been removed and can be analyzed as an int in PHP
	$stats = func_get_args();
	foreach ($stats as $stat) {
		array_push($statsarray, str_replace(",", '', $stat));
	}
	return $statsarray;
}
function atk_weight($x) {
}

function max_possible_def() {

}
function stat_array($units_array, $unit_data_array) {
	//This function takes two arguments. An array of unit names, and the array that stores the unit database. It will use the second argument to determine the stats for the units that the user has entered. By doing it this way, we can echo out a personal error message if the search fails
	$statarray = array();
	foreach($units_array as $unitname) {
		if (!array_key_exists($unitname, $unit_data_array)) {
			echo "Unable to find $unitname in our database. Please ensure you have spelt it correctly or report a bug";
		}
		else {
			$statarray[$unitname] = $unit_data_array[$unitname];
		}
	}
	return $statarray;
}
function enumerate_array() {
	global $dbc;
	$unit_data_array = array();
	$selectall = mysqli_query($dbc, "SELECT unit, hp, atk, def, rec FROM units"); //SELECT all the units in the database and begin to parse them by removing the HTML tags in the unitname and the commas from the stats so they can be treated as ints
	while ($row = mysqli_fetch_array($selectall, MYSQLI_NUM)) {
		list($unitstring, $hp, $atk, $def, $rec) = $row;
		$unitname = fetch_unitname($unitstring);
		$unit_data_array["$unitname"] = fetch_raw_value($hp, $atk, $def, $rec);
	}
	return $unit_data_array; //This array now holds all the unitnames without the HTML strings and their stats without the commas
}
//Function declaration ends now ----------------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {	
	$unit_data_array = enumerate_array();
	/*This array contains all the units with their name and stats and have been converted to string => ints for PHP modification or Python modification. The array is this:
	array(
		unitname => array(hp, atk, def, rec));
	*/
	$units_array = explode(", ", $_POST['units']);
	$unsortedstatarray = stat_array($units_array, $unit_data_array);


}
?>
<html>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<p>
Units (please seperate by comma)<br/>
<textarea name="units" value="<?php echo $_POST['units'];?>" style="width:500px;height:500px;">
</textarea>
<input type="submit" value="Suggest Squad" name="submit">
</form> 