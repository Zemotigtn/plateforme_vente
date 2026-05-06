<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste articles</title>
  <link rel="stylesheet" href="voirarticle.css">
</head>

<body>

  <?php
  include "connexion.php";
  ?>
  <?php
  $sql = "SELECT * FROM article ORDER BY catégorie";
  $row = $connect->query($sql);
  if (!$row) {
    echo "Lecture impossible";
  } else {
    $nbart = $row->num_rows;
    echo "<h3>Tous nos articles par catégorie</h3>";
    echo "<button style='padding:5px'><a href='ajouterarticle.php'> Ajouter</a></button>";
    echo "<h4>Il y a $nbart articles en magasin</h4>";
    echo "<table border=\"1\">";
    echo
    "<tr><th>Code 
article</th> 
<th>Description</th> 
<th>Prix</th>
<th>Catégorie</th></tr>";
    while ($ligne = $row->fetch_array(MYSQLI_NUM)) {
      echo "<tr>";
      foreach ($ligne as $valeur) {
        echo "<td> $valeur </td>";
      }
      echo "</tr>";
    }
    echo "</table>";
  }
  $row->free();
  $connect->close();
  ?>
  <footer>
    <?php
    echo "<button><a href='accueil.php'>Quitter</a></button>"
    ?>
  </footer>
</body>

</html>