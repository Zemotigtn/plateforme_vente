<?php
define("host","localhost");
      define("user","root");
      define("pass","");
      define("nameBDD","essaiebdd");
      $connect=new mysqli(host,user,pass,nameBDD);
      if(!$connect){
        die("Connexion échouée:".$connect->error);
      }
?>