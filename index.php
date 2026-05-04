<?php
define("host","localhost");
      define("user","root");
      define("pass","");
      define("nameBDD","essaiebdd");
      $connect=new mysqli(host,user,pass,nameBDD);
      if(!$connect){
        die("Connexion échouée:".$connect->error);
      }
      else{  
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="index.css" />
    <title>Plateforme</title>
  </head>
  <body>
    <section class="main">
      <form class="color" name="form1" id="f1" method="POST">
        <div>
          <label for="login">Login:</label><br />
          <input class="login" id="log" name="login" type="text"/><br />
        </div>
        <div>
          <label for="password">Password:</label><br />
          <input class="password" name="password" type="password" id="password" /><br />
        </div>
        <input  type="submit" value="Se connecter" class="subnit"  name="connecter"/>
        <button type="submit" value="" class="inscrire" /> <a href="formulaireuser.php">S'inscrire</a></button>
      </form>
       <?php
      echo " <span style='display:none;'>Connexion à la BDD réussie</span> </br>";
}
?>
    </section>
<?php
  @$login=$_POST["login"];
  @$password=$_POST["password"];
  @$seconnecter=$_POST["connecter"];
  if(isset($seconnecter)){
     if(empty($login)||empty($password)){
      echo "<script>alert('Veuillez renseignez tous les champs !')</script>";
  }
  else{
    if($_SERVER["REQUEST_METHOD"]=="POST"){ //on vérifie que le formulaire est envoyé avec la méthode POST
      $sql=$connect->prepare("SELECT * FROM user WHERE login= ?"); //on prépare notre requete sql en entrant le login en paramètre
      mysqli_stmt_bind_param($sql,"s", $login); // "s" = string
      mysqli_stmt_execute($sql);
      $resultat=mysqli_stmt_get_result($sql);
      $utilisateur=mysqli_fetch_assoc($resultat);
      // $user = $result->fetch_assoc();// exécute la requete et donne le résultat
        if($utilisateur && $password==$utilisateur['password']){
          header("location: accueil.php");
          exit;
        }
        else{
          echo "<script>alert('Utilisateur introuvable. Veuillez vous inscrire !')</script>";
        }
      }
    }
  }
?>
  </body>


</html>
