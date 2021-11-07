<?php

include_once 'db.php';
session_start();
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
		echo '<span class="errorMessage">username already available! Try with a new one / Login</span>';
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
			if($checkPassword == $password){
				$_SESSION['id'] = $rowUser['id'];
				$_SESSION['username'] = $rowUser['username'];
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
	$type = $_POST['type'];
	$addExpense = mysqli_query($conn,"INSERT INTO expenses (username, type, amount, date, category, details) VALUES ('$username', '$type', '$amount', '$date', '$category', '$details')");
	echo '<span class="successMessage">'.ucwords($type).' Added</span>
	-period-';/*To send two inputs to client*/
 	$totalExpense = getNewExpenseDetails($conn, $username, 'expense'); /*Get New Expense*/	
 	$totalIncome = getNewExpenseDetails($conn, $username, 'income'); /*Get New Income*/
	$totalBudget = $totalIncome - $totalExpense;
	echo number_format($totalExpense); 
	echo '-period-';
	echo number_format($totalIncome); 
	echo '-period-';
	echo number_format($totalBudget);
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
		echo 'Something went wrong!';
	}
}

/*Show Expenses Day-Wise Expese*/
if(isset($_POST['checkSubExpense'])){
	$type = $_POST['type'];
	$listType = $_POST['listType'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	$username = $_SESSION['username'];

	$getAllDays = mysqli_query($conn, "SELECT * FROM expenses WHERE type = '$type' AND username = '$username' AND month(date) = '$month' AND year(date) = '$year' ORDER BY date DESC");
	if(mysqli_num_rows($getAllDays)>0){
		/*Dummy Date*/$checkDate = date('1757-08-15');
		while ($rowDays = mysqli_fetch_assoc($getAllDays)) {
			if($checkDate != $rowDays['date']){
				$rowDay = $rowDays['date'];
				$rowDayInNumberFormat = strtotime($rowDay);/*Send date in number format*/
				echo '<div class = "expensesSubList" onclick = "toggleSubLowerListDiv(\''.$type.'\', \''.'All'.'\', \''.$rowDayInNumberFormat.'\')">
					<div>';
						/*mktime used to get time details mktime(hour, minute, second, month, date, year)*/
						echo '&nbsp;'.date('d F Y', strtotime($rowDay)).' - ';
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
						$checkDateInNumberFormat = strtotime($checkDate);
				
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
	$username = $_SESSION['username'];

	$checkDate = date('Y-m-d', $dateInNumberFormat);
	$getExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE date = '$checkDate' AND username = '$username' AND type = '$type' ORDER BY date DESC, id DESC");
	while($rowExpenses = mysqli_fetch_assoc($getExpenses)){
		echo '<div class = "expensesSubLowerList">
			<div>
				<span>&nbsp;&nbsp;&nbsp;'.date('d-M-Y', strtotime($rowExpenses['date'])).'</span> 
				- <i>'.ucwords($rowExpenses['category']).'</i> - <b>&#8377;'.number_format($rowExpenses['amount']).'</b>
				<div>&nbsp;&nbsp;&nbsp;'.$rowExpenses['details'].'</div>
			</div>
			<div>
				<button class = "expensesEditButton" onclick="editExpenses('.$rowExpenses['id'].')">Edit</button>
				<button class = "expensesDeleteButton" onclick="deleteExpenses('.$rowExpenses['id'].')">Delete</button>
			</div>
		</div>';
	}
}

/*Show Budget*/
if(isset($_POST['showBudget'])){
	$username = $_POST['username'];
	$getBudget = mysqli_query($conn, "SELECT * FROM expenses WHERE username = '$username' ORDER BY id DESC");
	if(mysqli_num_rows($getBudget)>0){
		echo '<div id = "budgetListDiv">
		<div class = "expensesListHeading">
			<div class = "listMainHeadingName">Budget List </div>
			<div class = "closeButton" onclick = "hideListDivs()">Close</div>
		</div>';
		while($rowBudget = mysqli_fetch_assoc($getBudget)){
			echo '<div class = "budgetList">
				<div>
					<span>
						'.date('d-M-Y', strtotime($rowBudget['date'])).'
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
			</div>';
		}
	}else{
		echo 'Something went wrong!';
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
			echo'<div id="editExpensesOuterDiv" ><div id="editExpensesInnerDiv">
				<h4>Edit Details</h4><hr><br>
				Date: <textarea id="editExpenseDate'.$rowExpenses['id'].'" type="date" placeholder = "Edit Date"></textarea><br>
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
				Category: <textarea id="editExpenseCategory'.$rowExpenses['id'].'" list = "categoryOptions" placeholder = "Edit Category">
				</textarea><br>
				Amount: <textarea id="editExpenseAmount'.$rowExpenses['id'].'" placeholder = "Edit Amount"></textarea><br>
				Details: <textarea id="editExpenseDetails'.$rowExpenses['id'].'" placeholder="Edit Details"></textarea><br><br>
				<div id="editExpenseErrorMessage"></div><br>
				<button class = "confirmButton closeButton" onclick = "confirmEdit('.$expensesId.')">Done</button>
				<button class = "closeButton" onclick = "hideEditExpense()">Cancel</button>
			</div></div>-period-';
			/*Send data to JS*/
			echo number_format($rowExpenses['amount']).'-period-'.
			$rowExpenses['category'].'-period-'.
			$rowExpenses['date'].'-period-'.
			$rowExpenses['details'];
		}
	}
}

/*Edit Expenses*/
if(isset($_POST['editExpenses'])){
	$expensesId = $_POST['expensesId'];
	$expensesDate = $_POST['expensesDate'];
	$expensesAmount = $_POST['expensesAmount'];
	$expensesCategory = $_POST['expensesCategory'];
	$expensesDetails = $_POST['expensesDetails'];
	$updateExpenses = mysqli_query($conn, "UPDATE expenses SET date = '$expensesDate', amount = '$expensesAmount', category = '$expensesCategory', details = '$expensesDetails' WHERE id = '$expensesId'");
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
			echo'<div id="deleteExpensesOuterDiv" ><div id="deleteExpensesInnerDiv">
				<br><br><div class = "headingName">Delete Details</div><hr><br>
				Are you sure you want to delete this data?<br><br>
				<div>Amount: <b>&#8377;'.number_format($rowExpenses['amount']).'</b></div>
				<div>Category: <b>'.ucwords($rowExpenses['category']).'</b></div>
				<div>Date: <b>'.date('d-M-Y', strtotime($rowExpenses['date'])).'</b></div>
				<div>Details: <b>'.$rowExpenses['category'].'</b><br><br></div>
				<div id="deleteExpenseErrorMessage"></div><br>
				<button class = "expensesDeleteButton" onclick = "confirmDelete('.$expensesId.')">Delete</button>
				<button class = "expensesEditButton" onclick = "hideDeleteExpense()">Cancel</button>
			</div></div>-period-';
			/*Send data to JS*/
			echo $rowExpenses['amount'].'-period-'.
			$rowExpenses['category'].'-period-'.
			$rowExpenses['date'].'-period-'.
			$rowExpenses['details'];
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
	$searchExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE amount LIKE '%$searchq%' OR type LIKE '%$searchq%' OR date LIKE '%$searchq%' OR category LIKE '%$searchq%' OR details LIKE '%$searchq%' ORDER BY date DESC, id DESC");
	if(mysqli_num_rows($searchExpenses)>0){
		echo '<div id = "searchContentDiv">
			<div id = "searchHeadingDiv">'.mysqli_num_rows($searchExpenses).' results have been found with '.$searchq.'<br><br>
			&#8593; = Income &nbsp;&nbsp;&nbsp; &#8595; = Expense<br>';
		while ($rowSearch = mysqli_fetch_assoc($searchExpenses)) {
			echo '<div class = "expensesSearchList">
				<div>
					<span>
						'.date('d-M-Y', strtotime($rowSearch['date'])).'
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
					echo '<div>'.$rowSearch['details'].'</div>
				</div>
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
	$filterExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE date BETWEEN '$fromDate' AND '$toDate' ORDER BY date DESC, id DESC");
	$totalFilterExpenses = mysqli_query($conn, "SELECT SUM(amount) AS filterExpense FROM expenses WHERE (date BETWEEN '$fromDate' AND '$toDate') AND (type = 'expense') ORDER BY date DESC, id DESC");
	$totalFilterIncome = mysqli_query($conn, "SELECT SUM(amount) AS filterIncome FROM expenses WHERE (date BETWEEN '$fromDate' AND '$toDate') AND (type = 'income') ORDER BY date DESC, id DESC");
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
							'.date('d-M-Y', strtotime($rowFilter['date'])).'
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
						echo '<div>'.$rowFilter['details'].'</div>
					</div>
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

?>