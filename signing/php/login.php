<!DOCTYPE html>
<html>
<head>
<title>Cert-O-Matic</title>
<link rel="stylesheet" href="css/app.css">
<head>

<body>

<h1> Cert-O-Matic </h1>
<h2> Restricted access.</h2>

<div class="login_link">
   <a href="signing.php">Cert-O-Matic</a>
</div>

<div class="all">

<div class="box">

<?php

   if(isset($_POST["username"]) && isset($_POST["password"])) {
       $user = $_POST["username"];
       $pass = $_POST["password"];
       
       
       $db = new SQLite3('../sql/base.sql', SQLITE3_OPEN_READONLY);
       $statement = $db->prepare('SELECT * from users where username=:user and password=:pass');
       $statement->bindValue(':user', $user);
       $statement->bindValue(':pass', $pass);
       
       $results = $statement->execute();
       
       if (($results instanceof Sqlite3Result)) {
	 $arr = $results->fetchArray();

	 if ($arr) {
	   if ($arr['id'] == 1) {
	     echo "I think you deserve your certification:<br><br><b>";
	     echo file_get_contents("../flag.txt")."</b>";
	   }
	   else {
	     echo "You're not admin";
	   }
	 }

	 else {
	   echo "<i>Invalid username or password</i>";
	 }
       }
   }
   else {
?>
   <form action="/login.php" method="POST">
   
   <div >
   <label><b>Username</b></label>
   </div><div>
   <input type="text" placeholder="Enter Username" name="username" required>
   </div><div>
   <label><b>Password</b></label>
   </div><div>
   <input type="password" placeholder="Enter Password" name="password" required>   
   </div>
    <button type="submit" >Login</button>
   </form>

<?php
   }
?>


</div>
</body>
</html>