<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
   Select image to upload:
    <input type="file" name="toupload" id="toupload">
    <input type="submit" value="Upload Image" name="submit">
</form>


<?php

$db = new SQLite3('../sql/base.sql', SQLITE3_OPEN_READONLY);

$cacert = file_get_contents("certs/cert.pem");
$privkey = array(file_get_contents("certs/key.pem"), "aaaa");

$uploadOk = 1;

if(isset($_POST["submit"])) {

  $cert_req = file_get_contents($_FILES["toupload"]["tmp_name"]);
  

  $usercert = openssl_csr_sign($cert_req, $cacert, $privkey, 365);
  openssl_x509_export($usercert, $certout);
  
  $cert_data = openssl_x509_parse ($certout);
  $city      = $cert_data["issuer"]["L"];
  
  $sql_req = "SELECT city,lat,lng FROM cities WHERE city='$city';";
  echo $sql_req;
  $results = $db->query($sql_req);
  if (($results instanceof Sqlite3Result)) {
    $arr = $results->fetchArray();
    echo '<a href=https://www.google.com/maps?hl=en&q='.$arr["lat"].'+'.$arr["lng"].'>';
    echo $arr["city"];
    echo '</a>';    
  }
  else {
    echo $arr["city"]. ' (<i>not found</i>)';
  }

}

?>


</body>
</html>
