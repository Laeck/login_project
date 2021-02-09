<?php
// On démarre notre session
session_start();

// Ce fichier sera appelé à chaque fois qu'on actualisera la page
require('src/connect.php');

// On vérifie si nous avons reçu quelque chose du formulaire
if (!empty($_POST['email']) && !empty($_POST['password'])) {

	require('src/connect.php');

	// VARIABLES
	$email = 		htmlspecialchars($_POST['email']);
	$password = 	htmlspecialchars($_POST['password']);

	// on vérifie la synthaxe de notre adresse email
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

		header('location: /espace_membre/index.php?error=1&message=Votre adresse mail est invalide !');
		exit();
	}

	// CHIFFRAGE DE MDP
	$password = "aq1" . sha1($password . "123") . "25";

	// EMAIL DEJA UTILISEE
	// Si on vérifie pas ca peut créer une erreur...
	$req = $db->prepare("SELECT count(*) as numberEmail from user WHERE email = ?");
	$req->execute((array($email)));

	while ($email_verification = $req->fetch()) {
		// On vérifie pas si c'est différent de 0 mais de 1 !
		// Car il faut qu'il y est 1 compte avec cette adresse là
		if ($email_verification['numberEmail'] != 1) {
			// NE SURTOUT PAS INFORMER QUE L'ADRESSE N'EST PAS BONNE !
			// Car une personne malveillante s'il trouve une adresse correcte 
			// Il comprendra donc que c'est le mot de passe qui n'est pas bon !
			header('location: /espace_membre/index.php?error=1&message=Impossible de vous authentifier correctement.');
			exit();
		}
	}

	// CONNEXION
	// Le mot de passe est le même mot de passe pour cette utilisateur avec cette adresse mail !
	$req = $db->prepare("SELECT * FROM user WHERE email = ?");
	$req->execute(array($email));

	// Tu mets tous les résultats dans notre tableau
	while ($user = $req->fetch()) {

		// On vérifie le password et si l'utilisateur n'a pas été bloqué
		if ($password == $user['password'] && $user['blocked'] == 0) {

			// S'il existe bien un utilisateur avec ce mot de passe
			// On créer la session
			// Si cette session existe on peut afficher des infos
			$_SESSION['connect'] = 1;
			// Permet de stocker l'email lors de la connexion (facultatif)
			$_SESSION['email'] = $user['email'];

			// Si la checkbox "se souvenir de moi" est coché
			// dans ce cas elle existe et on crée un coockie
			if(isset($_POST['auto'])){
				setcookie('auth', $user['secret'], time()+ 364*24*3600,
					'/', null, false, true);
			}

			header('location: /espace_membre/index.php?success=1');
			exit();
		} else {

			header('location: /espace_membre/index.php?error=1&message=Impossible de vous authentifier correctement.');
			exit();
		}
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>

<body>

	<?php include('src/header.php'); ?>

	<section>
		<div id="login-body">

			<!-- Enlever le form quand on est connecté -->
			<?php if (isset($_SESSION['connect'])) { ?>



				<h1> Bonjour ! </h1>

				<?php
				if (isset($_GET['success'])) {
					echo '<div class="alert success">Vous êtes connecté ! </div>';
				} ?>

				<p> Qu'allez vous visionner aujourd'hui ?</p>
				<small><a href="logout.php"> Déconnexion </a></small>

			<?php } else { ?>

				<h1>S'identifier</h1>

				<?php

				if (isset($_GET['error'])) {

					if (isset($_GET['message'])) {
						echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
					}
				} ?>

				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>


				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
			<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>