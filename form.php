<?php

$q = "SELECT * FROM logentries WHERE date > date_sub(now(), interval 366 day) AND service NOT LIKE '%NOTIFICATION%'";
$log = $DB->get_results($q);
$services = array();

foreach ($log as $k => $v) {
	$service = explode(": ",$v->service);
        if (strstr($service[0],"SERVICE")) {
		$services[$service[1]] = 1;
        }		
}

?>

<form method="POST">

<select name="service">
	<?php
		foreach ($services as $k => $v) {
			echo "<option value='" . $k . "'>" . $k;
		}
	?>
</select>

<input type="hidden" name="step" value="service">

<div style="height:20px;"></div>

<input type="hidden" name="start" value="<?php echo date('Y-m-d', strtotime('yesterday')); ?>">
<input type="hidden" name="end" value="<?php echo date('Y-m-d', strtotime('yesterday')); ?>">

<input type="submit" name="submit" value="Get Scores">


</form>
