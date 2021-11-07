<?php
	include 'db.php';
	session_start();
	if(!isset($_SESSION['id'])){echo '<script>window.location.href="index.php"</script>';}/*To redirect to login page when not logged in*/
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="index.css">
	<script type="text/javascript" src="index.js"></script>
	<title>Home Page</title>
</head>
<body>
	<?php 
	/*Check if Session is active*/
	if(isset($_SESSION['id'])){
		$userId = $_SESSION['id'];
		
		/*Store User Details in variables for access*/
		$getUserDetails = mysqli_query($conn,"SELECT * FROM users WHERE id='$userId'");
		if(mysqli_num_rows($getUserDetails)>0){
			while($rowUserDetails = mysqli_fetch_assoc($getUserDetails)){
				$username = $rowUserDetails['username'];
				$userfullName = $rowUserDetails['name'];
				$email = $rowUserDetails['email'];
			}
		}
		/*For JS*/
			echo'<input type="hidden" id="username" value="'.$username.'">';

		/*Main Part*/
		echo '<div id="header">
			<div><a id="aboutButton" href="about.php">About</a></div>
			<div id="headerName"';?> onclick = "window.location.href = 'home.php'" <?php echo '>Expense Manager</div>
			<div><a id="logoutButton" href="logout.php">Logout</a></div>
		</div>
		<div id = "content">
			<div id = "contentGreetings" onclick = "showProfile()">Hello '.$userfullName.'! </div>
			<div id = "profileInfo"></div>
			<div id = "editProfileInfo"></div>
			<div id = "contentDashboard">
				';

				/*Get all Expenses*/
				$getExpense = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'expense' AND username = '$username' ORDER BY id DESC");
				$totalExpenses = 0;
				if(mysqli_num_rows($getExpense)>0){
					while($rowExpense = mysqli_fetch_assoc($getExpense)){
						$totalExpenses += $rowExpense['amount'];
					}
				}

				/*Get all Incomes*/
				$getIncome = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'income' AND username = '$username' ORDER BY id DESC");
				$totalIncome = 0;
				if(mysqli_num_rows($getIncome)>0){
					while($rowIncome = mysqli_fetch_assoc($getIncome)){
						$totalIncome += $rowIncome['amount'];
					}
				}

				$totalBudget = $totalIncome - $totalExpenses;
				echo '<div class = "headingName">Summary: </div>';
				/*Net Budget*/
				echo '<button id = "dashBoardBudget" onclick = "showBudget()">
					Flow:&nbsp&#8377;<span class="bufferMessage" id="newBudget">'.number_format($totalBudget).'</span>
				</button>';

				echo '<div id = "dashBoard">';

					/*Show Total Expenses*/
					echo '<button id = "dashBoardExpense" onclick = "showExpenses(\''.'expense'.'\')">
						Expenses:&nbsp&#8377;<span class="errorMessage" id="newExpense">'.number_format($totalExpenses).'</span>
					</button>';
				
					/*Show Total Income*/
					echo '<button id = "dashBoardIncome" onclick = "showExpenses(\''.'income'.'\')">
						Income:&nbsp&#8377;<span class="successMessage" id="newIncome">'.number_format($totalIncome).'</span>
					</button>';
				echo '</div>';

			?>	
				<div id="utilityDiv">
					<button class="searchButton" onclick="showExpenses('expense')">Expenditures</button>
					<button class="searchButton" onclick="showExpenses('income')">Incomes</button>
					<button class="searchButton" onclick="showBudget()">Money Flow</button>
					<button class="searchButton" onclick="showFilters('search')">Search</button>
					<button class="searchButton" onclick="showFilters('filter')">Filter</button>
				</div>
				<div id="budgetDetailsDiv"></div>
				<div id="expenseDetailsDiv"></div>
				<div id="incomeDetailsDiv"></div>
				<div id="editExpensesDiv"></div>
				<div id="deleteExpensesDiv"></div>
				<div id="searchDiv">
					<div class = "expensesListHeading">
						<div class = "listMainHeadingName">Search </div>
						<div class = "closeButton" onclick = "showFilters('search')">Close</div>
					</div>
					<div class="budgetList">Search for detail / amount / category</div><br>
					<input class="searchBar" type="search" id="searchInput" placeholder="Search for any expense details">
					<button type="button" class="submitButton" onclick="getSearchResults()">Search</button>
					<div id="searchResults"></div>
				</div>
				<div id="filterDiv">
					<div class = "expensesListHeading">
						<div class = "listMainHeadingName">Filter </div>
						<div class = "closeButton" onclick = "showFilters('filter')">Close</div>
					</div>
					<div class="budgetList">Filter Expenses to get the results within these dates (inc)</div><br>
					<div class="filterInputs">From: <input id="filterFromDate" class="filterInputDate" type="date"></div>
					<div class="filterInputs">To: <input id="filterToDate" class="filterInputDate" type="date"></div><br>
					<button type="button" class="submitButton" onclick="getFilterResults()">Submit</button>
					<div id="filterResults"></div>
				</div>
			</div>

			<!--Content Expense Form-->
			<div id = "contentExpenseForm">
				<!--Expenses Form-->
				<form id="expenseForm">
					<div class = "headingName errorMessage">Add New Expense: </div>
					&#8377;<input type="number" min="0" max="9999999" id="expenseAmount" class="expenseInput inputStyle" placeholder="Amount" autofocus>
					<select id="expenseCategory" class="expenseInput inputStyle">
						<option value="">Select</option>
						<option value="food">Food</option>
						<option value="market">Market</option>
						<option value="travel">Travel</option>
						<option value="petrol">Petrol</option>
						<option value="houseWorks">House Works</option>
						<option value="health">Health</option>
						<option value="education">Education</option>
						<option value="personal">Personal / Shopping</option>
						<option value="savings">Savings</option>
						<option value="others">Others</option>
					</select>
					<input type="date" id="expenseDate" class="expenseInput inputStyle"><br>
					<textarea id="expenseDetails" class="expenseInput inputStyle" placeholder="More Details"></textarea><br>
					<div id="expenseErrorMessage"></div>
					<?php /*Username*/
						echo '<input type="hidden" id="expenseUsername" value="'.$username.'">';
					?>
					<button type="button" id="expenseSubmit" class="submitButton" onclick="addExpense('expense')">Submit</button>
				</form>
			</div>

			<!--Content Income Form-->
			<div id = "contentIncomeForm">
				<!--Income Form-->
				<form id="incomeForm">
					<div class = "headingName successMessage">Add New Income: </div>
					&#8377;<input type="number" min="0" max="9999999" id="incomeAmount" class="incomeInput inputStyle" placeholder="Amount">
					<select id="incomeCategory" class="incomeInput inputStyle">
						<option value="">Select</option>
						<option value="food">Food</option>
						<option value="market">Market</option>
						<option value="travel">Travel</option>
						<option value="petrol">Petrol</option>
						<option value="houseWorks">House Works</option>
						<option value="health">Health</option>
						<option value="education">Education</option>
						<option value="personal">Personal / Shopping</option>
						<option value="savings">Savings</option>
						<option value="others">Others</option>
					</select>
					<input type="date" id="incomeDate" class="incomeInput inputStyle"><br>
					<textarea id="incomeDetails" class="incomeInput inputStyle" placeholder="More Details"></textarea><br>
					<div id="incomeErrorMessage"></div><br>
					<?php /*Username*/
						echo '<input type="hidden" id="incomeUsername" value="'.$_SESSION['username'].'">';
					?>
					<button type="button" id="incomeSubmit" class="submitButton" onclick="addExpense('income')">Submit</button>
				</form>
			</div><br><br>

		</div>

	<?php	
	}else{
		echo '<h1>Hello There! You are not logged in!!!</h1>
		<a href="index.php">Login</a>
		<script type="text/javascript">window.location.href = "index.php";</script>';
	}

	include 'footer.php';
	?>

</body>
</html>