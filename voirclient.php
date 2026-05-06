<?php
include "connexion.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="voirclient.css">
  <title>Liste clients</title>
</head>

<body>
  <H3>LISTE DES CLIENTS</H3>
  <?php
  $sql = "SELECT * FROM client";
  $result = $connect->query($sql);
  if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Id_Client</th><th>Nom</th><th>Prenom</th><th>Age</th><th>Adresse</th><th>Ville</th><th>Mail</th></tr>";
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row["id_client"] . "</td>";
      echo "<td>" . $row["nom"] . "</td>";
      echo "<td>" . $row["prenom"] . "</td>";
      echo "<td>" . $row["age"] . "</td>";
      echo "<td>" . $row["adresse"] . "</td>";
      echo "<td>" . $row["ville"] . "</td>";
      echo "<td>" . $row["mail"] . "</td>";
      echo "</tr>";
    }
    echo "</table>";
  }
  ?>
  <footer>
    <?php
    echo "<button><a href='accueil.php'>Quitter</a></button>"
    ?>
  </footer>
</body>

</html>
<!-- //Récupérer les informations du client dans la base de données et afficher cette liste sans le bouton Ajouter
// Dans effectuer commande on créee un formulaire qui va enregistrer les informations du client , commande et contenir -->