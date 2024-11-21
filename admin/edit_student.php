<?php
include '../components/connect.php';
    if (isset($_COOKIE['tutor_id'])) {
        $tutor_id = $_COOKIE['tutor_id'];
    }
    else {
        $tutor_id = '';
        header("Location: login.php");
        exit();
    }
    
    if (isset($_GET['s_id'])) {
        $s_id = $_GET['s_id'];
    }
    else{
        $s_id = '';
        header("Location: taken_exams.php");
    }
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id=?");
    $select_user->execute([$s_id]);
    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);


    if (isset($_POST['update'])) {

        $select_user = $conn->prepare("SELECT * FROM `users` WHERE id=?");
        $select_user->execute([$s_id]);
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
        
        $name           = $_POST['name'];
        $name           = filter_var($name, FILTER_SANITIZE_STRING);
        
        $gender         = $_POST['gender'];
        $gender         = filter_var($gender, FILTER_SANITIZE_STRING);
        
        $governorate     = $_POST['governorate'];
        $governorate     = filter_var($governorate, FILTER_SANITIZE_STRING);

        $register          = $_POST['register'];
        $register          = filter_var($register, FILTER_SANITIZE_STRING);

        $email          = $_POST['email'];
        $email          = filter_var($email, FILTER_SANITIZE_STRING);
        
        $number          = $_POST['number'];
        $number          = filter_var($number, FILTER_SANITIZE_STRING);

        $parent_num          = $_POST['parent_num'];
        $parent_num          = filter_var($parent_num, FILTER_SANITIZE_STRING);

        $year          = $_POST['year'];
        $year          = filter_var($year, FILTER_SANITIZE_STRING);

        $empty_pass     = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; // كود التفشير بتاعة الاسترنج الفاضي
        
        $new_pass       = sha1($_POST['password']);
        $new_pass       = filter_var($new_pass, FILTER_SANITIZE_STRING);


        if (!empty($name)) {
            $update_name = $conn->prepare("UPDATE `users` SET name=? WHERE id=?");
            $update_name->execute([$name, $s_id]);
            $message[] = 'تم تحديث الاسم!';
        }
        
        if (!empty($email)) {
            $select_student_email = $conn->prepare("SELECT * FROM `users` WHERE email=?");
            $select_student_email->execute([$email]);
            if ($select_student_email->rowCount() > 0) {
                $message[] = 'الايميل مأخوذ من قبل !';
            }else{
                $update_email = $conn->prepare("UPDATE `users` SET email=? WHERE id=?");
                $update_email->execute([$email, $s_id]);
                $message[] = 'تم تحديث الايميل!';
            }
            
        }

        if (!empty($number)) {
            $update_number = $conn->prepare("UPDATE `users` SET number=? WHERE id=?");
            $update_number->execute([$number, $s_id]);
            $message[] = 'تم تحديث رقم الهاتف!';
        }
        if (!empty($parent_num)) {
            $update_number = $conn->prepare("UPDATE `users` SET parent_num=? WHERE id=?");
            $update_number->execute([$parent_num, $s_id]);
            $message[] = 'تم تحديث رقم ولي الامر !';
        }
        if (!empty($gender)) {
            $update_gender = $conn->prepare("UPDATE `users` SET gender=? WHERE id=?");
            $update_gender->execute([$gender, $s_id]);
            $message[] = 'تم تحديث النوع!';
        }
        if (!empty($governorate)) {
            $update_governorate = $conn->prepare("UPDATE `users` SET governorate=? WHERE id=?");
            $update_governorate->execute([$governorate, $s_id]);
            $message[] = 'تم تحديث المحافظة!';
        }
        if (!empty($register)) {
            $update_register = $conn->prepare("UPDATE `users` SET register=? WHERE id=?");
            $update_register->execute([$register, $s_id]);
            $message[] = 'تم تحديث طريقة الحضور !';
        }

        if(!empty($year)){
            $update_year = $conn->prepare("UPDATE `users` SET year=? WHERE id=?");
            $update_year->execute([$year , $s_id]);
            $message[] = 'تم تحديث السنة الدراسية!';


        }
        if ($new_pass != $empty_pass) {
            $update_pass = $conn->prepare("UPDATE `users` SET password=? WHERE id=?");
            $update_pass->execute([$new_pass, $s_id]);
            $message[] = 'تم تحديث كلمة السر!';
        }else{
            $message[] = 'برجاء كتابة كلمة سر جديدة !';
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الطالب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>
<section class="crud-form">
    <h1 class="heading">تعديل بيانات الطالب</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p>الاسم </p>
        <input type="text" name="name" placeholder="<?=$fetch_user['name'];?>"  class="box">

        <p>الايميل </p>
        <input type="email" name="email" placeholder="<?=$fetch_user['email'];?>" class="box">
        <p>رقم الهاتف </p>
        <input type="number" name="number"  placeholder="<?=$fetch_user['number'];?>" class="box" min="0">
        <p>رقم هاتف ولي الامر </p>
        <input type="number" name="parent_num"  placeholder="<?=$fetch_user['parent_num'];?>" class="box" min="0">
        <p>النوع </p>
        <input type="text" name="gender" placeholder="<?=$fetch_user['gender'];?>" class="box">
        <p>كلمة السر </p>
        <input type="text" name="password" placeholder="Leave it blank if you don't want to update" class="box">
        <p>السنة الدراسية </p>
        <select type="text" name="year"  class="box">
                <option value="<?=$fetch_user['year'];?>" selected><?php if($fetch_user['year'] == 1) echo'الصف الاول'; elseif($fetch_user['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></option>
                <option value="1">الصف الاول الاعدادي</option>
                <option value="2">الصف الثاني الاعدادي</option>
                <option value="3">الصف الثالث الاعدادي</option>
        </select>
        <p>المحافظة </p>
        <select name="governorate" class="box">
            <option value="<?=$fetch_user['governorate']?>"><?=$fetch_user['governorate']?></option>
            <option value="أسوان">أسوان</option>
            <option value="أسيوط">أسيوط</option>
            <option value="الأقصر">الأقصر</option>
            <option value="الإسماعيلية">الإسماعيلية</option>
            <option value="الإسكندرية">الإسكندرية</option>
            <option value="البحر الأحمر">البحر الأحمر</option>
            <option value="البحيرة">البحيرة</option>
            <option value="الجيزة">الجيزة</option>
            <option value="الدقهلية">الدقهلية</option>
            <option value="السويس">السويس</option>
            <option value="الشرقية">الشرقية</option>
            <option value="الغربية">الغربية</option>
            <option value="الفيوم">الفيوم</option>
            <option value="القاهرة">القاهرة</option>
            <option value="القليوبية">القليوبية</option>
            <option value="المنوفية">المنوفية</option>
            <option value="المنيا">المنيا</option>
            <option value="الوادي الجديد">الوادي الجديد</option>
            <option value="بني سويف">بني سويف</option>
            <option value="بورسعيد">بورسعيد</option>
            <option value="جنوب سيناء">جنوب سيناء</option>
            <option value="دمياط">دمياط</option>
            <option value="سوهاج">سوهاج</option>
            <option value="شمال سيناء">شمال سيناء</option>
            <option value="قنا">قنا</option>
            <option value="كفر الشيخ">كفر الشيخ</option>
            <option value="مطروح">مطروح</option>
            <hr> <hr>
            <option value="خارج مصر" style="font-weight: bold; color:red;">خارج مصر</option>
        </select>

        <p>نوع القيد بالمنصة </p>
        <select name="register"  class="box">
            <option value="<?=$fetch_user['register']?>"><?=$fetch_user['register']?></option>
            <option value="منصة فقط">منصة فقط</option>
            <option value="سنتر + منصة">سنتر + منصة</option>
        </select>
        <input type="submit" name="update"  value="تحديث" class="btn">
    </form>
</section>

<!-- footer section -->
<?php
include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>