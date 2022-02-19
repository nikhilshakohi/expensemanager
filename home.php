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
	<meta name="theme-color" content="#323232">
	<link rel="apple-touch-icon" href="logo.png">
	<link rel="icon" href="logo.png" type="image/png">
	<!--<link rel="manifest" crossorigin="use-credentials" href="manifest.json">-->
	<script type="text/javascript" src="index.js"></script>
	<title>Expense Manager</title>
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
				$userHalfName = explode(' ', $userfullName);
				$email = $rowUserDetails['email'];
			}
		}
		/*For JS*/
			echo'<input type="hidden" id="username" value="'.$username.'">';
			echo'<input type="hidden" id="userFullName" value="'.$userfullName.'">';

		/*Main Part*/
		echo '<div id="header">
			<div id="aboutButtonDiv"><a id="aboutButton" href="about.php">About</a></div>
			<div id="headerName"';?> onclick = "window.location.href = 'home.php'" <?php echo '><span class="headerIconShow">$</span> Expense Manager <span class="headerIconShow">&#9998;</span></div>
			<div><a id="logoutButton" href="logout.php">Logout</a><a class="redButton" id="logoutButtonSmall" href="logout.php">Logout</a></div>
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

			<div id = "contentGreetings" onclick = "showProfile()">';
				if(date('H', time())<12){echo 'Good Morning ';}
				if((date('H', time())>=12)&&(date('H', time())<16)){echo 'Good Afternoon ';}
				if((date('H', time())>=16)&&(date('H', time())<24)){echo 'Good Evening ';}
				echo $userHalfName[0].'! ';
			echo'</div>
			<div id = "dateShow">Today is '.date('d-M-Y, l').'</div>
			<div id = "profileInfo"></div>
			<div id = "editProfileInfo"></div>';?>

			<div id="utilityDiv">
				<button class="searchButton expenditureButton" onclick="showExpenses('expense')">Expenditures</button>
				<button class="searchButton incomeButton" onclick="showExpenses('income')">Incomes</button>
				<button class="searchButton budgetButton" onclick="showBudget()">Transactions</button>
				<div><button class="searchButton" onclick="showFilters('search')">Search</button>
				<button class="searchButton filterButton" onclick="showFilters('filter')">Filter</button></div>
			</div>

			<div id="allDetailsDiv">
				<div id="budgetDetailsDiv"></div>
				<div id="expenseDetailsDiv"></div>
				<div id="incomeDetailsDiv"></div>
				<div id="editExpensesDiv"></div>
				<div id="deleteExpensesDiv"></div>
				<div id="getSubDetailsDiv"></div>
				<div id="editWalletDiv"></div>
				<div id="deleteWalletDiv"></div>

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
								<div class = "summaryTime sideHeading">Today: </div><a id = "marqueeTodayExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."expense".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.$todayDateTime.'\', \''.$totalTodayExpenses.'\')">-&#8377;'.number_format($totalTodayExpenses).';</a> <a id = "marqueeTodayIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."income".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.$todayDateTime.'\', \''.$totalTodayIncome.'\')">+&#8377;'.number_format($totalTodayIncome).'</a>
							</div>
							<div class = "inlineSeparator"></div>
							<div class = "marqueeDiv">
								<div class = "summaryTime sideHeading">Yesterday: </div><a id = "marqueeYesterdayExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."expense".'\', \''.$yesterdayMonth.'\', \''.$yesterdayYear.'\', \''.$yesterdayDateTime.'\', \''.$totalYesterdayExpenses.'\')">-&#8377;'.number_format($totalYesterdayExpenses).';</a> <a id = "marqueeYesterdayIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."income".'\', \''.$yesterdayMonth.'\', \''.$yesterdayYear.'\', \''.$yesterdayDateTime.'\', \''.$totalYesterdayIncome.'\')">+&#8377;'.number_format($totalYesterdayIncome).'</a>
							</div>
							<div class = "inlineSeparator"></div>
							<div class = "marqueeDiv">
								<div class = "summaryTime sideHeading">This Month: </div><a id = "marqueeThisMonthExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."expense".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.'thisIsMonth'.'\', \''.$totalThisMonthExpenses.'\')">-&#8377;'.number_format($totalThisMonthExpenses).'</a>; <a id = "marqueeThisMonthIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showIndividualExpenses(\''."income".'\', \''.$thisMonth.'\', \''.$thisYear.'\', \''.'thisIsMonth'.'\', \''.$totalThisMonthIncome.'\')">+&#8377;'.number_format($totalThisMonthIncome).'</a>
							</div>
						</span></div>';
					}else if((mysqli_num_rows($getThisYearExpenseCount)>0) || (mysqli_num_rows($getThisYearIncomeCount)>0)){
						echo '<div style = "overflow:hidden;padding:5px"><span id = "marquee">
							<div class = "marqueeDiv" style = "width:90%">
								<div class = "summaryTime sideHeading">This Year: </div><a id = "marqueeThisYearExpense" href = "#" class = "errorMessage inlineBlockDiv" onclick = "showExpenses(\''."expense".'\')">-&#8377;'.number_format($totalThisYearExpenses).';</a> <a id = "marqueeThisYearIncome" href = "#" class = "successMessage inlineBlockDiv" onclick = "showExpenses(\''."income".'\')">+&#8377;'.number_format($totalThisYearIncome).'</a>
							</div>
						</span></div>';
					}
					echo '
					<div id="walletActionDiv">
						<button type="button" id="showWalletButtons" class="basicButtonOuter smallButton" onclick="showWalletButtons()">Wallet Details</button>
					</div>
					<div class="flexDisplay" id="walletButtons">
						<button type="button" id="addWalletButton" class="greenButtonOuter smallButton" onclick="showWalletDiv(\''."addWalletData".'\')">Add New Wallet</button>
						<button type="button" id="showWalletDataButton" class="basicButtonOuter smallButton" onclick="showWalletDiv(\''."allWalletList".'\')">Show Wallet Amount</button>
						<button type="button" id="addWalletMoneyButton" class="greenButtonOuter smallButton" onclick="showWalletDiv(\''."addMoneyToWallet".'\')">Transfer Money to Wallet</button>
						<button type="button" id="walletHistoryButton" class="greenButtonOuter smallButton" onclick="showWalletDiv(\''."walletHistory".'\')">Wallet History</button>
					</div>
					<div id="addWalletData">
						<br><u>Add New Wallet:</u><br><br>
						<div>Wallet / Bank Name : <input id="walletName" type="text" class="inputStyle" placeholder="Eg:Paytm / SBI Bank"></div>
						<div>Money in the Wallet : <div class="inlineBlockDiv">&#8377;<input id="walletAmount" type="number" class="inputStyle" placeholder="Amount"></div></div>
						<div id="walletErrorMessage"></div>
						<div>
							<button type="button" id="walletSubmit" class="submitButton" onclick="addWallet()">ADD</button>
							<button type="button" class="closeButton noDisplay" onclick="closeWalletDiv()">CLOSE</button>
						</div><br>';
	/*					echo'<div id="currentWalletsList">';
							$walletCheck=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
							if(mysqli_num_rows($walletCheck)>0){
								echo'<u>Current Wallets: </u><br><br>
								<div id="currentWallets">';
									while($rowWallets=mysqli_fetch_assoc($walletCheck)){
										echo '<div class="eachCurrentWallet">
											'.$rowWallets['walletName'].'<hr>
											<button class="basicButtonOuter smallButton" onclick="showEditWallet(\''.$rowWallets['id'].'\')">edit</button><br>
											<button class="redButtonOuter smallButton" onclick="showDeleteWallet(\''.$rowWallets['id'].'\')">delete</button>
										</div>';
									}
								echo'</div>';
							}else{
								echo 'No wallets registered!';
							}
						echo'</div>';	
*/					echo'<br></div>
					<div id="allWalletList">';
						$walletCheck=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
						if(mysqli_num_rows($walletCheck)>0){
							echo'<br><u>Wallet Info:</u><br><br>
							<div id="currentWallets">';
								while($rowWallets=mysqli_fetch_assoc($walletCheck)){
									echo '<div class="eachCurrentWallet">
										<div>'.$rowWallets['walletName'].'</div><hr> &#8377;'.number_format($rowWallets['walletValue']).'<br><br>
										<div>
											<button class="basicButtonOuter smallButton" onclick="showEditWallet(\''.$rowWallets['id'].'\')">edit</button>
											<button class="redButtonOuter smallButton" onclick="showDeleteWallet(\''.$rowWallets['id'].'\')">delete</button>
										</div>
									</div>';
								}
							echo'</div>';
							echo'<br><button type="button" class="closeButton noDisplay" onclick="closeWalletDiv()">CLOSE</button><br><br>';
						}else{
							echo'<br>No wallets registered.<br>';
						}
					echo '</div>';
					/*Wallet amount transfers*/
					echo'<div id="addMoneyToWallet">';
						if(mysqli_num_rows($walletCheck)>1){
							echo'<br><u>Add Money to Wallet:</u><br><br>
							Debit From:<select class="inputStyle" id="walletDebit" onchange="setWalletSelection(\''."debit".'\')"><option value="">Select</option>';
								$walletCheck=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
								while($rowWallets=mysqli_fetch_assoc($walletCheck)){
									echo '<option value="'.$rowWallets['walletName'].'">'.$rowWallets['walletName'].'</option>';
								}	
							echo'</select><br>
							Credit To:<select class="inputStyle" id="walletCredit" onchange="setWalletSelection(\''."credit".'\')"><option value="">Select</option>';
								$walletCheck=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
								while($rowWallets=mysqli_fetch_assoc($walletCheck)){
									echo '<option value="'.$rowWallets['walletName'].'">'.$rowWallets['walletName'].'</option>';
								}	
							echo'</select><br>
							&#8377;<input type="number" min="0" max="9999999" id="walletExchangeAmount" class="inputStyle" placeholder="Amount"><br>
							<div id="walletExchangeErrorMessage"></div>
							<button type="button" id="walletExchangeSubmit" class="submitButton" onclick="walletExchange()">SEND</button>
							<button type="button" class="closeButton noDisplay" onclick="closeWalletDiv()">CLOSE</button><br><br>';
						}else{
							if(mysqli_num_rows($walletCheck)<1){echo 'No wallets registered.';}
							else{echo'<br>Minimum of 2 wallets required to transfer amount!<br><br>';}
						}
					echo'</div>';
					/*Wallet History*/
					echo'<div id="walletHistory">';
					$walletCheckHistory=mysqli_query($conn,"SELECT * FROM wallethistory WHERE walletUsername='$username' ORDER BY walletTransferDate DESC");
						if(mysqli_num_rows($walletCheckHistory)>0){
							echo'<div class="tableContainer"><table class="analysisTable">
								<thead>
									<tr>
										<th>Transfer From</th><th>Transfer To</th><th>Amount Transfered</th><th>Date of Transfer</th><th>Expense / Income</th><th>Category</th><th>Details</th><th>Delete</th>
									</tr>
								</thead>
								<tbody>';
									while($rowWalletHistory=mysqli_fetch_assoc($walletCheckHistory)){
										echo'<tr>
											<td>'.$rowWalletHistory['walletNameFrom'].'</td>';
											if($rowWalletHistory['walletNameTo']=='walletExpenseOK'){echo'<td>-</td>';}else{echo'<td>'.$rowWalletHistory['walletNameTo'].'</td>';}
											echo'<td>&#8377;'.number_format($rowWalletHistory['walletValue']).'</td><td>'.date('d-M-Y (l)',strtotime($rowWalletHistory['walletTransferDate'])).'</td>';
											if($rowWalletHistory['type']=='walletTransfer'){
												echo'<td>-</td>
												<td>-</td><td>-</td>';
											}else{
												echo'<td>'.$rowWalletHistory['type'].'</td>
												<td>'.$rowWalletHistory['category'].'</td><td>'.$rowWalletHistory['details'].'</td>';
											}
											echo'<td><button class = "expensesDeleteButton" onclick="deleteWalletHistory('.$rowWalletHistory['id'].')">Delete</button></td>
										</tr>';
									}
								echo'</tbody>
								</table>
							</div>';
						}else{
							echo 'No transactions found.';
						}
					echo'<br><br></div>';
				echo'</div>';

			?>	
			</div>

			<!--Content Expense Form-->
			<div id = "contentExpenseForm">
				<!--Expenses Form-->
				<div id="expenseForm">
					<span id="expenseHeader" class = "headingName errorMessage">Add New Expense: </span>
					<span style="display: inline-block;">&#8377;<input type="number" min="0" max="9999999" id="expenseAmount" class="expenseInput inputStyle" placeholder="Amount" onfocus="showOtherDiv('expense')"></span>
					<span id="expenseFormOtherDataShow">
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
						<input type="date" id="expenseDate" class="expenseInput inputStyle" value="<?php echo $todayDate; ?>"><br>
						<textarea id="expenseDetails" class="expenseInput inputStyle" placeholder="More Details"></textarea><br>
						<?php
						echo'<div id="expenseWalletList">';
						$getWallet=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
						if(mysqli_num_rows($getWallet)>0){
							while($rowWallet=mysqli_fetch_assoc($getWallet)){
								$walletNameTrim=str_replace(' ', '', $rowWallet['walletName']);
								echo '<span class="inlineBlockDiv"><input type="radio" id="expenseWallet'.$walletNameTrim.'" class="inputStyle" name="expenseWallet" value="'.$rowWallet['walletName'].'"><label for="expenseWallet'.$walletNameTrim.'">'.$rowWallet['walletName'].'</label></span>';
							}
							echo'<span class="inlineBlockDiv"><input type="radio" id="expenseWalletCash" class="inputStyle" name="expenseWallet" value="cash"><label for="expenseWalletCash">Cash</label></span>
							<span class="inlineBlockDiv"><a href="#marquee" type="button" class="greenButtonOuter smallButton" onclick="showWalletDiv(\''."addWalletData".'\')">New Wallet</a></span>';
						}
						echo'</div>';
						?>
						<div id="expenseErrorMessage"></div><br>
						<?php /*Username*/
							echo '<input type="hidden" id="expenseUsername" value="'.$username.'">';
						?>
						<button type="button" id="expenseSubmit" class="submitButton" onclick="addExpense('expense')">ADD</button>
						<button type="button" class="closeButton hideButton" onclick="closeOtherDiv('expense')">CLOSE</button>
					</span>
				</div>
			</div>

			<!--Content Income Form-->
			<div id = "contentIncomeForm">
				<!--Income Form-->
				<div id="incomeForm">
					<span id="incomeHeader" class = "headingName successMessage">Add New Income: </span>
					<span style="display: inline-block;">&#8377;<input type="number" min="0" max="9999999" id="incomeAmount" class="incomeInput inputStyle" placeholder="Amount" onfocus="showOtherDiv('income')"></span>
					<span id="incomeFormOtherDataShow">
						<select id="incomeCategory" class="incomeInput inputStyle">
							<option value="">Select</option>
							<option value="salary">Salary</option>
							<option value="investment">Investment / Stocks</option>
							<option value="rent">Rent</option>
							<option value="bonus">Bonus</option>
							<option value="allowance">Allowance</option>
							<option value="others">Others</option>
						</select>
						<input type="date" id="incomeDate" class="incomeInput inputStyle" value="<?php echo $todayDate; ?>"><br>
						<textarea id="incomeDetails" class="incomeInput inputStyle" placeholder="More Details"></textarea><br>
						<?php
						echo'<div id="incomeWalletList">';
						$getWallet=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
						if(mysqli_num_rows($getWallet)>0){
							while($rowWallet=mysqli_fetch_assoc($getWallet)){
								$walletNameTrim=trim($rowWallet['walletName']);
								echo '<span class="inlineBlockDiv"><input type="radio" id="incomeWallet'.$walletNameTrim.'" class="inputStyle" name="incomeWallet" value="'.$rowWallet['walletName'].'"><label for="incomeWallet'.$walletNameTrim.'">'.$rowWallet['walletName'].'</label></span>';
							}
							echo'<span class="inlineBlockDiv"><input type="radio" id="incomeWalletCash" class="inputStyle" name="incomeWallet" value="Cash"><label for="incomeWalletCash">Cash</label></span>
							<a href="#marquee" type="button" class="greenButtonOuter smallButton" onclick="showWalletDiv(\''."addWalletData".'\')">New Wallet</a>';
						}
						echo'</div>';
						?>
						<div id="incomeErrorMessage"></div><br>
						<?php /*Username*/
							echo '<input type="hidden" id="incomeUsername" value="'.$username.'">';
						?>
						<button type="button" id="incomeSubmit" class="submitButton" onclick="addExpense('income')">ADD</button>
						<button type="button" class="closeButton hideButton" onclick="closeOtherDiv('income')">CLOSE</button>
					</span>
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

					/*Show Total Expenses*/
					echo '<button id = "dashBoardExpense" onclick = "showExpenses(\''.'expense'.'\')">
						<span class="sideHeading">Expenses:</span>&nbsp&#8377;<span class="errorMessage" id="newExpense">'.number_format($totalExpenses).'</span>
					</button>';
				
					/*Show Total Income*/
					echo '<button id = "dashBoardIncome" onclick = "showExpenses(\''.'income'.'\')">
						<span class="sideHeading">Income:</span>&nbsp&#8377;<span class="successMessage" id="newIncome">'.number_format($totalIncome).'</span>
					</button>';

					/*Net Budget*/
					echo '<button id = "dashBoardBudget" onclick = "showBudget()">
						<span class="sideHeading">Flow:</span>&nbsp&#8377;<span class="bufferMessage" id="newBudget">'.number_format($totalBudget).'</span>
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
								<td style="position:sticky;left:0px;z-index:2">'.date('M-Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'</td>';
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
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'food'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalFood']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalMarket']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'market'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalMarket']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalTravel']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'travel'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalTravel']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalPetrol']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'petrol'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalPetrol']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalHouseWorks']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'houseWorks'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalHouseWorks']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalHealth']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'health'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalHealth']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalEducation']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'education'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalEducation']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalPersonal']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'personal'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalPersonal']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalSavings']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'savings'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalSavings']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalOffice']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'office'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalOffice']).'</a>';
											}
										echo '</span></td>
										<td><span class = "errorMessage">';
											if($rowTotalsExpense['totalOthers']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'others'.'\', \''.'expense'.'\')">&#8377;'.number_format($rowTotalsExpense['totalOthers']).'</a>';
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
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'salary'.'\', \''.'income'.'\')">&#8377;'.number_format($rowTotalsIncome['totalSalary']).'</a>';
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalInvestment']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'investment'.'\', \''.'income'.'\')">&#8377;'.number_format($rowTotalsIncome['totalInvestment']).'</a>';
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalRent']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'rent'.'\', \''.'income'.'\')">&#8377;'.number_format($rowTotalsIncome['totalRent']).'</a>';
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalBonus']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'bonus'.'\', \''.'income'.'\')">&#8377;'.number_format($rowTotalsIncome['totalBonus']).'</a>';
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalAllowance']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'allowance'.'\', \''.'income'.'\')">&#8377;'.number_format($rowTotalsIncome['totalAllowance']).'</a>';
											}
										echo '</span></td>
										<td><span class = "successMessage">';
											if($rowTotalsIncome['totalOthers']==0){
												echo '-';
											}else{
												echo '<a class="subDetailsButton" onclick="showExpenseSubDetails(\''.date('m', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.date('Y', mktime(0, 0, 0, $monthOfAnalysis, 1, $yearOfAnalysis)).'\', \''.'others'.'\', \''.'income'.'\')">&#8377;'.number_format($rowTotalsIncome['totalOthers']).'</a>';
											}
										echo '</span></td>';
									}
								}
							echo '</tr></tbody>
							</div>';	
							}
						}
						echo'</table>';
					echo '</div></div>';		
				}

				/*Graphs*/
				/*Data Accumulation*/
				/*Get each month*/
				$getEachExpenseMonth = mysqli_query($conn, "SELECT DISTINCT month(date) AS month, year(date) AS year FROM expenses WHERE username = '$username' AND type='expense' ORDER BY date DESC");
				if(mysqli_num_rows($getEachExpenseMonth)>0){
					echo '<div id = "contentGraphForm">
						<div class = "headingName bufferMessage">Graphical Representations: </div>';
					
					while($EachExpenseMonth = mysqli_fetch_assoc($getEachExpenseMonth)){
						$monthOfGraph = $EachExpenseMonth['month'];
						if($monthOfGraph<10){$monthOfGraph='0'.$monthOfGraph;}
						$yearOfGraph = $EachExpenseMonth['year'];
						$monthWordOfGraph = date('M-Y', mktime(0, 0, 0, $monthOfGraph, 1, $yearOfGraph));

						$getMonthExpenses=mysqli_query($conn,"SELECT * FROM expenses WHERE month(date)='$monthOfGraph' AND year(date)='$yearOfGraph' AND username = '$username' AND type='expense' ORDER BY date ASC");
						if(mysqli_num_rows($getMonthExpenses)>0){$dateCheck=1;$largestExpenseInMonth=0;$largestExpenseInMonthSpecificDate=0;$averageExpenseInMonthTotal=0;$averageExpenseInMonth=0;$averageExpenseInMonthCount=1;
							
							while($rowMonthExpense=mysqli_fetch_assoc($getMonthExpenses)){
								if($dateCheck!=$rowMonthExpense['date']){/*Different Date*/
									if($largestExpenseInMonth==0){/*Initialize*/
										$largestExpenseInMonth=$rowMonthExpense['amount'];
									}else if($largestExpenseInMonth<$largestExpenseInMonthSpecificDate){/*Compare with previous*/
										$largestExpenseInMonth=$largestExpenseInMonthSpecificDate;	
									}else if($largestExpenseInMonth<$rowMonthExpense['amount']){
										$largestExpenseInMonth=$rowMonthExpense['amount'];	
									}
									$averageExpenseInMonthTotal+=$rowMonthExpense['amount'];/*To get average total*/
									$averageExpenseInMonthCount++;/*For calculating average*/
									$largestExpenseInMonthSpecificDate=$rowMonthExpense['amount'];
								}else{/*Same Date*/
									$largestExpenseInMonthSpecificDate+=$rowMonthExpense['amount'];
									$averageExpenseInMonthTotal+=$rowMonthExpense['amount'];/*To get average total*/
								}
								/*Send data to JS*/
								if($dateCheck!=$rowMonthExpense['date']){
									echo'<input id="expenseForGraphOf'.date('dmY',strtotime($rowMonthExpense['date'])).'" type="hidden" value="'.$largestExpenseInMonthSpecificDate.'">';
								}else{
									echo'<script type="text/javascript">
										document.getElementById("expenseForGraphOf'.date('dmY',strtotime($rowMonthExpense['date'])).'").value="'.$largestExpenseInMonthSpecificDate.'";
									</script>';
								}
								$dateCheck=$rowMonthExpense['date'];
							}
							/*Average Expense*/
							$averageExpenseInMonth=$averageExpenseInMonthTotal/($averageExpenseInMonthCount-1);
							/*Overall Expense*/
							$getOverallExpenseInMonth=mysqli_query($conn,"SELECT SUM(amount) AS totalExpenseAmount FROM expenses WHERE username = '$username' AND type = 'expense' AND month(date) = '$monthOfGraph' AND year(date) = '$yearOfGraph'");
							if(mysqli_num_rows($getOverallExpenseInMonth)>0){
								while($rowOverallExpenseInMonth=mysqli_fetch_assoc($getOverallExpenseInMonth)){
									$overallExpenseInMonth=$rowOverallExpenseInMonth['totalExpenseAmount'];
								}
							}
							/*Plotting graph*/
							echo'<div class="flexDisplay graphDescription">
								<div class="graphMonthName">'.$monthWordOfGraph.'</div>
								<div class="sideHeading">
									<span>Overall: &#8377;'.number_format($overallExpenseInMonth).'</span>;
									<span>Highest: &#8377;'.number_format($largestExpenseInMonth).'</span>;
									<span>Average: &#8377;'.number_format($averageExpenseInMonth).'</span>
								</div>
							</div>';
							echo'<div class="graphDiv">';
								echo'<div class="graphArea">';
									echo'<div class="graphGrid" onclick="showGraphValues()">';
									echo'<div class="sideHeading graphYAxisLabel"><span style="background-color: rgba(245, 245, 245, 0.5);">&#8377;'.number_format($largestExpenseInMonth).'</span></div>';
									for($m=1;$m<=31;$m++){
										if($m<10){$m='0'.$m;}
										echo '<div class="graphGridLines" id="expenseValueGraphOf'.$m.$monthOfGraph.$yearOfGraph.'"></div>';
									}
									echo'</div>
									<div class="graphXAxis">';
										echo'<div class="sideHeading graphYAxisLabel"><span style="background-color: rgba(245, 245, 245, 0.5);">&#8377;0</span></div>';
										for($m=1;$m<=31;$m++){
											if($m<10){$m='0'.$m;}
											echo'<div class="sideHeading graphXAxisLabel">'.$m.'</div>';
										}
									echo'</div>
								</div>';
							echo'</div><br>
							<br><br>
							<hr><br><br>';

							/*Scripts for graph*/
							echo'<script type="text/javascript">';
								for($dc=1;$dc<=31;$dc++){
									if($dc<10){$dc='0'.$dc;}
									echo'
									if(document.getElementById("expenseForGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'")){
										var graphExpenseID'.$dc.$monthOfGraph.$yearOfGraph.'=document.getElementById("expenseForGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'").value;
										/*console.log("'.$largestExpenseInMonth.'"+"--"+"'.$monthOfGraph.'"+" - "+"'.$yearOfGraph.'");*/
									}
									if(document.getElementById("expenseValueGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'")){
										if(graphExpenseID'.$dc.$monthOfGraph.$yearOfGraph.'){
											document.getElementById("expenseValueGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'").innerHTML="<span class='."graphValueText".'>&#8377;"+graphExpenseID'.$dc.$monthOfGraph.$yearOfGraph.'+"</span>";
											var expensePercentOfDate'.$dc.$monthOfGraph.$yearOfGraph.'=Math.ceil(parseInt(graphExpenseID'.$dc.$monthOfGraph.$yearOfGraph.' * 70 )/parseInt('.($averageExpenseInMonth).'));
											if(graphExpenseID'.$dc.$monthOfGraph.$yearOfGraph.'>'.'(2*'.$averageExpenseInMonth.')){
												document.getElementById("expenseValueGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'").style.background="linear-gradient(to top, rgba(250,50,100,0.7) "+expensePercentOfDate'.$dc.$monthOfGraph.$yearOfGraph.'+"%, rgb(245,245,245) "+expensePercentOfDate'.$dc.$monthOfGraph.$yearOfGraph.'+"%, rgb(245,245,245) 100%)";
											}else if((graphExpenseID'.$dc.$monthOfGraph.$yearOfGraph.'<'.'(2*'.$averageExpenseInMonth.')) && (graphExpenseID'.$dc.$monthOfGraph.$yearOfGraph.'>'.'('.$averageExpenseInMonth.'))){
												document.getElementById("expenseValueGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'").style.background="linear-gradient(to top, rgba(250,100,50,0.7) "+expensePercentOfDate'.$dc.$monthOfGraph.$yearOfGraph.'+"%, rgb(245,245,245) "+expensePercentOfDate'.$dc.$monthOfGraph.$yearOfGraph.'+"%, rgb(245,245,245) 100%)";
											}else{
												document.getElementById("expenseValueGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'").style.background="linear-gradient(to top, rgba(100,250,50,0.7) "+expensePercentOfDate'.$dc.$monthOfGraph.$yearOfGraph.'+"%, rgb(245,245,245) "+expensePercentOfDate'.$dc.$monthOfGraph.$yearOfGraph.'+"%, rgb(245,245,245) 100%)";
											}
										}else{
											document.getElementById("expenseValueGraphOf'.$dc.$monthOfGraph.$yearOfGraph.'").innerHTML="<span class='."graphValueText".' style='."background-color:green".'>&#8377;0</span>";
										}
									}';
								}
							echo'</script>';
						}
					}
					echo'</div>';
				}

			?>
		



			<br><br>

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
<script type="text/javascript">
	/*Enter Form*/
	var expenseFormEnter = document.getElementById('expenseForm');
	expenseFormEnter.addEventListener("keyup", function(event){
		if(event.keyCode == 13){
			event.preventDefault();
			document.getElementById("expenseSubmit").click();
		}
	})
	var incomeFormEnter = document.getElementById('incomeForm');
	incomeFormEnter.addEventListener("keyup", function(event){
		if(event.keyCode == 13){
			event.preventDefault();
			document.getElementById("incomeSubmit").click();
		}
	})
</script>
</html>