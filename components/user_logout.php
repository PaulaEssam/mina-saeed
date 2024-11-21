<?php
include 'connect.php';
// عملت سيشن بوقت بالماينص يعني خرجت من السيشن قبل ما تبدأ, كأني عملت ديستروي
setcookie('user_id', '', time() - 1 ,'/');
header("Location: ../index.php");

?>