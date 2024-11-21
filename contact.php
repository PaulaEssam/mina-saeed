<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);

    $msg = $_POST['msg'];
    $msg = filter_var($msg, FILTER_SANITIZE_STRING);

    $verify_contact = $conn->prepare("SELECT * FROM `contact` WHERE name=? AND email=? AND number=? AND message=?");
    $verify_contact->execute([$name, $email, $number, $msg]);
    if ($verify_contact->rowCount() > 0) {
        $message[] = 'تم ارسال الرسالة من قبل !';
    }
    elseif($user_id==''){
        $message[] = 'سجل دخول اولا !';
    }
    else{
            $send_message = $conn->prepare("INSERT INTO `contact` (name,email,number,message) VALUES (?,?,?,?)");
            $send_message->execute([$name,$email,$number,$msg]);
            $message[] = 'تم ارسال الرسالة بنجاح !';
        }
}
############################################active student############################################################
$active_student = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$active_student->execute([$user_id]);
$fetch_student = $active_student->fetch(PDO::FETCH_ASSOC);
    // $days_since_last_login = ($current_timestamp - $last_login_timestamp) / (60 * 60 * 24);
    if($active_student->rowCount()>0){
    $days = (time() - $fetch_student['last_login']) / (60*60*24);
    if ($days > 30 || $fetch_student['status'] == 0){
        $update = $conn->prepare("UPDATE `users` SET `status` =?  WHERE id = ?");
        $update->execute([0,$user_id]);
        $user_id = '';
        header("Location: components/user_logout.php");
    }
}
#######################################################################################################################
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تواصل معنا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
        
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- contact section start -->
<section class="contact">
    <div class="row">
        <!-- contact form -->
        <form action="" method="post">
            <h3>تواصل معنا </h3>
            <input type="text" name="name" class="box" required
            placeholder="الاسم" maxlength="50">
            <input type="email" name="email" class="box" required
            placeholder="الايميل" maxlength="50">
            <input type="number" name="number" class="box" placeholder="رقم الهاتف "
            maxlength="20" min="0" required>  
            <textarea class="box" name="msg" id="" cols="30" rows="10" placeholder="اترك رسالتك..."  maxlength="1000" required></textarea>
            <input type="submit" value="ارسال " class="inline-btn"
            name="submit">
        </form>
        <!-- contact img -->
        <div class="image">
            <img src="imgs/contact-img.png" alt="" />
        </div>
    </div>
    <div class="box-container">
        <div class="box">
            <i class="fas fa-phone"></i>
            <h3>رقم الهاتف</h3>
            <a href="tel:+201555644100">01555644100</a>
        </div>
        
        <div class="box">
            <i class="fa-brands fa-whatsapp"></i>
            <h3>واتساب</h3>
            <a href="https://wa.me/+201555644100">01555644100</a>
        </div>
        
        
        <div class="box">
            <i class="fa-brands fa-whatsapp"></i>
            <h3>واتساب السنتر</h3>
            <a href="https://wa.me/+201095095171">01095095171</a>
        </div>
        
        <!-- <div class="box">
            <i class="fas fa-envelope"></i>
            <h3>الايميل </h3>
            <a href="mailto:answerguide2021@gmail.com">answerguide2021@gmail.com</a>
        </div> -->

        <div class="box">
            <i class="fa-brands fa-facebook"></i>
            <h3>الفيس بوك  </h3>
            <a href="https://www.facebook.com/mena.said.58726">Mina Saeed Habib </a>
        </div>

        <div class="box">
            <i class="fa-brands fa-telegram"></i>
            <h3>قناة التيليجرام</h3>
            <a href="https://t.me/+96GbBV6UNKxhNmQ8">الرياضيات مفتاح الابداع</a>
        </div>
<!--
        <div class="box">
            <i class="fa-brands fa-tiktok"></i>
            <h3>tictok</h3>
            <a href="https://www.tiktok.com/@user729750316?_t=8nzFT32yQ4I&_r=1">mohammed elhetimy</a>
        </div> -->
    </div>
    <br> <br>
    <h1 class="heading"> technical support - الدعم الفني </h1>
    <div class="box-container">
        
        <div class="box">
            <i class="fa-brands fa-whatsapp"></i>
            <h3>مهندس: بولا عصام</h3>
            <a href="https://wa.me/+201014628698">01014628698</a>
        </div>
        
        <div class="box">
            <i class="fa-brands fa-whatsapp"></i>
            <h3>مهندس جورج</h3>
            <a href="https://wa.me/+201098921982">01098921982</a>
        </div>
        
        
    </div>
</section>
<!-- contact section end -->












<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>