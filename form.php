<?php

$status = '';
$erreurPrenom = '';
$erreurNom = '';
$erreurEnt = '';
$erreurMail = '';
$erreurTel = '';
$erreurMsg = '';
$erreurEnvoi = '';

$prenom = isset($_POST['prenom']) ? strip_tags(stripslashes($_POST['prenom'])) : '';
$nom = isset($_POST['nom']) ? strip_tags(stripslashes($_POST['nom'])) : '';
$ent = isset($_POST['entreprise']) ? strip_tags(stripslashes($_POST['entreprise'])) : '';
$mail = isset($_POST['email']) ? strip_tags(stripslashes($_POST['email'])) : '';
$tel = isset($_POST['tel']) ? strip_tags($_POST['tel']) : '';
$profil = isset($_POST['profil']) ? strip_tags(stripslashes($_POST['profil'])) : '';
$message = isset($_POST['message']) ? strip_tags(stripslashes($_POST['message'])) : '';

$mailto = '';

if(isset($_POST['submit'])){
	
 	if(empty($prenom)){
 		$erreurPrenom = 'Le champ Prénom est obligatoire';
 		$status = 'erreur';
 	}
 	if(empty($nom)){
 		$erreurNom = 'Le champ Nom est obligatoire';
 		$status = 'erreur'; 
 	}
 	if(empty($ent)){
 		$erreurEnt = 'Le champ Entreprise est obligatoire';
 		$status = 'erreur'; 
 	}
 	if(empty($message)){
 		$erreurMsg = 'Le champ Message est obligatoire';
 		$status = 'erreur'; 
 	}
 	if(empty($mail)){
 		$erreurMail = 'Le champ Email est obligatoire';
 		$status = 'erreur'; 
 	}else{
 		if(!preg_match('/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i', $mail)){
 			$erreurMail = "L'adresse email est invalide";
 			$status = 'erreur';
 		}
 	}
 	if(empty($tel)) {
 		$erreurTel = 'Le champ Téléphone est obligatoire';
 		$status = 'erreur'; 
 	}else {
 		if(!(strlen($tel) < 20 && strlen($tel) > 9 && preg_match("/^\+?[^.\-][0-9\.\- ]+$/", $tel))){
 			$erreurTel = 'Le numéro de téléphone est incorrect';
 			$status = 'erreur'; 
 		}
 	}

 	if(empty($status)){

        $nom = sprintf('%s %s', $prenom, $nom);
 		$subject = "Nouveau message provenant de ...";
 		$headers = 'From: "' . $nom . '" <' . $mail . '>' . "\r\n" .
 				   'Reply-To: ' . $mail . "\r\n";

 		$content = 'De: ' . $nom . "\r\n" .
 				   $profil . ': ' . $ent . "\r\n" .
 				   'Adresse e-mail: ' . $mail . "\r\n" .
 				   'Numéro de téléphone: ' . $tel . "\r\n\r\n\r\n" .
 				   'Message: ' . "\r\n" . $message;

        $sent = mail($mailto, $subject, $content, $headers);

 		if($sent){
 			$status = 'succes';
 		}else{ 
 			$status = 'erreur'; 	
 			$erreurEnvoi = "Nous sommes désolés, une erreur est survenue. Veuillez réessayer!";
 		}
 	}
}

?>


<?php if($status == 'succes'){ ?>

	<p>Merci, votre message a bien été envoyé. Nous vous répondrons dans les plus bref délais!</p>

<?php }else{ ?>

	<?php if($status == 'erreur'){
		echo "<p><b>Oups! Nous n'avons pas pu envoyer votre demande:</b><br/>";
		if($erreurPrenom != '') echo $erreurPrenom .'<br/>';
		if($erreurNom != '') echo $erreurNom .'<br/>';
		if($erreurEnt != '') echo $erreurEnt .'<br/>';
		if($erreurMail != '') echo $erreurMail .'<br/>';
		if($erreurTel != '') echo $erreurTel .'<br/>';
		if($erreurEnvoi != '') echo $erreurEnvoi;
		echo '</p>';
	}

	?>

	<form id='formContact' action='#' method='POST'>
		<fieldset class='profil'>
		    <label for='profil'>Votre profil *</label>
	    	<select class='select' name='profil' id='profil' required>
    		    <option value='entreprise' selected="selected">Entreprise</option>
    		    <option value='agence'>Agence</option>
    		    <option value='ecole'>Ecole</option>
    		    <option value='organisation'>Autre</option>
	    	</select>
		</fieldset>
		
		<fieldset class='<?php if($erreurPrenom != '') echo ' error'; ?>'>
			<label for='prenom'>Prénom *</label>
			<input type='text' name='prenom' id='prenom' value='<?php echo $prenom; ?>' required>
		</fieldset>
		
		<fieldset class='<?php if($erreurNom != '') echo ' error'; ?>'>
			<label for='nom'>Nom *</label>
			<input type='text' name='nom' id='nom' value='<?php echo $nom; ?>' required>
		</fieldset>
		
		<fieldset class="<?php if($erreurEnt != '') echo 'error'; ?>">
			<label for='entreprise' id='labelEnt'>Entreprise *</label>
			<input type='text' name='entreprise' id='entreprise' value='<?php echo $ent; ?>' required>
		</fieldset>
		
		<fieldset class='<?php if($erreurMail != '') echo ' error'; ?>'>
			<label for='email'>Email *</label>
			<input type='email' name='email' id='email' value='<?php echo $mail; ?>' required pattern="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?">
		</fieldset>
		
		<fieldset class='<?php if($erreurTel != '') echo ' error'; ?>'>
			<label for='tel'>Téléphone *</label>
			<input type='tel' name='tel' id='tel' value='<?php echo $tel; ?>' required pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$">
		</fieldset>
		
		<fieldset>
			<label for='message'>Message *</label>
			<textarea name='message' id='message' required><?php echo $message; ?></textarea>
		</fieldset>

		<button type='submit' name='submit' id='submit' form='formContact'>Envoyer</button>
	</form>

<?php } ?>