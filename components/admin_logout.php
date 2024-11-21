<?php
include 'connect.php';
// عملت سيشن بوقت بالماينص يعني خرجت من السيشن قبل ما تبدأ, كأني عملت ديستروي
setcookie('tutor_id', '', time() - 1 ,'/');
header("Location: ../admin/index.php");

?>