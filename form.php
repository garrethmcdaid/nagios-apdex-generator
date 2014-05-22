<form method="POST">

<select name="service">
	<option value="latam.mttnow.com">latam.mttnow.com
</select>

<div style="height:20px;"></div>

<select name="monitor">
	<option value="Apdex - Email">Apdex - Email
	<option value="Apdex - Flight Status">Apdex - Flight Status
</select>

<div style="height:20px;"></div>

<input type="hidden" name="start" value="<?php echo date('Y-m-d', strtotime('yesterday')); ?>">
<input type="hidden" name="end" value="<?php echo date('Y-m-d', strtotime('yesterday')); ?>">


<submit name="submit" value="Get Score">


</form>
