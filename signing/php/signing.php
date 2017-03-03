<!DOCTYPE html>
<html>
<head>
<title>Cert-O-Matic</title>
<link rel="stylesheet" href="css/app.css">
<head>

<body>

<h1> Cert-O-Matic </h1>
<h2> The One-Click CA</h2>

<div class="login_link">
   <a href="login.php">Login</a>
</div>
<div class="all">

<?php

$db = new SQLite3('../sql/base.sql', SQLITE3_OPEN_READONLY);

function print_city ($db, $city) {

  
  $sql_req = "SELECT city,lat,lng FROM zzcities WHERE city='$city';";
   $results = $db->query($sql_req);
  if (!$results) {
    echo  "(<i>$city not found in BD </i>(Error: <i>".$db->lastErrorMsg()."</i>)";
  }
  elseif (($results instanceof Sqlite3Result)) {
    $arr = $results->fetchArray();
	  
    echo '<a href="https://www.google.com/maps?z=10&hl=en&q='.$arr["lat"].'+'.$arr["lng"].'"  target="_blank">';
    echo $city;
    echo '</a>';    
    
  }
  else {
    echo "<i> Unknown error</i>";
  }
  
}

function print_country ($db, $country) {
  $sql_req = "SELECT name from zzcountries WHERE code='$country'";
  $results = $db->query($sql_req);

  if (!$results) {
    echo  "(<i>$country not found  in BD </i>(Error: <i>".$db->lastErrorMsg()."</i>)";
  }
  elseif (($results instanceof Sqlite3Result)) {
    $arr = $results->fetchArray();
    echo '<a href="https://en.wikipedia.org/wiki/'.$arr["Name"].'" target="_blank">'.$arr["Name"].'</a>';
  }
  else {
    echo "<i> Unknown error</i>";
  }

}

$cacert = file_get_contents("certs/cert.pem");
$privkey = array(file_get_contents("certs/key.pem"), "aaaa");

$uploadOk = 1;


if(isset($_POST["submit"])) {
  echo "<div class='box'>";
  $cert_req = file_get_contents($_FILES["toupload"]["tmp_name"]);
  

  $usercert = openssl_csr_sign($cert_req, $cacert, $privkey, 365);
  openssl_x509_export($usercert, $certout);
  
  $cert_data = openssl_x509_parse ($certout);
  
  //echo var_dump ($cert_data);

  echo "<h4>General informations:</h4><div class='info'><ul>";
  foreach (["serialNumber","validFrom", "validTo"] as $k) {
    echo "<li><b>$k: </b>";
    if (preg_match ("/valid/", $k)) {
      echo gmdate("Y-m-d H:i:s",$cert_data[$k]/1000);

    }
    else {
      echo $cert_data[$k];
    }
    echo "</li>";
  }
  echo '</ul></div>';

  
  foreach (["subject", "issuer",] as $m) {
    echo "<h4>$m</h4>";
    echo "<div class='info'><ul>";

    foreach ($cert_data[$m] as $key => $value) {
      echo "<li><b>$key: </b>";
      if ($key === "L") {
	$city = $cert_data[$m]["L"];
	print_city ($db, $city);
      }
      elseif ($key === "C") {
	$country =  $cert_data[$m]["C"];
	if (strlen($country) < 10) {
	  print_country ($db, $country);
	}
	else {
	  echo "<i>Not a country name...</i></li>";
	}
      }
      else {
	echo "$value";
      }
      echo "</li>";
    }
    echo "</ul></div>";
  }
  echo "</ul>";

  echo "<h4>Your Signed Certificate:</h4>";
  echo "<pre>";
  echo $certout;
  echo "</pre>";
  echo "</div>";
}
else {
?>
<div class="box">
   <form action="" method="post" enctype="multipart/form-data">
   <h3>Upload your CSR here:</h3>
   <div class="upload">
   <input type="file" name="toupload" id="toupload">
   <input type="submit" value="Upload now!" name="submit">
   </form>
   </div>
</div>

<?php
}


?>

</div>
</body>
</html>
