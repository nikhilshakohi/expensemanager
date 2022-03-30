<?php

include_once 'db.php';
session_start();
date_default_timezone_set('Asia/Kolkata');
/*if(!isset($_SESSION['id'])){echo '<script>window.location.href="index.php"</script>';}*//*To redirect to login page when not logged in*/

/*Signup*/
if(isset($_POST['signup'])){
	$username = mysqli_real_escape_string($conn,$_POST['username']);
	$password = mysqli_real_escape_string($conn,$_POST['password']);
	$email = mysqli_real_escape_string($conn,$_POST['email']);
	$fullName = mysqli_real_escape_string($conn,$_POST['fullName']);
	/*Check if user already created*/
	$checkUser = mysqli_query($conn,"SELECT * FROM users WHERE username = '$username' OR email = '$email'");
	if(mysqli_num_rows($checkUser)>0){
		echo '<span class="errorMessage">username or e-mail already available! Try with a new one / Login</span>';
	}else{
		$name = ucwords($fullName);/*To convert name to first letter Capital Name*/
		$addUser = mysqli_query($conn,"INSERT INTO users (username, password, email, name) VALUES ('$username', '$password', '$email', '$name')");
		echo '<span class="successMessage">User added successfully! Login to continue..</span>';
	}
}

/*Login*/
if(isset($_POST['login'])){
	$username = mysqli_real_escape_string($conn,$_POST['username']);
	$password = mysqli_real_escape_string($conn,$_POST['password']);
	/*Check for user*/
	$checkUser = mysqli_query($conn,"SELECT * FROM users WHERE (username = '$username' OR email = '$username')");
	if(mysqli_num_rows($checkUser)>0){
		while($rowUser = mysqli_fetch_assoc($checkUser)){
			$checkPassword = $rowUser['password'];
			$passwordChecked="notVerified";
			$encID=(149118912*$rowUser['id'])+149118912;
			if(isset($_COOKIE['userID'])){
				if($_COOKIE['userID']==$encID){$passwordChecked="verified";}
				else if($checkPassword == $password){$passwordChecked="verified";}
				else{$passwordChecked="notVerified";}
			}else{
				if($checkPassword == $password){$passwordChecked="verified";}
				else{$passwordChecked="notVerified";}
			}
			if($passwordChecked=="verified"){
				$_SESSION['id'] = $rowUser['id'];
				$_SESSION['username'] = $rowUser['username'];
				if($_POST['autoLogin']=='enabled'){
					setcookie('autoLogin','yes',time()+18000,'/');
					setcookie('username',$rowUser['username'],time()+18000,'/');
					$encID=(149118912*$rowUser['id'])+149118912;
					setcookie('userID',$encID,time()+18000,'/');
				}
				echo 'loginSuccess';
				exit();
			}else{
				echo '<span class="errorMessage">Invalid Credentials</span>';
			}	
		}
	}else{
		echo '<span class="errorMessage">No User Found! Signup to create an account</span>';
	}
}

/*Add Expense*/
if(isset($_POST['addExpense'])){
	$amount = mysqli_real_escape_string($conn,$_POST['expenseAmount']);
	$date = mysqli_real_escape_string($conn,$_POST['expenseDate']);
	$category = mysqli_real_escape_string($conn,$_POST['expenseCategory']);
	$details = mysqli_real_escape_string($conn,$_POST['expenseDetails']);
	$username = mysqli_real_escape_string($conn,$_POST['expenseUsername']);
	$wallet = mysqli_real_escape_string($conn,$_POST['expenseWallet']);
	$type = $_POST['type'];
	$checkProcess='check';
	if($wallet!='noWalletRegd' && $wallet!='cash'){
		/*Change Wallet Data*/
		$walletCheck=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username' AND walletName='$wallet'");
		if(mysqli_num_rows($walletCheck)>0){
			while($rowWallets=mysqli_fetch_assoc($walletCheck)){
				$currentWalletValue=$rowWallets['walletValue'];
			}
			if($type=='expense'){$newWalletValue=$currentWalletValue-$amount;}
			elseif($type=='income'){$newWalletValue=$currentWalletValue+$amount;}
			if($newWalletValue<0){
				echo 'noEnoughMoneyInWallet';
				$checkProcess='stop';
			}else{
				echo 'enoughMoneyInWallet';
				$checkProcess='continue';
			}
		}
	}else{
		echo 'noWalletRegd';
	}

	if($checkProcess!='stop'){
		$addExpense = mysqli_query($conn,"INSERT INTO expenses (username, type, amount, date, category, wallet, details) VALUES ('$username', '$type', '$amount', '$date', '$category', '$wallet', '$details')");
		$addWalletHistory = mysqli_query($conn, "INSERT INTO wallethistory (walletUsername, walletNameFrom, walletNameTo, walletValue, walletTransferDate, type, category, details) VALUES ('$username', '$wallet', 'walletExpenseOK', '$amount', '$date', '$type', '$category', '$details')");
		echo '-period-<span class="successMessage">'.ucwords($type).' Added</span>
		-period-';/*To send two inputs to client*/
	 	$totalExpense = getNewExpenseDetails($conn, $username, 'expense'); /*Get New Expense*/	
	 	$totalIncome = getNewExpenseDetails($conn, $username, 'income'); /*Get New Income*/
		$totalBudget = $totalIncome - $totalExpense;
		echo number_format($totalExpense); 
		echo '-period-';
		echo number_format($totalIncome); 
		echo '-period-';
		echo number_format($totalBudget);
		/*Get Marquee Outputs*/

		/*Calculate Today Expenses*/
		$todayDate = date('Y-m-d',time());
		$yesterdayDate = date('Y-m-d', strtotime("-1 days"));
		$thisMonth = date('m',time());
		$thisYear = date('Y',time());
		$totalTodayExpenses = 0; $totalTodayIncome = 0;
		$totalYesterdayExpenses = 0; $totalYesterdayIncome = 0;
		$totalThisMonthExpenses = 0; $totalThisMonthIncome = 0;
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
		echo '-period-'.number_format($totalTodayExpenses).'-period-'.number_format($totalTodayIncome).
		'-period-'.number_format($totalYesterdayExpenses).'-period-'.number_format($totalYesterdayIncome).
		'-period-'.number_format($totalThisMonthExpenses).'-period-'.number_format($totalThisMonthIncome);

		/*Change Wallet Data*/
		if($checkProcess=='continue'){
			$addWalletMoney=mysqli_query($conn,"UPDATE wallet SET walletValue='$newWalletValue' WHERE walletName='$wallet' AND walletUsername='$username'");
			echo'-period-';
			getWalletSummary($conn,$username);
			echo'-period-';
			getWalletHistory($conn,$username);
		}
	}else{
		echo '-period-noEnoughMoneyInWalletAgain';
	}
}

/*Show Expenses*/
if(isset($_POST['showExpense'])){
	$changeType = $_POST['changeType'];
	$username = $_POST['username'];
	$getExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' AND type = '$changeType' ORDER BY date DESC, id DESC");
	if(mysqli_num_rows($getExpenses)>0){
		echo '<div id = "expensesListDiv">
			 <div class = "expensesListMainHeading">
				<div class = "listMainHeadingName">'.ucwords($changeType).' Details</div>
				<div id = "loaderOf'.$changeType.'"></div>
				<div class = "closeButton" onclick = "hideListDivs()">Close</div>
			</div>';
			/*Month-Wise Expese*/
			$getAllMonths = mysqli_query($conn, "SELECT DISTINCT month(date) AS Month, year(date) AS Year FROM expenses WHERE type = '$changeType' AND username = '$username' ORDER BY date DESC");
			if(mysqli_num_rows($getAllMonths)>0){
				echo '<div id = "expenseMonthList">';
					while ($rowMonths = mysqli_fetch_assoc($getAllMonths)) {
						$rowMonth = $rowMonths['Month'];
						$rowYear = $rowMonths['Year'];
						echo '<div class = "'.$changeType.'sList" onclick = "toggleSubListDiv(\''.$changeType.'\', \''.'Day'.'\', \''.$rowMonth.'\', \''.$rowYear.'\')">
							<div>';
								echo date('F Y', mktime(0, 0, 0, $rowMonth+1, 0, $rowYear)).' - ';
								/*mktime used to get time details mktime(hour, minute, second, month, date, year)*/
								/*Get the expense of each month*/
								$getMonthExpenses = mysqli_query($conn, "SELECT SUM(amount) AS monthExpense FROM expenses WHERE month(date) = $rowMonth AND year(date) = $rowYear AND type = '$changeType' AND username = '$username'");
								if(mysqli_num_rows($getMonthExpenses)>0){
									while($rowMonthExpense = mysqli_fetch_assoc($getMonthExpenses)){
										echo '&#8377;<span ';
											if($changeType == 'income'){
												echo ' class = "successMessage">';
											}else{
												echo ' class = "errorMessage">';
											}
											echo number_format($rowMonthExpense['monthExpense']).'</span><br>';
									}
								}
							echo '</div>
							<div id = "loader'.$changeType.'DayListOfMonth'.$rowMonth.'AndYear'.$rowYear.'"></div>
							<div class = "arrowDiv">
								<div class = "rightDoubleArrow"></div>
							</div>
						</div>';
						/*Day Wise Expenses*/
						echo '<div id = "'.$changeType.'DayListOfMonth'.$rowMonth.'AndYear'.$rowYear.'"></div>';
					}
				echo '</div>';
			}
		echo '</div>';
	}else{
		echo 'No expenses added!';
	}
}

/*Show Expenses Day-Wise Expese*/
if(isset($_POST['checkSubExpense'])){
	$type = $_POST['type'];
	$listType = $_POST['listType'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	$username = getUsername($conn, $_SESSION['id']);

	$getAllDays = mysqli_query($conn, "SELECT * FROM expenses WHERE type = '$type' AND username = '$username' AND month(date) = '$month' AND year(date) = '$year' ORDER BY date DESC");
	if(mysqli_num_rows($getAllDays)>0){
		/*Dummy Date*/$checkDate = date('1757-08-15');
		while ($rowDays = mysqli_fetch_assoc($getAllDays)) {
			if($checkDate != $rowDays['date']){
				$rowDay = $rowDays['date'];
				$rowDayInNumberFormat = strtotime(date('Y-m-d 05:30:00', strtotime($rowDay)));/*Send date in number format*/
				echo '<div class = "expensesSubList" onclick = "toggleSubLowerListDiv(\''.$type.'\', \''.'All'.'\', \''.$rowDayInNumberFormat.'\')">
					<div>';
						/*mktime used to get time details mktime(hour, minute, second, month, date, year)*/
						echo '&nbsp;'.date('d F Y (D)', strtotime($rowDay)).' - ';
						/*Get the expense of each day*/
						$getDayExpenses = mysqli_query($conn, "SELECT SUM(amount) AS dayExpense FROM expenses WHERE date = '$rowDay' AND type = '$type' AND username = '$username' ORDER BY date DESC");
						if(mysqli_num_rows($getDayExpenses)>0){
							while($rowDayExpense = mysqli_fetch_assoc($getDayExpenses)){
								echo '&#8377;<span ';
								if($type == 'income'){
									echo ' class = "successMessage">';
								}else{
									echo ' class = "errorMessage">';
								}
								echo number_format($rowDayExpense['dayExpense']).'</span><br>';
							}
						}
						
						$checkDate = $rowDays['date'];
						$checkDateInNumberFormat = strtotime(date('Y-m-d 05:30:00', strtotime($checkDate)));
				
					echo '</div>
					<div id = "loader'.$type.'AllListOfDate'.$checkDateInNumberFormat.'"></div>
					<div class = "arrowDiv">
						<div class = "rightDoubleArrow"></div>
						<div class = "rightDoubleArrow"></div>
					</div>
				</div>';
				echo '<div id = "'.$type.'AllListOfDate'.$checkDateInNumberFormat.'"></div>';
			}
		}
	}
}

/*Show Expense Sub Inner Details*/
if(isset($_POST['checkSubInnerExpense'])){
	$type = $_POST['type'];
	$listType = $_POST['listType'];
	$dateInNumberFormat = $_POST['date'];
	$username = getUsername($conn, $_SESSION['id']);

	$checkDate = date('Y-m-d', $dateInNumberFormat);
	$getExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE date = '$checkDate' AND username = '$username' AND type = '$type' ORDER BY date DESC, id DESC");
	if(mysqli_num_rows($getExpenses)>0){
		echo '<div class="tableContainer"><table class="analysisTable">
			<thead>
				<tr>
					<th>Category</th><th>Money</th><th>Details</th><th>Wallet</th><th>Edit</th><th>Delete</th>
				</tr>
			</thead>
			<tbody>
		';
		while($rowExpenses = mysqli_fetch_assoc($getExpenses)){
			/*<div class = "expensesSubLowerList">
				<div>
					<span>&nbsp;&nbsp;&nbsp;'.date('d-M-Y (D)', strtotime($rowExpenses['date'])).'</span> 
					- <i>'.ucwords($rowExpenses['category']).'</i> - <b>&#8377;'.number_format($rowExpenses['amount']).'</b>
					<div>&nbsp;&nbsp;&nbsp;'.$rowExpenses['details'].'</div>
				</div>
				<div>
					<button class = "expensesEditButton" onclick="editExpenses('.$rowExpenses['id'].')">Edit</button>
					<button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowExpenses['id'].')">Delete</button>
				</div>
			</div>
			<div>*/
			if(date('h',strtotime($rowExpenses['date']))==12 && date('i',strtotime($rowExpenses['date']))==0 && date('A',strtotime($rowExpenses['date']))=='AM'){
				$getDateTime='-';
			}else{
				$getDateTime=date('h:i A',strtotime($rowExpenses['date']));
			}

			echo '<tr>
				<td>'.ucwords($rowExpenses['category']).'</td><td>'.'&#8377;'.number_format($rowExpenses['amount']).'</td><td>';
				if($rowExpenses['details']!=''){echo $rowExpenses['details'];}else{echo '-';}
				echo'</td><td>';
				if($rowExpenses['wallet']!=''&&$rowExpenses['wallet']!='noWalletRegd'){echo $rowExpenses['wallet'];}else{echo '-';}
				echo'</td><td><button class = "expensesEditButton" onclick="editExpenses('.$rowExpenses['id'].')">Edit</button></td>
				<td><button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowExpenses['id'].')">Delete</button></td>
			</tr>';
		}
		echo'</tbody>
		</table></div>';
	}
}

/*Show Budget*/
if(isset($_POST['showBudget'])){
	$username = $_POST['username'];
	$getBudget = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' ORDER BY date DESC");
	if(mysqli_num_rows($getBudget)>0){
		echo '<div id = "budgetListDiv">
		<div class = "expensesListHeading">
			<div class = "listMainHeadingName">Budget List </div>
			<div class = "closeButton" onclick = "hideListDivs()">Close</div>
		</div>
		<div class="tableContainer"><table class="analysisTable">
		<thead>
			<tr>
				<th>Date</th><th>Category</th><th>Money</th><th>Type</th><th>Details</th><th>Wallet</th><th>Edit</th><th>Delete</th>
			</tr>
		</thead>
		<tbody>';

		while($rowBudget = mysqli_fetch_assoc($getBudget)){
			/*echo '<div class = "budgetList">
				<div>
					<span>
						'.date('d-M-Y (D)', strtotime($rowBudget['date'])).'
					</span> 
					- 
					<i>
						'.ucwords($rowBudget['category']).'
					</i>
					 -  
					<b>
						&#8377;
						<span ';
							if($rowBudget['type'] == 'income'){
								echo ' class = "successMessage"';
							}else{
								echo ' class = "errorMessage"';
							}
							echo ' >'.number_format($rowBudget['amount']); 
						echo'</span>
					</b>';
					if($rowBudget['type'] == 'income'){
						echo '&#8593;';
					}else{
						echo '&#8595;';
					}
					echo '<div>'.$rowBudget['details'].'</div>
				</div>
				<div>
					<button class = "expensesEditButton" onclick="editExpenses('.$rowBudget['id'].')">Edit</button>
					<button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowBudget['id'].')">Delete</button>
				</div>
			</div>';*/
			echo '<tr>
				<td>'.date('d-M-Y (D)', strtotime($rowBudget['date'])).'</td><td>'.ucwords($rowBudget['category']).'</td>
				<td>&#8377;<span ';
					if($rowBudget['type'] == 'income'){
						echo ' class = "successMessage"';
					}else{
						echo ' class = "errorMessage"';
					}
					echo ' >'.number_format($rowBudget['amount']).'
				</span></td>
				<td>'.ucwords($rowBudget['type']).'</td>
				<td>'.$rowBudget['details'].'</td>';
				if($rowBudget['wallet']!=''&&$rowBudget['wallet']!='noWalletRegd'){echo'<td>'.$rowBudget['wallet'].'</td>';}else{echo'<td>-</td>';}
				echo'<td><button class = "expensesEditButton" onclick="editExpenses('.$rowBudget['id'].')">Edit</button></td>
				<td><button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowBudget['id'].')">Delete</button></td>
			</tr>';

		}
		echo'</tbody>
		</table></div>';
	}else{
		echo 'No expenses added!';
	}
	echo '</div>';
}

/*Show Edit Expenses*/
if(isset($_POST['showEditExpenses'])){
	$expensesId = $_POST['expensesId'];
	$checkExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE id = '$expensesId'");
	if(mysqli_num_rows($checkExpenses)>0){
		while($rowExpenses = mysqli_fetch_assoc($checkExpenses)){
			$category = ucwords($rowExpenses['category']);
			echo'<div id="editExpensesOuterDiv" style="z-index: 3;"><div id="editExpensesInnerDiv">
				<h4>Edit Details</h4><hr><br>
				Date: <br><textarea class = "editTextArea" id="editExpenseDate'.$rowExpenses['id'].'" type="date" placeholder = "Edit Date"></textarea><br><br>
				<datalist id = "categoryOptions">
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
				</datalist>
				Category: <br><textarea class = "editTextArea" id="editExpenseCategory'.$rowExpenses['id'].'" list = "categoryOptions" placeholder = "Edit Category">
				</textarea><br><br>
				Amount: <br><textarea class = "editTextArea" id="editExpenseAmount'.$rowExpenses['id'].'" placeholder = "Edit Amount"></textarea><br><br>
				Wallet: <br><textarea class = "editTextArea" id="editExpenseWallet'.$rowExpenses['id'].'" placeholder = "Edit Wallet"></textarea><br><br>
				Details: <br><textarea class = "editTextArea" id="editExpenseDetails'.$rowExpenses['id'].'" placeholder="Edit Details"></textarea><br><br>
				<div id="editExpenseErrorMessage"></div><br><br>
				<button class = "confirmButton closeButton" onclick = "confirmEdit('.$expensesId.')">Done</button>
				<button class = "closeButton" onclick = "hideEditExpense()">Cancel</button><br><br><br>
			</div></div>-period-';
			/*Send data to JS*/
			echo $rowExpenses['amount'].'-period-'.
			$rowExpenses['category'].'-period-'.
			date('d-m-Y',strtotime($rowExpenses['date'])).'-period-'.
			$rowExpenses['details'].'-period-'.
			$rowExpenses['wallet'];
		}
	}
}

/*Show Individual Sub Details*/
if(isset($_POST['checkSubDetails'])){
	$username = $_POST['username'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	$category = $_POST['category'];
	$type = $_POST['type'];
	$checkExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' AND category = '$category' AND month(date) = '$month' AND year(date) = '$year' AND type = '$type' ORDER BY date DESC, id DESC");
	if($category=='others'){
		if($type=='expense'){
			$checkExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' AND type = '$type' AND month(date) = '$month' AND year(date) = '$year' AND (category != 'food' AND category != 'market' AND category != 'travel' AND category != 'petrol' AND category != 'houseWorks' AND category != 'health' AND category != 'education' AND category != 'personal' AND category != 'savings' AND category != 'office') ORDER BY date DESC, id DESC");	
		}else if($type=='income'){
			$checkExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' AND type = '$type' AND month(date) = '$month' AND year(date) = '$year' AND (category != 'salary' AND category != 'investment' AND category != 'rent' AND category != 'bonus' AND category != 'allowance') ORDER BY date DESC, id DESC");	
		}
	}
	if(mysqli_num_rows($checkExpenses)>0){
		echo'<div id="editExpensesOuterDiv" ><div id="editExpensesInnerDiv">
			<div style="font-size:large">More Details:</div><br><br>
			<div class="tableContainer"><table class="analysisTable">
				<thead>
					<tr>
						<th>Date</th><th>Category</th><th>Money</th><th>Details</th><th>Type</th><th>Wallet</th><th>Edit</th><th>Delete</th>
					</tr>
				</thead>
				<tbody>';
				while($rowExpenses = mysqli_fetch_assoc($checkExpenses)){
					echo'<tr>
						<td>'.date('d-M-Y (D)', strtotime($rowExpenses['date'])).'</td><td>'.ucwords($rowExpenses['category']).'</td>
						<td>&#8377;<span ';
							if($rowExpenses['type'] == 'income'){
								echo ' class = "successMessage"';
							}else{
								echo ' class = "errorMessage"';
							}
							echo ' >'.number_format($rowExpenses['amount']).'
						</span></td>
						<td>';
							if($rowExpenses['details']!=''){echo $rowExpenses['details'];}else{echo '-';}
						echo'</td>
						<td>'.ucwords($rowExpenses['type']).'</td>
						<td>';
							if($rowExpenses['wallet']!=''&&$rowExpenses['wallet']!='noWalletRegd'){echo $rowExpenses['wallet'];}else{echo '-';}
						echo'</td>
						<td><button class = "expensesEditButton" onclick="editExpenses('.$rowExpenses['id'].')">Edit</button></td>
						<td><button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowExpenses['id'].')">Delete</button></td>
					</tr>';		
				}
				echo'</tbody>
			</table></div><br><br>
			<button class = "closeButton hideButton" onclick = "hideSubDetailsDiv()">Close</button><br><br><br>
		</div></div>';
	}else{
		echo'<div id="editExpensesOuterDiv" ><div id="editExpensesInnerDiv">
			<br><br>No Details Found..<br><br>
			<button class = "closeButton hideButton" onclick = "hideSubDetailsDiv()">Cancel</button><br><br><br>
		</div></div>';
	}
}

/*Edit Expenses*/
if(isset($_POST['editExpenses'])){
	$expensesId = $_POST['expensesId'];
	$expensesDate = date('Y-m-d',strtotime($_POST['expensesDate']));
	$expensesAmount = $_POST['expensesAmount'];
	$expensesCategory = $_POST['expensesCategory'];
	$expensesDetails = $_POST['expensesDetails'];
	$expensesWallet = $_POST['expensesWallet'];
	$updateExpenses = mysqli_query($conn, "UPDATE expenses SET date = '$expensesDate', amount = '$expensesAmount', category = '$expensesCategory', details = '$expensesDetails', wallet='$expensesWallet' WHERE id = '$expensesId'");
	/*Check Income or Expense*/
	$expensesType = checkIncomeOrExpense($expensesId,$conn);
	/*Check Username*/
	$expensesUsername = checkUsername($expensesId,$conn);
	/*Send Type to JS*/
	echo $expensesType.'-period-';
	/*Get New Expense Value*/
	$getExpenses = mysqli_query($conn,"SELECT * FROM expenses WHERE type = '$expensesType' AND username = '$expensesUsername' ORDER BY id DESC");
	$totalExpenses = 0;
	if(mysqli_num_rows($getExpenses)>0){
		while($rowIncome = mysqli_fetch_assoc($getExpenses)){
			$totalExpenses += $rowIncome['amount'];
		}
		echo number_format($totalExpenses);
	}
}

/*Show Delete Expenses*/
if(isset($_POST['showDeleteExpenses'])){
	$expensesId = $_POST['expensesId'];
	$checkExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE id = '$expensesId'");
	if(mysqli_num_rows($checkExpenses)>0){
		while($rowExpenses = mysqli_fetch_assoc($checkExpenses)){
			$category = ucwords($rowExpenses['category']);
			echo'<div id="deleteExpensesOuterDiv" style="z-index:3"><div id="deleteExpensesInnerDiv">
				<br><br><div class = "headingName">Delete Details</div><hr><br>
				Are you sure you want to delete this data?<br><br>
				<div>Amount: <b>&#8377;'.number_format($rowExpenses['amount']).'</b></div>
				<div>Category: <b>'.ucwords($rowExpenses['category']).'</b></div>
				<div>Date: <b>'.date('d-M-Y (D)', strtotime($rowExpenses['date'])).'</b></div>
				<div>Details: <b>'.$rowExpenses['details'].'</b><br><br></div>
				<div>Wallet: <b>'.$rowExpenses['wallet'].'</b><br><br></div>
				<div id="deleteExpenseErrorMessage"></div><br>
				<button class = "expensesDeleteButton" onclick = "confirmDelete('.$expensesId.')">Delete</button>
				<button class = "expensesEditButton" onclick = "hideDeleteExpense()">Cancel</button>
			</div></div>-period-';
			/*Send data to JS*/
			echo $rowExpenses['amount'].'-period-'.
			$rowExpenses['category'].'-period-'.
			$rowExpenses['date'].'-period-'.
			$rowExpenses['details'].'-period-'.
			$rowExpenses['wallet'];
		}
	}
}

/*Delete Expenses*/
if(isset($_POST['deleteExpenses'])){
	$expensesId = $_POST['expensesId'];
	/*Check Income or Expense*/
	$expensesType = checkIncomeOrExpense($expensesId,$conn);
	/*Check Username*/
	$expensesUsername = checkUsername($expensesId,$conn);
	
	/*Delete Now*/
	$updateExpenses = mysqli_query($conn, "DELETE FROM expenses WHERE id = '$expensesId'");
	/*Get New Expense Value*/
	$getExpenses = getNewExpenseDetails($conn, $expensesUsername, $expensesType);

	/*Calculate Budget*/
	if($expensesType == 'expense'){
		$getIncome = getNewExpenseDetails($conn, $expensesUsername, 'income');
		$getBudget = $getIncome - $getExpenses;
	}else{
		$getExpense = getNewExpenseDetails($conn, $expensesUsername, 'expense');
		$getBudget = $getExpenses - $getExpense;
	}

	echo $expensesType.'-period-';
	echo number_format($getExpenses).'-period-';
	echo number_format($getBudget);
}

/*Search Expenses*/
if(isset($_POST['searchExpenses'])){
	$searchq = mysqli_real_escape_string($conn, $_POST['searchq']);
	$username = $_POST['username'];
	$searchExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE (username = '$username') AND (amount LIKE '%$searchq%' OR type LIKE '%$searchq%' OR date LIKE '%$searchq%' OR category LIKE '%$searchq%' OR details LIKE '%$searchq%' OR wallet LIKE '%$searchq%') ORDER BY date DESC, id DESC");
	$totalSearchExpenses = mysqli_query($conn, "SELECT SUM(amount) AS searchExpense FROM expenses WHERE (amount LIKE '%$searchq%' OR type LIKE '%$searchq%' OR date LIKE '%$searchq%' OR category LIKE '%$searchq%' OR details LIKE '%$searchq%' OR wallet LIKE '%$searchq%') AND (type = 'expense') AND (username = '$username') ORDER BY date DESC, id DESC");
	$totalSearchIncome = mysqli_query($conn, "SELECT SUM(amount) AS searchIncome FROM expenses WHERE (amount LIKE '%$searchq%' OR type LIKE '%$searchq%' OR date LIKE '%$searchq%' OR category LIKE '%$searchq%' OR details LIKE '%$searchq%' OR wallet LIKE '%$searchq%') AND (type = 'income') AND (username = '$username') ORDER BY date DESC, id DESC");
	if(mysqli_num_rows($searchExpenses)>0){
		echo '<div id = "searchContentDiv">
			<div id = "searchHeadingDiv">'.mysqli_num_rows($searchExpenses).' results have been found with '.$searchq.'<br><br>
				<div style = "font-size:large">
					In this search results:<br>';
					$overallSearchIncome = 0; $overallSearchExpense = 0;
					if (mysqli_num_rows($totalSearchExpenses)>0) {
						while($rowSearchExpenseAmount = mysqli_fetch_assoc($totalSearchExpenses)){
							if($rowSearchExpenseAmount['searchExpense'] != ''){
								echo '<div> Overall Expense: &#8377;<span class = "errorMessage">'.number_format($rowSearchExpenseAmount['searchExpense']).'</span></div>';
								$overallSearchExpense = $rowSearchExpenseAmount['searchExpense']; 
							}
						}
					}
					if (mysqli_num_rows($totalSearchIncome)>0) {
						while($rowSearchIncomeAmount = mysqli_fetch_assoc($totalSearchIncome)){
							if($rowSearchIncomeAmount['searchIncome'] != ''){
								echo '<div> Overall Income: &#8377;<span class = "successMessage">'.number_format($rowSearchIncomeAmount['searchIncome']).'</span></div>';
								$overallSearchIncome = $rowSearchIncomeAmount['searchIncome'];
							}
						}
					}
					if ((mysqli_num_rows($totalSearchExpenses)>0) || (mysqli_num_rows($totalSearchIncome)>0)) {
						$netFlow = $overallSearchIncome - $overallSearchExpense;
					}
					echo '<div> Net Flow: &#8377;<span class = "bufferMessage">'.number_format($netFlow).'</span></div>';
				echo '</div><br><br>
				&#8593; = Income &nbsp;&nbsp;&nbsp; &#8595; = Expense
			<div>';
		while ($rowSearch = mysqli_fetch_assoc($searchExpenses)) {
			echo '<div class = "expensesSearchList">
				<div>
					<span>
						'.date('d-M-Y (D)', strtotime($rowSearch['date'])).'
					</span> 
					- 
					<i>
						'.ucwords($rowSearch['category']).'
					</i>
					 -  
					<b>
						&#8377;
						<span ';
							if($rowSearch['type'] == 'income'){
								echo ' class = "successMessage"';
							}else{
								echo ' class = "errorMessage"';
							}
							echo ' >'.number_format($rowSearch['amount']); 
						echo'</span>
					</b>';
					if($rowSearch['type'] == 'income'){
						echo '&#8593;';
					}else{
						echo '&#8595;';
					}
					echo '<div>'.$rowSearch['details'].'</div>';
					if($rowSearch['wallet']!=''&&$rowSearch['wallet']!='noWalletRegd'){echo '<div class="sideHeading">Wallet: '.$rowSearch['wallet'].'</div>';}
				echo'</div>
				<div>
					<button class = "expensesEditButton" onclick="editExpenses('.$rowSearch['id'].')">Edit</button>
					<button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowSearch['id'].')">Delete</button>
				</div>
			</div>';
		}
		echo '</div>';
	}else{
		echo '<div id = "searchContentDiv"><div id = "searchHeadingDiv">No results found..</div></div>';
	}
}

/*Filter Expenses*/
if(isset($_POST['filterExpenses'])){
	$fromDate = mysqli_real_escape_string($conn, $_POST['fromDate']);
	$toDate = mysqli_real_escape_string($conn, $_POST['toDate']);
	$username = $_POST['username'];
	$filterExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE (date BETWEEN '$fromDate' AND '$toDate') AND (username = '$username') ORDER BY date DESC, id DESC");
	$totalFilterExpenses = mysqli_query($conn, "SELECT SUM(amount) AS filterExpense FROM expenses WHERE (date BETWEEN '$fromDate' AND '$toDate') AND (type = 'expense') AND (username = '$username') ORDER BY date DESC, id DESC");
	$totalFilterIncome = mysqli_query($conn, "SELECT SUM(amount) AS filterIncome FROM expenses WHERE (date BETWEEN '$fromDate' AND '$toDate') AND (type = 'income') AND (username = '$username') ORDER BY date DESC, id DESC");
	if(mysqli_num_rows($filterExpenses)>0){
		echo '<div id = "filterContentDiv">
			<div id = "filterHeadingDiv">
				<div>'.mysqli_num_rows($filterExpenses).' results have been found between '.date('d-M-Y', strtotime($fromDate)).' and '.date('d-M-Y', strtotime($toDate)).'. </div><br>
				<div style = "font-size:large">
					In this period:<br>';
					if (mysqli_num_rows($totalFilterExpenses)>0) {
						while($rowfilterExpenseAmount = mysqli_fetch_assoc($totalFilterExpenses)){
							if($rowfilterExpenseAmount['filterExpense'] != ''){
								echo '<div> Overall Expense: &#8377;<span class = "errorMessage">'.number_format($rowfilterExpenseAmount['filterExpense']).'</span></div>';
							}
						}
					}
					if (mysqli_num_rows($totalFilterIncome)>0) {
						while($rowfilterIncomeAmount = mysqli_fetch_assoc($totalFilterIncome)){
							if($rowfilterIncomeAmount['filterIncome'] != ''){
								echo '<div> Overall Income: &#8377;<span class = "successMessage">'.number_format($rowfilterIncomeAmount['filterIncome']).'</span></div>';
							}
						}
					}
				echo '</div>
				<br><br>&#8593; = Income &nbsp;&nbsp;&nbsp; &#8595; = Expense';
			echo '</div>';
			while ($rowFilter = mysqli_fetch_assoc($filterExpenses)) {
				echo '<div class = "expensesSearchList">
					<div>
						<span>
							'.date('d-M-Y (D)', strtotime($rowFilter['date'])).'
						</span> 
						- 
						<i>
							'.ucwords($rowFilter['category']).'
						</i>
						 -  
						<b>
							&#8377;
							<span ';
								if($rowFilter['type'] == 'income'){
									echo ' class = "successMessage"';
								}else{
									echo ' class = "errorMessage"';
								}
								echo ' >'.number_format($rowFilter['amount']); 
							echo'</span>
						</b>';
						if($rowFilter['type'] == 'income'){
							echo '&#8593;';
						}else{
							echo '&#8595;';
						}
						echo '<div>'.$rowFilter['details'].'</div>';
						if($rowFilter['wallet']!=''&&$rowFilter['wallet']!='noWalletRegd'){echo '<div class="sideHeading">Wallet: '.$rowFilter['wallet'].'</div>';}
					echo'</div>
					<div>
						<button class = "expensesEditButton" onclick="editExpenses('.$rowFilter['id'].')">Edit</button>
						<button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowFilter['id'].')">Delete</button>
					</div>
				</div>';
			}
		echo '</div>';
	}else{
		echo 'No expenses added on this date..';
	}
}

/*Profile Details*/
if(isset($_POST['getProfileDetails'])){
	$username = $_POST['username'];
	$getDetails = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
	if(mysqli_num_rows($getDetails)>0){
		while($rowDetails = mysqli_fetch_assoc($getDetails)){
			echo'<div id="profileOuterDiv" ><div id="profileInnerDiv">
				<br><br><div class = "headingName">Profile Details</div><hr><br>
				Username  <b>: '.$rowDetails['username'].'</b><button id = "editProfileusername" class = "editButton" onclick = "editProfile(\''.'username'.'-period-'.$rowDetails['username'].'-period-'.$rowDetails['id'].'\')">edit</button><br>
				Password  <b>: ********</b><button id = "editProfilepassword" class = "editButton" onclick = "editProfile(\''.'password'.'-period-'.$rowDetails['password'].'-period-'.$rowDetails['id'].'\')">edit</button><br>
				Email ID  <b>: '.$rowDetails['email'].'</b><button id = "editProfileemail" class = "editButton" onclick = "editProfile(\''.'email'.'-period-'.$rowDetails['email'].'-period-'.$rowDetails['id'].'\')">edit</button><br>
				Name : <b>'.ucwords($rowDetails['name']).'</b><button id = "editProfilename" class = "editButton" onclick = "editProfile(\''.'name'.'-period-'.$rowDetails['name'].'-period-'.$rowDetails['id'].'\')">edit</button><br><br><br>
				<button class = "closeButton" onclick = "hideProfileDetails()">Close</button>
			</div></div>';
		}
	}
}


/*Edit Profile*/
if(isset($_POST['editProfile'])){
	$type = $_POST['type'];
	$value = $_POST['value'];
	$id = $_POST['id'];
	echo '<div id="editProfileOuterDiv" ><div id="editProfileInnerDiv"><br><br>
		Edit the '.ucwords($type).'<br><br>
		<input type = "hidden" id = "oldValueEditProfileDetail'.$type.'" value = "'.$value.'">
		<textarea type = "text" id = "newValueEditProfileDetail'.$type.'" placeholder="Edit '.$type.'"></textarea>
		<div id = "editProfileStatus"></div><br><br><br>
		<button class = "closeButton confirmButton" id="confirmEditProfileDetailsButton" onclick = "confirmEditProfileDetails(\''.$type.'\',\''.$id.'\')">Done</button>
		<button class = "closeButton" onclick = "hideEditProfileDetails()">Cancel</button>
	</div></div>';
}

if (isset($_POST['confirmEditProfile'])) {
	$type = $_POST['type'];
	$id = $_POST['id'];
	$newValue = mysqli_real_escape_string($conn, $_POST['newValue']);
	$oldValue = $_POST['oldValue'];
	$updateCheck = 0;
	if($type == 'email'){
		$checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email = '$newValue'");
		if(mysqli_num_rows($checkEmail)<1){
			$updateCheck = 1;
		}else{
			echo 'AlreadyExists';
		}	
	}else if($type == 'username'){
		$checkUsername = mysqli_query($conn, "SELECT * FROM users WHERE username = '$newValue'");
		if(mysqli_num_rows($checkUsername)<1){
			$updateCheck = 1;
		}else{
			echo 'AlreadyExists';
		}	
	}else if($type == 'password' || $type == 'name'){
		$updateCheck = 1;
	}
	if($updateCheck == 1){
		if($type == 'username'){
			$updater = mysqli_query($conn, "UPDATE users SET username = '$newValue' WHERE id = '$id'");	
			$updater = mysqli_query($conn, "UPDATE expenses SET username = '$newValue' WHERE username = '$oldValue'");	
			$updater = mysqli_query($conn, "UPDATE wallet SET walletUsername = '$newValue' WHERE walletUsername = '$oldValue'");	
			echo 'updateDone';	
		}else if($type == 'email'){
			$updater = mysqli_query($conn, "UPDATE users SET email = '$newValue' WHERE id = '$id'");	
			echo 'updateDone';	
		}else if($type == 'name'){
			$updater = mysqli_query($conn, "UPDATE users SET name = '$newValue' WHERE id = '$id'");	
			echo 'updateDone';	
		}else if($type == 'password'){
			$updater = mysqli_query($conn, "UPDATE users SET password = '$newValue' WHERE id = '$id'");	
			echo 'updateDone';	
		}
	}
}

/*Get Today's data*/
if(isset($_POST['getExpenseForNotif'])){
	$todayDate = date('Y-m-d',time());
	$username = $_POST['username'];
	/*$yesterdayDate = date('Y-m-d', strtotime("-1 days"));
	$thisMonth = date('m',time());
	$thisYear = date('Y',time());*/
	$totalTodayExpenses = 0; $totalTodayIncome = 0;
	
	/*Calculate Today Expenses*/
	$getTodayExpense = mysqli_query($conn,"SELECT SUM(amount) AS todayExpense FROM expenses WHERE type = 'expense' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
	$getTodayExpenseCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'expense' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
	if(mysqli_num_rows($getTodayExpenseCount)>0){while($rowTodayExpense = mysqli_fetch_assoc($getTodayExpense)){$totalTodayExpenses = $rowTodayExpense['todayExpense'];}}

	/*Calculate Today Incomes*/
	$getTodayIncome = mysqli_query($conn,"SELECT SUM(amount) AS todayIncome FROM expenses WHERE type = 'income' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
	$getTodayIncomeCount = mysqli_query($conn,"SELECT * FROM expenses WHERE type = 'income' AND username = '$username' AND date = '$todayDate' ORDER BY id DESC");
	if(mysqli_num_rows($getTodayIncomeCount)>0){while($rowTodayIncome = mysqli_fetch_assoc($getTodayIncome)){$totalTodayIncome = $rowTodayIncome['todayIncome'];}}

	echo $totalTodayExpenses.'-period-'.$totalTodayIncome;

}

/*Add Wallet*/
if(isset($_POST['addWallet'])){
	$username = mysqli_real_escape_string($conn,$_POST['username']);
	$walletName = mysqli_real_escape_string($conn,$_POST['walletName']);
	$walletAmount = mysqli_real_escape_string($conn,$_POST['walletAmount']);
	$checkWallet = mysqli_query($conn,"SELECT * FROM wallet WHERE walletName = '$walletName' AND walletUsername = '$username'");
	if(mysqli_num_rows($checkWallet)>0){
		echo 'registeredAlready';
	}else{
		if(strpos($walletName, 'bank')!==false){
			$walletNewName=str_replace('BANK', 'Bank', strtoupper($walletName));	
		}else{
			$walletNewName=ucwords($walletName);
		}
		$addWallet = mysqli_query($conn,"INSERT INTO wallet (walletUsername, walletName, walletValue) VALUES ('$username', '$walletNewName', '$walletAmount')");
		echo 'success';
		echo '-period-';
		getWalletRadioButton($conn,$username);
		echo'-period-';
		getWalletSummary($conn,$username);
		echo'-period-';
		getWalletTransferInfo($conn,$username);
	}
}

/*Set select wallet options*/
if(isset($_POST['setWalletSelection'])){
	$username = mysqli_real_escape_string($conn,$_POST['username']);
	$selectedValue = mysqli_real_escape_string($conn,$_POST['selectedValue']);
	$getWalletOptions=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
	if(mysqli_num_rows($getWalletOptions)>0){echo'<option value="">Select</option>';
		while($rowWallet=mysqli_fetch_assoc($getWalletOptions)){
			if($rowWallet['walletName']!=$selectedValue){
				echo '<option value="'.$rowWallet['walletName'].'">'.$rowWallet['walletName'].'</option>';
			}
		}
	}
}

/*Exchange Wallet Amount*/
if(isset($_POST['exchangeWalletAmount'])){
	$username = mysqli_real_escape_string($conn,$_POST['username']);
	$walletCredit = mysqli_real_escape_string($conn,$_POST['walletCredit']);
	$walletDebit = mysqli_real_escape_string($conn,$_POST['walletDebit']);
	$walletExchangeAmount = mysqli_real_escape_string($conn,$_POST['walletExchangeAmount']);
	$date=date('Y-m-d', time());
	/*Get Credit Balance*/
	$checkWalletCreditBal=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username' AND walletName='$walletCredit' LIMIT 1");
	if(mysqli_num_rows($checkWalletCreditBal)>0){
		while($rowWalletBal=mysqli_fetch_assoc($checkWalletCreditBal)){
			$walletCreditBalance=$rowWalletBal['walletValue'];
		}
	}
	/*Check Debit Balance*/
	$checkWalletDebitBal=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username' AND walletName='$walletDebit' LIMIT 1");
	if(mysqli_num_rows($checkWalletDebitBal)>0){
		while($rowWalletBal=mysqli_fetch_assoc($checkWalletDebitBal)){
			if($rowWalletBal['walletValue']>=$walletExchangeAmount){
				$newDebitBal=$rowWalletBal['walletValue']-$walletExchangeAmount;
				$newCreditBal=$walletCreditBalance+$walletExchangeAmount;
				mysqli_query($conn,"UPDATE wallet SET walletValue='$newDebitBal' WHERE walletUsername='$username' AND walletName='$walletDebit'");
				mysqli_query($conn,"UPDATE wallet SET walletValue='$newCreditBal' WHERE walletUsername='$username' AND walletName='$walletCredit'");
				mysqli_query($conn, "INSERT INTO wallethistory (walletUsername, walletNameFrom, walletNameTo, walletValue, walletTransferDate, type, category, details) VALUES ('$username', '$walletDebit', '$walletCredit', '$walletExchangeAmount', '$date', 'walletTransfer', 'walletTransfer', 'walletTransfer')");
				echo'exchangeDone';
				echo'-period-';
				getWalletSummary($conn,$username);
				echo'-period-';
				getWalletHistory($conn,$username);
			}else{
				echo'insufficientBalance';
			}
		}
	}
}

/*Show Edit Wallet*/
if(isset($_POST['showEditWallet'])){
	$walletId = $_POST['walletId'];
	$checkWallet = mysqli_query($conn, "SELECT * FROM wallet WHERE id = '$walletId'");
	if(mysqli_num_rows($checkWallet)>0){
		while($rowWallet = mysqli_fetch_assoc($checkWallet)){
			echo'<div class="editOuterDiv" style="z-index: 3;"><div class="editInnerDiv">
				<h4>Edit Details</h4><hr><br>
				Wallet Name: <br><textarea class = "editTextArea" id="editWalletName'.$rowWallet['id'].'" type="date" placeholder = "Edit Wallet Name"></textarea><br><br>
				Wallet Amount: <br><textarea class = "editTextArea" id="editWalletAmount'.$rowWallet['id'].'" placeholder = "Edit Amount"></textarea><br><br>
				<div id="editWalletErrorMessage"></div><br><br>
				<button class = "confirmButton closeButton" onclick = "confirmEditWallet(\''.$walletId.'\',\''.$rowWallet['walletName'].'\')">Done</button>
				<button class = "closeButton" onclick = "hideEditWallet()">Cancel</button><br><br><br>
			</div></div>-period-';
			/*Send data to JS*/
			echo $rowWallet['walletName'].'-period-'.
			$rowWallet['walletValue'];
		}
	}
}

/*Edit Wallet*/
if(isset($_POST['editWallet'])){
	$walletId = $_POST['walletId'];
	$walletAmount = $_POST['walletAmount'];
	$walletName = $_POST['walletName'];
	$oldWalletName = $_POST['oldWalletName'];
	$username=$_POST['username'];
	$updateWallet = mysqli_query($conn, "UPDATE wallet SET walletName = '$walletName', walletValue = '$walletAmount' WHERE id = '$walletId'");
	/*Update wallet Edited info in expenses for further reference issue*/
	$updateWalletEditedInfoInExpense=mysqli_query($conn,"UPDATE expenses SET wallet='$walletName' WHERE username='$username' AND wallet='$oldWalletName'");
	echo'-period-';
	getWalletSummary($conn,$username);
	echo'-period-';
	getWalletTransferInfo($conn,$username);
	echo'-period-';
	getWalletRadioButton($conn,$username);
}

/*Show Delete Wallet*/
if(isset($_POST['showDeleteWallet'])){
	$walletId = $_POST['walletId'];
	$checkWallet = mysqli_query($conn, "SELECT * FROM wallet WHERE id = '$walletId'");
	if(mysqli_num_rows($checkWallet)>0){
		while($rowWallet = mysqli_fetch_assoc($checkWallet)){
			$walletName=$rowWallet['walletName'];
			$username=$rowWallet['walletUsername'];
			$walletTransactions='no';
			/*Check for wallet transactions*/
			$checkWalletExpenses=mysqli_query($conn,"SELECT * FROM expenses WHERE wallet='$walletName' AND username='$username'");
			if(mysqli_num_rows($checkWalletExpenses)>0){
				$walletTransactions='yes';
			}
			echo'<div class="editOuterDiv" style="z-index:3"><div class="editInnerDiv">
				<br><br><div class = "headingName">Delete Details</div><hr><br>
				Are you sure you want to delete this wallet?<br><br>';
				echo'<div>Wallet Name: <b>'.$rowWallet['walletName'].'</b></div>
				<div>Current Value: <b>&#8377;'.number_format($rowWallet['walletValue']).'</b></div><br>';
				if($walletTransactions=='yes'){
					echo'<div class="errorMessageShake">
						There are already some transactions / expenses registered with '.$walletName.'.<br>
						If this wallet is deleted, the wallet-name would be deleted from the registered <b>expense Info</b>!
					</div>';
				}
				echo'<div id="deleteWalletErrorMessage"></div><br>
				<button class="redButton" onclick = "confirmDeleteWallet(\''.$walletId.'\',\''.$walletName.'\')">Delete</button>
				<button class="redButtonOuter" onclick = "hideDeleteWallet()">Cancel</button>
			</div></div>';
		}
	}
}

/*Delete Wallet*/
if(isset($_POST['deleteWallet'])){
	$walletId = $_POST['walletId'];
	$username = $_POST['username'];
	$walletName = $_POST['walletName'];
	$newWalletName=$walletName.'-Deleted';
	$updateWallet = mysqli_query($conn, "DELETE FROM wallet WHERE id = '$walletId'");
	/*Update wallet deleted info in expenses for further reference issue*/
	$updateWalletDeletedInfoInExpense=mysqli_query($conn,"UPDATE expenses SET wallet='$newWalletName' WHERE username='$username' AND wallet='$walletName'");
	/*Get New List*/
	getWalletSummary($conn,$username);
	echo'-period-';
	getWalletTransferInfo($conn,$username);
	echo'-period-';
	getWalletRadioButton($conn,$username);
}

/*Show Delete Wallet History*/
if(isset($_POST['showDeleteWalletHistory'])){
	$walletId = $_POST['walletId'];
	$checkWallet = mysqli_query($conn, "SELECT * FROM wallethistory WHERE id = '$walletId'");
	if(mysqli_num_rows($checkWallet)>0){
		while($rowWallet = mysqli_fetch_assoc($checkWallet)){
			$walletName=$rowWallet['walletName'];
			$username=$rowWallet['walletUsername'];
			echo'<div class="editOuterDiv" style="z-index:3"><div class="editInnerDiv">
				<br><br><div class = "headingName">Delete Details</div><hr><br>
				Are you sure you want to delete this wallet transaction?<br><br>';
				echo'<div>Transaction From: <b>'.$rowWallet['walletNameFrom'].'</b></div>';
				if($rowWallet['walletNameTo']!='walletExpenseOK'){echo'<div>Transaction To: <b>'.$rowWallet['walletNameTo'].'</b></div>';}
				echo'<div>Transaction Amount: <b>&#8377;'.number_format($rowWallet['walletValue']).'</b></div>
				<div>Transaction Date: <b>'.date('d-M-Y (l)',strtotime($rowWallet['walletTransferDate'])).'</b></div>';
				if($rowWallet['type']!='walletTransfer'){
					echo'<div>Transaction Type: <b>'.$rowWallet['type'].'</b></div>
					<div>Transaction Category: <b>'.$rowWallet['category'].'</b></div>
					<div>Transaction Details: <b>'.$rowWallet['details'].'</b></div>';
				}
				echo'<br>
				Only this transaction history will be deleted.. <br>To delete expense amount, select delete option from the expenses list<br>';
				echo'<div id="deleteWalletErrorMessage"></div><br>
				<button class="redButton" onclick = "confirmDeleteWalletHistory(\''.$walletId.'\')">Delete</button>
				<button class="redButtonOuter" onclick = "hideDeleteWallet()">Cancel</button>
			</div></div>';
		}
	}
}

/*Delete Wallet History*/
if(isset($_POST['deleteWalletHistory'])){
	$walletId = $_POST['walletId'];
	$username = $_POST['username'];
	$updateWallet = mysqli_query($conn, "DELETE FROM wallethistory WHERE id = '$walletId'");
	/*Get New List*/
	getWalletSummary($conn,$username);
	echo'-period-';
	getWalletTransferInfo($conn,$username);
	echo'-period-';
	getWalletRadioButton($conn,$username);
	echo'-period-';
	getWalletHistory($conn,$username);
}

/*Show Edit Wallet History*/
if(isset($_POST['showEditWalletHistory'])){
	$walletId = $_POST['walletId'];
	$checkWallet = mysqli_query($conn, "SELECT * FROM wallethistory WHERE id = '$walletId'");
	if(mysqli_num_rows($checkWallet)>0){
		while($rowWallet = mysqli_fetch_assoc($checkWallet)){
			$walletName=$rowWallet['walletName'];
			$username=$rowWallet['walletUsername'];
			echo'<div class="editOuterDiv" style="z-index:3"><div class="editInnerDiv">
				<br><br><div class = "headingName">Transaction Details</div><hr><br>';
				echo'<div>Transaction From: <textarea class = "editTextArea" id="editWalletHistoryFrom'.$rowWallet['id'].'"></textarea></div>';
				if($rowWallet['walletNameTo']!='walletExpenseOK'){echo'<div>Transaction To: <textarea class = "editTextArea" id="editWalletHistoryTo'.$rowWallet['id'].'"></textarea></div>';}
				echo'<div>Transaction Amount: &#8377;<textarea class = "editTextArea" id="editWalletHistoryValue'.$rowWallet['id'].'"></textarea></div>
				<div>Transaction Date: <textarea class = "editTextArea" id="editWalletHistoryDate'.$rowWallet['id'].'"></textarea></div>';
				if($rowWallet['type']!='walletTransfer'){
					echo'<div>Transaction Type: <textarea class = "editTextArea" id="editWalletHistoryType'.$rowWallet['id'].'"></textarea></div>
					<div>Transaction Category: <textarea class = "editTextArea" id="editWalletHistoryCategory'.$rowWallet['id'].'"></textarea></div>
					<div>Transaction Details: <textarea class = "editTextArea" id="editWalletHistoryDetails'.$rowWallet['id'].'"></textarea></div>';
				}
				echo '<input type="hidden" id="currentWalletValue'.$rowWallet['id'].'" value="'.$rowWallet['walletValue'].'">';//Send Current Wallet Value to JS
				echo'<div id="editWalletErrorMessage"></div><br>
				<button class="greenButton" onclick = "confirmEditWalletHistory(\''.$walletId.'\')">Done</button>
				<button class="redButtonOuter" onclick = "hideEditWallet()">Cancel</button>
			</div></div>';
			//Send Data to JS
			echo'-period-';
			echo $rowWallet['walletNameFrom'];
			echo'-period-';
			echo $rowWallet['walletNameTo'];
			echo'-period-';
			echo $rowWallet['walletValue'];
			echo'-period-';
			echo date('d-m-Y',strtotime($rowWallet['walletTransferDate']));
			echo'-period-';
			echo $rowWallet['type'];
			echo'-period-';
			echo $rowWallet['category'];
			echo'-period-';
			echo $rowWallet['details'];
		}
	}
}

/*Confirm Edit Wallet History*/
if(isset($_POST['editWalletHistory'])){
	$walletId = $_POST['walletId'];
	$walletHistoryFrom = $_POST['walletHistoryFrom'];
	$walletHistoryTo = $_POST['walletHistoryTo'];
	$walletHistoryValue = $_POST['walletHistoryValue'];
	$walletHistoryDate = date('Y-m-d',strtotime($_POST['walletHistoryDate']));
	$walletHistoryType = $_POST['walletHistoryType'];
	$walletHistoryCategory = $_POST['walletHistoryCategory'];
	$walletHistoryDetails = $_POST['walletHistoryDetails'];
	$bufferWalletValue = $_POST['bufferWalletValue'];
	$username = $_POST['username'];
	
	$updateExpenses = mysqli_query($conn, "UPDATE wallethistory SET walletNameFrom = '$walletHistoryFrom', walletNameTo = '$walletHistoryTo', walletValue = '$walletHistoryValue', walletTransferDate = '$walletHistoryDate', type='$walletHistoryType', category='$walletHistoryCategory', details='$walletHistoryDetails' WHERE id = '$walletId'");
	if($walletHistoryTo == 'walletExpenseOK'){
		$OldWalletValFrom = getWalletValue($conn,$username,$walletHistoryFrom);
		$NewWalletValFrom = $OldWalletValFrom + $bufferWalletValue;
		mysqli_query($conn, "UPDATE wallet SET walletValue='$NewWalletValFrom' WHERE walletName = '$walletHistoryFrom' AND walletUsername = '$username'");
	}else{
		$OldWalletValFrom = getWalletValue($conn,$username,$walletHistoryFrom);
		$NewWalletValFrom = $OldWalletValFrom + $bufferWalletValue;
		$OldWalletValTo = getWalletValue($conn,$username,$walletHistoryTo);
		$NewWalletValTo = $OldWalletValTo - $bufferWalletValue;
		mysqli_query($conn, "UPDATE wallet SET walletValue='$NewWalletValTo' WHERE walletName = '$walletHistoryTo' AND walletUsername = '$username'");
		mysqli_query($conn, "UPDATE wallet SET walletValue='$NewWalletValFrom' WHERE walletName = '$walletHistoryFrom' AND walletUsername = '$username'");
	}
	
}


/*Income or Expense Checker*/
function checkIncomeOrExpense($id,$conn){
	$checkIE = mysqli_query($conn, "SELECT * FROM expenses WHERE id = '$id'");
	$checkedIE = '';
	if(mysqli_num_rows($checkIE)>0){
		while($rowCheckIE = mysqli_fetch_assoc($checkIE)){
			$checkedIE = $rowCheckIE['type'];
		}
	}
	return $checkedIE;
}

/*Username Checker*/
function checkUsername($id,$conn){
	$checkUsername = mysqli_query($conn, "SELECT * FROM expenses WHERE id = '$id'");
	$checkedUsername = '';
	if(mysqli_num_rows($checkUsername)>0){
		while($rowCheckIE = mysqli_fetch_assoc($checkUsername)){
			$checkedUsername = $rowCheckIE['username'];
		}
	}
	return $checkedUsername;
}

/*Get New Expense Details*/
function getNewExpenseDetails($conn, $username, $type){
	$getNewExpense = mysqli_query($conn,"SELECT * FROM expenses WHERE type = '$type' AND username = '$username' ORDER BY id DESC");
	$totalExpenses = 0;
	if(mysqli_num_rows($getNewExpense)>0){
		while($rowExpense = mysqli_fetch_assoc($getNewExpense)){
			$totalExpenses += $rowExpense['amount'];
		}
	}
	return $totalExpenses;
}

/*Get Username*/
function getUsername($conn, $id){
	$getUsername = mysqli_query($conn,"SELECT * FROM users WHERE id = '$id'");
	if(mysqli_num_rows($getUsername)>0){
		while($rowUsername = mysqli_fetch_assoc($getUsername)){
			$username = $rowUsername['username'];
		}
	}
	return $username;	
}

/*Get Wallet Summary*/
function getWalletSummary($conn,$username){
	/*Wallet Summary*/
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
		echo'<br><button type="button" class="closeButton" onclick="closeWalletDiv()">CLOSE</button><br><br>';
	}else{
		echo'<br>No wallets registered.<br>';
	}
}

/*Get Wallet Info*/
function getWalletInfo($conn,$username){
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
}

/*Get Wallet Selections for transfers*/
function getWalletTransferInfo($conn,$username){
	$walletCheck=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
	if(mysqli_num_rows($walletCheck)>0){
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
		<button type="button" class="closeButton" onclick="closeWalletDiv()">CLOSE</button><br><br>';
	}else{
		echo'<br>No wallets registered.<br><br>';
	}
}

function getWalletRadioButton($conn,$username){
	/*Wallet List in add expense/income div*/
	/*Expense Wallet Options*/
	$getWallet=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
	if(mysqli_num_rows($getWallet)>0){
		while($rowWallet=mysqli_fetch_assoc($getWallet)){
			$walletNameTrim=str_replace(' ', '', $rowWallet['walletName']);
			echo '<input type="radio" id="expenseWallet'.$walletNameTrim.'" class="inputStyle" name="expenseWallet" value="'.$rowWallet['walletName'].'"><label for="expenseWallet'.$walletNameTrim.'">'.$rowWallet['walletName'].'</label>';
		}
		echo'<input type="radio" id="expenseWalletCash" class="inputStyle" name="expenseWallet" value="Cash"><label for="expenseWalletCash">Cash</label>
		<a href="#marquee" type="button" class="greenButtonOuter smallButton">New Wallet</a>';
	}
	echo'-period-';/*Income Wallet Options*/
	$getWallet=mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername='$username'");
	if(mysqli_num_rows($getWallet)>0){
		while($rowWallet=mysqli_fetch_assoc($getWallet)){
			$walletNameTrim=str_replace(' ', '', $rowWallet['walletName']);
			echo '<input type="radio" id="incomeWallet'.$walletNameTrim.'" class="inputStyle" name="incomeWallet" value="'.$rowWallet['walletName'].'"><label for="incomeWallet'.$walletNameTrim.'">'.$rowWallet['walletName'].'</label>';
		}
		echo'<input type="radio" id="incomeWalletCash" class="inputStyle" name="incomeWallet" value="Cash"><label for="incomeWalletCash">Cash</label>
		<a href="#marquee" type="button" class="greenButtonOuter smallButton">New Wallet</a>';
	}
}

function getWalletHistory($conn,$username){
	$walletCheckHistory=mysqli_query($conn,"SELECT * FROM wallethistory WHERE walletUsername='$username' ORDER BY walletTransferDate DESC");
	if(mysqli_num_rows($walletCheckHistory)>0){
		echo'<div class="tableContainer"><table class="analysisTable">
			<thead>
				<tr>
					<th>Date of Transfer</th><th>Transfer From</th><th>Transfer To</th><th>Amount Transfered</th><th>Expense / Income</th><th>Category</th><th>Details</th><th>Delete</th>
				</tr>
			</thead>
			<tbody>';
				while($rowWalletHistory=mysqli_fetch_assoc($walletCheckHistory)){
					echo'<tr>
						<td>'.date('d-M-Y (l)',strtotime($rowWalletHistory['walletTransferDate'])).'</td><td>'.$rowWalletHistory['walletNameFrom'].'</td>';
						if($rowWalletHistory['walletNameTo']=='walletExpenseOK'){echo'<td>-</td>';}else{echo'<td>'.$rowWalletHistory['walletNameTo'].'</td>';}
						echo'<td>&#8377;'.number_format($rowWalletHistory['walletValue']).'</td>';
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
}

function getWalletValue($conn,$walletUsername,$walletName){
	$checkWalletVal = mysqli_query($conn,"SELECT * FROM wallet WHERE walletUsername = '$walletUsername' AND walletName = '$walletName' ORDER BY id DESC LIMIT 1");
	$walletVal = '';
	if(mysqli_num_rows($checkWalletVal)>0){
		while($rowWallet = mysqli_fetch_assoc($checkWalletVal)){
			$walletVal = $rowWallet['walletValue'];
		}
	}
	return $walletVal;
}

?>