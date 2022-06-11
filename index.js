/*Service Worker*/
/*if ("serviceWorker" in navigator) {
	window.addEventListener('load', () => {
		navigator.serviceWorker.register("service-worker.js").then(registration => {
			console.log(registration);
			console.log("Service Worker Registered! Scope is :",registration.scope);
		}).catch(error => {
			console.log("Service Worker Registration Failed!");
			console.log(error);
		});
	});
}*/

/*Install Prompt*/
/*var btnAdd=document.getElementById("installButton");
var deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
	e.preventDefault();
	deferredPrompt=e;
	btnAdd.style.display="block";
});*/

/*if(btnAdd){
	btnAdd.addEventListener('click', (e) => {
		deferredPrompt.prompt();
		deferredPrompt.userChoice.then((choiceResult) => {
			if(choiceResult.outcome === 'accepted'){
				console.log('User accepted the A2HS prompt');
			}
			deferredPrompt = null;
		});
	});
}*/

/*window.addEventListener('appinstalled', (evt) =>{
	app.logEvent('a2hs','installed');
});*/

/*Toggle Signup, Login forms*/
function toggleSignIn(formType){
	document.getElementById("loginForm").style.display = "none";
	document.getElementById("signupForm").style.display = "none";
	document.getElementById("loginErrorMessage").innerHTML = "";
	/*Form which is to be displayed*/
	document.getElementById(formType+"Form").style.display = "block";	
	document.getElementById(formType+"Username").focus();	
}

/*Show Password*/
function showPassword(type){
	if(document.getElementById(type+"Password").type == "text"){
		document.getElementById(type+"Password").type = "password";
		document.getElementById(type+"PasswordBoxLabel").style.textDecoration = "line-through";
	}else{
		document.getElementById(type+"Password").type = "text";
		document.getElementById(type+"PasswordBoxLabel").style.textDecoration = "none";
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
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessageShake'>Please Fill in all the fields</span>";
	}else if(!/^[a-zA-Z0-9 ]+$/.test(username)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessageShake'>Invalid charecters in Username</span>";
		document.getElementById("signupUsername").style.border = "3px solid red";
		document.getElementById("signupUsername").focus();
	}else if(!/^[a-zA-Z0-9!@#$%^&*]{4,15}$/.test(password)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessageShake'>Invalid charecters in Password / Minimum 4 charecters are required</span>";
		document.getElementById("signupPassword").style.border = "1px solid red";
	}else if(!/^[a-zA-Z0-9.-_]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessageShake'>Email not valid</span>";
		document.getElementById("signupEmail").style.border = "3px solid red";
		document.getElementById("signupEmail").focus();
	}else if(!/^[a-zA-Z ]+$/.test(fullName)){ /*Validate expressions*/
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessageShake'>Invalid charecters in Name</span>";
		document.getElementById("signupFullName").style.border = "1px solid red";
	}else if(password != confirmPassword){ /*If password, confirm password do not match */
		document.getElementById("signupErrorMessage").innerHTML = "<span class='errorMessageShake'>Password and confirm Password do not match!</span>";
		document.getElementById("signupPassword").style.border = "1px solid red";
		document.getElementById("signupConfirmPassword").style.border = "1px solid red";
	}else{ /*If all above conditions are valid, User is signed up!*/
		/*AJAX Functionality*/
		/*Declare variables*/
		var signup = "RandomInput";
		var data = "signup=" + signup + "&username=" + username + "&password=" + password + "&fullName=" + fullName + "&email=" + email;
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
	document.getElementById("loginUsername").focus();
	var username = document.getElementById("loginUsername").value;
	var password = document.getElementById("loginPassword").value;
	document.getElementById("loginButton").innerHTML = "<div class='loaderButton'></div>";
	document.getElementById("loginErrorMessage").innerHTML = "";/*To remove any previous error Messages*/
	if(document.getElementById("rememberMeLoginBox").checked!=false){var autoLogin = 'enabled';}else{var autoLogin = 'disabled';}
	/*Validation*/
	if(username == '' || password == ''){ /*Empty Fields*/
		document.getElementById("loginErrorMessage").innerHTML = "<span class='errorMessageShake'>Please Fill in all the fields</span>";
	}else{
		/*AJAX Functionality*/
		/*Declare variables*/
		var login = "RandomInput";
		var data = "login="+login+"&username="+username+"&password="+password+"&autoLogin="+autoLogin;
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
						document.getElementById("loginButton").innerHTML = "<div class = 'loaderButton'></div>";
					}else{
						document.getElementById("loginErrorMessage").innerHTML = this.responseText;
					}
				}
			}
		}
	}
	document.getElementById("loginButton").innerHTML = "LOGIN";
}

/*Show Expense Form*/
function showOtherDiv(type){
	document.getElementById(type+"FormOtherDataShow").style.display="block";
	document.getElementById(type+"Header").style.display="block";
}

/*Hide Expense Form*/
function closeOtherDiv(type){
	document.getElementById(type+"FormOtherDataShow").style.display="none";
	document.getElementById(type+"Header").style.display="inline";	
}

/*Add Expense*/
function addExpense(type){
	document.getElementById(type+"Submit").innerHTML = "<div class = 'loaderButton'></div>";
	var expenseAmount = document.getElementById(type+"Amount").value;
	var expenseDate = document.getElementById(type+"Date").value;
	var expenseCategory = document.getElementById(type+"Category").value;
	var expenseDetails = document.getElementById(type+"Details").value;
	var expenseUsername = document.getElementById(type+"Username").value;
	var expenseWallets = document.getElementsByName(type+"Wallet");
	var expenseWallet = 'noWalletRegd';
	for(var w=0; w<expenseWallets.length; w++){
		if(expenseWallets[w].checked!==false){
			expenseWallet = expenseWallets[w].value;
		}
	}

	/*Validation*/
	if((expenseAmount == '') || (expenseDate == '') || (expenseCategory == '')){
		document.getElementById(type+"ErrorMessage").innerHTML = "<span class='errorMessageShake'>Fill in the required fields..</span>";
	}else{
		/*AJAX Functionality*/
		/*Declare variables*/
		var addExpense = "RandomInput";
		var data = "addExpense="+addExpense+"&expenseAmount="+expenseAmount+"&expenseDate="+expenseDate+"&expenseCategory="+expenseCategory+"&expenseDetails="+expenseDetails+"&expenseUsername="+expenseUsername+"&expenseWallet="+expenseWallet+"&type="+type;
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
					/*document.getElementById(type+"ErrorMessage").innerHTML = "<div class='loaderButton'></div>";*/
					if(eachResponse[0]=='noEnoughMoneyInWallet'){
						document.getElementById(type+"ErrorMessage").innerHTML = '<div class="errorMessageShake">No enough money in '+expenseWallet+'! <br>Select another wallet or update amount in wallet</div>';
					}else{
						document.getElementById(type+"ErrorMessage").innerHTML = eachResponse[1];
						document.getElementById(type+"Submit").innerHTML = "<div class = 'loaderButton'></div>";
						setTimeout(function(){document.getElementById(type+"Submit").innerHTML = "ADD";},500);
						setTimeout(function(){document.getElementById(type+"ErrorMessage").innerHTML = "";},2000);
						document.getElementById("newExpense").innerHTML = eachResponse[2];
						document.getElementById("newIncome").innerHTML = eachResponse[3];
						document.getElementById("newBudget").innerHTML = eachResponse[4];
						/*Marquee Inputs*/
						if(document.getElementById("marqueeTodayExpense")){document.getElementById("marqueeTodayExpense").innerHTML = "-&#8377;"+eachResponse[5];}
						if(document.getElementById("marqueeTodayIncome")){document.getElementById("marqueeTodayIncome").innerHTML = "+&#8377;"+eachResponse[6];}
						if(document.getElementById("marqueeYesterdayExpense")){document.getElementById("marqueeYesterdayExpense").innerHTML = "-&#8377;"+eachResponse[7];}
						if(document.getElementById("marqueeYesterdayIncome")){document.getElementById("marqueeYesterdayIncome").innerHTML = "+&#8377;"+eachResponse[8];}
						if(document.getElementById("marqueeThisMonthExpense")){document.getElementById("marqueeThisMonthExpense").innerHTML = "-&#8377;"+eachResponse[9];}
						if(document.getElementById("marqueeThisMonthIncome")){document.getElementById("marqueeThisMonthIncome").innerHTML = "+&#8377;"+eachResponse[10];}
						if(expenseWallet!='noWalletRegd'){
							document.getElementById("allWalletList").innerHTML=eachResponse[11];
							document.getElementById("walletHistory").innerHTML=eachResponse[12];
							/*Retain current value*/
							for(var wa=0; wa<expenseWallets.length; wa++){
								expenseWallets[wa].checked=false;
							}
						}
						
						/*Clear Form*/
						var clearInputs = document.getElementsByClassName(type+"Input");
						for(var i = 0; i < clearInputs.length; i++){
							clearInputs[i].value = "";
						}
						document.getElementById(type+"Date").value = expenseDate;/*Retain Previously updated date*/
					}
				}
			}
		}	
	}
	document.getElementById(type+"Submit").innerHTML = "ADD";
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
				if(document.getElementById("expenseDetailsDiv")){document.getElementById("expenseDetailsDiv").innerHTML = "";}			
				if(document.getElementById("incomeDetailsDiv")){document.getElementById("incomeDetailsDiv").innerHTML = "";}	
				if(document.getElementById("budgetDetailsDiv")){document.getElementById("budgetDetailsDiv").innerHTML = "";}	
				if(document.getElementById("filterDiv")){document.getElementById("filterDiv").style.display = "none";}
				if(document.getElementById("searchDiv")){document.getElementById("searchDiv").style.display = "none";}

				document.body.scrollTop = document.documentElement.scrollTop = 0;	
				document.getElementById(changeType+"DetailsDiv").innerHTML = this.responseText;			
			}
		}
	}
}

/*Show Individual Expense*/
function showIndividualExpenses(type, month, year, day, amount){
	/*Remove leading 0 from month*/
	if(month<10){
		month = month / 10;
		month = month * 10;
	}
	showExpenses(type);
	if(amount > 0){
		setTimeout(function(){document.getElementById("loaderOf"+type).innerHTML = "<div class = 'loaderButton'></div>";},1000)
		setTimeout(function(){toggleSubListDiv(type, 'Day', month, year);},2000);
		if(day != 'thisIsMonth'){
			/*Added for Debugging*//*document.getElementById("contentGreetings").innerHTML = document.getElementById("contentGreetings").innerHTML+"loader"+type+'All'+"ListOfDate"+day;*/
			setTimeout(function(){toggleSubLowerListDiv(type, 'All', day);},3000);
		}
	}
	setTimeout(function(){document.getElementById("loaderOf"+type).innerHTML = "";},3200)
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
				if(document.getElementById("budgetListDiv")){document.getElementById("budgetListDiv").innerHTML = "";}
				if(document.getElementById("expenseDetailsDiv")){document.getElementById("expenseDetailsDiv").innerHTML = "";}			
				if(document.getElementById("incomeDetailsDiv")){document.getElementById("incomeDetailsDiv").innerHTML = "";}	
				if(document.getElementById("budgetDetailsDiv")){document.getElementById("budgetDetailsDiv").innerHTML = "";}	
				document.getElementById("filterDiv").style.display = "none";
				document.getElementById("searchDiv").style.display = "none";

				document.body.scrollTop = document.documentElement.scrollTop = 0;	
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
				document.getElementById("editExpenseWallet"+expensesId).innerHTML = eachResponse[5];
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
	var expensesWallet = document.getElementById("editExpenseWallet"+expensesId).value;
	/*Validation*/
	if((expensesAmount == '') || (expensesDate == '') || (expensesCategory == '')){
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Fill in the required fields.</span>";
	}else if(isNaN(expensesAmount)){
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Enter only numbers in amount.</span>";
	}else if(expensesDate.match(/^\d{2}-\d{2}-\d{4}$/) === null){
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Please Enter Date in dd-mm-yyyy format only.</span>";
	}else{
		/*AJAX Functionality*/
		/*Declare variables*/
		var editExpenses = "RandomInput";
		var data = "editExpenses="+editExpenses+"&expensesId="+expensesId+"&expensesAmount="+expensesAmount+"&expensesDate="+expensesDate+"&expensesCategory="+expensesCategory+"&expensesDetails="+expensesDetails+"&expensesWallet="+expensesWallet;
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
				document.getElementById("deleteExpenseErrorMessage").innerHTML = "<span class = 'successMessage'>Data Deleted Successfully!</span>";
				setTimeout(function(){document.getElementById("deleteExpenseErrorMessage").innerHTML = "<div class = 'loaderButton'></div>";},500)
				setTimeout(function(){document.getElementById("deleteExpensesDiv").innerHTML = "";},1000);
				
				document.getElementById("expenseDetailsDiv").innerHTML = "";
				document.getElementById("incomeDetailsDiv").innerHTML = "";
				document.getElementById("budgetDetailsDiv").innerHTML = "";
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

/*Hide Sub Details Div*/
function hideSubDetailsDiv(){
	document.getElementById("getSubDetailsDiv").innerHTML = "";	
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
	if(document.getElementById('statementListDiv')){
		document.getElementById('statementListDiv').innerHTML = "";
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

/*Hide Edit Wallet Div*/
function hideEditWallet(){
	document.getElementById("editWalletDiv").innerHTML = "";
}

/*Hide Delete Wallet Div*/
function hideDeleteWallet(){
	document.getElementById("deleteWalletDiv").innerHTML = "";
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
	/*Added for Debugging*//*document.getElementById("contentGreetings").innerHTML = document.getElementById("contentGreetings").innerHTML+"loader"+type+listType+"ListOfDate"+dateInNumberFormat;*/
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
	//Clear old state
	if(type == 'search'){document.getElementById("filterDiv").style.display = "none";document.getElementById("statementDiv").style.display = "none"; }
	if(type == 'filter'){document.getElementById("searchDiv").style.display = "none";document.getElementById("statementDiv").style.display = "none"; }
	if(type == 'statement'){document.getElementById("searchDiv").style.display = "none";document.getElementById("filterDiv").style.display = "none"; }
	//Setting State
	if(document.getElementById(type+"Div").style.display == "block"){
		document.getElementById(type+"Div").style.display = "none";
	}else{
		document.getElementById(type+"Div").style.display = "block";
		if(type == 'search'){
			document.getElementById("searchInput").focus();
			/*Enter to search*/
			var searchEnter = document.getElementById('searchInput');
			searchEnter.addEventListener("keyup", function(event){
				if(event.keyCode == 13){
					event.preventDefault();getSearchResults();
				}
			})
		}else if(type == 'filter'){
			document.getElementById("filterFromDate").focus();
		}else if(type == 'statement'){
			document.getElementById("statementFromDate").focus();
		}
		/*To prevent automatic scroll while focussing*/
		/*var x = window.scrollX; var y = window.scrollY;
		window.scrollTo(x,y);*/
		document.body.scrollTop = document.documentElement.scrollTop = 0;	

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
	var username = document.getElementById("username").value;
	document.getElementById("searchResults").innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var searchExpenses = "RandomInput";
	var data = "searchExpenses="+searchExpenses+"&searchq="+searchq+"&username="+username;
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
	var username = document.getElementById("username").value;
	document.getElementById("filterResults").innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var filterExpenses = "RandomInput";
	var data = "filterExpenses="+filterExpenses+"&fromDate="+fromDate+"&toDate="+toDate+"&username="+username;
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
		var error = "<span class = 'errorMessageShake'>Please Fill the required details.</span>";
	}else if(type == 'email'){
		if(!/^[a-zA-Z0-9.-_]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(newValue)){ /*Validate expressions*/
			var error = "<span class = 'errorMessageShake'>Enter a valid Email ID.</span>";
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
						document.getElementById("editProfileStatus").innerHTML = "<span class = 'errorMessageShake'>Username / Email Already Exists, Try Again.</span>";
					}
				}
			}
		}
	}	
}

/*Show Individual Expense Details*/
function showExpenseSubDetails(month, year, category, type){
	var month = month;
	var year = year;
	var category = category;
	var type = type;
	var username = document.getElementById("username").value;
	/*AJAX Functionality*/
	/*Declare variables*/
	var checkSubDetails = "RandomInput";
	var data = "checkSubDetails="+checkSubDetails+"&month="+month+"&year="+year+"&category="+category+"&username="+username+"&type="+type;
	document.getElementById("getSubDetailsDiv").style.display="block";
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
				document.getElementById("getSubDetailsDiv").innerHTML = this.responseText;
			}
		}
	}	
}

/*Open Calculator*/
function toggleCalc(func){
	if(func == 'open'){
		document.getElementById("calcDiv").style.display = "block";
		document.getElementById("calcOuterDiv").style.display = "block";
		document.getElementById("calcInput").focus();
		var searchEnter = document.getElementById('calcInput');
		searchEnter.addEventListener("keyup", function(event){
			if(event.keyCode == 13){
				event.preventDefault();calculate();
			}
		})
	}else{
		document.getElementById("calcDiv").style.display = "none";
		document.getElementById("calcOuterDiv").style.display = "none";
	}
}

/*Calculator*/
function calculate(){
	var query = document.getElementById("calcInput").value;

	/*Validation*/
	document.getElementById("calcResult").innerHTML = "";
	if(query == ''){
		document.getElementById("calcResult").innerHTML = "<span class = 'errorMessageShake'>No input given.</span>";
	}else if(!/^[0-9\.\+\-\*\/\%\^\*\(\)]+$/.test(query)){
		document.getElementById("calcResult").innerHTML = "<span class = 'errorMessageShake'>Enter a valid input.</span>";
	}else{
		var output = eval(query);
		document.getElementById("calcResult").innerHTML = "Result: <span class = 'successMessage'>"+query+" = "+output+"</span>";
		document.getElementById("calcInput").value = output;
	}
}

/*Calc Help*/
function putCalc(val){
	if(val == 'C'){
		document.getElementById("calcInput").value = "";
	}else if(val == 'CE'){
		var newEl = document.getElementById("calcInput").value.slice(0,-1);
		document.getElementById("calcInput").value = newEl;
	}else{
		document.getElementById("calcInput").value = document.getElementById("calcInput").value+val;
		/*document.getElementById("calcInput").focus();*/
	}
}

/*Notifications*/
/*Discontinued as not much effective!*/
function notifyMe() {
	if (!("Notification" in window)) {
    	alert("This browser does not support desktop notification");
  	}else if(Notification.permission === "granted") {
  		var name = document.getElementById("userFullName").value;
  		var username = document.getElementById("username").value;
  		/*AJAX Functionality*/
		/*Declare variables*/
		var getExpenseForNotif = "RandomInput";
		var data = "getExpenseForNotif="+getExpenseForNotif+"&username="+username;
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
					var time = new Date();
				    if((time.getHours() == 21 && time.getMinutes() == 30 && time.getSeconds() < 1) || (time.getHours() == 10 && time.getMinutes() == 0 && time.getSeconds() < 1)){
				    	var options = {
				    		body: "Today's Expenses : ₹"+eachResponse[0]+", Income: ₹"+eachResponse[1],
				    		vibrate: [200, 100, 200]
				    	};
				    	var notification = new Notification("Hi "+name+"! Added all expenses for today?", options);
				    	notification.onclick = function(event){
				    		event.preventDefault();
				    		window.open('home.php');
				    	}
				    }
				}
			}
		}
  	}else if(Notification.permission !== "denied") {
    	Notification.requestPermission().then(function (permission) {
    	  	if (permission === "granted") {
        		var notification = new Notification("Hi there! Daily at 10:00 and 21:30; you will receive a notification, Click on it to fill the day's expenses!");
    		}
    	});
  	}
  	setTimeout(notifyMe, 1000);
}

/*Show Graph Values*/
function showGraphValues(){
	var graphValues=document.getElementsByClassName("graphValueText");
	/*var showGraphButton=document.getElementsByClassName("showGraphValuesButton");*/
	for(var i=0;i<graphValues.length;i++){
		if(graphValues[i].style.display=="inline-block"){
			graphValues[i].style.display="none";
			/*for(var j=0;j<showGraphButton.length;j++){showGraphButton[j].innerHTML="Show Values";}*/
		}else{
			graphValues[i].style.display="inline-block";
			/*for(var j=0;j<showGraphButton.length;j++){showGraphButton[j].innerHTML="Hide Values";}*/
		}
	}
}

/*Add new Wallet*/
function addWallet(){
	var walletName = document.getElementById("walletName").value;
	var walletAmount = document.getElementById("walletAmount").value;
	var username = document.getElementById("username").value;
	document.getElementById("walletSubmit").innerHTML = "<div class='loaderButton'></div>";
	/*Validations*/
	if(walletName == '' || walletAmount == ''){ /*Empty Fields*/
		document.getElementById("walletErrorMessage").innerHTML = "<span class='errorMessageShake'>Please Fill in all the fields</span>";
		setTimeout(function(){document.getElementById("walletErrorMessage").innerHTML = "";},4000)
	}else{ 
		/*AJAX Functionality*/
		/*Declare variables*/
		var addWallet = "RandomInput";
		var data = "addWallet="+addWallet+"&walletName="+walletName+"&walletAmount="+walletAmount+"&username="+username;
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
					eachResponse=this.responseText.split('-period-');
					if(eachResponse[0]=='success'){
						document.getElementById("walletErrorMessage").innerHTML = "<span class='successMessage'>Wallet Added Successfully!</span>";
						setTimeout(function(){document.getElementById("walletErrorMessage").innerHTML = '';},4000);
						/*Set wallet list in add expense and income*/
						document.getElementById("expenseWalletList").innerHTML=eachResponse[1];
						document.getElementById("incomeWalletList").innerHTML=eachResponse[2];
						document.getElementById("allWalletList").innerHTML=eachResponse[3];
						document.getElementById("addMoneyToWallet").innerHTML=eachResponse[4];
						/*Clearing inputs*/
						document.getElementById("walletName").value="";
						document.getElementById("walletAmount").value="";
					}else if(eachResponse[0]=='registeredAlready'){
						document.getElementById("walletErrorMessage").innerHTML = "<span class='errorMessageShake'>Wallet with this name is already registered with us.. Try with a new name!</span>";
						setTimeout(function(){document.getElementById("walletErrorMessage").innerHTML = '';},4000);
					}else{
						document.getElementById("walletErrorMessage").innerHTML = "<span class='errorMessageShake'>Something went wrong!</span>";
						setTimeout(function(){document.getElementById("walletErrorMessage").innerHTML = '';},4000);
					}

				}
			}
		}
	}
	document.getElementById("walletSubmit").innerHTML = "ADD";
}

/*Wallet select*/
function setWalletSelection(type){
	var username=document.getElementById("username").value;
	if(type=='credit'){var selectedValue=document.getElementById("walletCredit").value;}
	else if(type=='debit'){var selectedValue=document.getElementById("walletDebit").value;}
	/*AJAX Functionality*/
	/*Declare variables*/
	var setWalletSelection = "RandomInput";
	var data = "setWalletSelection="+setWalletSelection+"&username="+username+"&selectedValue="+selectedValue;
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
				if(type=='debit'){
					document.getElementById("walletCredit").innerHTML=this.responseText;
				}/*else if(type=='credit'){
					document.getElementById("walletDebit").innerHTML=this.responseText;
				}*/
			}
		}
	}
}

/*Wallet Exhange Amount*/
function walletExchange(){
	var username=document.getElementById("username").value;
	var walletExchangeAmount=document.getElementById("walletExchangeAmount").value;
	var walletDebit=document.getElementById("walletDebit").value;
	var walletCredit=document.getElementById("walletCredit").value;
	var walletExchangeDate=document.getElementById("walletTransferDate").value;
	document.getElementById("walletExchangeSubmit").innerHTML = "<div class='loaderButton'></div>";
	/*Validations*/
	if(walletDebit == '' || walletCredit == '' || walletExchangeAmount == '' || walletExchangeDate == ''){ /*Empty Fields*/
		document.getElementById("walletExchangeErrorMessage").innerHTML = "<span class='errorMessageShake'>Please Fill in all the fields</span>";
		setTimeout(function(){document.getElementById("walletExchangeErrorMessage").innerHTML = "";},4000)
	}else{ 
		/*AJAX Functionality*/
		/*Declare variables*/
		var exchangeWalletAmount = "RandomInput";
		var data = "exchangeWalletAmount="+exchangeWalletAmount+"&walletCredit="+walletCredit+"&walletDebit="+walletDebit+"&walletExchangeAmount="+walletExchangeAmount+"&walletExchangeDate="+walletExchangeDate+"&username="+username;
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
					eachResponse=this.responseText.split("-period-");
					if(eachResponse[0]=='insufficientBalance'){
						document.getElementById("walletExchangeErrorMessage").innerHTML = "<span class='errorMessageShake'>Insufficient Balance in "+walletDebit+"</span>";
						setTimeout(function(){document.getElementById("walletExchangeErrorMessage").innerHTML = "";},4000)
					}else if(eachResponse[0]=='exchangeDone'){
						document.getElementById("walletExchangeErrorMessage").innerHTML = "<span class='successMessage'>&#8377;"+walletExchangeAmount+" added from "+walletDebit+" to "+walletCredit+" Successfully!</span>";
						setTimeout(function(){document.getElementById("walletExchangeErrorMessage").innerHTML = '';},4000);
						document.getElementById("allWalletList").innerHTML=eachResponse[1];
						document.getElementById("walletHistory").innerHTML=eachResponse[2];
						/*Clearing Form*/
						document.getElementById("walletExchangeAmount").value="";
						document.getElementById("walletDebit").value="";
						document.getElementById("walletCredit").value="";
					}
				}
			}
		}
	}
	document.getElementById("walletExchangeSubmit").innerHTML = "SEND";
}


/*Show Wallet Divs*/
function showWalletDiv(type){
	closeWalletDiv();
	document.getElementById(type).style.display="block";
	//Update new values while showing div
	if(type=="allWalletList" || type=="walletHistory"){
		document.getElementById(type).innerHTML="<br><div class='loaderButton'></div><br>";
		var username=document.getElementById("username").value;
		/*AJAX Functionality*/
		/*Declare variables*/
		var updateWalletDetails = "RandomInput";
		var data = "updateWalletDetails="+updateWalletDetails+"&username="+username;
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
					eachResponse=this.responseText.split("-period-");
					if(type=='allWalletList'){
						document.getElementById("allWalletList").innerHTML=eachResponse[0];
					}
					else if(type=='walletHistory'){
						document.getElementById("walletHistory").innerHTML=eachResponse[1];
					}
				}
			}
		}	
	}
}

/*Close Wallet Div*/
function closeWalletDiv(){
	document.getElementById("addWalletData").style.display="none";
	document.getElementById("allWalletList").style.display="none";
	document.getElementById("addMoneyToWallet").style.display="none";
	document.getElementById("walletHistory").style.display="none";
}

/*Edit wallet data*/
function showEditWallet(walletId){
	/*AJAX Functionality*/
	/*Declare variables*/
	var showEditWallet = "RandomInput";
	var data = "showEditWallet="+showEditWallet+"&walletId="+walletId;
	document.getElementById("editWalletDiv").style.display = "block";
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
				document.getElementById("editWalletDiv").innerHTML = eachResponse[0];
				document.getElementById("editWalletName"+walletId).innerHTML = eachResponse[1];
				document.getElementById("editWalletAmount"+walletId).innerHTML = eachResponse[2];
			}
		}
	}
}

/*Edit Wallet*/
function confirmEditWallet(id,oldWalletName){
	var walletId = id;
	var oldWalletName=oldWalletName;
	var walletName = document.getElementById("editWalletName"+walletId).value;
	var walletAmount = document.getElementById("editWalletAmount"+walletId).value;
	var username = document.getElementById("username").value;
	/*Validation*/
	if((walletAmount == '') || (walletName == '')){
		document.getElementById("editWalletErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Fill in the required fields.</span>";
	}else if(isNaN(walletAmount)){
		document.getElementById("editWalletErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Enter only numbers in amount.</span>";
	}else{
		/*AJAX Functionality*/
		/*Declare variables*/
		var editWallet = "RandomInput";
		var data = "editWallet="+editWallet+"&walletId="+walletId+"&walletAmount="+walletAmount+"&walletName="+walletName+"&username="+username+"&oldWalletName="+oldWalletName;
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
					document.getElementById("editWalletErrorMessage").innerHTML = "<span class = 'successMessage'>Details Edited</span>";
					setTimeout(function(){document.getElementById("editWalletErrorMessage").innerHTML = "<div class = 'loaderButton'></div>";},500)
					setTimeout(function(){document.getElementById("editWalletDiv").innerHTML = "";},1000);
					var eachResponse = this.responseText.split("-period-");
					document.getElementById("allWalletList").innerHTML=eachResponse[1];
					document.getElementById("addMoneyToWallet").innerHTML=eachResponse[2];
					document.getElementById("expenseWalletList").innerHTML=eachResponse[3];
					document.getElementById("incomeWalletList").innerHTML=eachResponse[4];
				}
			}
		}
	}
}

/*Show delete Wallet*/
function showDeleteWallet(id){
	var walletId = id;
	/*AJAX Functionality*/
	/*Declare variables*/
	var showDeleteWallet = "RandomInput";
	var data = "showDeleteWallet="+showDeleteWallet+"&walletId="+walletId;
	document.getElementById("deleteWalletDiv").style.display = "block";
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
				document.getElementById("deleteWalletDiv").innerHTML = this.responseText;
			}
		}
	}
}

/*Delete Wallet*/
function confirmDeleteWallet(id,walletName){
	var walletId = id;
	var username=document.getElementById("username").value;
	/*AJAX Functionality*/
	/*Declare variables*/
	var deleteWallet = "RandomInput";
	var data = "deleteWallet="+deleteWallet+"&walletId="+walletId+"&username="+username+"&walletName="+walletName;
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
				document.getElementById("deleteWalletErrorMessage").innerHTML = "<span class = 'successMessage'>Wallet Deleted Successfully!</span>";
				setTimeout(function(){document.getElementById("deleteWalletErrorMessage").innerHTML = "<div class = 'loaderButton'></div>";},500)
				setTimeout(function(){document.getElementById("deleteWalletDiv").innerHTML = "";},1000);
				/*Update New List*/
				var eachResponse = this.responseText.split("-period-");
				document.getElementById("allWalletList").innerHTML=eachResponse[0];
				document.getElementById("addMoneyToWallet").innerHTML=eachResponse[1];
				document.getElementById("expenseWalletList").innerHTML=eachResponse[2];
				document.getElementById("incomeWalletList").innerHTML=eachResponse[3];
			}
		}
	}
}

/*Delete Wallet History*/
function deleteWalletHistory(id){
	var walletId = id;
	/*AJAX Functionality*/
	/*Declare variables*/
	var showDeleteWalletHistory = "RandomInput";
	var data = "showDeleteWalletHistory="+showDeleteWalletHistory+"&walletId="+walletId;
	document.getElementById("deleteWalletDiv").style.display = "block";
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
				document.getElementById("deleteWalletDiv").innerHTML = this.responseText;
			}
		}
	}
}

/*Show Delete Wallet History*/
function confirmDeleteWalletHistory(id){
	var walletId = id;
	var username=document.getElementById("username").value;
	/*AJAX Functionality*/
	/*Declare variables*/
	var deleteWalletHistory = "RandomInput";
	var data = "deleteWalletHistory="+deleteWalletHistory+"&walletId="+walletId+"&username="+username;
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
				document.getElementById("deleteWalletErrorMessage").innerHTML = "<span class = 'successMessage'>Transaction Deleted Successfully!</span>";
				setTimeout(function(){document.getElementById("deleteWalletErrorMessage").innerHTML = "<div class = 'loaderButton'></div>";},500)
				setTimeout(function(){document.getElementById("deleteWalletDiv").innerHTML = "";},1000);
				/*Update New List*/
				var eachResponse = this.responseText.split("-period-");
				document.getElementById("allWalletList").innerHTML=eachResponse[0];
				document.getElementById("addMoneyToWallet").innerHTML=eachResponse[1];
				document.getElementById("expenseWalletList").innerHTML=eachResponse[2];
				document.getElementById("incomeWalletList").innerHTML=eachResponse[3];
				document.getElementById("walletHistory").innerHTML=eachResponse[4];
			}
		}
	}
}

/*Show Edit Wallet History*/
function editWalletHistory(id) {
	var walletId = id;
	/*AJAX Functionality*/
	/*Declare variables*/
	var showEditWalletHistory = "RandomInput";
	var data = "showEditWalletHistory=" + showEditWalletHistory + "&walletId=" + walletId;
	document.getElementById("editWalletDiv").style.display = "block";
	/*Declare XML*/
	if (window.XMLHttpRequest) { var xhr = new XMLHttpRequest(); }
	else if (window.ActiveXObject) { var xhr = new ActiveXObject("Microsoft.XMLHTTP"); }
	/*AJAX Methods*/
	xhr.open("POST", "conditions.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4) {
			if (xhr.status == 200) {
				eachResponse = this.responseText.split("-period-");
				document.getElementById("editWalletDiv").innerHTML = eachResponse[0];
				document.getElementById("editWalletHistoryFrom" + walletId).innerHTML = eachResponse[1];
				if (document.getElementById("editWalletHistoryTo" + walletId)) { document.getElementById("editWalletHistoryTo" + walletId).innerHTML = eachResponse[2]; }
				document.getElementById("editWalletHistoryValue" + walletId).innerHTML = eachResponse[3];
				document.getElementById("editWalletHistoryDate" + walletId).innerHTML = eachResponse[4];
				if (document.getElementById("editWalletHistoryType" + walletId)) { document.getElementById("editWalletHistoryType" + walletId).innerHTML = eachResponse[5]; }
				if (document.getElementById("editWalletHistoryCategory" + walletId)) { document.getElementById("editWalletHistoryCategory" + walletId).innerHTML = eachResponse[6]; }
				if (document.getElementById("editWalletHistoryDetails" + walletId)) { document.getElementById("editWalletHistoryDetails" + walletId).innerHTML = eachResponse[7]; }
			}
		}
	}
}

/*Edit Wallet History*/
function confirmEditWalletHistory(id) {
	var walletId = id;
	var username = document.getElementById("username").value;
	var walletHistoryFrom = document.getElementById("editWalletHistoryFrom" + walletId).value;
	if (document.getElementById("editWalletHistoryTo" + walletId)) {
		var walletHistoryTo = document.getElementById("editWalletHistoryTo" + walletId).value;
	} else { var walletHistoryTo = 'walletExpenseOK';}
	var walletHistoryValue = document.getElementById("editWalletHistoryValue" + walletId).value;
	var walletHistoryDate = document.getElementById("editWalletHistoryDate" + walletId).value;
	if (document.getElementById("editWalletHistoryType" + walletId)) {
		var walletHistoryType = document.getElementById("editWalletHistoryType" + walletId).value;
	} else { var walletHistoryType = 'walletTransfer';}
	if (document.getElementById("editWalletHistoryCategory" + walletId)) {
		var walletHistoryCategory = document.getElementById("editWalletHistoryCategory" + walletId).value;
	} else { var walletHistoryCategory = 'walletTransfer';}
	if (document.getElementById("editWalletHistoryDetails" + walletId)) {
		var walletHistoryDetails = document.getElementById("editWalletHistoryDetails" + walletId).value;
	} else { var walletHistoryDetails = 'walletTransfer';}
	/*Validation*/
	if ((walletHistoryFrom == '') || (walletHistoryValue == '') || (walletHistoryDate == '')) {
		document.getElementById("editWalletErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Fill in the required fields.</span>";
	} else if (isNaN(walletHistoryValue)) {
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Enter only numbers in amount.</span>";
	} else if (walletHistoryDate.match(/^\d{2}-\d{2}-\d{4}$/) === null) {
		document.getElementById("editExpenseErrorMessage").innerHTML = "<span class = 'errorMessageShake'>Please Enter Date in dd-mm-yyyy format only.</span>";
	} else {
		/*New Wallet Value*/
		var currentOldWalletValue = document.getElementById("currentWalletValue" + walletId).value;
		var bufferWalletValue = currentOldWalletValue - walletHistoryValue;
		/*AJAX Functionality*/
		/*Declare variables*/
		var editWalletHistory = "RandomInput";
		var data = "editWalletHistory=" + editWalletHistory + "&username=" + username + "&walletId=" + walletId + "&walletHistoryFrom=" + walletHistoryFrom + "&walletHistoryTo=" + walletHistoryTo + "&walletHistoryValue=" + walletHistoryValue + "&walletHistoryDate=" + walletHistoryDate + "&walletHistoryType=" + walletHistoryType + "&walletHistoryCategory=" + walletHistoryCategory + "&walletHistoryDetails=" + walletHistoryDetails + "&bufferWalletValue=" + bufferWalletValue;
		/*Declare XML*/
		if (window.XMLHttpRequest) { var xhr = new XMLHttpRequest(); }
		else if (window.ActiveXObject) { var xhr = new ActiveXObject("Microsoft.XMLHTTP"); }
		/*AJAX Methods*/
		xhr.open("POST", "conditions.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(data);
		xhr.onreadystatechange = function () {
			if (xhr.readyState == 4) {
				if (xhr.status == 200) {
					document.getElementById("editWalletErrorMessage").innerHTML = "<span class = 'successMessage'>Details Edited</span>";
					console.log(this.responseText);
					setTimeout(function () { document.getElementById("editWalletErrorMessage").innerHTML = "<div class = 'loaderButton'></div>"; }, 500)
					setTimeout(function () { document.getElementById("editWalletDiv").innerHTML = ""; }, 1000);
					document.getElementById("expenseDetailsDiv").innerHTML = "";
					document.getElementById("incomeDetailsDiv").innerHTML = "";
					document.getElementById("budgetDetailsDiv").innerHTML = "";
				}
			}
		}
	}
}

function showWalletButtons(){
	if(document.getElementById("walletButtons").style.display=="block"){
		document.getElementById("walletButtons").style.display="none";
		closeWalletDiv();
		document.getElementById("walletActionDiv").innerHTML="<button type='button' id='showWalletButtons' class='basicButtonOuter smallButton' onclick='showWalletButtons()'>Wallet Details</button>";
	}else{
		document.getElementById("walletButtons").style.display="block";
		showWalletDiv("allWalletList");
		document.getElementById("walletActionDiv").innerHTML="<button type='button' class='redButton' onclick='showWalletButtons()'>CLOSE</button>";
	}
}

function updateWebsite(){
	var username=document.getElementById("username").value;
	document.getElementById("updateWebsiteButton").innerHTML = "Updating... Do not Refresh..<div class = 'loaderButton'></div>";
	setTimeout(function () { document.getElementById("updateWebsiteButton").innerHTML = "<div class = 'loaderButton'></div>"; }, 1500);
	/*AJAX Functionality*/
	/*Declare variables*/
	var updateWebsite = "RandomInput";
	var data = "updateWebsite=" + updateWebsite + "&username=" + username;
	/*Declare XML*/
	if (window.XMLHttpRequest) { var xhr = new XMLHttpRequest(); }
	else if (window.ActiveXObject) { var xhr = new ActiveXObject("Microsoft.XMLHTTP"); }
	/*AJAX Methods*/
	xhr.open("POST", "conditions.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4) {
			if (xhr.status == 200) {
				if(this.responseText=='UpdateDone'){
					setTimeout(function () { document.getElementById("updateWebsiteButton").style.display="none"; }, 2000);
					document.getElementById("updateWebsiteButton").innerHTML="Update Done!";
				}else if(this.responseText=='UpdateNotDone'){
					setTimeout(function () { document.getElementById("updateWebsiteButton").innerHTML="Something Went Wrong!"; }, 2000);
				}else{
					setTimeout(function () { document.getElementById("updateWebsiteButton").innerHTML="Something Fishy!!!"; }, 2000);
				}
			}
		}
	}
}

//Filter Wallet
function filterWalletHistory(wallet){
	var allRows=document.getElementsByClassName('allWalletHistoryRows');
	if(wallet!='all'){
		for(var i = 0; i < allRows.length; i++){
			allRows[i].style.display = "none";
		}
		//Display Required wallet Details
		var reqRows=document.getElementsByClassName(wallet+"WalletDetails");
		for(var i = 0; i < reqRows.length; i++){
			reqRows[i].style.display = "table-row";
		}
	}else{
		for(var i = 0; i < allRows.length; i++){
			allRows[i].style.display = "table-row";
		}
	}
}

/*Statement Div*/
function getStatementResults(){
	var fromDate = document.getElementById("statementFromDate").value;
	var toDate = document.getElementById("statementToDate").value;
	var username = document.getElementById("username").value;
	document.getElementById("statementResults").innerHTML = "<div class = 'loaderButton'></div>";
	/*AJAX Functionality*/
	/*Declare variables*/
	var statementExpenses = "RandomInput";
	var data = "statementExpenses="+statementExpenses+"&fromDate="+fromDate+"&toDate="+toDate+"&username="+username;
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
				document.getElementById("statementResults").innerHTML = this.responseText;
			    var table2excel = new Table2Excel(); //External function
		        table2excel.export(document.getElementById('statementTable')); //External function
		        document.getElementById("downloadStatementButton").innerHTML="File Downloaded";
		        setTimeout(function(){document.getElementById("downloadStatementButton").innerHTML = "Download";},2500);
			}
		}
	}
}
