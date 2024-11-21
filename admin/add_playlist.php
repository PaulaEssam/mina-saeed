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
    if (isset($_POST['submit'])) {
        $id             = create_unique_id();
    
        $status         = $_POST['status'];
        $status         = filter_var($status, FILTER_SANITIZE_STRING);
        
        $title          = $_POST['title'];
        $title          = filter_var($title, FILTER_SANITIZE_STRING);
        
        $description    = $_POST['description'];
        $description    = filter_var($description, FILTER_SANITIZE_STRING);

        $thumb          = $_FILES['thumb']['name'];
        $thumb          = filter_var($thumb, FILTER_SANITIZE_STRING);
        
        $ext            = pathinfo($thumb, PATHINFO_EXTENSION);
        $rename         = create_unique_id().'.'.$ext;
        $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
        $thumb_size     = $_FILES['thumb']['size'];
        $thumb_folder   = '../uploaded_files/'.$rename;

        $year        = $_POST['year'];
        $year        = filter_var($year, FILTER_SANITIZE_STRING);

        $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id =? AND title =? AND description =? ");
        $verify_playlist -> execute([$tutor_id, $title, $description]);

        if ($verify_playlist->rowCount() > 0) {
            $message[] = 'الوحدة موجودة بالفعل !';
        }
        else{
            $add_playlist = $conn->prepare("INSERT INTO `playlist` (id, tutor_id, title, description, thumb, year, status)
                                            VALUES (?,?,?,?,?,?,?)");
            $add_playlist -> execute([$id, $tutor_id, $title, $description, $rename, $year, $status]);            
            move_uploaded_file($thumb_tmp_name, $thumb_folder);
            $message[] = 'تم اضافة الوحدة بنجاح !';
            
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضافة وحدة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>


<!-- add playlist section start -->
<section class="crud-form">
    <h1 class="heading">اضافة وحدة</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <p>الحالة <span>*</span></p>
        <select name="status" class="box" required>
            <option value="active">مفعل</option>
            <option value="deactive">غير مفعل</option>
        </select>
        
        <p>عنوان الوحدة <span>*</span></p>
        <input type="text" name="title" placeholder="عنوان الوحدة" class="box" maxlength="100" required>
        
        <p>وصف الوحدة</p>
        <textarea name="description" placeholder="وصف الوحدة" class="box" maxlength="1000" cols="30" rows="10"></textarea>
    
        <p>صورة الوحدة<span>*</span></p>
        <input type="file" name="thumb"  class="box" accept="image/*" required>
        
        <p>السنة الدراسية<span>*</span></p>
        <select name="year" class="box" required>
            <option value="1" >الصف الاول</option>
            <option value="2" >الصف الثاني</option>
            <option value="3" >الصف الثالث</option>
        </select>

        <input type="submit" name="submit"  value="اضافة وحدة" class="btn">
    
    </form>
</section>
<!-- add playlist section end -->









<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>