<?php
include "connexion.php";
?>
<?php
@$login = $_POST["login"];
@$password = $_POST["password"];
@$seconnecter = $_POST["connecter"];
if (isset($seconnecter)) {
  if (empty($login) || empty($password)) {
    echo "<script>alert('Veuillez renseignez tous les champs !')</script>";
  } else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") { //on vérifie que le formulaire est envoyé avec la méthode POST
      $sql = $connect->prepare("SELECT * FROM user WHERE login= ?"); //on prépare notre requete sql en entrant le login en paramètre
      mysqli_stmt_bind_param($sql, "s", $login); // "s" = string
      mysqli_stmt_execute($sql);
      $resultat = mysqli_stmt_get_result($sql);
      $utilisateur = mysqli_fetch_assoc($resultat);
      // $user = $result->fetch_assoc();// exécute la requete et donne le résultat
      if ($utilisateur && $password == $utilisateur['password']) {
        header("location: accueil.php");
        exit;
      } else {
        echo "<script>alert('Utilisateur introuvable. Veuillez vous inscrire !')</script>";
      }
    }
  }
}
?>
<?php
include "index.html";
?>