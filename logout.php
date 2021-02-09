<?php

    session_start(); //Initialise la session avec un numéro unique
    session_unset(); //Génère un nouveau numéro de session
    session_destroy(); //Détruit la session
    // Comme la date du coockie sera inferieur à la date actuelle
    // ça rendra le coockie obsolète et donc le supprimera ! 
    setcookie('auth', '', time()-1, '/', null, false, true); 

    header('location: /espace_membre/index.php');
    exit();
