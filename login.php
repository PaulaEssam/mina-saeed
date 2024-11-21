<?php
session_start();
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
}

// $session_id = session_id();
// $session_id= create_unique_id();
// setcookie('session_id',$session_id,time()+31556926);
//     echo $_COOKIE['session_id'];
##################################################### session id #########################################
// الحصول على معرف المتصفح
$browser_id = md5($_SERVER['HTTP_USER_AGENT']);

// تحقق من وجود ملف لتخزين المعرف
$file_path = 'browser_ids.txt';
if (!file_exists($file_path)) {
    // إذا لم يكن الملف موجود، قم بإنشائه
    file_put_contents($file_path, '');
}

// قراءة المعرفات المخزنة في الملف
$stored_ids = file_get_contents($file_path);
$ids_array = explode("\n", $stored_ids);

// التحقق من وجود المعرف في الملف
if (!in_array($browser_id, $ids_array)) {
    // إذا لم يكن المعرف موجود، قم بإضافته إلى الملف
    file_put_contents($file_path, $browser_id . "\n", FILE_APPEND);
}

// echo "Your unique ID is: " . $browser_id;
################################################################################################################

// echo $check_login->rowCount() ;
if (isset($_POST['submit'])) {
    // Get the data from form    
    $email          = $_POST['email'];
    $email          = filter_var($email, FILTER_SANITIZE_STRING);
    
    $pass           = sha1($_POST['pass']);
    $pass           = filter_var($pass, FILTER_SANITIZE_STRING);
    
    $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email=? AND password =? LIMIT 1");
    $verify_user->execute([$email, $pass]);
    $row = $verify_user->fetch(PDO::FETCH_ASSOC);
    
    // Check if the user is already logged in from another device
    $check_login = $conn->prepare("SELECT * FROM `users` WHERE session_id !=? AND is_logged_in =? AND email=?");
    $check_login->execute([$browser_id,1,$email]);
    $fetch_login = $check_login->fetch(PDO::FETCH_ASSOC);

        if ($verify_user->rowCount() > 0 && $check_login->rowCount() == 0 && $row['status']==1) {

            // update session and logged in for user
            $update_session = $conn->prepare("UPDATE `users` SET session_id =? WHERE email=?");
            $update_session->execute([$browser_id,$email]);

            // update looggin and logged in for user
            $update_loggin = $conn->prepare("UPDATE `users` SET is_logged_in =? WHERE session_id=?");
            $update_loggin->execute([1,$browser_id]);


                setcookie('user_id', $row['id'], time() + 60*60*24*30,'/');
                header("Location: index.php");
        } 
        elseif ($verify_user->rowCount() > 0 && $check_login->rowCount() > 0) {
            $message[] = 'الحساب مسجل علي جهاز من قبل !';
        }
        elseif($verify_user->rowCount() > 0 && $row['status'] == 0){
            $message[] = 'حسابك غير مفعل , برجاء التواصل مع المستر للتفعيل !';
        } 
        else {
            $message[] = 'خطأ في الايميل او كلمة السر !';
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
    <title>تسجيل الدخول </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
        
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- login section start -->
<section class="form-container">
    
    <form action="" class="login" method="post" enctype="multipart/form-data">
        <h3>مرحبا!</h3>
                <p>الايميل <span>*</span></p>
                <input type="email" name="email" maxlength="50" required
                placeholder="الايميل " class="box">

                <p>كلمة السر  <span>*</span></p>
                    <input type="password" name="pass" maxlength="20" required
                    placeholder="كلمة السر" class="box">
                
                <input type="submit" name="submit" value="تسجيل دخول" class="btn">
    </form>
</section>
<!-- login section end -->












<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>