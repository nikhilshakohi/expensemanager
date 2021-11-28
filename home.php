<?php
	include 'db.php';
	date_default_timezone_set('Asia/Kolkata');
	session_start();
	if(!isset($_SESSION['id'])){echo '<script>window.location.href="index.php"</script>';}/*To redirect to login page when not logged in*/
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="M6Wbpyq5eHZattL1_fqsV8HxSLz8T_U1UlUppkvfLtU" /> <!--Google Verification-->
	<link rel="stylesheet" type="text/css" href="index.css">
	<script type="text/javascript" src="index.js"></script>
	<title>Expense Manager</title>
</head>
<body onload="notifyMe()">
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
			echo'<input type="hidden" id="userFullName" value="'.$userfullName.'">';

		/*Main Part*/
		echo '<div id="header">
			<div><a id="aboutButton" href="about.php">About</a></div>
			<div id="headerName"';?> onclick = "window.location.href = 'home.php'" <?php echo '><span class="headerIconShow">$</span> Expense Manager <span class="headerIconShow">&#9998;</span></div>
			<div><a id="logoutButton" href="logout.php">Logout</a></div>
		</div>
		<div id = "content">
			<div id = "calcIcon" onclick = "toggleCalc(\''."open".'\')">Calc</div>
			<div id = "calcDiv" onclick = "toggleCalc(\''."close".'\')"></div>
			<div id = "calcOuterDiv">
				<div id = "calcInnerDiv">
					<h3>Calculator</h3>
					<input id = "calcInput" type = "search" class = "searchBar" placeholder = "Enter Calculation (Eg: 5+5)"><br><br>
					<div id = "calcResult"></div><br>
					<div id = "calcHelperButtons">
						<table class = "calcTable">
							<tr>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'1'.'\')">1</button></td>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'2'.'\')">2</button></td>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'3'.'\')">3</button></td>
								<td><button class = "calcVButtons calcVHelper" onclick = "putCalc(\''.'/'.'\')">/</button></td>
							</tr>
							<tr>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'4'.'\')">4</button></td>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'5'.'\')">5</button></td>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'6'.'\')">6</button></td>
								<td><button class = "calcVButtons calcVHelper" onclick = "putCalc(\''.'*'.'\')">*</button></td>
							</tr>
							<tr>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'7'.'\')">7</button></td>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'8'.'\')">8</button></td>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'9'.'\')">9</button></td>
								<td><button class = "calcVButtons calcVHelper" onclick = "putCalc(\''.'-'.'\')">-</button></td>
							</tr>
							<tr>
								<td><button class = "calcVButtons calcVHelper calcCancel" onclick = "putCalc(\''.'C'.'\')">C</button></td>
								<td><button class = "calcVButtons" onclick = "putCalc(\''.'0'.'\')">0</button></td>
								<td><button class = "calcVButtons calcVHelper calcCancel" onclick = "putCalc(\''.'CE'.'\')"><small>CE</small></button></td>
								<td><button class = "calcVButtons calcVHelper" onclick = "putCalc(\''.'+'.'\')">+</button></td>
							</tr>
							<tr>
								<td colspan = "4"><button class = "calcVButtons calcVHelper calcVSubmit" onclick = "calculate()">Calculate</button></td>
							</tr>
						</table>
						
					</div>
					<br><button id = "calcSubmitBigScreen" class = "submitButton" onclick = "calculate()">Calculate</button><br>
					<button class = "expensesDeleteButton" onclick = "toggleCalc(\''."close".'\')">Minimize</button><br><br><br><br>
				</div>
			</div>

			<div id = "contentGreetings" onclick = "showProfile()">Hello '.$userfullName.'! </div>
			<div id = "dateShow">Today is '.date('d-M-Y, l').'</div>
			<div id = "profileInfo"></div>
			<div id = "editProfileInfo"></div>';?>

			<div id="utilityDiv">
				<button class="searchButton expenditureButton" onclick="showExpenses('expense')">Expenditures</button>
				<button class="searchButton incomeButton" onclick="showExpenses('income')">Incomes</button>
				<button class="searchButton budgetButton" onclick="showBudget()">Transactions</button>
				<button class="searchButton" onclick="showFilters('search')">Search</button>
				<button class="searchButton filterButton" onclick="showFilters('filter')">Filter</button>
			</div>

			<div id="allDetailsDiv">
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
			
			<?php echo '<div id = "contentDashboard">

				';

				echo '<div id="marqueeDashboard">';
					
					$todayDate = date('Y-m-d',time());
					$yesterdayDate = date('Y-m-d', strtotime("-1 days"));
					$thisMonth = date('m',time());
					$thisYear = date('Y',time());
					$totalTodayExpenses = 0; $totalTodayIncome = 0;
					$totalYesterdayExpenses = 0; $totalYesterdayIncome = 0;
					$totalThisMonthExpenses = 0; $totalThisMonthIncome = 0;
					$totalThisYearExpenses = 0; $totalThisYearIncome = 0;

					$todayDateTime = strtotime(date('Y-m-d 05:30:00', time()));
					$yesterdayDateTime = strtotime(date('Y-m-d 05:30:00', strtotime("-1 day", time())));
					$yesterdayMonth = date('m',strtotime("-1 day", time()));
					$yesterdayYear = date('Y',strtotime("-1 day", time()));
					/*Calculate Today Expenses*/
					$getTodayExpense = mysqli_query($conn,"SELECT SUM(amount) AS todayExpense FROM expenses WHERE type = 'expense' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
					$getTodayExpenseCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'expense' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
					if(mysqli_num_rows($getTodayExpenseCount)>0){while($rowTodayExpense = mysqli_fetch_assoc($getTodayExpense)){$totalTodayExpenses = $rowTodayExpense['todayExpense'];}}

					/*Calculate Today Incomes*/
					$getTodayIncome = mysqli_query($conn,"SELECT SUM(amount) AS todayIncome FROM expenses WHERE type = 'income' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
					$getTodayIncomeCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'income' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
					if(mysqli_num_rows($getTodayIncomeCount)>0){while($rowTodayIncome = mysqli_fetch_assoc($getTodayIncome)){$totalTodayIncome = $rowTodayIncome['todayIncome'];}}

					/*Calculate Yesterday Expenses*/
					$getYesterdayExpense = mysqli_query($conn,"SELECT SUM(amount) AS yesterdayExpense FROM expenses WHERE type = 'expense' AND username = '$username' AND date = '$yesterdayDate' ORDER BY id DESC");
					$getYesterdayExpenseCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'expense' AND username = '$username' AND date = '$yesterdayDate' ORDER BY id DESC");
					if(mysqli_num_rows($getYesterdayExpenseCount)>0){while($rowYesterdayExpense = mysqli_fetch_assoc($getYesterdayExpense)){$totalYesterdayExpenses = $rowYesterdayExpense['yesterdayExpense'];}}

					/*Calculate Yesterday Incomes*/
					$getYesterdayIncome = mysqli_query($conn,"SELECT SUM(amount) AS yesterdayIncome FROM expenses WHERE type = 'income' AND username = '$username' AND date = '$yesterdayDate' ORDER BY id DESC");
					$getYesterdayIncomeCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'income' AND username = '$username' AND date = '$yesterdayDate' ORDER BY id DESC");
					if(mysqli_num_rows($getYesterdayIncomeCount)>0){while($rowYesterdayIncome = mysqli_fetch_assoc($getYesterdayIncome)){$totalYesterdayIncome = $rowYesterdayIncome['yesterdayIncome'];}}

					/*Calculate Month's Expenses*/
					$getThisMonthExpense = mysqli_query($conn,"SELECT SUM(amount) AS thisMonthExpense FROM expenses WHERE type = 'expense' AND username = '$username' AND MONTH(date) = '$thisMonth' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					$getThisMonthExpenseCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'expense' AND username = '$username' AND MONTH(date) = '$thisMonth' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					if(mysqli_num_rows($getThisMonthExpenseCount)>0){while($rowThisMonthExpense = mysqli_fetch_assoc($getThisMonthExpense)){$totalThisMonthExpenses = $rowThisMonthExpense['thisMonthExpense'];}}

					/*Calculate Month's Incomes*/
					$getThisMonthIncome = mysqli_query($conn,"SELECT SUM(amount) AS thisMonthIncome FROM expenses WHERE type = 'income' AND username = '$username' AND MONTH(date) = '$thisMonth' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					$getThisMonthIncomeCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'income' AND username = '$username' AND MONTH(date) = '$thisMonth' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					if(mysqli_num_rows($getThisMonthIncomeCount)>0){while($rowThisMonthIncome = mysqli_fetch_assoc($getThisMonthIncome)){$totalThisMonthIncome = $rowThisMonthIncome['thisMonthIncome'];}}

					/*Calculate Month's Expenses*/
					$getThisYearExpense = mysqli_query($conn,"SELECT SUM(amount) AS thisYearExpense FROM expenses WHERE type = 'expense' AND username = '$username' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					$getThisYearExpenseCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'expense' AND username = '$username' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					if(mysqli_num_rows($getThisYearExpenseCount)>0){while($rowThisYearExpense = mysqli_fetch_assoc($getThisYearExpense)){$totalThisYearExpenses = $rowThisYearExpense['thisYearExpense'];}}

					/*Calculate This Year's Incomes*/
					$getThisYearIncome = mysqli_query($conn,"SELECT SUM(amount) AS thisYearIncome FROM expenses WHERE type = 'income' AND username = '$username' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					$getThisYearIncomeCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'income' AND username = '$username' AND YEAR(date) = '$thisYear' ORDER BY id DESC");
					if(mysqli_num_rows($getThisYearIncomeCount)>0){while($rowThisYearIncome = mysqli_fetch_assoc($getThisYearIncome)){$totalThisYearIncome = $rowThisYearIncome['thisYearIncome'];}}

					/*Show Marquee*/
					if((mysqli_num_rows($getTodayExpenseCount)>0) || (mysqli_num_rows($getTodayIncomeCount)>0) || (mysqli_num_rows($getYesterdayExpenseCount)>0) || (mysqli_num_rows($getYesterdayIncomeCount)>0) || (mysqli_num_rows($getThisMonthExpenseCount)>0) || (mysqli_num_rows($getThisMonthIncomeCount)>0)){
						echo '<div style = "overflow:hidden;padding:5px"><span id = "marquee">
							<div class = "marqueeDiv">
								<div class = "summaryTime">Today: </div><a id = "marqueeTodayExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."expense".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.$todayDateTime.'\', \''.$totalTodayExpenses.'\')">-&#8377;'.number_format($totalTodayExpenses).';</a> <a id = "marqueeTodayIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."income".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.$todayDateTime.'\', \''.$totalTodayIncome.'\')">+&#8377;'.number_format($totalTodayIncome).'</a>
							</div>
							<div class = "inlineSeparator"></div>
							<div class = "marqueeDiv">
								<div class = "summaryTime">Yesterday: </div><a id = "marqueeYesterdayExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."expense".'\', \''.$yesterdayMonth.'\', \''.$yesterdayYear.'\', \''.$yesterdayDateTime.'\', \''.$totalYesterdayExpenses.'\')">-&#8377;'.number_format($totalYesterdayExpenses).';</a> <a id = "marqueeYesterdayIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."income".'\', \''.$yesterdayMonth.'\', \''.$yesterdayYear.'\', \''.$yesterdayDateTime.'\', \''.$totalYesterdayIncome.'\')">+&#8377;'.number_format($totalYesterdayIncome).'</a>
							</div>
							<div class = "inlineSeparator"></div>
							<div class = "marqueeDiv">
								<div class = "summaryTime">This Month: </div><a id = "marqueeThisMonthExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."expense".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.'thisIsMonth'.'\', \''.$totalThisMonthExpenses.'\')">-&#8377;'.number_format($totalThisMonthExpenses).'</a>; <a id = "marqueeThisMonthIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."income".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.'thisIsMonth'.'\', \''.$totalThisMonthIncome.'\')">+&#8377;'.number_format($totalThisMonthIncome).'</a>
							</div>
						</span></div>';
					}else if((mysqli_num_rows($getThisYearExpenseCount)>0) || (mysqli_num_rows($getThisYearIncomeCount)>0)){
						echo '<div style = "overflow:hidden;padding:5px"><span id = "marquee">
							<div class = "marqueeDiv" style = "width:90%">
								<div class = "summaryTime">This Year: </div><a id = "marqueeThisYearExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showExpenses(\''."expense".'\')">-&#8377;'.number_format($totalThisYearExpenses).';</a> <a id = "marqueeThisYearIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showExpenses(\''."income".'\')">+&#8377;'.number_format($totalThisYearIncome).'</a>
							</div>
						</span></div>';
					}
					
				echo '</div>';

			?>	
			</div>

			<!--Content Expense Form-->
			<div id = "contentExpenseForm">
				<!--Expenses Form-->
				<div id="expenseForm">
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
						<option value="office">Office</option>
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
				</div>
			</div>

			<!--Content Income Form-->
			<div id = "contentIncomeForm">
				<!--Income Form-->
				<div id="incomeForm">
					<div class = "headingName successMessage">Add New Income: </div>
					&#8377;<input type="number" min="0" max="9999999" id="incomeAmount" class="incomeInput inputStyle" placeholder="Amount">
					<select id="incomeCategory" class="incomeInput inputStyle">
						<option value="">Select</option>
						<option value="salary">Salary</option>
						<option value="investment">Investment / Stocks</option>
						<option value="rent">Rent</option>
						<option value="bonus">Bonus</option>
						<option value="allowance">Allowance</option>
						<option value="others">Others</option>
					</select>
					<input type="date" id="incomeDate" class="incomeInput inputStyle"><br>
					<textarea id="incomeDetails" class="incomeInput inputStyle" placeholder="More Details"></textarea><br>
					<div id="incomeErrorMessage"></div><br>
					<?php /*Username*/
						echo '<input type="hidden" id="incomeUsername" value="'.$username.'">';
					?>
					<button type="button" id="incomeSubmit" class="submitButton" onclick="addExpense('income')">Submit</button>
				</div>
			</div>

			<!--Content Short Summary Form-->
			<div id = "contentShortSummaryForm">

				<?php

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

				/*Get Start Date*/
				$rowStartDate = 0;
				$checkStartDate = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' ORDER BY date ASC LIMIT 1");
				if(mysqli_num_rows($checkStartDate) > 0){
					while($rowcheckDate = mysqli_fetch_assoc($checkStartDate)){
						$rowStartDate = $rowcheckDate['date'];
					}
				}
				
				echo '<div id = "dashBoard"><div class = "headingName bufferMessage">Total Summary: </div>';

					/*Net Budget*/
					echo '<button id = "dashBoardBudget" onclick = "showBudget()">
						Flow:&nbsp&#8377;<span class="bufferMessage" id="newBudget">'.number_format($totalBudget).'</span>
					</button>';
						
					/*Show Total Expenses*/
					echo '<button id = "dashBoardExpense" onclick = "showExpenses(\''.'expense'.'\')">
						Expenses:&nbsp&#8377;<span class="errorMessage" id="newExpense">'.number_format($totalExpenses).'</span>
					</button>';
				
					/*Show Total Income*/
					echo '<button id = "dashBoardIncome" onclick = "showExpenses(\''.'income'.'\')">
						Income:&nbsp&#8377;<span class="successMessage" id="newIncome">'.number_format($totalIncome).'</span>
					</button>';
					if($rowStartDate != 0){echo '<div id = "dataCaption">(since '.date('d-M-Y D', strtotime($rowStartDate)).')</div>';}
				echo '</div>';

				?>
				
			</div>

			<!--Content Analysis Form-->
			<?php 
				$getAllExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' ORDER BY date DESC");
				if(mysqli_num_rows($getAllExpenses)>0){
					echo '<div id = "contentAnalysisForm">
						<div class = "headingName bufferMessage">Analysis: </div>';
						echo '<div id = "analysisDiv"><table class = "analysisTable"><thead>
							<tr style="position:sticky;top:0px;text-align:left" ><th style="position:sticky;left:0px;top:0; background-color:rgba(170,150,220)">Monthly</th><th colspan ="2">Overall</th><th colspan = "11">Expense</th><th colspan = "6">Income</th></tr>
							<tr style="position:sticky;top:40px">
								<th style="position:sticky;left:0px;top:40px">Date</th><th>Total Expense</th><th>Total Income</th><th>Food</th><th>Market</th><th>Travel</th><th>Petrol</th><th>House Works</th><th>Health</th><th>Education</th><th>Personal</th><th>Savings</th><th>Office</th><th>Others</th>
								<th>Salary</th><th>Investment</th><th>Rentals</th><th>Bonus</th><th>Allowance</th><th>Others</th>
							</tr>
						</thead>';
						$getMonthAnalysis = mysqli_query($conn, "SELECT DISTINCT month(date) AS month, year(date) AS year FROM expenses WHERE username = '$username' ORDER BY date DESC");
						if(mysqli_num_rows($getMonthAnalysis)>0){
							while($rowMonthName = mysqli_fetch_assoc($getMonthAnalysis)){
								$monthOfAnalysis = $rowMonthName['month'];
								$yearOfAnalysis = $rowMonthName['year'];

								/*Table*/
								echo '
								<tbody><tr>
								<td style="position:sticky;left:0px">'.date('M-Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'</td>';
								/*Get Totals Expense*/	
								$getTotalsExpense = mysqli_query($conn, "SELECT SUM(CASE WHEN username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalExpenseAmount, SUM(CASE WHEN username = '$username' AND type = 'income' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalIncomeAmount, SUM(CASE WHEN category = 'food' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalFood, SUM(CASE WHEN category = 'market' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalMarket, SUM(CASE WHEN category = 'travel' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalTravel, SUM(CASE WHEN category = 'petrol' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalPetrol, SUM(CASE WHEN category = 'houseWorks' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalHouseWorks, SUM(CASE WHEN category = 'health' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalHealth, SUM(CASE WHEN category = 'education' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalEducation, SUM(CASE WHEN category = 'personal' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalPersonal, SUM(CASE WHEN category = 'office' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalOffice, SUM(CASE WHEN category = 'savings' AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalSavings, SUM(CASE WHEN (category != 'food' AND category != 'market' AND category != 'travel' AND category != 'petrol' AND category != 'houseWorks' AND category != 'health' AND category != 'education' AND category != 'personal' AND category != 'savings' AND category != 'office') AND username = '$username' AND type = 'expense' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalOthers FROM expenses");
								if(mysqli_num_rows($getTotalsExpense)>0){
									while($rowTotalsExpense = mysqli_fetch_assoc($getTotalsExpense)){
										echo '
										<td><span class = "bufferMessage">';
											if($rowTotalsExpense['totalExpenseAmount']==0){
												echo '-';
											}else{
												echo '<a href = "#" onclick = "showIndividualExpenses(\''."expense".'\', \''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'thisIsMonth'.'\', \''.$rowTotalsExpense['totalExpenseAmount'].'\')">-&#8377;'.number_format($rowTotalsExpense['totalExpenseAmount']).'</a>';
											}
										echo '</span></td>
										<td><span class = "bufferMessage">';
											if($rowTotalsExpense['totalIncomeAmount']==0){
												echo '-';
											}else{
												echo '<a href = "#" onclick = "showIndividualExpenses(\''."income".'\', \''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'thisIsMonth'.'\', \''.$rowTotalsExpense['totalIncomeAmount'].'\')">+&#8377;'.number_format($rowTotalsExpense['totalIncomeAmount']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalFood']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalFood']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalMarket']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalMarket']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalTravel']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalTravel']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalPetrol']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalPetrol']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalHouseWorks']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalHouseWorks']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalHealth']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalHealth']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalEducation']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalEducation']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalPersonal']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalPersonal']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalSavings']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalSavings']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalOffice']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalOffice']);
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalOthers']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsExpense['totalOthers']);
											}
										echo '</span></td>';
									}
								}
								/*Get Totals Income*/
								$getTotalsIncome = mysqli_query($conn, "SELECT SUM(CASE WHEN category = 'salary' AND username = '$username' AND type = 'income' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalSalary, SUM(CASE WHEN category = 'investment' AND username = '$username' AND type = 'income' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalInvestment, SUM(CASE WHEN category = 'rent' AND username = '$username' AND type = 'income' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalRent, SUM(CASE WHEN category = 'bonus' AND username = '$username' AND type = 'income' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalBonus, SUM(CASE WHEN category = 'allowance' AND username = '$username' AND type = 'income' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalAllowance, SUM(CASE WHEN (category != 'salary' AND category != 'investment' AND category != 'rent' AND category != 'bonus' AND category != 'allowance') AND username = '$username' AND type = 'income' AND month(date) = '$monthOfAnalysis' AND year(date) = '$yearOfAnalysis' THEN amount END) AS totalOthers FROM expenses");
								if(mysqli_num_rows($getTotalsIncome)>0){
									while($rowTotalsIncome = mysqli_fetch_assoc($getTotalsIncome)){
										echo '
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalSalary']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsIncome['totalSalary']);
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalInvestment']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsIncome['totalInvestment']);
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalRent']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsIncome['totalRent']);
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalBonus']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsIncome['totalBonus']);
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalAllowance']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsIncome['totalAllowance']);
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalOthers']==0){
												echo '-';
											}else{
												echo '&#8377;'.number_format($rowTotalsIncome['totalOthers']);
											}
										echo '</span></td>';
									}
								}
							echo '</tr></tbody>
							</div>';	
							}
						}
						echo'</table>';
					echo '</div>';		
				}
			?>
		



			<br><br>

			<script type="text/javascript">
				/*Enter Form*/
				var expenseFormEnter = document.getElementById('expenseForm');
				expenseFormEnter.addEventListener("keyup", function(event){
					if(event.keyCode == 13){
						event.preventDefault();addExpense('expense');
					}
				})
				var incomeFormEnter = document.getElementById('incomeForm');
				incomeFormEnter.addEventListener("keyup", function(event){
					if(event.keyCode == 13){
						event.preventDefault();addExpense('income');
					}
				})
			</script>

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