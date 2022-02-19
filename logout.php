<?php

session_start();
session_destroy();
session_unset();
setcookie('autoLogin','No',time()-3600*5,'/');
setcookie('username','',time()-3600*5,'/');
setcookie('userID','',time()-3600*5,'/');
header('Location:index.php')

?>