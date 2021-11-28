	<footer id="footer">
		<div id="footerDiv" style="display: flex;justify-content: center;align-content: center;align-items: center;width: 80%;margin:auto;">
			<div style="width: 25%;">
				<h2 style="font-family: sans-serif;">Expense Manager</h2>
			</div>
			<div style="width: 37.5%;">
				<b style="text-decoration: underline;">Pages</b><br>
				<a class="coolButtonFooter" href="index.php">Home</a><br>
				<?php 
				if(isset($_SESSION['id'])){
					echo '<a id="footerProfileButton" class="coolButtonFooter" href = "#" onclick = "showProfile()">Profile</a><br>';
				}else{
					echo '<a id="footerProfileButton" class="coolButtonFooter" href = "index.php">Profile</a><br>';
				}
				?>
			</div>
			<div style="width: 37.5%;">
				<b style="text-decoration: underline;">About</b><br>
				<a class="coolButtonFooter" href="about.php">About Expense Manager</a><br>
				<a class="coolButtonFooter" target="_blank" href="https://github.com/nikhilshakohi/expensemanager">Source Code</a><br>
			</div>
		</div>
	</footer>
