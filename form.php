<?php

$q = "SELECT * FROM logentries WHERE date > date_sub(now(), interval 365 day)";
$log = $DB->get_results($q);
$services = array();

foreach ($log as $k => $v) {
	$service = explode(": ",$v->service);
	$services[$service[0]] = 1;		
}

print_r($services);

?>


<form method="POST">

<select name="service">
	<option value="latam.mttnow.com">latam.mttnow.com
</select>

<div style="height:20px;"></div>

<select name="monitor">
	<option value="Apdex Monitor">Apdex Monitor
	<option value="Apdex - Email">Apdex - Email
	<option value="Apdex - Flight Status">Apdex - Flight Status
</select>

<div style="height:20px;"></div>

<input type="hidden" name="start" value="<?php echo date('Y-m-d', strtotime('yesterday')); ?>">
<input type="hidden" name="end" value="<?php echo date('Y-m-d', strtotime('yesterday')); ?>">


<input type="submit" name="submit" value="Get Score">


</form>
