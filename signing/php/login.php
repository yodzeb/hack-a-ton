<?

if(isset($_POST["username"]) && isset($_POST["password"]) {
    $user = $_POST["username"];
    $pass = $_POST["password"];
    

    $db = new SQLite3('../sql/base.sql', SQLITE3_OPEN_READONLY);
    $statement = $db->prepare('SELECT * from users where username=:user and password=:pass');
    $statement->bindValue(':user', $user);
    $statement->bindValue(':pass', $pass);

    $result = $statement->execute();
?>