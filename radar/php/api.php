<?php

session_start();
  
$targets = array(
		 array(
		       "name" => "Cathedrale",
		       "lat"  => "49.253885",
		       "lon"  => "4.033999"
		       ),
		 array(
		       "name" => "Champfleury",
		       "lat"  => "49.196064",
		       "lon"  => "4.013958",
		       ),
		 array(
		       "name" => "Université",
		       "lat"  => "49.239009",
		       "lon"  => "4.003143",
		       ),
		 array (
			"name" => "Cormontreuil",
			"lat"  => "49.222306",
			"lon"  => "4.055586"
			)
		 );


$probes     = array(array(
			  "name"=> "Chenay",
			  "ip"  => "192.168.0.43",
			  "tcp" => "81",
			  "lag" => "3040",
			  // default: 3040
			  "lat" => "49.297255",
			  "lon" => "3.93023",
			  ),
		    array("name" => "Witry-les-Reims",
			  "ip"   => "192.168.0.44",
			  "tcp"  => "82",
			  "lag"  => "3090",
			  //default: 3090
			  "lat"  => "49.291993",
			  "lon"  => "4.127941",
			  ),
		    array(
			  "name" => "Sermiers",
			  "ip"  => "192.168.0.45",
			  "tcp" => "83",
			  "lag" => "3050",
			  //default: 3050
			  "lat" => "49.162849",
			  "lon" => "3.983402",
			  )
		    );

// Default / Univ:
// 3040 / 3090 / 3050

// Cathe
// 3100 / 3090 / 3130

// Champfleury
// 3200 / 3230 / 3050

// Cormontreuil
// 3130 / 3080 / 3055

include("functions.php");

$command = $_GET["cmd"];
$_SESSION["CLIENT_IP"] = $_SERVER['REMOTE_ADDR'];

if ($command === "targets") {
  foreach ($targets as &$t) {
    if ($_SESSION["TARGET"][$t["name"]] > 0) {
      $t["flagged"] =1;
    }
  }
  print json_encode ($targets);
}

elseif ($command === "update") {
  print (json_encode ($_SESSION));
}

elseif ($command === "probes") {
  print json_encode ( $probes );
}

elseif ($command === "ping") {
  $date = new DateTime();
  $unix = $date->getTimestamp();

  if (array_key_exists('LAST_PING', $_SESSION) &&
      ($unix - $_SESSION['LAST_PING']) < 8 ) {
    print "Don't be too aggressive, come back later";
  }
  else {
    $_SESSION['LAST_PING'] = $unix;
    
    if (array_key_exists('probe', $_GET) && $_GET["probe"] === "tcp") {
      $_SESSION["PROBING"] = "tcp";
    }
    elseif(array_key_exists('probe',$_GET) && $_GET["probe"] === "icmp") {
      $_SESSION["PROBING"] = "icmp";
    }
    do_ping($probes, $targets);
  }
}
    


?>