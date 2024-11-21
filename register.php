<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
}

if (isset($_POST['submit'])) {
    // Get the data from form
    $id             = create_unique_id();

    $name           = $_POST['name'];
    $name           = filter_var($name, FILTER_SANITIZE_STRING);
    
    $email          = $_POST['email'];
    $email          = filter_var($email, FILTER_SANITIZE_STRING);

    $number          = $_POST['number'];
    $number          = filter_var($number, FILTER_SANITIZE_STRING);

    $parent_num      = $_POST['parent_num'];
    $parent_num      = filter_var($parent_num, FILTER_SANITIZE_STRING);

    $gender          = $_POST['gender'];
    $gender          = filter_var($gender, FILTER_SANITIZE_STRING);

    $governorate     = $_POST['governorate'];
    $governorate     = filter_var($governorate, FILTER_SANITIZE_STRING);
    
    $register          = $_POST['register'];
    $register          = filter_var($register, FILTER_SANITIZE_STRING);
    
    $pass           = sha1($_POST['pass']);
    $pass           = filter_var($pass, FILTER_SANITIZE_STRING);
    
    $c_pass         = sha1($_POST['c_pass']);
    $c_pass         = filter_var($c_pass, FILTER_SANITIZE_STRING);

    $year         = $_POST['year'];
    $year         = filter_var($year, FILTER_SANITIZE_STRING);

    $image          = $_FILES['image']['name'];
    $image          = filter_var($image, FILTER_SANITIZE_STRING);
    
    $ext            = pathinfo($image, PATHINFO_EXTENSION);
    if($image){
        $rename         = create_unique_id().'.'.$ext;
    }
    else{
        $rename         = '';
    }
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size     = $_FILES['image']['size'];
    $image_folder   = 'uploaded_files/'.$rename;
    
    // select from database************
    $select_user_email = $conn->prepare("SELECT * FROM `users` WHERE email=?");
    $select_user_email->execute([$email]);
    if ($select_user_email->rowCount() > 0) {
        $message[] = 'الايميل مأخوذ بالفعل !';
    }
    else{
        if ($pass != $c_pass) {
            $message[] = 'كلمة السر غير متطابقة!'; 
        }
        else{
            if ($image_size > 2000000) {
                $message[] = "برجاء اختيار صورة اصغر حجما ";
            }else{
                // insert tutor into database
                $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, number, parent_num, gender, governorate, register, password, image, year)
                VALUE(?,?,?,?,?,?,?,?,?,?,?)");
                $insert_user->execute([$id, $name, $email, $number, $parent_num, $gender, $governorate, $register, $c_pass,$rename, $year]);
                move_uploaded_file($image_tmp_name, $image_folder);
            
                $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email=? AND password =? LIMIT 1");
                $verify_user->execute([$email,$c_pass]);
                $row = $verify_user->fetch(PDO::FETCH_ASSOC);
                if($insert_user){
                    if ($verify_user->rowCount() > 0) {
                            //setcookie('user_id', $row['id'], time() + 60*60*24*30,'/');
                            header("Location: login.php");
                    }else{
                        $message[] = 'حدث شئ ما خاطئ !';

                    }
                }
            
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
    <title>انشاء حساب </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->

<!-- register section start -->
<section class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>انشاء حساب ك طالب </h3>
        <div class="flex">
            <div class="col">
                <p>الاسم <span>*</span></p>
                <input type="text" name="name" maxlength="50" required
                placeholder="اسمك رباعي " class="box">

                <p>الايميل  <span>*</span></p>
                <input type="email" name="email" maxlength="50" required
                placeholder="الايميل المسجل به علي اليوتيوب بتاعك" class="box">
                
                <p>رقم الهاتف <span>*</span></p>
                <input type="number" name="number" required
                placeholder="رقم الهاتف" class="box" min="0">
                
                <p>رقم هاتف ولي الامر <span>*</span></p>
                <input type="number" name="parent_num" required
                placeholder="رقم هاتف ولي الامر" class="box" min="0">

                <p>النوع <span>*</span></p>
                <select name="gender" required class="box">
                    <option value="">النوع</option>
                    <option value="male">ذكر</option>
                    <option value="female">انثي</option>
                </select>

                <p>المحافظة <span>*</span></p>
                <select name="governorate" required class="box">
                    <option value="">اختار المحافظة...</option>
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

                <p>نوع القيد بالمنصة <span>*</span></p>
                <select name="register" required class="box">
                    <option value="">منصة ولا سنتر ؟</option>
                    <option value="منصة فقط">منصة فقط</option>
                    <option value="سنتر + منصة">سنتر + منصة</option>
                </select>
            </div>

            <div class="col">
                <p>كلمة السر <span>*</span></p>
                    <input type="password" name="pass" maxlength="20" required
                    placeholder="كلمة السر" class="box">
                
                <p>تأكيد كلمة السر <span>*</span></p>
                    <input type="password" name="c_pass" maxlength="20" required
                    placeholder="تأكيد كلمة السر" class="box">
                
            </div>

            <div class="col">
                <p>السنة الدراسية <span>*</span></p>
                    <select name="year" class="box" required>
                        <option value="1">الصف الاول الاعدادي</option>
                        <option value="2">الصف الثاني الاعدادي</option>
                        <option value="3">الصف الثالث الاعدادي</option>
                    </select>
            </div>
        </div>
        <p>اختار صورة</p>
        <input type="file" name="image" class="box" accept="image/*">
        <input type="submit" name="submit" value="انشاء حساب" class="btn">
    </form>
</section>
<!-- register section end -->
 
<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>
