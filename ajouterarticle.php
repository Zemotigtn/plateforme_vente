<?php
include "connexion.php";
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="ajouterarticle.css">
  <title>Ajouter article</title>
</head>

<body>
  <form action="" method="post">
    <fieldset>
      <legend>Formulaires d'enregistrement des articles</legend>
      <label for="id_num">ID_article :</label>
      <input type="number" id="id_num" name="id" value='<?php  ?>' /><br />
      <label for="id_des">Désignation:</label>
      <input type="text" id="id_des" name="des" value='<?php ?>' /><br />
      <label for="id_prix">Prix:</label>
      <input type="number" id="id_prix" name="prix" value='<?php  ?>' /><br />
      <label for="id_cat">Catégorie:</label>
      <input type="text" id="id_cat" name="cat" value='<?php ?>' /><br />
      <input type="submit" value="Envoyer" name="envoyer">
      <input type="reset" value="Annuler" name="annuler">
    </fieldset>
  </form>
  <?php
  // echo "Connexion réussie </br>";
  ?>
  <?php
  @$idarticle = $_POST["id"];
  @$designation = $_POST["des"];
  @$prix = $_POST["prix"];
  @$categorie = $_POST["cat"];
  @$submit = $_POST["envoyer"];
  @$annuler = $_POST["annuler"];
  if (isset($submit)) {
    if ((empty($idarticle) || empty($designation) || empty($prix) || empty($categorie))) {
      echo "<script>alert('Veuillez renseignez tous les champs !')</script>";
    } else {
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        @$idarticle = $_POST["id"];
        @$designation = $_POST["des"];
        @$prix = $_POST["prix"];
        @$categorie = $_POST["cat"];

        $stmt = $connect->prepare("INSERT INTO `article`(`id_article`, `designation`,`prix`,`catégorie`) VALUES ('$idarticle','$designation','$prix','$categorie')");
        // $stmt->bind_param("ss", $idarticle, $designation,$prix,$categorie);

        if ($stmt->execute()) {
          // echo "Données enregistrées avec succès.";
          header("location: voirarticle.php");
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
    <?php
    echo "<button><a href='voirarticle.php'>Retour</a></button>"
    ?>
  </footer>
</body>

</html>