<?php
// Nota che l'operatore !== non è esistito fino alla versione 4.0.0-RC2
if ($handle = opendir('gallery/2006/')) {
    echo "Handle della directory: $handle\n";
    echo "File:\n";

   /* Questa è la maniera corretta di eseguire un loop all'interno di una directory. */
   while (false !== ($file = readdir($handle))) { 
       echo "$file\n";
   }

   /* Questa è la maniera SCORRETTA di eseguire un loop all'interno di una directory. */
   while ($file = readdir($handle)) { 
       echo "$file\n";
   }

   closedir($handle); 
}
?>

