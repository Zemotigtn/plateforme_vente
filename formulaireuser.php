<?php
  include "connexion.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="formulaireuser.css">
    <title>UserPHP</title>
</head>
<body>
    <form  action="" method="post">
      <fieldset>
        <legend>Formulaires d'enregistrement des articles</legend>
        <label for="id_user">ID_USER :</label>
        <input type="number" id="id_user" name="id" value='<?php  ?>' /><br />

        <label for="id_nom">nom:</label>
        <input type="text" id="id_nom" name="nom" value='<?php ?>' /><br />

        <label for="id_prenom">prenom:</label>
        <input type="text" id="id_prenom" name="prenom" value='<?php  ?>' /><br />

        <label for="id_contact">contact:</label>
        <input type="number" id="id_contact" name="contact" value='<?php ?>' /><br />

        <label for="id_login">login:</label>
        <input type="text" id="id_login" name="login" value='<?php ?>' /><br />

        <label for="id_password">password:</label>
        <input type="text" id="id_password" name="password" value='<?php ?>' /><br />

        <input type="submit" value="Envoyer" name="envoyer">
        <input type="reset" value="Annuler" name="annuler">
      </fieldset>
    </form>
    <?php
      @$iduser=$_POST["id"];
      @$nom=$_POST["nom"];
      @$prenom=$_POST["prenom"];
      @$contact=$_POST["contact"];
      @$login=$_POST["login"];
      @$password=$_POST["password"];
      @$submit=$_POST["envoyer"];
      if(isset($submit)){
        if((empty($iduser) || empty($nom) || empty($prenom) || empty($contact)||empty($login)||empty($password))){
      echo "<script>alert('Veuillez renseignez tous les champs !')</script>";
        }
        else{
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
        @$iduser=$_POST["id"];
         @$nom=$_POST["nom"];
      @$prenom=$_POST["prenom"];
      @$contact=$_POST["contact"];
      @$login=$_POST["login"];
      @$password=$_POST["password"];
      // $password=password_hash($password,PASSWORD_BCRYPT);
      $stmt = $connect->prepare("INSERT INTO `user`(`id_user`, `nom`,`prenom`,`contact`,`login`,`password`) VALUES ('$iduser','$nom','$prenom','$contact','$login','$password')");
      // $stmt->bind_param("ss", $idarticle, $designation,$prix,$categorie);

      if ($stmt->execute()) {
        // echo "Données enregistrées avec succès.";
        header("location: index.php");
      } else {
        echo "Erreur: " . $stmt->error;
        }

      $stmt->close();
      $connect->close();
}
        }
        
      }
      

    ?>
    <footer>
      <button>
        <a href="index.php"> Retour </a>
      </button>
    </footer>
</body>
</html>