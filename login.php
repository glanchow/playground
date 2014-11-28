<!--div id="update-nag">Avertissement : la connexion au Back Office System se fait d�sormais avec l'adresse email.</div-->
<div id="content">
<?php
// Table
$table = 'users';

function index(){
global $db_system, $db, $table;	

	ini_set('display_errors', 0);
	
	// Deconnexion
	if(isset($_GET['action']) == 'logout')
	{	
		if(isset($_SESSION))
			session_destroy();
			
		setcookie('site_id', NULL);
		$message = message('Vous �tes d�sormais d�connect�.', 'info');
	}
	
	// Form
	if(isset($_POST['form']))
	{	
		$post['email'] = clean($_POST['form_email']);
		$post['password'] = clean($_POST['form_password']);
		
		foreach($post as $key => $value)
			if(empty($value)) $message = message('Merci de remplir tous les champs.', 'warning');
		
		if(!$message)
		{			
			$query = $db->select('users', array('WHERE' => 'email = "'.$post['email'].'" AND password = "'.md5($post['password']).'"'), $db_system);
			if($db->num_rows($query))
			{
				$row = $db->fetch_object($query);
				
				if($row->valide)
				{
					$_SESSION['session_user_id'] = $row->id;
					$_SESSION['session_user_surname'] = $row->prenom;
					$_SESSION['session_user_name'] = $row->nom;
					
					$db->update('users', array('last_log' => time()), 'id = "'.$row->id.'"', $db_system);
						
					if($_GET['url'] != 'login')
						redirect($_GET['url']);		
					else
						redirect('accueil');	
				}
				else
					$message = message('Les acc�s de cet utilisateur ont �t� d�sactiv�.', 'error');
			}
			else
				$message = message('Ces acc�s sont inconnus dans notre base de donn�es.', 'error');
				
		} // if
	
	} // form
?>
<h1>Connexion</h1>
<?= $message; ?>
<form action="<?= base_url($_GET['url']); ?>" method="post" id="form">
    <?= input_text('Adresse email *', 'email'); ?>
    <?= input_password('Mot de passe *', 'password'); ?>
    <?= input_submit('Me connecter', false); ?>
</form>
<p class="right" style="font-size:11px;">&raquo; <a href="<?= base_url($_GET['url'].'&ft=password'); ?>">Mot de passe perdu</a></p>
<?php
}
?>

<?php
function password(){
global $db, $table;	

	// Class
	require(_system_folder.'class/Email_class.php');
	
	// Form
	if(isset($_POST['form'])){
		
		$post['email'] = clean($_POST['form_email']);
		
		foreach($post as $key => $value)
			if(empty($value)) $message = message('Merci d\'indiquer votre email.', 'warning');
		
		if(!$message)
		{	
			$query = $db->select('users', array('WHERE' => 'email = "'.$post['email'].'"'));
			if($db->num_rows($query)){
				
				$str = str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789');
				$random = substr($str, 1, 5);
				
				$row = $db->fetch_object($query);
				
				$db->update($table, array('password' => md5($random)), 'id = "'.$row->id.'"');
				
				$email_message = '
				Bonjour '.$row->prenom.',
				
				suite � votre demande voici votre nouveau mot de passe : '.$random.'
				';
				
				// Email
				$email = new Email();
				$email->to($row->email);
				$email->subject('Votre nouveau mot de passe');
				$email->message($email_message);
				$email->send();
			
				$message = message('Votre nouveau mot de passe vous a �t� envoy�.', 'confirm', 'Retour � la page de connnexion', base_url($_GET['url']));
				
				$ok = true;
			}
			else
				$message = message('Cet utilisateur est inconnu dans notre base de donn�e.', 'error');
				
		} // if
	
	} // form
?>
<h1>Mot de passe perdu</h1>
<?= $message; ?>

<?php if(!$ok){ ?>
<p>Merci d'indiquer votre adresse email afin de recevoir un nouveau mot de passe :</p>
<form action="<?= base_url($_GET['url'].'&ft='.$_GET['ft']); ?>" method="post">
    <?= input_text('Email *', 'email'); ?>
    <?= input_submit('Envoyer'); ?>
</form>
<?php } ?>
<?php
}
?>

<?php
switch($_GET['ft'])
{		
	default:
	index();
	break;
	
	case 'logout':
	logout();
	break;
	
	case 'password':
	password();
	break;
}
?>
</div>