<?php
// On démarre notre SESSION
session_start();

// Ce fichier sera appelé à chaque fois qu'on actualisera la page
require('src/connect.php');

// Cela nous permet d'éviter d'acceder à la page s'inscrire alors que nous sommes
// connecté 
if (isset($_SESSION['connect'])) {
	header('location: /espace_membre/index.php');
	exit();
}

// On vérifie si l'on a recu quelque chose via le formulaire
if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])) {

	// On a besoin de notre bdd uniquement lors de l'envoi du form
	require('src/connect.php');

	// VARIABLES
	$email 			= htmlspecialchars($_POST['email']);
	$password 		= htmlspecialchars($_POST['password']);
	$passwordTwo 	= htmlspecialchars($_POST['password_two']);

	if ($password != $passwordTwo) {

		header('location: /espace_membre/inscription.php?error=1&message=Les mots de passes ne correspondent pas !');
		exit();
	}

	// ADRESSE EMAIL VALIDE
	// lorsque l'adresse mail n'est pas valide
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		header('location: /espace_membre/inscription.php?error=1&message=Votre adresse mail est invalide !');
		exit();
	}

	// EMAIL DEJA UTILISEE
	$req = $db->prepare("SELECT count(*) as numberEmail from user WHERE email = ?");
	$req->execute((array($email)));

	while($email_verification = $req->fetch()) {

		if($email_verification['numberEmail'] != 0) {

			header('location: /espace_membre/inscription.php?error=1&message=Votre adresse mail est déjà utilisé');
			exit();
		}
	}

	// HASH
	$secret = sha1($email).time();
	$secret = sha1($ecret).time();

	// CHIFFRAGE DE MDP
	$password = "aq1".sha1($password."123")."25";

	// ENVOI
	$req = $db->prepare("INSERT INTO user(email, password, secret) VALUES(?,?,?)");
	$req->execute(array($email, $password, $secret));

	header('location: /espace_membre/inscription.php?success=1');
	exit();
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
			<h1>S'inscrire</h1>

			<?php if (isset($_GET['error'])) {
				// Il faut aussi vérifier la variable message !
				// Car si on met la variable error sans message
				// On peut créer un bug !
				if (isset($_GET['message'])) {

					echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
				}
			} else if(isset($_GET['success'])){

				echo'<div class="alert success"> Vous êtes désormais inscrit !
				<a href="index.php"> Connectez-vous </a>
				</div>';

			} ?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>