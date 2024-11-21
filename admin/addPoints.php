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
        header("Location: students.php");
    }
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id=?");
    $select_user->execute([$s_id]);
    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['submit'])) {
    
        $addpoint        = $_POST['addpoint'];
        $addpoint        = filter_var($addpoint, FILTER_SANITIZE_STRING);
    
        $editaddpoint = $conn->prepare("UPDATE `users` SET points=? WHERE id=?");

        $editaddpoint->execute([$addpoint, $s_id]);
        $message[] = 'تم اضافة النقاط للطالب  !';
    
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> نقاط الطالب </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>
<!-- add contensection start-->
<section class="crud-form">
    <h1 class="heading">اضافة نقاط</h1>
    <form action="" method="post" enctype="multipart/form-data">

        <p>اضافة نقاط</p>
        <input type="number" name="addpoint"  class="box" min="0" required>
        
        <input type="submit" name="submit"  value="تعديل" class="btn">
    </form>

</section>

<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>