<?php
ini_set('display_errors', 'On');
require_once('connectvars.php');
$dbc = mysqli_connect('localhost', USER, PASS, 'kuhaku_voting')
or die ("Error connecting to database");
//Constants defined now
define('IDEAL_TEAM', 34000);
define('HP_BASE', 1.00028);
define('HP_EXP_ABOVE', 0.91);
define('REC_EXP', 0.9);
define('DIMINISH_MARGINAL_DEF_BUFF', 0.05);
define('DIMINISH_MARGINAL_MITIGATOR', 0.1); //This is to dimish the marginal benefit for each additional damage mitiagtor we put on the team
define('DIMINISH_MARGINAL_HEALER', 0.5); //This is to diminish the marginal benefit for each additional healer we put on the team
define('DIMINISH_MARGINAL_UNLIMITED_BB', 0.1); //This is to diminish the marginal benefit for each additional unlimited BB unit we put on the team
//Declaration of unit arrays needed or data calculation begins now--------------------------------------------------------------
$units = array(
	"Alpha Tree Altri" => array(3, "Healer"),
	"Grahdens" => array(2, "Leader"),
	"Terminator Lilith" => array(1.5, "Synergy", "UnlimitedBB"),
	"Tyrant Lilly Matah" => array(3, "Leader"),
	"Creator Maxwell" => array(1.15, "Leader",), 
	"Lightning Gun Rowgen" => array(1.7, "Synergy", "UnlimitedBB"), 
	"Guardian Darvanshel" => array(2, "defence_buff", "Leader"),
	"Mad God Narza" => array(2.2, "defence_buff", "Leader"),
	"Colossal Tridon" => array(2.05, "Synergy", "Leader"),
	"Ice Fortress Oulu" => array(1.8, "defence_buff", "Leader"),
	"Duel SGX" => array(1.2, "Synergy"),
	"Fire Goddess Ulkina" => array(2.2, "Healer"),
	"Ice God Arius" => array(1.2, "Healer"),
	"Guardian Goddess Tia" => array(1.4, "Healer"), 
	"Goddess Axe Michele" => array(1.5, "Synergy", "Leader"),
	"Beast God Exvehl" => array(1.6, "Leader"),
	"Empyreal Drake Lodin" => array(1.35, "Synergy"),
	"Ivy Goddess Nalmika" => array(1.2, "Synergy"),
	"Thief God Zelnite" => array(1.18, "Synergy", "Leader"),
	"God Engineer Garnan" => array(1.2, "Snergy"),
	"Divine Light Alyut" => array(1.15, "Synergy"),
	"Wordly Themis" => array(2, "Healer"),
	"Legatus Melchio" => array(1.3, "Synergy"),
	"Black Lotus Lunaris" => array(1.15, "Synergy"),
	"Duel-SGX" => array(1.5, "Synergy", "", "EXCEPTION1", 1.8),
	"Mad God Zebra" => array(1.1, "Synergy", "Leader", "EXCEPTION1", 1.8, "Leader"),
	"Felneus" => array(1.5, "Leader"), 
	"Havoc Angel Ronel" => array(1.3, "Synergy", "Leader"),
	"Blazing Mare" => array(1, "Synergy", "Leader"),
	"Thunder Savior Shera" => array(2.8, "defence_buff", "Leader"),
	"Phoenix God Arus" => array(1.5, "Synergy", "UnlimitedBB"),
	"Thunder Sentry Shera" => array(1.5, "defence_buff", "Leader"),
	"Divine Whip Orna" => array(1.105, "Synergy", "Leader"),
	"Protector Darvanshel" => array(1.1, "defence_buff"),
	"Empress Lilly Matah" => array(1.2, "Synergy", "Leader"),
	"Master Assassin Kuda" => array(1.5, "Synergy", "Leader", "EXCEPTION2"),
	"Snow Queen Eva" => array(1.4, "Synergy", "Leader"),
	"Massacre God Belfura" => array(1.05, "Synergy"),
	"Dark Demigod Ardin" => array(2.2, "Synergy", "Leader"),
	"Havoc Angel Ronel" => array(1.7, "Leader"),
	"Blade Emperor Zelban" => array(1.8, "Synergy", "Leader"),
	"Holy Arms Douglas" => array(1.3, "Synergy"),
	"God Engineer Garnan" => array(1.3, "Synergy", "Leader"),
	"Cyclopean Ultor" => array(2, "Synergy", "Leader")
	);
$squad_template = array("Leader", "defence_buff", "Healer"); //Attempt to create this squad
//Function declaration begins now --------------------------------------------------------------------------------------------------
function assign_exception($scoredarray, $data_array, $units, $exception) {
	//Uses three arguments. The first is the current scored array, the second is any needed additional data that will make this function faster, the unit database that contains the unit bonuses, and the fourth is the exception number that should be applied
	$exceptionarray = array(); //Temporary holding array variable for modifiying the bonuses for exceptions
	switch ($exception) {
		case "EXCEPTION1":
			if (array_key_exists('Duel-SGX', $scoredarray) && array_key_exists('Mad God Zebra', $scoredarray)) {
				//These two units should create a leader-unit duo if their combined bonuses are not higher than the current leaders
				$exceptionarray['Duel-SGX'] = $scoredarray['Duel-SGX'] * $units['Duel-SGX'][4];
				$exceptionarray['Mad God Zebra'] = $scoredarray['Mad God Zebra'] * $units['Mad God Zebra'][4];
				$x = array_sum($data_array);
				$y = array_sum($exceptionarray);
				if ($x < $y) {
					//This means that the two current leaders are less than what they could be, so assign these duo into the scoredarray
					$keytoreplace = array_search(min($data_array), $data_array); //Move the lowest scored leader in the leader array into the units array first
					echo "imagine";
					$scoredarray[$keytoreplace] = $data_array[$keytoreplace];
					unset($data_array[$keytoreplace]);
					$data_array['Mad God Zebra'] = $exceptionarray['Mad God Zebra'];
					$scoredarray['Duel-SGX'] *= 500; //To guarantee that Duel-SGX will be assigned onto the team
					unset($scoredarray['Mad God Zebra']);
					arsort($scoredarray);
					return array($data_array, $scoredarray);
				}
				else{
					return array($data_array, $scoredarray);
				}
			}
			else {
				return array($data_array, $scoredarray);
			}
			break;
		default:
		return array($data_array, $scoredarray);
		break;
	}
}
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
function stat_array($units_array, $unit_data_array) {
	//This function takes two arguments. An array of unit names, and the array that stores the unit database. It will use the second argument to determine the stats for the units that the user has entered. By doing it this way, we can echo out a personal error message if the search fails
	$statarray = array();
	foreach($units_array as $unitname) {
		if (!array_key_exists($unitname, $unit_data_array)) {
			echo "Unable to find $unitname in our database. Please ensure you have spelt it correctly or report a bug";
			echo '<br/><a href=".?inputname=' . $unitname . '">Add unit: ' . $unitname . '?</a><br>';
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
	$selectall = mysqli_query($dbc, "SELECT name, hp, atk, def, rec FROM units_raw"); //SELECT all the units in the database and begin to parse them by removing the HTML tags in the unitname and the commas from the stats so they can be treated as ints
	while ($row = mysqli_fetch_array($selectall, MYSQLI_NUM)) {
		list($unitname, $hp, $atk, $def, $rec) = $row;
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
function assign_mitigator($squad, $units) {
	$mitigatorneeded = 1;
	$diminish = 1;
	foreach ($squad as $squad_unit => $val) {
		if(array_key_exists($squad_unit, $units) && $units[$squad_unit][1] == "Mitigator" && $mitigatorneeded > 0) {
			$mitigatorneeded--;
			$mitigator = $squad_unit;
			unset($squad[$squad_unit]);
			break;
		}
	foreach ($squad as $squad_unit => $val) {
			if (array_key_exists($squad_unit, $units) && $units[$squad_unit][1] == "Mitigator" && $mitigatorneeded == 0) {
				//Diminish the other mitigators
				$diminish -= DIMINISH_MARGINAL_MITIGATOR;
				$squad[$squad_unit] *= $diminish;
			}
		}
	}
	if (!$mitigator) {
		//No mitigator was found, set as false
		$mitigator = false;
	}
	arsort($squad);
	return array($mitigator, $squad); //Returns the key (unitname) for the mitigator and the array of units with the diminished damage mitigator applied
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
function assign_leaders($scoredarray, $units) {
	//Function assigns the two best leaders based on two arguments, an array of the scored units the user has submitted and the units array that contain the bonuses for units against Maxwell
	//This function returns two arrays, one array is the array with the leaders and the other is the array of units that the user has submitted without the leaders included
	$x = 0; //x is the variable for the number of leaders we have assigned so far
	$leaderarray = array();
	foreach ($scoredarray as $key => $val) {
		if (array_key_exists($key, $units) && $units[$key][1] == "Leader" && $x < 2) {
			$leaderarray[$key] = $val;
			unset($scoredarray[$key]);
			$x++;
		}
	}
	//Preform a search for units with a subset leader as the next priority
	if ($x < 2) {			
		foreach($scoredarray as $key => $val) {
			if (array_key_exists($key, $units) && $x < 2 && $units[$key][2] == "Leader") {
					$leaderarray[$key] = $val;
					unset($scoredarray[$key]);
					$x++;				
			}
		} 
	}
	//If still no leaders found, search for synergy next:
	if ($x < 2) {
		foreach($scoredarray as $key => $val) {
			if (array_key_exists($key, $units) && $x < 2 && $units[$key][1] == "Synergy") {
					$leaderarray[$key] = $val;
					unset($scoredarray[$key]);
					$x++;				
			}
		} 
	}
	//If still no leaders present, then search based on highest scored unit that is not a healer
	if ($x < 2) {
		$leaderstoadd = array_slice($scoredarray, 0, (2 - $x));
		foreach ($leaderstoadd as $key => $val) {
			if (array_key_exists($key, $units) && $x < 2 && ($units[$key][1] == "Healer" || $units[$key][1] == "Mitigator")) {
				//Do not assign this unit
				continue;
			}
			else {
				$leaderarray[$key] = $val;
				unset($scoredarray[$key]);
				$x++;
			}
		}
	}
	if ($x < 2) {
		//If the number of leaders is still less than two, return this function as false
		return $scoredarray;
	}
	return array($leaderarray, $scoredarray);
}
function assign_healer($scoredarray, $units) {
	//This function takes a provided array and attempts to find the best healer appropiate. Returns an array with the first element as the suggested healer and the second element as the array without the healer
	$diminish = 1;
	$healer = 0;
	foreach ($scoredarray as $key => $val) {
		if (array_key_exists($key, $units) && $units[$key][1] == "Healer"){
			$healerunit = $key;
			unset($scoredarray[$key]);
			$healer = 1;
			break;
			
		}
	}
	foreach ($scoredarray as $key => $val) {
		if (array_key_exists($key, $units) && $units[$key][1] == "Healer" && $healer == 1) {
			//A healer has been found, diminish the other healers
			$diminish -= DIMINISH_MARGINAL_HEALER;
			if ($diminish < 0.01) {
				$diminish == 0.01;
			}
			$scoredarray[$key] *= $diminish;
		}
	} 
	if ($healer == 0) {
		//No healer was found pick the highest scored unit currently in the array
		if (count($scoredarray) > 0) {
			$healer = array_slice($scoredarray, 0, 1);
		}
		else {
			$healer = false;
		}
	}
	return array($healerunit, $scoredarray);
}
function assign_defence_buff($scoredarray, $units) {
	//scored array is the units that need to be evaluated and $units is the skeleton and outline of the units that have the bonuses directed
	foreach ($scoredarray as $key => $val) {
		if (array_key_exists($key, $units) && $units[$key][1] == "defence_buff") {
			//This is a the healer we need and it has been listed in descending order
			$def = $key;
			unset($scoredarray[$key]);
			break;
		}
	}
	return array($def, $scoredarray);
}
function assign_unlimitedBB($scoredarray, $units) {
	//Returns two variabes, one is the unlimitedBB unit discovered and the other is the scored array with the unlimitedBB removed from it
	$diminish = 1;
	$unitneeded = 1;
	foreach ($scoredarray as $key => $val) {
		if(array_key_exists($key, $units) && $units[$key][2] == "UnlimitedBB" && $unitneeded > 0) {
			//An unlimited BB user
			$unlimitedBB = $key;
			unset($scoredarray[$key]);
			$unitneeded--;
		}
		elseif (array_key_exists($key, $units) && $units[$key][2] == "UnlimitedBB") {
			//An unlimited BB unit was already discovered, diminish the rest
			$diminish -= DIMINISH_MARGINAL_UNLIMITED_BB;
			$scoredarray[$key] *= $diminish;
		}
	}
	if (!isset($unlimitedBB)) {
		$unlimitedBB = false;
	}
	return array($unlimitedBB, $scoredarray);
}
//Function declaration ends now ----------------------------------------------------------------------------------------------------
if (isset($_GET['inputname'])) {
	$inputname = mysqli_real_escape_string($dbc, trim($_GET['inputname']));
	$parserequest = mysqli_query($dbc, "INSERT INTO add_unit VALUES ('$inputname')");
	if (!$parserequest) {
		echo '<font color="red">There was some error inserting the unit into the database.. Please try again letter. Sorry :(</font><br/>';
	}
	else {
		echo 'Thanks for adding in ' . $_GET['inputname'] . ' !';
	}
}
if (isset($_POST['submit'])) {	
	echo "<hr>";
	echo "<h3><center>Console Output</h3>";
	$units_array = trim($_POST['units'], '<br />');
	$printed = false;
	$unit_data_array = enumerate_array();
		/*This array contains all the units with their name and stats and have been converted to string => ints for PHP modification or Python modification. The array is this:
		array(
			unitname => array(hp, atk, def, rec));
		*/
	for ($x = 1; $x < ($_POST['squadnum'] + 1); $x++) {
		if (!isset($scoredarray)) {
			$units_array = explode(", ", trim($_POST['units'])); //These are the units that the user has entered
			$unsortedstatarray = stat_array($units_array, $unit_data_array); 
			$stats = array("hp", "atk", "def", "rec");
			//First assign a score to every single unit:
			$scoredarray = array(); //This is the array that will hold every single unit and their overall score
			foreach ($unsortedstatarray as $key => $val) {
				$scoredarray[$key] = assign_score($val[0], "hp") + assign_score($val[1], "atk") + assign_score($val[2], "def") +assign_score($val[3], "rec");
				//Assign the bonus if the unit is in the special unit array
				if (array_key_exists($key, $units)) {
					$scoredarray[$key] = $scoredarray[$key] * $units[$key][0];
				}
			}
			$temp_array = $scoredarray; //Assign temporary variable for scoring purposes
		}
		else{
			$units_array = $scoredarray;
			$temp_array = $units_array;
		}
		if (count($units_array) <= 2) {
			//This means less than 2 units. just echo out these units as the leaders
			foreach ($units_array as $key => $val) {
				echo "<h2>Suggested Squad $x:</h2>";
				echo "$key (Leader)";
			}
			break;
		}
		arsort($scoredarray);//This array is the array of units scored by their importance. Now, we will search for a viable leader
		//Now we are going to attempt to assemble a squad that closely follows with the guidelines put in place from the provided squad templates. $result will be the temporary holding variable for holding the array of the units that the user has submitted while the array is undergoing modification
		if (!$printed) {
			$printed = true;
		}
		$unitsneeded = 6;
		foreach ($squad_template as $unittype) {
				switch ($unittype) {
					case "Leader":
						$result = assign_leaders($scoredarray, $units);
						if (!$result) {
							//The function failed to find two leaders, this means less than two units were submitted. Echo out this error
							echo 'Less than 2 units were submitted, please submit at least 2 units in order for this to work';
							break 2;
							//Apply exception1here							
						}
						else {
							$leadersarray = $result[0];
							$scoredarray = $result[1];
							$unitsneeded -= count($leadersarray);
							$result_exception1 = assign_exception($scoredarray, $leadersarray, $units, 'EXCEPTION1');
							$leadersarray = $result_exception1[0];
							$scoredarray = $result_exception1[1];
						}
						break;
					case "Healer":
						$result = assign_healer($scoredarray, $units);
						$healer = $result[0];
						$scoredarray = $result[1];
						if (!$healer) {
							//No healer was found
							break;
						}
						else {
							$healer = $result[0];
							$unitsneeded--;
							break;
						}
					case "defence_buff":
						$result = assign_defence_buff($scoredarray, $units);
						$defence_buff = $result[0];
						$scoredarray = $result[1];
						if ($defence_buff) {
							$unitsneeded--;
						}
						break;
				}
		}
		$suggestedsquad = array_slice($scoredarray, 0, $unitsneeded);
		$array_score_sum = array();
		$squadStr = '';
		foreach ($leadersarray as $key => $val) {
			$squadStr .= "$key (Leader)<br/>";
			array_push($array_score_sum, $key);
		}
		if ($healer) {
			$squadStr .= "$healer (Healer) <br/>";
			array_push($array_score_sum, $healer);
		}
		if ($defence_buff) {
			$squadStr .= "$defence_buff  (defence_buff)<br/>";
			array_push($array_score_sum, $unlimitedBB);
		}
		foreach($suggestedsquad as $key => $val) {
			$squadStr .= "$key<br/>";
			array_push($array_score_sum, $key);
			unset($scoredarray[$key]);
		}
		$total_score = 0;
		foreach ($array_score_sum as $unit) {
			$total_score += $temp_array[$unit];
		}
		$viability = round((($total_score / IDEAL_TEAM) * 100), 2);
		if ($viability >= 100) {
			$viability == 100;
		}
		echo "<h3>Suggested Squad $x has viability percentage of $viability % and is:</h3>";
		echo $squadStr;
	}
}
?>
<html>
<form method="post" action=".">
<hr>
<h4>Squad Suggester</h4>
<p>
Please enter in the units (seperate each unit name by commas with space) you wish to form a squad with. Squads become more inaccruate the more squads you choose. Maxwell allows 3 squads, but you can ask the app to suggest a mazimum of 10. Ensure you spell the unit name correctly. Example: Tyrant Lilly Matah, Creator Maxwell, Thief God Zelnite<br/>
<label for ="number">Number of Squads to Suggest:</label> 
<input type="number" name="squadnum" min="1" max="10" value="1">
Units
<textarea name="units" style="width:500px;height:100px;" value="<?php if (!empty($_POST['units'])) { echo $_POST['units'];} else {
	echo '';}?>"></textarea>
<input type="submit" value="Suggest Squad" name="submit">
</form> 