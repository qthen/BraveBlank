<?php
$dbc = mysqli_connect(HOST, USER, PASS, DATABASE)
or die ("Error connecting");
function reverse_boolean($arg) {
	if ($arg == 1) {
		return 0;
	}
	else {
		return 1;
	}
}
//Table sorting code
$sort_array = array(0 => "DESC", 1 => "ASC"); //Array to store different sorting possibilites
if (isset($_GET['sort'])) {
	//A request has been made to sort the table, parse this request
	$type_sort = $_GET['type_sort'];
	$sort_order = $_GET['sort_order'];
	$query_sel = "SELECT * FROM units ORDER BY $type_sort $sort_order";
	$result_sel = mysqli_query($dbc, $query_sel);
	$$type_sort = reverse_boolean(array_search($sort_order, $sort_array));
}
//Free floating table
if (isset($_GET['id'])) {
	//This means that the user has voted so check first if they voted prior to 24 hours
	$id = $_GET['id'];
	$action = $_GET['action'];
	if (isset($_COOKIE['units'])) {
		$unit_array = explode(',', $_COOKIE['units']);
		if (!in_array($id, $unit_array)) {
			//Only if the user has not voted in the past 24 hours for this unit
			$id = $_GET['id'];
			$action = $_GET['action'];
			if ($action == 1) {
				//The user is upvoting so cast the upvote
				$query_upvote = "UPDATE units SET upvotes = upvotes + 1 WHERE id = '$id'";
				$result_upvote = mysqli_query($dbc, $query_upvote)
				or die ("Error querying database");
			}
			else {
				//The user is downvoting
				$query_downvote = "UPDATE units SET downvotes = downvotes + 1 WHERE id = '$id'";
				$result_downvote = mysqli_query($dbc, $query_downvote)
				or die ("Error querying database");
			}
			array_push($unit_array, $id);
			$unit_array = implode(',', $unit_array);
			setcookie('units', $unit_array, time() + 43200);
		}
	}
	else {
		//The user has never voted, allow the vote without array check
		$id = $_GET['id'];
		$action = $_GET['action'];
		$unit_array = array();	
		if ($action == 1) {
			//The user is upvoting so cast the upvote
			$query_upvote = "UPDATE units SET upvotes = upvotes + 1 WHERE id = '$id'";
			$result_upvote = mysqli_query($dbc, $query_upvote)
			or die ("Error querying database");
		}
		else {
			//The user is downvoting
			$query_downvote = "UPDATE units SET downvotes = downvotes + 1 WHERE id = '$id'";
			$result_downvote = mysqli_query($dbc, $query_downvote)
			or die ("Error querying database");
		}
		array_push($unit_array, $id);
		$unit_array = implode(',', $unit_array);
		setcookie('units', $unit_array, time() + 43200);
	}
}
$query_sel = "SELECT * FROM units ORDER BY Ranking ASC";
$result_sel = mysqli_query($dbc, $query_sel)	
or die ("Error selecting units");
$img_parameters = '<img src="http://www.braveblank.com/wp-content/uploads/2014/12/sort.png" width="20" height="40"></img>';
echo '<table id="tablepress-19" class="tablepress tablepress-id-19">
<caption style="caption-side:bottom;text-align:left;border:none;background:none;margin:0;padding:0;"><a href="http://www.braveblank.com/wp-admin/admin.php?page=tablepress&action=edit&table_id=19" >Edit</a></caption>
<thead>
<tr class="row-1 odd">
<th class="column-1"><div>Overall Ranking' . $img_parameters . '</div></th><th class="column-2"><div>Unit</div></th><th class="column-3"><div>Tier</div></th><th class="column-4"><div>Element</div></th><th class="column-5"><div>HP&nbsp;&nbsp;<a href=".?' . $img_parameters . '</div></th><th class="column-6"><div>ATK</div></th><th class="column-7"><div>DEF' . $img_parameters . '</div></th><th class="column-8"><div>REC' . $img_parameters . '</div></th><th class="column-9"><div>Total' . $img_parameters . '</div></th><th class = "column-10"><div>Today\'s Votes</div></th>
</tr>
</thead>
<tbody class="row-hover">';
$x = 2;
while ($row = mysqli_fetch_array($result_sel, MYSQLI_NUM)) {
	list($id, $ranking, $unit, $tier, $element, $hp, $atk, $def, $rec, $total, $last_position, $upvotes, $downvotes, $sum) = $row;
	$unitStr = explode(">", $unit);
	if ($ranking == 1) {
		if ($ranking > $last_position) {
			//This means the unit went down from last week's close
			$tableStr = '<td class = "column-1" width = "13%"><img src = "http://www.braveblank.com/wp-content/uploads/2014/12/goldstar.png" height = "20" width = "20"></img>&nbsp;&nbsp;<img src="http://www.braveblank.com/wp-content/uploads/2014/12/down.png" height="40" width = "30"></img><font size="6" color = "black">' . $ranking . '</font><font size="3"><hr>&nbsp;&nbsp;Last Week:' . $last_position . '</td><td class="column-2">' . $unitStr[0] . "onclick=\"javascript:_gaq.push(['_trackEvent','outbound-article','http://touchandswipe.github.io']);\">" . $unitStr[1] . ">" . $unitStr[2] . ">" . $unitStr[3] . ">" . $unitStr[4] . ">" . "</td><td class=\"column-3\">" . $tier . "</td><td class=\"column-4\">" . $element . "</td><td class=\"column-5\">" . $hp . "</td><td class=\"column-6\">" . $atk . '</td><td class="column-7">' . $def . '</td><td class="column-8">' . $rec . '</td><td class="column-9">' . $total . '</td><td class="column-10" width = "10%"><a href=".?id=' . $id . '&action=1">' . $upvotes . '&nbsp;Upvotes<hr></a><a href=".?id=' . $id . '&action=2">' . $downvotes . '&nbsp;Downvotes</a></td></tr>';					
		}
		elseif ($ranking == $last_position) {
			//This means that the ranking did not change from last weeks close
			$tableStr = '<td class = "column-1" width = "13%"><img src = "http://www.braveblank.com/wp-content/uploads/2014/12/goldstar.png" height = "20" width = "20"></img>&nbsp;&nbsp;<img src="http://www.braveblank.com/wp-content/uploads/2014/12/same.png" height="40" width = "30"></img><font size="6" color = "black">' . $ranking . '</font><font size="3"><hr>&nbsp;&nbsp;Last Week:' . $last_position . '</td><td class="column-2">' . $unitStr[0] . "onclick=\"javascript:_gaq.push(['_trackEvent','outbound-article','http://touchandswipe.github.io']);\">" . $unitStr[1] . ">" . $unitStr[2] . ">" . $unitStr[3] . ">" . $unitStr[4] . ">" . "</td><td class=\"column-3\">" . $tier . "</td><td class=\"column-4\">" . $element . "</td><td class=\"column-5\">" . $hp . "</td><td class=\"column-6\">" . $atk . '</td><td class="column-7">' . $def . '</td><td class="column-8">' . $rec . '</td><td class="column-9">' . $total . '</td><td class="column-10" width = "10%"><a href=".?id=' . $id . '&action=1">' . $upvotes . '&nbsp;Upvotes<hr></a><a href=".?id=' . $id . '&action=2">' . $downvotes . '&nbsp;Downvotes</a></td></tr>';		
		}
		else {
			$tableStr = '<td class = "column-1" width = "13%"><img src = "http://www.braveblank.com/wp-content/uploads/2014/12/goldstar.png" height = "20" width = "20"></img>&nbsp;&nbsp;<img src="http://www.braveblank.com/wp-content/uploads/2014/12/up.png" height="40" width = "30"></img><font size="6" color = "black">' . $ranking . '</font><font size="3"><hr>&nbsp;&nbsp;Last Week:' . $last_position . '</td><td class="column-2">' . $unitStr[0] . "onclick=\"javascript:_gaq.push(['_trackEvent','outbound-article','http://touchandswipe.github.io']);\">" . $unitStr[1] . ">" . $unitStr[2] . ">" . $unitStr[3] . ">" . $unitStr[4] . ">" . "</td><td class=\"column-3\">" . $tier . "</td><td class=\"column-4\">" . $element . "</td><td class=\"column-5\">" . $hp . "</td><td class=\"column-6\">" . $atk . '</td><td class="column-7">' . $def . '</td><td class="column-8">' . $rec . '</td><td class="column-9">' . $total . '</td><td class="column-10" width = "10%"><a href=".?id=' . $id . '&action=1">' . $upvotes . '&nbsp;Upvotes<hr></a><a href=".?id=' . $id . '&action=2">' . $downvotes . '&nbsp;Downvotes</a></td></tr>';		
		}
	}
	else {
		if ($ranking > $last_position) {
			//This means the unit went down from last week's close
			$tableStr = '<td class = "column-1" width = "13%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="http://www.braveblank.com/wp-content/uploads/2014/12/down.png" height="40" width = "30"></img><font size="6" color = "black">' . $ranking . '</font><font size="3"><hr>&nbsp;&nbsp;Last Week:' . $last_position . '</td><td class="column-2">' . $unitStr[0] . "onclick=\"javascript:_gaq.push(['_trackEvent','outbound-article','http://touchandswipe.github.io']);\">" . $unitStr[1] . ">" . $unitStr[2] . ">" . $unitStr[3] . ">" . $unitStr[4] . ">" . "</td><td class=\"column-3\">" . $tier . "</td><td class=\"column-4\">" . $element . "</td><td class=\"column-5\">" . $hp . "</td><td class=\"column-6\">" . $atk . '</td><td class="column-7">' . $def . '</td><td class="column-8">' . $rec . '</td><td class="column-9">' . $total . '</td><td class="column-10" width = "10%"><a href=".?id=' . $id . '&action=1">' . $upvotes . '&nbsp;Upvotes<hr></a><a href=".?id=' . $id . '&action=2">' . $downvotes . '&nbsp;Downvotes</a></td></tr>';					
		}
		elseif ($ranking == $last_position) {
			//This means that the ranking did not change from last weeks close
			$tableStr = '<td class = "column-1" width = "13%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="http://www.braveblank.com/wp-content/uploads/2014/12/same.png" height="40" width = "30"></img><font size="6" color = "black">' . $ranking . '</font><font size="3"><hr>&nbsp;&nbsp;Last Week:' . $last_position . '</td><td class="column-2">' . $unitStr[0] . "onclick=\"javascript:_gaq.push(['_trackEvent','outbound-article','http://touchandswipe.github.io']);\">" . $unitStr[1] . ">" . $unitStr[2] . ">" . $unitStr[3] . ">" . $unitStr[4] . ">" . "</td><td class=\"column-3\">" . $tier . "</td><td class=\"column-4\">" . $element . "</td><td class=\"column-5\">" . $hp . "</td><td class=\"column-6\">" . $atk . '</td><td class="column-7">' . $def . '</td><td class="column-8">' . $rec . '</td><td class="column-9">' . $total . '</td><td class="column-10" width = "10%"><a href=".?id=' . $id . '&action=1">' . $upvotes . '&nbsp;Upvotes<hr></a><a href=".?id=' . $id . '&action=2">' . $downvotes . '&nbsp;Downvotes</a></td></tr>';		
		}
		else {
			$tableStr = '<td class = "column-1" width = "13%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="http://www.braveblank.com/wp-content/uploads/2014/12/up.png" height="40" width = "30"></img><font size="6" color = "black">' . $ranking . '</font><font size="3"><hr>&nbsp;&nbsp;Last Week:' . $last_position . '</td><td class="column-2">' . $unitStr[0] . "onclick=\"javascript:_gaq.push(['_trackEvent','outbound-article','http://touchandswipe.github.io']);\">" . $unitStr[1] . ">" . $unitStr[2] . ">" . $unitStr[3] . ">" . $unitStr[4] . ">" . "</td><td class=\"column-3\">" . $tier . "</td><td class=\"column-4\">" . $element . "</td><td class=\"column-5\">" . $hp . "</td><td class=\"column-6\">" . $atk . '</td><td class="column-7">' . $def . '</td><td class="column-8">' . $rec . '</td><td class="column-9">' . $total . '</td><td class="column-10" width = "10%"><a href=".?id=' . $id . '&action=1">' . $upvotes . '&nbsp;Upvotes<hr></a><a href=".?id=' . $id . '&action=2">' . $downvotes . '&nbsp;Downvotes</a></td></tr>';		
		}
	}
	if ($x %2 == 0) {
		$bin = 'even';
	}
	else {
		$bin = 'odd';
	}
	echo "<tr class=\"row-$x $bin\">";
	echo $tableStr;
	$x++;
}
echo '</tbody></table>';
?>