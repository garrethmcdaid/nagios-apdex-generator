<?php

date_default_timezone_set('Europe/Dublin');

include('header.php');

if (empty($_REQUEST['service'])) {
	include('form.php');	
} else {

	//VARS
	$total_ok = 0;
	$total_warning = 0;
	$total_critical = 0;
	
	//DATABASE
	$GLOBALS['dbserver'] = '127.0.0.1';
	$GLOBALS['dbname'] = 'nagios';
	$GLOBALS['dbuser'] = 'root';
	$GLOBALS['dbpass'] = '';
	
	
	include('db.class.php');
	global $DB;
	
	if (!empty($_REQUEST)) {
		$start = $_REQUEST['start'];
		$end = $_REQUEST['end'];
		$service = $_REQUEST['service'];
		$monitor = $_REQUEST['monitor'];
	} else {
		$start = $argv[1];
		$end = $argv[2];
		$service = $argv[3];
		$monitor = $argv[4];
	}
	
	$samples = number_format((date('U',strtotime($end . " 23:59:59")) - date('U',strtotime($start . " 00:00:00"))) / 300,"0",".","");
		
	$q = "SELECT * FROM logentries WHERE date >= '" . $start . " 00:00:00' AND date < '" . $end . " 23:59:59' AND service LIKE '%" . $service . "%' AND monitor = '" . $monitor . "'";
	$data = $DB->get_results($q);
	
	echo $q . "<br>";
	
	//print_r($data);
	
	$q = "SELECT * FROM logentries WHERE date < '" . $data[0]->date . "' AND service LIKE '%" . $service . "%' AND monitor = '" . $monitor . "' ORDER BY date DESC LIMIT 1";
	$r = $DB->get_result($q);
	
	echo $q . "<br>";
	
	if (!$r) die("Insufficient data<br>");
	
	$ds = date('U',strtotime($start . " 00:00:00"));
	
	$i = $data[0]->seconds - $ds;
	
	switch($r->type) {
		
		case "OK":
			$total_ok = $i;
			break;
		case "WARNING":
			$total_warning = $i;
			break;
		case "CRITICAL":
			$total_critical = $i;
			break;
		
	}
	
	foreach ($data as $k => $v) {
	
		if ($k === 0) continue;
	
		$p = $k -1;
		//echo "Previous key = " . $p . "<br>"; 
		
		//echo "Current key seconds = " . $v->seconds . "<br>";
		//echo "Previous key seconds = " . $data[$p]->seconds . "<br>";
	
		
		$i = $v->seconds - $data[$p]->seconds;
		//echo "Interval = " . $i . "<br>";
	
		switch($data[$p]->type) {
			
			case "OK":
				//echo "Previous type = OK<br>";
				$total_ok = $total_ok + $i;
				break;
			case "WARNING":
				//echo "Previous type = WARNING<br>";
				$total_warning = $total_warning + $i;
				break;
			case "CRITICAL":
				//echo "Previous type = CRITICAL<br>";
				$total_critical = $total_critical + $i;
				break;
			
		}
		
	}
	
	$de = date('U',strtotime($end . " 23:59:59"));
	
	$i = $de - end($data)->seconds;
	
	switch(end($data)->type) {
		
		case "OK":
			$total_ok = $total_ok + $i;
			break;
		case "WARNING":
			$total_warning = $total_warning + $i;
			break;
		case "CRITICAL":
			$total_critical = $total_critical + $i;
			break;
		
	}
	
	
	
	$samples_ok = number_format($total_ok/60,"0",".","")/5;
	$samples_warning = number_format($total_warning/60,"0",".","")/5;
	$samples_critical = number_format($total_critical/60,"0",".","")/5;
	
	echo "~~~~~~~~~~~~~~~~~~~<br>";
	
	echo "SAMPLES: " . $samples . "<br>";
	
	echo "~~~~~~~~~~~~~~~~~~~<br>";
	
	echo "SATISFIED SECS: " . $total_ok . "<br>";
	echo "TOLERATING SECS: " . $total_warning . "<br>";
	echo "FRUSTRATED SECS: " . $total_critical . "<br>";
	
	echo "~~~~~~~~~~~~~~~~~~~<br>";
	
	echo "SATISFIED MINS: " . number_format($total_ok/60,"0",".",",") . "<br>";
	echo "TOLERATING MINS: " . number_format($total_warning/60,"0",".",",") . "<br>";
	echo "FRUSTRATED MINS: " . number_format($total_critical/60,"0",".",",") . "<br>";
	
	echo "~~~~~~~~~~~~~~~~~~~<br>";
	
	echo "SATISFIED SAMPLES: " . $samples_ok . "<br>";
	echo "TOLERATING SAMPLES: " . $samples_warning . "<br>";
	echo "FRUSTRATED SAMPLES: " . $samples_critical . "<br>";
	
	echo "~~~~~~~~~~~~~~~~~~~<br>";
	
	$apdex = ($samples_ok + ($samples_warning/2))/$samples;
	
	echo "APDEX FORMULA: " . $samples_ok . " + " . "(" . $samples_warning . "/2) / " . $samples . "<br>";
	
	echo "APDEX SCORE: " . number_format($apdex,"4",".","") . "<br>";

}
	
include('footer.php');


?>
