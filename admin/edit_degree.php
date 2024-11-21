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
        header("Location: taken_exams.php");
    }
    if (isset($_GET['s_id'])) {
        $s_id = $_GET['s_id'];
    }
    else{
        $s_id = '';
        header("Location: taken_exams.php");
    }
    $select_degree = $conn->prepare("SELECT * FROM `exam_results` WHERE user_id=?  AND exam_id=?");
    $select_degree->execute([$s_id, $get_id]);
    $fetch_degree = $select_degree->fetch(PDO::FETCH_ASSOC);

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id=?");
    $select_user->execute([$s_id]);
    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

    $select_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id=?");
    $select_exam->execute([$get_id]);
    $fetch_exam = $select_exam->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['update'])) {
    
        $degree        = $_POST['degree'];
        $degree        = filter_var($degree, FILTER_SANITIZE_STRING);
    
    
        $update_degree = $conn->prepare("UPDATE `exam_results` SET degree=? WHERE user_id=? AND exam_id=?");

        $update_degree->execute([$degree, $s_id, $get_id]);
        $message[] = 'تم تعديل الدرجة !';
        header("Location: taken_exams.php");
    
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الدرجة </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>
<section class="crud-form">
    <h1 class="heading">تعديل الدرجة </h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p>اسم الطالب : <span style="color:var(--orange)"><?=$fetch_user['name'];?></span></p>
        <p>عنوان الامتحان : <span style="color:var(--orange)"><?=$fetch_exam['exam_title'];?></span></p>
            <p>تعديل الدرجة </p>
            <input type="text" name="degree" value="<?=$fetch_degree['degree'];?>" class="box">
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