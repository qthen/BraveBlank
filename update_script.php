<?php
//Cron job to update the table rankings on vote float
define('HOST', 'localhost');
$dbc = mysqli_connect(HOST, 'kuhaku_admin', 'universa12A', 'kuhaku_voting')
or die ("Error connecting");
//First update all the sums
$updateStr = "UPDATE units SET sum = upvotes - downvotes";
$resultupdate = mysqli_query($dbc, $updateStr)
or die ("Error updating sums");
//Binary update method - invented by me! :D. Treat every single row as the first and second and apply the adjustment one by one
function switch_sort($array, $switch1, $switch2) {
	//Function for switch reversal
	$temp = $array[$switch1];
	$array[$switch1] = $array[$switch2];
	$array[$switch2] = $temp;
	return $array;
}
//Begin by fetching ALL unit information and sums and storing into global array
$fetchStr = "SELECT id, sum FROM units ORDER BY ranking ASC";
$resultfetch = mysqli_query($dbc, $fetchStr);
$globalarray = array();
while ($row = mysqli_fetch_array($resultfetch, MYSQLI_NUM)) {
	array_push($globalarray, $row);
}
//Now begin a binary comparison with every single row in the global array, only reversing rows if the one below has a higher sum vote than the one above it
for ($x = 0; $x <= 155; $x++) {
	if ($x == 165) {
		break;
	}
	$y = $x + 1;
	if ($globalarray[$x][1] < $globalarray[$y][1] && $globalarray[$y][1] != 0 ) {
		//Attempt reversal
		$id_1 = $globalarray[$x][0];
		$id_2 = $globalarray[$y][0];
		$downrankStr = "UPDATE units SET Ranking = Ranking + 1 WHERE id = '$id_1'";
		$resultupdate = mysqli_query($dbc, $downrankStr);
		$uprankStr = "UPDATE units SET Ranking = Ranking - 1 WHERE id = '$id_2'";
		$resultupdate = mysqli_query($dbc, $uprankStr);
		//Now begin the uasort reversal	
		$globalarray = switch_sort($globalarray, $x, $y);
	}
}
//Assign the tiers
//The top 9% will become Godly Tier, then the next 9% are borderline 1
//Tier 1 - Next 11%, Borderline 2 Tier - Next 11%
//Tier 2 - Next 15%, Borderline 3 Tier - Next 20%
//Tier 3 - Next 25%
/*$countStr = "SELECT COUNT(Ranking) FROM units";
$unitcount = mysqli_fetch_row(mysqli_query($dbc, $countStr))[0];
$rankarray = array();
$rankarray['Godly Tier'] = ceil(0.09 * $unitcount);
$rankarray['Borderline 1 Tier'] = ceil(0.09 * $unitcount);
$rankarray['Tier 1'] = ceil(0.11 * $unitcount);
$rankarray['Borderline 2 Tier'] = ceil(0.11 * $unitcount);
$rankarray['Tier 2'] = ceil(0.15 * $unitcount);
$rankarray['Borderline 3 Tier'] = ceil(0.20 * $unitcount);
$rankarray['Tier 3'] = ($unitcount - $rank1 - $rank2 - $rank3 - $rank4);
$count = 0;
foreach($rankarray as $tier => $val) {
	$val = $count + $val;
	$updaterankStr = "UPDATE units SET Tier = '$tier' WHERE Ranking BETWEEN $count AND $val";
	$resultupdate = mysqli_query($dbc, $updaterankStr);
	$count += $val;
}


*/

?>