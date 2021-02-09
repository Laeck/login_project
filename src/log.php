<?php

    // Si le coockie 'auth' existe mais que la session 'connect'
    // n'existe pas : lancement du script
    // Cela evite de lancer les REQ à chaque fois qu'on est connecté
    if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])){

        //Vartiable
        $secret = htmlspecialchars($_COOKIE['auth']);

        //Verification
        require('./connect.php');

        // Req pour vérifier que l'utilisateur existe
        $req = $db->prepare("SELECT count(*) as numberAccount FROM user WHERE secret = ?");
        $req->execute(array($secret));

        while($user = $req->fetch()){

            if($user['numberAccount'] == 1) {

                // Req qui recupère toutes les infos
                $reqUser = $db->prepare("SELECT * FROM user WHERE secret = ?");
                $reqUser->execute(array($secret));

                while($userAccount = $reqUser->fetch()){
                    

                    $_SESSION['connect'] = 1;
                    $_SESSION['email'] = $userAccount['email'];        
                    
                }
            }
        }
    }

    if(isset($_SESSION['connect'])) {

        require('./connect.php');

        $reqUser = $db->prepare("SELECT * FROM user WHERE secret = ?");
        $reqUser->execute(array($_SESSION['email']));

        while($userAccount = $reqUser->fetch()){

            if($userAccount['blocked'] == 1) {
                header('location: ../logout.php');
                exit();
            }
    }
}