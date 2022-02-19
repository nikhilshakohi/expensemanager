<?php
	include 'db.php';
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="M6Wbpyq5eHZattL1_fqsV8HxSLz8T_U1UlUppkvfLtU" /> <!--Google Verification-->
	<link rel="stylesheet" type="text/css" href="index.css">
	<meta name="theme-color" content="#323232">
	<link rel="apple-touch-icon" href="logo.png">
	<link rel="icon" href="logo.png" type="image/png">
	<!--<link rel="manifest" crossorigin="use-credentials" href="manifest.json">-->
	<script type="text/javascript" src="index.js"></script>
	<title>Expense Manager</title>
</head>
<body onload="login()">
	<?php
		if(!isset($_SESSION['id'])){
	?>
		<div id="headerLoginPage">
			<div id="headerName"><span class="headerIconShow">$</span> Expense Manager <span class="headerIconShow">&#9998;</span></div>
		</div>
		<div id="content" style="margin-top: 75px;">
			<div id="contentLogin"><br>
				<!--Login Form -->
				<form id="loginForm">
					<div class="headingName">Welcome Back, Login</div><hr><br><br>
					<input type="username" id="loginUsername" class="inputStyleLoginPage" placeholder="Username / E-mail" autofocus autocomplete 
					<?php
					//Check for Cookies
					if(isset($_COOKIE['autoLogin'])){
						if($_COOKIE['autoLogin']=='yes'){echo ' value="'.$_COOKIE['username'].'" ';}
						else{echo ' ';}
					}else{echo ' ';}
					?> ><br>
					<input type="password" id="loginPassword" class="inputStyleLoginPage" placeholder="Password" autocomplete 
					<?php
					//Check for Cookies
					if(isset($_COOKIE['autoLogin'])){
						if($_COOKIE['autoLogin']=='yes'){echo ' value="'.$_COOKIE['userID'].'" ';}
						else{echo ' ';}
					}else{echo ' ';}

					?> >
					<input class="noDisplay" type="checkBox" id="showLoginPasswordBox" onclick="showPassword('login')">
					<label class="showPasswordCheckLabel" id="loginPasswordBoxLabel" for="showLoginPasswordBox">&#128065;</label><br>
					<input type="checkBox" id="rememberMeLoginBox" checked>
					<label for="rememberMeLoginBox">Remember Me</label><br>
					<div id="loginErrorMessage"></div><br>
					<button type="button" id="loginButton" onclick="login()">LOGIN</button><br>
					<input type="button" id="toggleSignupButton" value="SIGNUP" onclick="toggleSignIn('signup')">
				</form>
				<!--Signup Form-->
				<form id="signupForm">
					<div class="headingName">Hello There, Signup</div><hr><br><br>
					<input type="username" id="signupUsername" class="inputStyleLoginPage" placeholder="Enter Username" autocomplete><br>
					<input type="password" id="signupPassword" class="inputStyleLoginPage" placeholder="Enter Password" autocomplete>
					<input class="noDisplay" type="checkBox" id="showSignupPasswordBox" onclick="showPassword('signup')" >
					<label class="showPasswordCheckLabel" id="signupPasswordBoxLabel" for="showSignupPasswordBox">&#128065;</label><br>
					<input type="password" id="signupConfirmPassword" class="inputStyleLoginPage" placeholder="Re-Enter Password" autocomplete><br>
					<input type="name" id="signupFullName" class="inputStyleLoginPage" placeholder="Name" autocomplete><br>
					<input type="email" id="signupEmail" class="inputStyleLoginPage" placeholder="E-Mail" autocomplete><br>
					<div id="signupErrorMessage"></div>
					<button type="button" id="signupButton" onclick="signup()">SIGNUP</button><br>
					<input type="button" id="toggleLoginButton" value="LOGIN" onclick="toggleSignIn('login')"><br>
				</form>
				<?php
				}else{
					?>
					<h1>Hello There! You are logged in!!!</h1>
					<a href="home.php">Home</a><br><br>
					<script type="text/javascript">window.location.href = "home.php";</script>
					<button onclick="window.location.href='logout.php'">Logout</button>
					<?php
				}
				?>
			</div>
		</div>

		<?php 
		include 'footer.php';
		?>
</body>
<script type="text/javascript">
	var loginEnter = document.getElementById("loginForm");
	loginEnter.addEventListener("keyup", function(event){
		if(event.keyCode == 13){event.preventDefault();login();}
	})
	var signupEnter = document.getElementById("signupForm");
	signupEnter.addEventListener("keyup", function(event){
		if(event.keyCode == 13){event.preventDefault();signup();}
	})
</script>
</html>