<!DOCTYPE html>
<html>
<head>
<title>Cert-O-Matic</title>
<link rel="stylesheet" href="css/app.css">
<head>

<body style=margin:0 onload="for(s=window.screen,w=q.width=s.width,h=q.height=s.height,m=Math.random,p=[],i=0;i<256;p[i++]=1);setInterval('9Style=\'rgba(0,0,0,.05)\'9Rect(0,0,w,h)9Style=\'#0F0\';p.map(function(v,i){9Text(String.fromCharCode(33+m()*80),i*10,v);p[i]=v>758+m()*1e4?0:v+10})'.split(9).join(';q.getContext(\'2d\').fill'),33)">

<canvas id=q></canvas>


<div class="login_link">
   <a href="signing.php"><img src="images/key.png" height="30px"></a>
</div>


<div class="all">

<div>
<h1> Cert-O-Matic </h1>
<h2> Restricted access</h2>


</div>


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
	     echo file_get_contents("../flag/flag.txt")."</b>";
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