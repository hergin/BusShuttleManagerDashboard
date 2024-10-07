<?php

// This is an example that shows how to incorporate uLogin into a webpage.
// It showcases nonces, login authentication, account creation, deletion and
// remember-me functionality, all at the same time in a single page.
// Because of the number of functions shown and all the comments,
// it seems a little bit longish, but fear not.

// This is the one and only public include file for uLogin.
// Include it once on every authentication and for every protected page.
require_once('ulogin/config/all.inc.php');
require_once('ulogin/main.inc.php');
$loginFailed;

// Start a secure session if none is running
if (!sses_running())
	sses_start();

// We define some functions to log in and log out,
// as well as to determine if the user is logged in.
// This is needed because uLogin does not handle access control
// itself.

function isAppLoggedIn(){
	return isset($_SESSION['uid']) && isset($_SESSION['username']) && isset($_SESSION['loggedIn']) && ($_SESSION['loggedIn']===true);
}

function appLogin($uid, $username, $ulogin){
	$_SESSION['uid'] = $uid;
	$_SESSION['username'] = $username;
	$_SESSION['loggedIn'] = true;

	if (isset($_SESSION['appRememberMeRequested']) && ($_SESSION['appRememberMeRequested'] === true))
	{
		// Enable remember-me
		if ( !$ulogin->SetAutologin($username, true))
			echo "cannot enable autologin<br>";

		unset($_SESSION['appRememberMeRequested']);
	}
	else
	{
		// Disable remember-me
		if ( !$ulogin->SetAutologin($username, false))
			echo 'cannot disable autologin<br>';
	}
}

function appLoginFail($uid, $username, $ulogin){
	// Note, in case of a failed login, $uid, $username or both
	// might not be set (might be NULL).
	$loginFailed = "Login failed, please try again.";
	//echo "<div align='center' class='alert-danger'>Login failed, please try again.</div>";
}

function appLogout(){
  // When a user explicitly logs out you'll definetely want to disable
  // autologin for the same user. For demonstration purposes,
  // we don't do that here so that the autologin function remains
  // easy to test.
  //$ulogin->SetAutologin($_SESSION['username'], false);

	unset($_SESSION['uid']);
	unset($_SESSION['username']);
	unset($_SESSION['loggedIn']);
}

// Store the messages in a variable to prevent interfering with headers manipulation.
$msg = '';

// This is the action requested by the user
$action = @$_POST['action'];

// This is the first uLogin-specific line in this file.
// We construct an instance and pass a function handle to our
// callback functions (we have just defined 'appLogin' and
// 'appLoginFail' a few lines above).
$ulogin = new uLogin('appLogin', 'appLoginFail');


// First we handle application logic. We make two cases,
// one for logged in users and one for anonymous users.
// We will handle presentation after our logic because what we present is
// also based on the logon state, but the application logic might change whether
// we are logged in or not.
/*
if (isAppLoggedIn()){
	if ($action=='delete')	{	// We've been requested to delete the account

		// Delete account
		if ( !$ulogin->DeleteUser( $_SESSION['uid']) )
			$msg = 'account deletion failure';
		else
			$msg = 'account deleted ok';

		// Logout
		appLogout();
	} else if ($action == 'logout'){ // We've been requested to log out
		// Logout
		appLogout();
		$msg = 'logged out';
	}
} else {
	// We've been requested to log in
	if ($action=='login') {
		// Here we verify the nonce, so that only users can try to log in
		// to whom we've actually shown a login page. The first parameter
		// of Nonce::Verify needs to correspond to the parameter that we
		// used to create the nonce, but otherwise it can be anything
		// as long as they match.
		if (isset($_POST['nonce']) && ulNonce::Verify('login', $_POST['nonce'])){
			// We store it in the session if the user wants to be remembered. This is because
			// some auth backends redirect the user and we will need it after the user
			// arrives back.
      if (isset($_POST['autologin']))
        $_SESSION['appRememberMeRequested'] = true;
      else
        unset($_SESSION['appRememberMeRequested']);

			// This is the line where we actually try to authenticate against some kind
			// of user database. Note that depending on the auth backend, this function might
			// redirect the user to a different page, in which case it does not return.
			$ulogin->Authenticate($_POST['user'],  $_POST['pwd']);
			if ($ulogin->IsAuthSuccess()){
				// Since we have specified callback functions to uLogin,
				// we don't have to do anything here.
			} else {
				$loginFailed = "Login failed, please try again.";
			}
		} else
		$loginFailed = "Login failed, please try again.";

	} else if ($action=='autologin'){	// We were requested to use the remember-me function for logging in.
		// Note, there is no username or password for autologin ('remember me')
		$ulogin->Autologin();
		if (!$ulogin->IsAuthSuccess())
			$msg = 'autologin failure';
		else
			$msg = 'autologin ok';

	} else if ($action=='create'){	// We were requested to try to create a new acount.
		// New account
		if ( !$ulogin->CreateUser( $_POST['user'],  $_POST['pwd']) )
			$msg = 'account creation failure';
		else
			$msg = 'account created';
	}
}
 */
// Now we handle the presentation, based on whether we are logged in or not.
// Nothing fancy, except where we create the 'login'-nonce towards the end
// while generating the login form.

header('Content-Type: text/html; charset=UTF-8');  

// This inserts a few lines of javascript so that we can debug session problems.
// This will be very usefull if you experience sudden session drops, but you'll
// want to avoid using this on a live website.
//ulLog::ShowDebugConsole();

if (isAppLoggedIn()){
	header("Location: ./Pages/Users.php"); /* Redirect browser */
	?>
		<?php echo ($msg);?>
		
		<h3>This is a protected page. You are logged in, <?php echo($_SESSION['username']);?>.</h3>
		<form action="index.php" method="POST"><input type="hidden" name="action" value="refresh"><input type="submit" value="Refresh page"></form>
		<form action="index.php" method="POST"><input type="hidden" name="action" value="logout"><input type="submit" value="Logout"></form>
		<form action="index.php" method="POST"><input type="hidden" name="action" value="delete"><input type="submit" value="Delete account"></form>
	<?php
} else {
?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
	<div style="background-color:#BA0C2F;" class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 align="center" style="color:#FFFFFF;" class="display-4">Transportation Management Dashboard</h1>
  </div>
</div>
	<form align="center" action="login.php" method="POST">
	<fieldset>
<!-- Form Name -->
<legend align="center">Login</legend>

<!-- Text input-->
<div align="center" class="form-group">
  <label class="col-md-4 control-label" for="user"></label>  
  <div class="col-md-4">
  <input id="user" name="username" type="text" placeholder="Username" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Password input-->
<div align="center" class="form-group">
  <label class="col-md-4 control-label" for="pwd"></label>
  <div class="col-md-4">
    <input id="pwd" name="password" type="password" placeholder="Password" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Multiple Checkboxes -->
<div align="center" class="form-group">
  <label class="col-md-4 control-label" for="autologin"></label>
  <div class="col-md-4">
  <div class="checkbox">
    <label for="autologin-0">
      <input type="checkbox" name="autologin" id="autologin-0" value="1">
      Remember Me
    </label>
	</div>
  </div>
</div>

<!-- Button -->
<div align="center" class="form-group">
  <label class="col-md-4 control-label" for=""></label>
  <div class="col-md-4">
    <button id="" name="" class="btn btn-lg btn-block btn-dark">Login</button>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="autologin"></label>
	<input style="display:none;"type="text" id="nonce" name="nonce" value="<?php echo ulNonce::Create('login');?>">
	</div>
  </div>
</div>

<select style="display:none" name="action">
			<option selected>login</option>
			<option>create</option>
			</select>

</fieldset>

	</form>
<?php if(isset($loginFailed)){echo "<div align='center' class='alert-danger'>".$loginFailed."</div>"; }?>
<?php
}
?>


