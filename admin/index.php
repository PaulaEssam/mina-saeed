<?php
include '../components/connect.php';
    if (isset($_COOKIE['tutor_id'])) {
        $tutor_id = $_COOKIE['tutor_id'];
    }
    else {
        $tutor_id = '';
    }

    if (isset($_POST['submit'])) {
        // Get the data from form    
        $email          = $_POST['email'];
        $email          = filter_var($email, FILTER_SANITIZE_STRING);
        
        $pass           = sha1($_POST['pass']);
        $pass           = filter_var($pass, FILTER_SANITIZE_STRING);
        
        $verify_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email=? AND password =? LIMIT 1");
        $verify_tutor->execute([$email, $pass]);
        $row = $verify_tutor->fetch(PDO::FETCH_ASSOC);
        
            if ($verify_tutor->rowCount() > 0) {
                    setcookie('tutor_id', $row['id'], time() + 60*60*24*30,'/');
                    header("Location: dashboard.php");
            }else{
                $message[] = 'incorrect email or password!';
            }         
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body style="padding-left:0;">


<?php
if (isset($message)) {
    foreach($message as $msg){
        echo '
            <div class="message form">
                <span>'.$msg.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
        
        ';
    }
}


?>


<!-- login section start -->
<section class="form-container">
    
    <form action="" class="login" method="post" enctype="multipart/form-data">
        <h3>welcome admin!</h3>
                <p>الايميل <span>*</span></p>
                <input type="email" name="email" maxlength="50" required
                placeholder="الايميل" class="box">

                <p>كلمة السر <span>*</span></p>
                    <input type="password" name="pass" maxlength="20" required
                    placeholder="كلمة السر" class="box">
                
                <input type="submit" name="submit" value="تسجيل" class="btn">
                <!-- <p class="link">don't have an account? <a href="register.php">register now</a></p> -->
    </form>
</section>
<!-- login section end -->











<script src="../js/admin_script.js"></script>
</body>
</html>