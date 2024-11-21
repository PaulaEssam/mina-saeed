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
}else{
    $get_id = '';
    header("Location: exams.php");
    exit();
}


if (isset($_POST['update'])) {

    $question          = $_POST['question'];
    $question          = filter_var($question, FILTER_SANITIZE_STRING);

    $ch_1    = $_POST['ch_1'];
    $ch_1    = filter_var($ch_1, FILTER_SANITIZE_STRING);

    $ch_2    = $_POST['ch_2'];
    $ch_2    = filter_var($ch_2, FILTER_SANITIZE_STRING);

    $ch_3    = $_POST['ch_3'];
    $ch_3    = filter_var($ch_3, FILTER_SANITIZE_STRING);

    $ch_4    = $_POST['ch_4'];
    $ch_4    = filter_var($ch_4, FILTER_SANITIZE_STRING);

    $correct_answer    = $_POST['correct_answer'];
    $correct_answer    = filter_var($correct_answer, FILTER_SANITIZE_STRING);

    $degree    = $_POST['degree'];
    $degree    = filter_var($degree, FILTER_SANITIZE_STRING);

    if (!empty($_FILES['image']['name'])) 
    {
        $image          = $_FILES['image']['name'];
        $image          = filter_var($image, FILTER_SANITIZE_STRING);
        $ext            = pathinfo($image, PATHINFO_EXTENSION);
        $rename         = create_unique_id().'.'.$ext;
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size     = $_FILES['image']['size'];
        $image_folder   = '../uploaded_files/'.$rename;

        $add_question = $conn->prepare("UPDATE `exam_questions` SET exam_question=?, ch_1=?, ch_2=?, ch_3=?, ch_4=?, img=?, correct_answer=? ,degree=? WHERE question_id=?");

        $add_question -> execute([null,$ch_1, $ch_2, $ch_3, $ch_4, $rename, $correct_answer, $degree, $get_id]);
        move_uploaded_file($image_tmp_name, $image_folder);
        $message[] = 'تم تعديل السؤال!';
    }
    else
    {
        $update_qyestion = $conn->prepare("UPDATE `exam_questions` SET exam_question=?, ch_1=?, ch_2=?, ch_3=?, ch_4=?, img=?, correct_answer=?, degree=? WHERE question_id=?");
        $update_qyestion -> execute([$question, $ch_1, $ch_2, $ch_3, $ch_4, null, $correct_answer, $degree, $get_id]);
        $message[] = 'تم تعديل السؤال!';
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل السؤال</title>
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
    <h1 class="heading">تعديل السؤال</h1>
    <?php
    $select_questions = $conn->prepare("SELECT * FROM `exam_questions` WHERE question_id = ?");
    $select_questions->execute([$get_id]);
    if ($select_questions->rowCount() > 0) {
    while($fetch_questions = $select_questions->fetch(PDO::FETCH_ASSOC)){
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <p>عنوان السؤال </p>
        <input type="text" name="question" placeholder="عنوان السؤال" class="box" maxlength="100" value="<?=$fetch_questions['exam_question']?>">

        <p>اختيار A </p>
        <input type="text" name="ch_1" placeholder=" اختيار A " class="box" maxlength="100" value="<?=$fetch_questions['ch_1']?>">

        <p>اختيار B </p>
        <input type="text" name="ch_2" placeholder=" اختيار B " class="box" maxlength="100" value="<?=$fetch_questions['ch_2']?>">

        <p>اختيار C </p>
        <input type="text" name="ch_3" placeholder=" اختيار C " class="box" maxlength="100" value="<?=$fetch_questions['ch_3']?>">

        <p>اختيار D </p>
        <input type="text" name="ch_4" placeholder=" اختيار D " class="box" maxlength="100" value="<?=$fetch_questions['ch_4']?>">
        
        <p>رفع صورة</p>
        <input type="file" name="image" class="box" accept="image/*">
        <?php
            if($fetch_questions['img']){
        ?>
        <img src="../uploaded_files/<?=$fetch_questions['img']?>" alt="<?=$fetch_questions['exam_question']?>" style="width: 100px; height: 100px;">
        
        <?php 
            }
        ?>
        <p>الاجابة الصحيحة </p>
        <input type="text" name="correct_answer" placeholder=" الاجابة الصحيحة " class="box" maxlength="100" value="<?=$fetch_questions['correct_answer']?>">
        
        <p>درجة السؤال</p>
        <input type="number" min="1" name="degree" placeholder="درجة السؤال" class="box" value="<?=$fetch_questions['degree']?>">
        
        <input type="submit" name="update"  value="تعديل السؤال" class="btn">
    </form>
    <?php
        }
    }
    else{
        echo '<p class="empty">لا توجد اسئلة!</p>';
    }
    ?>
</section>


<!-- add contensection end -->



<!-- footer section -->
<?php
include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>