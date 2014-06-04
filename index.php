<?php

date_default_timezone_set('Europe/Dublin');

include('db.class.php');
global $DB;

//DATABASE
$GLOBALS['dbserver'] = '127.0.0.1';
$GLOBALS['dbname'] = 'nagios';
$GLOBALS['dbuser'] = 'root';
$GLOBALS['dbpass'] = '';

include('header.php');

$q = "SELECT * FROM logentries WHERE date > date_sub(now(), interval 366 day) AND service NOT LIKE '%FLAPPING%' AND service NOT LIKE '%NOTIFICATION%' AND service LIKE '%" . $_REQUEST['service'] . "%'";
$log = $DB->get_results($q);
$monitors = array();

foreach ($log as $k => $v) {
	$monitors[$v->monitor] = 1;
}


if (empty($_REQUEST['service'])) {
		
} else {

	
	$reports = array();
	$reports['yesterday'] = array('yesterday','today', 'Yesterday');
	$reports['last_7_days'] = array('8 days ago 00:00','today', 'Last 7 Days');
	$reports['last_28_days'] = array('29 days ago 00:00','today', 'Last 28 days');
	$reports['last_92_days'] = array('93 days ago 00:00','today', 'Last 92 days');
	$reports['last_365_days'] = array('366 days ago 00:00','today', 'Last year');

	echo "<br>";
	
	foreach ($monitors as $monitor => $v) {
		
		echo "<div>";
	
		foreach ($reports as $report => $v) {
		
			//VARS
			$total_ok = 0;
			$total_warning = 0;
			$total_critical = 0;
			
			$interval = date('U', strtotime($v[1])) - date('U', strtotime($v[0]));
	
			$samples = number_format($interval/300,"0",".","");
			
			$q = "SELECT * FROM logentries WHERE seconds >= " . date('U', strtotime($v[0])) . " AND seconds <= " . date('U', strtotime($v[1])) . " AND service LIKE '%" . $_REQUEST['service'] . "%' AND monitor = '" . $monitor . "' AND service NOT LIKE '%FLAPPING%' AND service NOT LIKE '%NOTIFICATION%' ORDER BY date";
			$data = $DB->get_results($q);
			
			//echo $q . "<br>";
			
			echo '<div style="float:left;width:240px;height:320px;border-bottom:1px solid #6CC0E5;margin-bottom:14px;">';
								
			$q = "SELECT * FROM logentries WHERE date < '" . date('Y-m-d H:i:s',strtotime($v[0])) . "' AND service LIKE '%" . $_REQUEST['service'] . "%' AND monitor = '" . $monitor . "' AND service NOT LIKE '%FLAPPING%' AND service NOT LIKE '%NOTIFICATION%' ORDER BY date DESC LIMIT 1";
			$r = $DB->get_result($q);
		
			//echo $q . "<br>";
		
				
			$i = $data[0]->seconds - date('U',strtotime($v[0]));
			
			//echo $i . "<br>";
		
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
		
			foreach ($data as $k => $vv) {
			
				if ($k === 0) continue;
			
				$p = $k -1;
				//echo "Previous key = " . $p . "<br>"; 
				
				//echo "Current key seconds = " . $v->seconds . "<br>";
				//echo "Previous key seconds = " . $data[$p]->seconds . "<br>";
			
				
				$i = $vv->seconds - $data[$p]->seconds;
				//echo $data[$p]->type . " Interval = " . $i . "<br>";
			
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
						
			$i = date('U',strtotime($v[1])) - end($data)->seconds;
						
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
				
			echo "<b>" . $monitor . " | " . $v[2] . "</b><br>";
			
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
			
			if ($apdex < .99) {
				$cl = 'red';
			} else if ($apdex < 1) {
				$cl = 'orange';
			} else {
				$cl = 'green';
			}
			
			echo "<b style='color:" . $cl . "'>APDEX SCORE: " . number_format($apdex,"4",".","") . "</b><br>";
			
			echo "~~~~~~~~~~~~~~~~~~~<br>";
			
			if (!$data) { echo("No data available<br>");} 
			
			if (!$r) { echo("Insufficient historical data<br>");} 
			
			echo '<br><br>';
			
			echo '</div>';

	
		}

		echo '</div><br style="clear:both;">';

	}
}
		
include('footer.php');


?>
