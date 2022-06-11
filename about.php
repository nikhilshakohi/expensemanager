<?php
include 'db.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="index.css">
	<script type="text/javascript" src="index.js"></script>
	<title>About</title>
</head>
<body>

		<div id="header">
			<div><a id="aboutButton" href="home.php">Home</a></div>
			<div id="headerName" onclick = "window.location.href = 'home.php'" ><span class="headerIconShow">$</span> Expense Manager <span class="headerIconShow">&#9998;</span></div>
			<?php
			if(isset($_SESSION['id'])){
				echo '<div><a id="logoutButton" href="logout.php">Logout</a></div>';
			}else{
				echo '<div><a id="aboutLoginButton" href="index.php">Login</a></div>';
			}
			?>
		</div>
		<div id = "content" style="margin-top:90px">
			<h3 style="animation:fade 1s ease-in-out;">Hello there!</h3>
				
			<div id="contentAbout">
				<div style="text-align:left;padding:20px 30px">
					<h4>About Site:</h4>
					<li style="list-style-type:circle">Expense Manager is a utility site for maintaining and managing Money related data developed using </li>
					<div style="display:flex;">
						<div style="width:20%"></div>
						<div style="width:30%">
							<li>HTML</li>
							<li>CSS</li>
							<li>Vanilla JS</li>
						</div>
						<div style="width:30%">
							<li>AJAX</li>
							<li>PHP</li>
							<li>SQL</li>
						</div>
						<div style="width:20%"></div>
					</div><br>
					<li style="list-style-type:circle">Source code is available at <a style="color:blue" target="_blank" href="https://github.com/nikhilshakohi/expensemanager">Expense Manager.</a></li>
					<li style="list-style-type:circle">AJAX functionality is used at adding expenses, editing expenses, deleting expenses, fetching data without refreshing the page.</li>
					<h4>Functionalities:</h4>
						<div style="padding:0 20px">
							<li>User can signup, login with minimum of username, email, password.</li>
							<li>User can edit personal details like email, password, name or username if required.</li>
							<li>User can add expense / income amount without page refresh.</li>
							<li>User can also add additional details corresponding to the amount.</li>
							<li>User can edit the amount / data / category / details of the expene without page refresh.</li>
							<li>User can see the history of all data added in a sorted way.</li>
							<li>User can search through all of the data added.</li>
							<li>User can filter data by giving specific time periods.</li>
							<li>User can find the source code in the github link provided.</li>
							<li>Device friendly Interface.</li>
						</div>
					<h4>About the developer:</h4>
					<li style="list-style-type:circle">Developed by <a style="color:blue" target="_blank" href="https://github.com/nikhilshakohi">Nikhil Shakohi.</a></li>
					<li style="list-style-type:circle">Site Version v5.4 <small>(11-06-2022)</small></li>
					<br><br><br>
				</div>
			</div>
		</div>
		
		<?php	
		
		include 'footer.php';

		?>


</body>
</html>