// header.php
<?php
	if(!session_id())
		session_start();

	$_POST = $_SESSION;
	global $errorComment;
	$errorComment = isset($_POST['errorcomment']) ? $_POST['errorcomment'] : false;

	if(!isset($_GET['error'])){
		if($errorComment){
			$_POST['errorcomment'] = false;
		}
		session_destroy();
	}
?>


// functions.php
<?php
	function custom_die_handler($message, $title='', $args=array()){
	    if(empty($_POST['errorcomment'])){
	        $_POST['errorcomment'] = $message;
	    }

	    if(!session_id())
	        session_start();

	    $_SESSION = $_POST;
	    session_write_close();

	    $url = strtok(wp_get_referer(), '?');
	    header('Location: ' . $url . '?error=true#comments');
	    die();
	}
	function get_custom_die_handler(){
	    return 'custom_die_handler';
	}
	add_filter('wp_die_handler', 'get_custom_die_handler');
?>


//comments.php
<?php global $errorComment; if($errorComment){ ?>
	<p class='error'>
		<?php echo $errorComment; ?>
	</p>
<?php } ?>