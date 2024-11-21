<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
    header("Location: index.php");
}
if (isset($_POST['submit'])) {

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id =? LIMIT 1");
    $select_user->execute([$user_id]);
    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

    $prev_pass      = $fetch_user['password'];
    $prev_image     = $fetch_user['image'];
    
    $name           = $_POST['name'];
    $name           = filter_var($name, FILTER_SANITIZE_STRING);
    
    $email          = $_POST['email'];
    $email          = filter_var($email, FILTER_SANITIZE_STRING);
    
    $governorate     = $_POST['governorate'];
    $governorate     = filter_var($governorate, FILTER_SANITIZE_STRING);

    if (!empty($name)) {
        $update_name = $conn->prepare("UPDATE `users` SET name=? WHERE id=?");
        $update_name->execute([$name, $user_id]);
        $message[] = 'تم تعديل الاسم !';
    }

    if (!empty($email)) {
        $select_user_email = $conn->prepare("SELECT * FROM `users` WHERE email=?");
        $select_user_email->execute([$email]);
        if ($select_user_email->rowCount() > 0) {
            $message[] = 'الايميل مأخوذ بالفعل !';
        }else{
            $update_email = $conn->prepare("UPDATE `users` SET email=? WHERE id=?");
            $update_email->execute([$email, $user_id]);
            $message[] = 'تم تعديل الايميل !';
        }
        
    }
    
  if (!empty($governorate)) {
        $update_governorate = $conn->prepare("UPDATE `users` SET governorate=? WHERE id=?");
        $update_governorate->execute([$governorate, $user_id]);
        $message[] = 'تم تعديل المحافظة !';
    }
    
    $image          = $_FILES['image']['name'];
    $image          = filter_var($image, FILTER_SANITIZE_STRING);
    
    $ext            = pathinfo($image, PATHINFO_EXTENSION);
    $rename         = create_unique_id().'.'.$ext;
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size     = $_FILES['image']['size'];
    $image_folder   = 'uploaded_files/'.$rename;

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = 'برجاء اختيار صورة اصغر حجما !';
        }else{
            $update_image = $conn->prepare("UPDATE `users` SET `image`=? WHERE id=?");
            $update_image->execute([$rename, $user_id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            if ($prev_image != '' && $prev_image != $rename) {
                unlink('uploaded_files/'.$prev_image);
            }
            $message[] = 'تم تعديل الصورة !';
        }
    }

    $empty_pass     = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; // كود التفشير بتاعة الاسترنج الفاضي
    $old_pass       = sha1($_POST['old_pass']);
    $old_pass       = filter_var($old_pass, FILTER_SANITIZE_STRING);
    
    $new_pass       = sha1($_POST['new_pass']);
    $new_pass       = filter_var($new_pass, FILTER_SANITIZE_STRING);
    
    $c_pass         = sha1($_POST['c_pass']);
    $c_pass         = filter_var($c_pass, FILTER_SANITIZE_STRING);
    
    if ($old_pass != $empty_pass) {
        if ($old_pass != $prev_pass) {
            $message[] = "كلمة السر القديمة غير متطابقة!";
        }elseif ($new_pass != $c_pass) {
            $message[] = "كلمة السر الجديدة غير متطابقة !";
        }else {
            if ($new_pass != $empty_pass) {
                $update_pass = $conn->prepare("UPDATE `users` SET password=? WHERE id=?");
                $update_pass->execute([$c_pass, $user_id]);
                $message[] = 'تم تعديل كلمة السر !';
            }else{
                $message[] = 'برجاء ادخال كلمة سر !';
            }
        }
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
    <title>تعديل </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->

<!-- update section start -->
<section class="form-container">   
    <form action="" method="post" enctype="multipart/form-data">
        <h3>تعديل البروفايل </h3>
        <div class="flex">
            <div class="col">
                <p>الاسم </p>
                <input type="text" name="name" maxlength="50" 
                placeholder="<?=$fetch_profile['name'];?>" class="box">

                <p>الايميل</p>
                <input type="email" name="email" maxlength="50" 
                placeholder="<?=$fetch_profile['email'];?>" class="box">
            </div>

            <div class="col">
            <p>كلمة السر القديمة  </p>
                <input type="password" name="old_pass" maxlength="20" 
                placeholder="كلمة السر القديمة " class="box">

                <p>كلمة السر الجديدة  </p>
                <input type="password" name="new_pass" maxlength="20" 
                placeholder="كلمة السر الجديدة " class="box">

                <p>تأكيد كلمة السر  </p>
                <input type="password" name="c_pass" maxlength="20" 
                placeholder="تأكيد كلمة السر " class="box">

            </div>
        </div>
        <p>المحافظة</p>
                <select name="governorate"  class="box">
                    <option value="<?=$fetch_profile['governorate'];?>" selected><?=$fetch_profile['governorate'];?></option>
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
        <p>تعديل الصورة  </p>
        <input type="file" name="image" class="box" accept="image/*">
        <input type="submit" name="submit" value="تعديل" class="btn">
    </form>
</section>     
<!-- update section end -->

<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>