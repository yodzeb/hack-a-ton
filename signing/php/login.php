<form action="/login.php" method="POST">

  <div class="container">
    <label><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="username" required>

    <label><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="password" required>

  </div>

  <div class="container" style="background-color:#f1f1f1">
    <button type="submit" >Login</button>
  </div>
</form>

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
	 if ($arr['id'] == 1) {
	   echo file_get_contents("../flag.txt");
	 }
       }
       else {
	 echo "no res";
       }
   }
?>