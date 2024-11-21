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
    if (isset($_GET['get_id'])) {
        $get_id = $_GET['get_id'];
    }
    else{
        $get_id = '';
        header("Location: homework.php");
    }
    if (isset($_GET['s_id'])) {
        $s_id = $_GET['s_id'];
    }
    else{
        $s_id = '';
        header("Location: homework.php");
    }

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id=?");
    $select_user->execute([$s_id]);
    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

    $select_homework = $conn->prepare("SELECT * FROM `homework` WHERE id=?");
    $select_homework->execute([$get_id]);
    $fetch_homework = $select_homework->fetch(PDO::FETCH_ASSOC);

    $select_content = $conn->prepare("SELECT * FROM `content` WHERE id=?");
    $select_content->execute([$fetch_homework['content_id']]);
    $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['update'])) {
    
        $degree        = $_POST['degree'];
        $degree        = filter_var($degree, FILTER_SANITIZE_STRING);
    
        $update_degree = $conn->prepare("UPDATE `homework` SET degree=? WHERE user_id=? AND id=?");

        $update_degree->execute([$degree, $s_id, $get_id]);
        $message[] = 'تم تعديل الدرجة !';
    
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الواجب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>
<section class="crud-form">
    <h1 class="heading">تعديل الواجب</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p>اسم الطالب : <span style="color:var(--orange)"><?=$fetch_user['name'];?></span></p>
        <p>عنوان المحتوي : <span style="color:var(--orange)"><?=$fetch_content['title'];?></span></p>
            <p>تعديل الدرجة </p>
            <input type="number" name="degree" value="<?=$fetch_homework['degree'];?>" class="box" min="0" >
            <input type="submit" name="update"  value="تعديل" class="btn">
    </form>
</section>

<!-- footer section -->
<?php
include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>