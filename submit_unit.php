<!--
This is code for a simple unit submission. This is what it looks like in HTML: http://www.braveblank.com/submit-unit/
It is my hope that this will be able for users to submit a unit AND for updating current units. For example, if a user
wants to update a unit page, the edit will be redirected to this script and they can update the unit through a similar script 
like this, although the main problem I am running into is how to redirect wordpress's edit function to this page. I've been
considering just creating a href link and think this is the simplest solution but this will require downgrading all users's roles
to subscribor-->

<html>
<form method = "post" action = "<?php echo $_SERVER['PHP_SELF'];?>">
<h2>Unit Name</h2>
<input type="text" name="unitname" style="width:500px">
<?php
function sticky_val($arg) {
	echo "$_POST[$arg];?>";
}
if (isset($_POST['submit'])) {
	//Search to see if they have already made this unit
	if (!empty($_POST['unitname'])) {
		require_once('unitconnectvars.php');
		$dbc = mysqli_connect(HOST, USER, PASS, DATABASE);
		$unitname = mysqli_real_escape_string($dbc, trim($_POST['unitname']));
		$checkStr = "SELECT * FROM units_table WHERE name = ";
		$resultcheck = mysqli_query($dbc, $checkStr);
		if (mysqli_num_rows($resultcheck) == 0) {
			//The unit does not exist, allow the post to be created
			foreach ($_POST as $key => $value) {
				//This would be the code to loop through the $_POST array and post or update the unit
			}
		}
	}
	else {
		echo "<h3><font color=\"red\">You did not enter a unit name.....</font></h3>";
	}
}
echo'<table id="tablepress-34" class="tablepress tablepress-id-34">
<caption style="caption-side:bottom;text-align:left;border:none;background:none;margin:0;padding:0;"><a href="http://www.braveblank.com/wp-admin/admin.php?page=tablepress&action=edit&table_id=34" ></a></caption>
<tbody class="row-hover">
<tr class="row-1 odd">
	<td class="column-1">Unit Number<br />
</td><td class="column-2"> ' . $unitnum . '';
echo "<input type=\"number\" name=\"unitnum\" min=\"1\">";
echo '<br />
</td>
</tr>
<tr class="row-2 even">
	<td class="column-1"> Element<br />
</td><td class="column-2">'. $element . '';?>
<select>
<option value="Fire">Fire</option>
<option value="Water">Water</option>
<option value="Earth">Earth</option>
<option value="Thunder">Thunder</option>
<option value="Light">Light</option>
<option value="Dark">Dark</option>
</select>
<?php
echo'
</td>
</tr>
<tr class="row-3 odd">
	<td class="column-1"> Rarity<br />
</td><td class="column-2">' . $rarity . '<input type="number" min="1" max="6" name="rarity">*</td>
</tr>
<tr class="row-4 even">
	<td class="column-1"> Max Lv. <br />
</td><td class="column-2">' . $maxlvl . '<input type="number" min="1" max="100"><br />
</td>
</tr>
<tr class="row-5 odd">
	<td class="column-1"> Cost<br />
</td><td class="column-2">'. $cost . '<input type="number" min="1"></a><br />
</td>
</tr>
<tr class="row-6 even">
	<td class="column-1">Hit Count</td><td class="column-2">' . $hitcount. '<input type="number" min="1"></td>
</tr>
<tr class="row-7 odd">
	<td class="column-1">BB fill</td><td class="column-2">' . $bbbc .  '<input type="number" min="1" max="100"></td>
</tr>
<tr class="row-8 even">
	<td class="column-1">SBB fill</td><td class="column-2">' . $sbbbc . '<input type="number" min="1" max="100"></td>
</tr>
</tbody>
</table>';

echo'<table id="tablepress-31" class="tablepress tablepress-id-31">
<caption style="caption-side:bottom;text-align:left;border:none;background:none;margin:0;padding:0;"><a href="http://www.braveblank.com/wp-admin/admin.php?page=tablepress&action=edit&table_id=31" ></a></caption>
<thead>
<tr class="row-1 odd">
	<th class="column-1"><div>Type</div></th><th class="column-2"><div>HP</div></th><th class="column-3"><div>ATK</div></th><th class="column-4"><div>DEF</div></th><th class="column-5"><div>REC</div></th>
</tr>
</thead>
<tbody class="row-hover">
<tr class="row-2 even">
	<td class="column-1">Lord</td><td class="column-2">' . $lordhp . '<input type="number" min="1" style="width:100px" name="lordhp"></td><td class="column-3">' . $lordatk . '<input type="number" min="1" style="width:100px" name="lordatk"></td><td class="column-4">' . $lorddef . '<input type="number" min="1" style="width:100px" name="lorddef"></td><td class="column-5">' . $lordrec . '<input type="number" min="1" style="width:100px" name="lordrec"></td>
</tr>
<tr class="row-3 odd">
	<td class="column-1">Anima</td><td class="column-2">' . $animahp . '<input type="number" min="1" style="width:100px" name="animahp"></td><td class="column-3">' . $lordatk . 'Same Value as Lord</td><td class="column-4">' . $lorddef . 'Same Value as Lord</td><td class="column-5">' . $animarec . '<input type="number" min="1" style="width:100px" name="animarec"></td>
</tr>
<tr class="row-4 even">
	<td class="column-1">Breaker</td><td class="column-2">' . $lordhp . 'Same Value as Lord</td><td class="column-3">' . $breakeratk . '<input type="number" min="1" name="breakeratk" style="width:100px"></td><td class="column-4">' . $breakerdef . '<input type="number" min="1" name="breakerdef" style="width:100px"></td><td class="column-5">' . $lordrec . 'Same Value as Lord</td>
</tr>
<tr class="row-5 odd">
	<td class="column-1">Guardian</td><td class="column-2">' . $lordhp . 'Same Value as Lord</td><td class="column-3">' . $guardianatk . '<input type="number" min="1" name="guardianatk" style="width:100px"></td><td class="column-4">' . $guardianddef . '<input type="number" min="1" style="width:100px" name="guardiandef" ></td><td class="column-5">' . $lordrec . 'Same Value as Lord</td>
</tr>
<tr class="row-6 even">
	<td class="column-1">Oracle</td><td class="column-2"><input type="number" min="1" style="width:100px" name="oraclehp"></td><td class="column-3">Same Value as Lord</td><td class="column-4">Same Value as Lord</td><td class="column-5"><input type="number" min="1" style="width:100px" name="oraclerec"></td>
</tr>
<tr class="row-7 odd">
	<td class="column-1">Max Bonus</td><td class="column-2"><a href="http://www.braveblank.com/?attachment_id=1423"  rel="attachment wp-att-1423"><img data-id="1423"  src="http://www.braveblank.com/wp-content/uploads/2014/12/20.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1423" /></a>+' . $imphp . '<input type="number" min="1" style="width:100px" name="imphp"></td><td class="column-3"><a href="http://www.braveblank.com/?attachment_id=1422"  rel="attachment wp-att-1422"><img data-id="1422"  src="http://www.braveblank.com/wp-content/uploads/2014/12/20-1.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1422" /></a>+' . $impatk . '<input type="number" min="1" style="width:100px" name="impatk"></td><td class="column-4"><a href="http://www.braveblank.com/?attachment_id=1421"  rel="attachment wp-att-1421"><img data-id="1421"  src="http://www.braveblank.com/wp-content/uploads/2014/12/20-2.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1421" /></a>+' . $impdef . '<input type="number" min="1" style="width:100px" name="impdef"></td><td class="column-5"><a href="http://www.braveblank.com/?attachment_id=1420"  rel="attachment wp-att-1420"><img data-id="1420"  src="http://www.braveblank.com/wp-content/uploads/2014/12/20-3.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1420" /></a>+' . $imprec . '<input type="number" min="1" style="width:100px" name="imprec"></td>
</tr>
</tbody>
</table>';

echo'<table id="tablepress-32" class="tablepress tablepress-id-32">
<caption style="caption-side:bottom;text-align:left;border:none;background:none;margin:0;padding:0;"><a href="http://www.braveblank.com/wp-admin/admin.php?page=tablepress&action=edit&table_id=32" ></a></caption>
<thead>
<tr class="row-1 odd">
	<th class="column-1"><div>Skill</div></th><th class="column-2"><div>Effect</div></th>
</tr>
</thead>
<tbody class="row-hover">
<tr class="row-2 even">
	<td class="column-1"><strong>Leader Skill: ' . $lsname . '<input type="text" style="width: 300px;" name="lsname"></strong></td><td class="column-2">' . $ls . '<textarea name="ls"  style="width: 300px; height: 100px;"></textarea></td>
</tr>
<tr class="row-3 odd">
	<td class="column-1"><strong>Brave Burst: ' . $bbname . '<input type="text" style="width: 300px;" name="bbname"></td><td class="column-2">' . $bb . '<textarea name="bb" style="width: 300px; height: 150px;"></textarea></td>
</tr>
<tr class="row-4 even">
	<td class="column-1"><strong>Super Brave Burst: ' . $sbbname . '<input type="text" style="width: 300px;" name="sbbname"></textarea></td><td class="column-2">' . $sbb . '<textarea name="sbb" style="width: 300px; height: 150px;"></textarea></td>
</tr>
</tbody>
</table>';
?>
<h3>Description</h3>
<textarea name="description" style="width:600px; height:100px;"></textarea>
<input type="submit" value="Submit Unit" name="submit">
</p>
</form>


