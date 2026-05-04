<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="voiruser.css">
    <title>Liste des users</title>
</head>
<body>
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
      // echo " <span style='display:none;'>Connexion à la BDD réussie</span> </br>"
        echo "<h2>Liste des utilisateurs</h2>"."</br>";
        $sql="SELECT nom,prenom FROM user";
        $result=$connect->query($sql);
        if($result->num_rows>0){
          echo "<table border='1' cellpadding='5' cellspacing='0'>";
          echo "<tr><th>Index</th><th>Nom</th><th>Prénom</th></tr>";
          $index=1;
          while($row=$result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $index . "</td>";
            echo "<td>" . $row['nom'] . "</td>";
            echo "<td>" . $row['prenom'] . "</td>";
            echo "</tr>";
            $index++;
        }
        

        echo "</table>";
        }
        else{
          echo "Aucun client trouvé";
        }
      }
?>
<footer>
      <?php
        echo "<button style='margin-top: 20px;'><a href='accueil.php'>Quitter</a></button>"
      ?>
    </footer>
</body>
</html>
