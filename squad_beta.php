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

0.03 -- Proof of concept working!
Ok, so basically i did a very lazy way of grading the units. You can see from the code that i just opened up a "bonus" array for the special units that are especially good against maxwell (i.e. Lilith and Grahdens). We might reach out to the communtiy to get a general consensus of whats good or not, but that's what I have so far. You can see a proof of concept version here:
http://www.braveblank.com/squad-suggester-poc/

 */
error_reporting(-1);
ini_set('display_errors', 'On');
require_once('connectvars.php');
$dbc = mysqli_connect('localhost', USER, PASS, 'kuhaku_voting')
or die ("Error connecting to database");
//Constants defined now
define('HP_BASE', 1.00028);
define('HP_EXP_ABOVE', 0.91);
define('REC_EXP', 0.9);
//Declaration of unit arrays needed or data calculation begins now--------------------------------------------------------------
$units = array(
	"Alpha Tree Altri" => array(1.45, "Healer"),
	"Grahdens" => array(1.2, "Leader"),
	"Terminator Lilith" => array(1.2, "Synergy"),
	"Tyrant Lilly Matah" => array(1.5, "Leader"),
	"Creator Maxwell" => array(1.05, "Synergy"), 
	"Lightning Gun Rowgen" => array(1.22, "Synergy"), 
	"Guardian Darvanshel" => array(1.45, "Synergy"),
	"Mad God Narza" => array(1.45, "Synergy"),
	"Colossal Tridon" => array(1.08, "Synergy"));
//Function declaration begins now --------------------------------------------------------------------------------------------------
function fetch_unitname($unitstr) {
	return explode("</b>", explode("<b>", $unitstr)[1])[0];
}
function point_of_intersection($equation1, $equation2) {
	//stil trying to figure out how to do this...
	$sum = 1;
	for ($x = 1;  $x <= 999999999; $x++) {
		$hpscore_1 = pow(HP_BASE, $sum);
		$hpscore_2 = pow($sum, HP_EXP_ABOVE) - 3000;
		if ($hpscore_1 - $hpscore_2 < 5) {
			//They are not apporximately equal
			break;
		}	
		else {
			continue;
		}
	}
}
function assign_score($sum, $type) {
	switch($type){
		case"atk":
			return pow($sum, 0.95); //This growth shows decreasing marginal returns as it increases
			break;
		case "def":
			return $sum; //DEF is always important
			break;
		case "hp":
			$hpscore_1 = pow(HP_BASE, $sum);
			$hpscore_2 = pow($sum, HP_EXP_ABOVE) - 3000;
			if ($sum < 32000) {
				return pow(HP_BASE, $sum);
			}	
			else {
				return pow($sum, HP_EXP_ABOVE) - 3000;
			}
			break;
		case "rec":
			return pow($sum, REC_EXP);
			break;
	}
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
	$dbc = mysqli_connect('localhost', USER, PASS, 'kuhaku_voting')
	or die ("Error connecting to database");
	$unit_data_array = array();
	mysqli_query($dbc, "SELECT * from units")
	or (print(mysqli_error($dbc)));
	$selectall = mysqli_query($dbc, "SELECT Unit, HP, ATK, DEF, REC FROM units"); //SELECT all the units in the database and begin to parse them by removing the HTML tags in the unitname and the commas from the stats so they can be treated as ints
	while ($row = mysqli_fetch_array($selectall, MYSQLI_NUM)) {
		list($unitstring, $hp, $atk, $def, $rec) = $row;
		$unitname = fetch_unitname($unitstring);
		$unit_data_array["$unitname"] = fetch_raw_value($hp, $atk, $def, $rec);
	}
	return $unit_data_array; //This array now holds all the unitnames without the HTML strings and their stats without the commas
}
function find_sum($array, $stat) {
	$sum = 0;
	switch($stat) {
		case "hp" || "HP":
		$keysum = 0;
		break;
		case "atk" || "ATK":
		$keysum = 1;
		break;
		case "def" || "DEF":
		$keysum = 2;
		break;
		case "rec" || "REC":
		$keysum = 3;
		break;
	}
	foreach ($array as $key => $array_to_sum) {
			$sum += $array[$key][$keysum];
	}
	return $sum;
}
function sort_array($array, $stat) {
	//Function to sort array based on provided arguments , the array and the type (which stat)
	switch($stat) {
		case "hp":
		$keysum = 0;
		break;
		case "atk":
		$keysum = 1;
		break;
		case "def":
		$keysum = 2;
		break;
		case "rec":
		$keysum = 3;
		break;
	}
	$sortedarray = array();
	foreach ($array as $key => $val) {
		$sortedarray[$key] = $val[$keysum];
	}
	array_multisort($sortedarray, SORT_DESC, $array);
	return($sortedarray);
}
//Function declaration ends now ----------------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {	
	$unit_data_array = enumerate_array();
	/*This array contains all the units with their name and stats and have been converted to string => ints for PHP modification or Python modification. The array is this:
	array(
		unitname => array(hp, atk, def, rec));
	*/
	$units_array = explode(", ", $_POST['units']); //These are the units that the user has entered
	$unsortedstatarray = stat_array($units_array, $unit_data_array); 
	$stats = array("hp", "atk", "def", "rec");
	//First assign a score to every single unit
	$scoredarray = array();
	foreach ($unsortedstatarray as $key => $val) {
		$scoredarray[$key] = assign_score($val[0], "hp") + assign_score($val[1], "atk") + assign_score($val[2], "def") +assign_score($val[3], "rec");
		//Assign the bonus if the unit is in the special unit array
		if (array_key_exists($key, $units)) {
			echo "assigned bonus for $key <br/>";
			$scoredarray[$key] = $scoredarray[$key] * $units[$key][0];
		}
	}
	arsort($scoredarray);//This array is the array of units scored by their importance. Now, we will search for a viable leader
	$top10 = array_slice($scoredarray, 0, 10);
	$idealsquad = array_slice($scoredarray, 0, 6);
	//Find the leaders
	$leaderarray = array();
	$x = 0;
	foreach ($top10 as $key => $val) {
		if (array_key_exists($key, $units) && $units[$key][1] == "Leader" && $x < 2) {
			$leaderarray[$key] = $val;
			$x++;
		}
	}
	//Now search all units if no leader was found
	if ($x != 2) {
		foreach($scoredarray as $key => $val) {
			if (array_key_exists($key, $units) && $x < 2 && $units[$key][1] == "Leader" ) {
					$leaderarray[$key] = $val;
					$x++;				
			}
		} 
	}
	if ($x!= 2) {
		foreach($scoredarray as $key => $val) {
			if (array_key_exists($key, $units) && $x < 2 && ($units[$key][1] == "Leader" || $units[$key][1] == "Synergy")) {
					$leaderarray[$key] = $val;
					$x++;				
			}
		} 
	}
	if ($x!= 2) {
		//No leaders present, just suggest the top unit score as leader than
		$top = array_slice($scoredarray, 0, 1);
		foreach ($top as $key => $val) {
			$leaderarray[$key] = $val;
		}
	}
	//Now just grab the top 4 units that are not in the leader array depending on how many units left
	$scoredarraynoleader = array_diff_key($scoredarray, $leaderarray);
	$suggestedsquad = array_slice($scoredarraynoleader, 0, 4);
	echo "<h2>Suggested Squad is:</h2>";;
	foreach ($leaderarray as $key => $val) {
		echo "$key (Leader)<br/>";
	}
	foreach($suggestedsquad as $key => $val) {
		echo "$key<br/>";
	}
}
?>
<html>
<form method="post" action="http://www.braveblank.com/squad-suggester-poc/">
<p>
Units (please seperate by comma)
<textarea name="units" style="width:500px;height:500px;">
</textarea>
<input type="submit" value="Suggest Squad" name="submit">
</form> 