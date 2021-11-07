/*Toggle Signup, Login forms*/
function toggleSignIn(formType){
	document.getElementById("loginForm").style.display = "none";
	document.getElementById("signupForm").style.display = "none";
	document.getElementById("loginErrorMessage").innerHTML = "";
	/*Form which is to be displayed*/
	document.getElementById(formType+"Form").style.display = "block";	
}

/*Show Password*/
function showPassword(type){
	if(document.getElementById(type+"Password").type == "text"){
		document.getElementById(type+"Password").type = "password";
	}else{
		document.getElementById(type+"Password").type = "text";
	}
}

/*Signup*/
function signup(){
	var username = document.getElementById("signupUsername").value;
	var password = document.getElementById("signupPassword").value;
	var confirmPassword = document.getElementById("signupConfirmPassword").value;
	var fullName = document.getElementById("signupFullName").value;
	var email = document.getElementById("signupEmail").value;
	document.getElementById("signupButton").innerHTML = "<div class='loaderButton'></div>";

	/*Remove Previous Validation error messges*/
	document.getElementById("signupUsername").style.border ="none";
	document.getElementById("signupPassword").style.border ="none";
	document.getElementById("signupConfirmPassword").style.border ="none";
	document.getElementById("signupFullName").style.border ="none";
	document.getElementById("signupEmail").style.border ="none";
	document.getElementById("signupErrorMessage").innerHTML = "";

	/*Validations*/
	if(username == '' || password == '' || confirmPassword == '' || fullName == '' || email == ''){ /*Empty Fields*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessage'>Please Fill in all the fields</span>";
	}else if(!/^[a-zA-Z0-9 ]+$/.test(username)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessage'>Invalid charecters in Username</span>";
		document.getElementById("signupUsername").style.border = "1px solid red";
	}else if(!/^[a-zA-Z0-9!@#$%^&*]{4,15}$/.test(password)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessage'>Invalid charecters in Password / Minimum 4 charecters are required</span>";
		document.getElementById("signupPassword").style.border = "1px solid red";
	}else if(!/^[a-zA-Z0-9.-_]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessage'>Email not valid</span>";
		document.getElementById("signupEmail").style.border = "1px solid red";
	}else if(!/^[a-zA-Z ]+$/.test(fullName)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessage'>Invalid charecters in Name</span>";
		document.getElementById("signupFullName").style.border = "1px solid red";
	}else if(password != confirmPassword){ /*If password, confirm password do not match */
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessage'>Password and confirm Password do not match!</span>";
		document.getElementById("signupPassword").style.border = "1px solid red";
		document.getElementById("signupConfirmPassword").style.border = "1px solid red";
	}else{ /*If all above conditions are valid, User is signed up!*/
		/*AJAX Functionality*/
		/*Declare variables*/
		var signup = "RandomInput";
		var data = "signup="+signup+"&username="+username+"&password="+password+"&fullName="+fullName+"&email="+email;
		/*Declare XML*/
		if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
		else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
		/*AJAX Methods*/
		xhr.open("POST","conditions.php",true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(data);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				if(xhr.status == 200){
					document.getElementById("signupErrorMessage").innerHTML = this.responseText;
				}
			}
		}
	}
	document.getElementById("signupButton").innerHTML = "SIGNUP";
}

/*Login*/
function login(){
	var username = document.getElementById("loginUsername").value;
	var password = document.getElementById("loginPassword").value;
	document.getElementById("loginButton").innerHTML = "<div class='loaderButton'></div>";
	document.getElementById("loginErrorMessage").innerHTML = "";/*To remove any previous error Messages*/
	/*Validation*/
	if(username == '' || password == ''){ /*Empty Fields*/
		document.getElementById("loginErrorMessage").innerHTML = "<span class='errorMessage'>Please Fill in all the fields</span>";
	}else{
		/*AJAX Functionality*/
		/*Declare variables*/
		var login = "RandomInput";
		var data = "login="+login+"&username="+username+"&password="+password;
		/*Declare XML*/
		if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
		else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
		/*AJAX Methods*/
		xhr.open("POST","conditions.php",true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(data);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				if(xhr.status == 200){
					if(this.responseText == "loginSuccess"){
						window.location.href = "home.php";
					}
					document.getElementById("loginErrorMessage").innerHTML = this.responseText;
				}
			}
		}
	}
	document.getElementById("loginButton").innerHTML = "LOGIN";
}

/*Add Expense*/
function addExpense(type){
	var expenseAmount = document.getElementById(type+"Amount").value;
	var expenseDate = document.getElementById(type+"Date").value;
	var expenseCategory = document.getElementById(type+"Category").value;
	var expenseDetails = document.getElementById(type+"Details").value;
	var expenseUsername = document.getElementById(type+"Username").value;
	document.getElementById(type+"Submit").innerHTML = "<div class='loaderButton'></div>";
	/*Validation*/
	if((expenseAmount == '') || (expenseDate == '') || (expenseCategory == '')){
		document.getElementById(type+"ErrorMessage").innerHTML = "<span class='errorMessage'>Fill in the required fields</span>";
	}else{
		/*AJAX Functionality*/
		/*Declare variables*/
		var addExpense = "RandomInput";
		var data = "addExpense="+addExpense+"&expenseAmount="+expenseAmount+"&expenseDate="+expenseDate+"&expenseCategory="+expenseCategory+"&expenseDetails="+expenseDetails+"&expenseUsername="+expenseUsername+"&type="+type;
		/*Declare XML*/
		if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
		else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
		/*AJAX Methods*/
		xhr.open("POST","conditions.php",true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(data);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				if(xhr.status == 200){
					eachResponse = this.responseText.split("-period-");/*Get two Messages from server*/
					document.getElementById(type+"ErrorMessage").innerHTML = "<div class='loaderButton'></div>";
					setTimeout(function(){document.getElementById(type+"ErrorMessage").innerHTML = eachResponse[0];},500);
					setTimeout(function(){document.getElementById(type+"ErrorMessage").innerHTML = "";},2500);
					document.getElementById("newExpense").innerHTML = eachResponse[1];
					document.getElementById("newBudget").innerHTML = eachResponse[3];
					/*Clear Form*/
					var clearInputs = document.getElementsByClassName(type+"Input");
					for(var i = 0; i < clearInputs.length; i++){
						clearInputs[i].value = "";
					}
				}
			}
		}	
	}
	document.getElementById(type+"Submit").innerHTML = "Submit";
}

/*Show Expense*/
function showExpenses(changeType){
	var username = document.getElementById("username").value;
	document.getElementById(changeType+"DetailsDiv").innerHTML = "<div class = 'loaderButton'></div>";			
	/*AJAX Functionality*/
	/*Declare variables*/
	var showExpense = "RandomInput";
	var data = "showExpense="+showExpense+"&username="+username+"&changeType="+changeType;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				if(document.getElementById("budgetListDiv")){document.getElementById("budgetListDiv").innerHTML = "";}
				if(document.getElementById("incomeListDiv")){document.getElementById("incomeListDiv").innerHTML = "";}
				if(document.getElementById("expenseListDiv")){document.getElementById("expenseListDiv").innerHTML = "";}
				document.getElementById("expenseDetailsDiv").innerHTML = "";			
				document.getElementById("incomeDetailsDiv").innerHTML = "";			
				document.getElementById("filterDiv").style.display = "none";
				document.getElementById("searchDiv").style.display = "none";

				document.getElementById(changeType+"DetailsDiv").innerHTML = this.responseText;			
			}
		}
	}
}

/*Show Income*/
/*Not using now as combined with the showexpense function*/
function showIncome(){
	var username = document.getElementById("username").value;
	document.getElementById("incomeDetailsDiv").innerHTML = "<div class = 'loaderButton'></div>";			
	/*AJAX Functionality*/
	/*Declare variables*/
	var showIncome = "RandomInput";
	var data = "showIncome="+showIncome+"&username="+username;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				document.getElementById("incomeDetailsDiv").innerHTML = this.responseText;			
				if(document.getElementById("expensesListDiv")){document.getElementById("expensesListDiv").innerHTML = "";}
				if(document.getElementById("budgetListDiv")){document.getElementById("budgetListDiv").innerHTML = "";}
			}
		}
	}
}

/*Show Budget*/
function showBudget(){
	var username = document.getElementById("username").value;
	document.getElementById("budgetDetailsDiv").innerHTML = "<div class = 'loaderButton'></div>";			
	/*AJAX Functionality*/
	/*Declare variables*/
	var showBudget = "RandomInput";
	var data = "showBudget="+showBudget+"&username="+username;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				if(document.getElementById("expensesListDiv")){document.getElementById("expensesListDiv").innerHTML = "";}
				if(document.getElementById("incomeListDiv")){document.getElementById("incomeListDiv").innerHTML = "";}
				document.getElementById("filterDiv").style.display = "none";
				document.getElementById("searchDiv").style.display = "none";

				document.getElementById("budgetDetailsDiv").innerHTML = this.responseText;			
			}
		}
	}
}

/*Show Edit Expenses*/
function editExpenses(id){
	var expensesId = id;
	/*AJAX Functionality*/
	/*Declare variables*/
	var showEditExpenses = "RandomInput";
	var data = "showEditExpenses="+showEditExpenses+"&expensesId="+expensesId;
	document.getElementById("editExpensesDiv").style.display = "block";
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				eachResponse = this.responseText.split("-period-");
				document.getElementById("editExpensesDiv").innerHTML = eachResponse[0];
				document.getElementById("editExpenseAmount"+expensesId).innerHTML = eachResponse[1];
				document.getElementById("editExpenseCategory"+expensesId).innerHTML = eachResponse[2];
				document.getElementById("editExpenseDate"+expensesId).innerHTML = eachResponse[3];
				document.getElementById("editExpenseDetails"+expensesId).innerHTML = eachResponse[4];
			}
		}
	}
}


/*Edit Expenses*/
function confirmEdit(id){
	var expensesId = id;
	var expensesAmount = document.getElementById("editExpenseAmount"+expensesId).value;
	var expensesDate = document.getElementById("editExpenseDate"+expensesId).value;
	var expensesCategory = document.getElementById("editExpenseCategory"+expensesId).value;
	var expensesDetails = document.getElementById("editExpenseDetails"+expensesId).value;
	/*Validation*/
	if((expensesAmount == '') || (expensesDate == '') || (expensesCategory == '')){
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessage'>Fill in the required fields.</span>";
	}else if(isNaN(expensesAmount)){
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessage'>Enter only numbers in amount.</span>";
	}else if(expensesDate.match(/^\d{4}-\d{2}-\d{2}$/) === null){
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessage'>Please Enter Date in yyyy-mm-dd format only.</span>";
	}else{
		/*AJAX Functionality*/
		/*Declare variables*/
		var editExpenses = "RandomInput";
		var data = "editExpenses="+editExpenses+"&expensesId="+expensesId+"&expensesAmount="+expensesAmount+"&expensesDate="+expensesDate+"&expensesCategory="+expensesCategory+"&expensesDetails="+expensesDetails;
		/*Declare XML*/
		if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
		else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
		/*AJAX Methods*/
		xhr.open("POST","conditions.php",true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(data);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				if(xhr.status == 200){
					document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'successMessage'>Details Edited</span>";
					setTimeout(function(){document.getElementById("editExpenseErrorMessage").innerHTML = "<div class = 'loaderButton'></div>";},500)
					setTimeout(function(){document.getElementById("editExpensesDiv").innerHTML = "";},1000);
					document.getElementById("expenseDetailsDiv").innerHTML = "";
					document.getElementById("incomeDetailsDiv").innerHTML = "";
					document.getElementById("budgetDetailsDiv").innerHTML = "";
					var eachResponse = this.responseText.split("-period-");
					if(eachResponse[0] == 'expense'){
						document.getElementById("newExpense").innerHTML = eachResponse[1];
					}else if(eachResponse[0] == 'income'){
						document.getElementById("newIncome").innerHTML = eachResponse[1];
					}
				}
			}
		}
	}
}


/*Show delete Expenses*/
function deleteExpenses(id){
	var expensesId = id;
	/*AJAX Functionality*/
	/*Declare variables*/
	var showDeleteExpenses = "RandomInput";
	var data = "showDeleteExpenses="+showDeleteExpenses+"&expensesId="+expensesId;
	document.getElementById("deleteExpensesDiv").style.display = "block";
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				eachResponse = this.responseText.split("-period-");
				document.getElementById("deleteExpensesDiv").innerHTML = eachResponse[0];
			}
		}
	}
}

/*Delete Expenses*/
function confirmDelete(id){
	var expensesId = id;
	/*AJAX Functionality*/
	/*Declare variables*/
	var deleteExpenses = "RandomInput";
	var data = "deleteExpenses="+deleteExpenses+"&expensesId="+expensesId;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				document.getElementById("deleteExpensesDiv").innerHTML = "";
				document.getElementById("expenseDetailsDiv").innerHTML = "";
				document.getElementById("incomeDetailsDiv").innerHTML = "";
				eachResponse = this.responseText.split("-period-");
				if(eachResponse[0] == 'expense'){
					document.getElementById("newExpense").innerHTML = eachResponse[1];
				}else if(eachResponse[0] == 'income'){
					document.getElementById("newIncome").innerHTML = eachResponse[1];
				}
				document.getElementById("newBudget").innerHTML = eachResponse[2];
			}
		}
	}
}


/*Hide Edit Div*/
function hideEditExpense(){
	document.getElementById("editExpensesDiv").innerHTML = "";
}

/*Hide Delete Div*/
function hideDeleteExpense(){
	document.getElementById("deleteExpensesDiv").innerHTML = "";
}

/*Hide Income, Expense List Div*/
function hideListDivs(){
	if(document.getElementById('incomeListDiv')){
		document.getElementById('incomeListDiv').innerHTML = "";
	}
	if(document.getElementById('expensesListDiv')){
		document.getElementById('expensesListDiv').innerHTML = "";
	}
	if(document.getElementById('budgetListDiv')){
		document.getElementById('budgetListDiv').innerHTML = "";
	}
}


/*Hide Profile Details*/
function hideProfileDetails(){
	document.getElementById("profileInfo").innerHTML = "";
}

/*Hide Edit Profile Details*/
function hideEditProfileDetails(){
	document.getElementById("editProfileInfo").innerHTML = "";
}

/*Toggle List Divs*/
function toggleListDiv(type, listType){
	if(document.getElementById(type+listType+"List").style.display == "none"){
		document.getElementById(type+listType+"List").style.display = "block";
	}else{
		document.getElementById(type+listType+"List").style.display = "none";
	}
}

/*Toggle Sub Lists*/
function toggleSubListDiv(type, listType, month, year){
	var requiredDiv = document.getElementById(type+listType+"ListOfMonth"+month+"AndYear"+year);
	var requiredDivLoader = document.getElementById("loader"+type+listType+"ListOfMonth"+month+"AndYear"+year);
	requiredDivLoader.innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var checkSubExpense = "RandomInput";
	var data = "checkSubExpense="+checkSubExpense+"&type="+type+"&listType="+listType+"&month="+month+"&year="+year;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				requiredDivLoader.innerHTML = "";
				if((requiredDiv.innerHTML == "")){
					requiredDiv.innerHTML = this.responseText;
				}else{
					requiredDiv.innerHTML = "";
				}
			}
		}
	}
}

function toggleSubLowerListDiv(type, listType, dateInNumberFormat){
	var requiredDiv = document.getElementById(type+listType+"ListOfDate"+dateInNumberFormat);
	var requiredDivLoader = document.getElementById("loader"+type+listType+"ListOfDate"+dateInNumberFormat);
	requiredDivLoader.innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var checkSubInnerExpense = "RandomInput";
	var data = "checkSubInnerExpense="+checkSubInnerExpense+"&type="+type+"&listType="+listType+"&date="+dateInNumberFormat;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				requiredDivLoader.innerHTML = "";
				if(requiredDiv.innerHTML == ""){
					requiredDiv.innerHTML = this.responseText;
				}else{
					requiredDiv.innerHTML = "";
				}
			}
		}
	}
}

/*Toggle Search, Filter Div*/
function showFilters(type){
	if(type == 'search'){document.getElementById("filterDiv").style.display = "none"; }
	if(type == 'filter'){document.getElementById("searchDiv").style.display = "none"; }
	if(document.getElementById(type+"Div").style.display == "block"){
		document.getElementById(type+"Div").style.display = "none";
	}else{
		document.getElementById(type+"Div").style.display = "block";
		if(type == 'search'){
			document.getElementById("searchInput").focus();
		}else if(type == 'filter'){
			document.getElementById("filterFromDate").focus();
		}
	}
	document.getElementById("incomeDetailsDiv").innerHTML = ""; /*To clear any previous inputs*/
	document.getElementById("expenseDetailsDiv").innerHTML = ""; /*To clear any previous inputs*/
	document.getElementById("budgetDetailsDiv").innerHTML = ""; /*To clear any previous inputs*/
	document.getElementById("searchResults").innerHTML = ""; /*To clear any previous inputs*/
	document.getElementById("filterResults").innerHTML = ""; /*To clear any previous inputs*/
}

/*Search Div*/
function getSearchResults(){
	var searchq = document.getElementById("searchInput").value;
	document.getElementById("searchResults").innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var searchExpenses = "RandomInput";
	var data = "searchExpenses="+searchExpenses+"&searchq="+searchq;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				document.getElementById("searchResults").innerHTML = this.responseText;
			}
		}
	}
}

/*Filter Div*/
function getFilterResults(){
	var fromDate = document.getElementById("filterFromDate").value;
	var toDate = document.getElementById("filterToDate").value;
	document.getElementById("filterResults").innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var filterExpenses = "RandomInput";
	var data = "filterExpenses="+filterExpenses+"&fromDate="+fromDate+"&toDate="+toDate;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				document.getElementById("filterResults").innerHTML = this.responseText;
			}
		}
	}
}

/*Get Profile Details*/
function showProfile(){
	var username = document.getElementById("username").value;
	document.getElementById("footerProfileButton").innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var getProfileDetails = "RandomInput";
	var data = "getProfileDetails="+getProfileDetails+"&username="+username;
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				document.getElementById("profileInfo").innerHTML = this.responseText;
				document.getElementById("footerProfileButton").innerHTML = "Profile";
			}
		}
	}
}

/*Edit Profile*/
function editProfile(type){
	var typeValue = type.split("-period-");
	/*AJAX Functionality*/
	/*Declare variables*/
	var editProfile = "RandomInput";
	var data = "editProfile="+editProfile+"&type="+typeValue[0]+"&value="+typeValue[1]+"&id="+typeValue[2];
	/*Declare XML*/
	if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
	else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
	/*AJAX Methods*/
	xhr.open("POST","conditions.php",true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				document.getElementById("profileInfo").innerHTML = "";
				document.getElementById("editProfileInfo").innerHTML = this.responseText;
				document.getElementById("newValueEditProfileDetail"+typeValue[0]).innerHTML = typeValue[1];
			}
		}
	}	
}

/*Confirm Edit Details*/
function confirmEditProfileDetails(type, id){
	var newValue = document.getElementById("newValueEditProfileDetail"+type).value;
	var oldValue = document.getElementById("oldValueEditProfileDetail"+type).value;
	document.getElementById("confirmEditProfileDetailsButton").innerHTML = "<div class = 'loaderButton'></div>";

	var error = "";
	/*Validation*/
	if(newValue == ''){
		var error = "<span class = 'errorMessage'>Please Fill the required details.</span>";
	}else if(type == 'email'){
		if(!/^[a-zA-Z0-9.-_]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(newValue)){ /*Validate expressions*/
			var error = "<span class = 'errorMessage'>Enter a valid Email ID.</span>";
		}
	}
	document.getElementById("confirmEditProfileDetailsButton").innerHTML = "Done";
	document.getElementById("editProfileStatus").innerHTML = error;
	/*If valid input*/
	if(error == ''){
		/*AJAX Functionality*/
		/*Declare variables*/
		var confirmEditProfile = "RandomInput";
		var data = "confirmEditProfile="+confirmEditProfile+"&type="+type+"&newValue="+newValue+"&oldValue="+oldValue+"&id="+id;
		/*Declare XML*/
		if(window.XMLHttpRequest){var xhr = new XMLHttpRequest();}
		else if(window.ActiveXObject){var xhr = new ActiveXObject("Microsoft.XMLHTTP");}
		/*AJAX Methods*/
		xhr.open("POST","conditions.php",true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(data);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				if(xhr.status == 200){
					if(this.responseText == 'updateDone'){
						document.getElementById("editProfileStatus").innerHTML = "<span class = 'successMessage'>Edited Successfully</span>";	
						setTimeout(function(){document.getElementById("editProfileStatus").innerHTML = "<span class = 'successMessage'>Edited Successfully</span><div class = 'loaderButton'></div>";},400);
						setTimeout(function(){document.getElementById("editProfileInfo").innerHTML = "";},1000);
						if(type == 'username'){
							document.getElementById("username").value = newValue;
						}
						if(type == 'name'){
							document.getElementById("contentGreetings").innerHTML = "Hello "+newValue+"!";
						}
					}else{
						document.getElementById("editProfileStatus").innerHTML = "<span class = 'errorMessage'>Username / Email Already Exists, Try Again.</span>";
					}
				}
			}
		}
	}	
}