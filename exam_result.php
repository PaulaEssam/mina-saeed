<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
}
if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
}
else{
    $get_id = '';
    header("Location: courses.php");
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
    <title>نتيجة الامتحان </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->
<section class="comments">
    <h1 class="heading">نتيجة الامتحان </h1>
    <div class="box-container">
    <?php 
            $exam_result = $conn->prepare("SELECT * FROM `exam_results` WHERE user_id = ? AND exam_id=?");
            $exam_result->execute([$user_id,$get_id]);
            if($exam_result->rowCount() > 0){
                while($fetch_result = $exam_result->fetch(PDO::FETCH_ASSOC)){
                    
                    $score = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ?");
                    $score -> execute([$get_id]);
                    $exam_degree = 0 ;
                    $user_degree = 0 ;
                    while($fetch_res = $score->fetch(PDO::FETCH_ASSOC)){
                        $exam_degree += $fetch_res['degree'];
                    }

                    $exam_name = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
                    $exam_name->execute([$fetch_result['exam_id']]);
                    $fetch_exam = $exam_name->fetch(PDO::FETCH_ASSOC);
                    ?>
    <div class="box">
        <div class="comment-content">
            <p><?=$fetch_exam['exam_title']?></p>
        </div>
        <div class="user">
            <div>
                <h3>النتيجة</h3>
            </div>
        </div>         
        <?php if($fetch_result['show_degree']){?>
        <p class="comment-box">درجتك : <?=$fetch_result['degree'] ." / " . $exam_degree?></p>
        <br> <br>
        <a href="show_exam.php?examID=<?=$get_id;?>" class="inline-btn">عرض الامتحان</a>

        <?php } else {?>
            <p class="comment-box" style="color: red;">لم يتم تصحيح الامتحان بالكامل !</p>
        <?php }?>
        <br> <br>

    </div>
    <?php }}?>
    </div>
</section>

<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>

