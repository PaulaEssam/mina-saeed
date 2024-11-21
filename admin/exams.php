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
if (isset($_POST['delete_exam'])) {
    $delete_id = $_POST['exam_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
    $verify_exam->execute([$delete_id]);

    if ($verify_exam->rowCount() > 0) {
        $delete_exam = $conn->prepare("DELETE FROM `exam_name` WHERE exam_id = ?");
        $delete_exam->execute([$delete_id]);
        
        $delete_Q = $conn->prepare("DELETE FROM `exam_questions` WHERE exam_id = ?");
        $delete_Q->execute([$delete_id]);

        $message[] = 'تم حذف الامتحان!';
    }
    else{
        $message[] = 'الامتحان محذوف بالفعل!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الامتحانات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<!--header section -->
<?php
include '../components/admin_header.php';
?>


<!-- comments section start -->
<section class="comments">
    <h1 class="heading">الامتحانات</h1>
        <?php
        $i=1;
            $select_exams = $conn->prepare("SELECT * FROM `exam_name` WHERE tutor_id = ? ORDER BY exam_id DESC");
            $select_exams->execute([$tutor_id]);

            if ($select_exams->rowCount() > 0) {
                while($fetch_exam = $select_exams->fetch(PDO::FETCH_ASSOC)){
                    $exam_id = $fetch_exam['exam_id'];
                    if ($fetch_exam['year'] == 1){
                        $year = 'الصف الاول';
                    } elseif ($fetch_exam['year'] == 2){
                        $year = 'الصف الثاني';
                    }elseif ($fetch_exam['year'] == 3){
                        $year = 'الصف الثالث';
                    }

                    $playlist_id = $fetch_exam['playlist_id'];
                    $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
                    $select_playlist->execute([$playlist_id]);
                    $fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);
        ?>
    <div class="box-container">
                <div class="box">
                    <div class="comment-content">
                        <p>
                            <span style="color: #f39c12">#<?= $i;?> : </span>
                            <?= $fetch_exam['exam_title'];?>
                        </p>
                        <div>
                            <i class="fas fa-circle-dot" style="color:<?php if($fetch_exam['status'] == 'active'){echo'limegreen';}else{echo'red';}?>"></i>
                            <span style="color:<?php if($fetch_exam['status'] == 'active'){echo'limegreen';}else{echo'red';}?>"><?= $fetch_exam['status'];?></span>
                        </div>
                    </div>
                    <div class="user">
                        <div>
                            <span>وصف الامتحان: </span>
                            <h3><?=$fetch_exam['exam_description'];?></h3><br>
                            
                            <span>السنة الدراسية >> <span style="color:#f39c12;"><?=$year;?></span></span><br><br>
                            <span>وقت الامتحان >></span>
                            <span style="color: #f39c12; font-weight: bold ">  <?=($fetch_exam['exam_time'] * 50) / 60;?></span>
                            <span> دقيقة</span>
                        </div>
                    </div>
                    <p class="comment-box"> <span style="color: #f39c12; font-weight: bold"> الوحدة: </span><?= $fetch_playlist['title'];?></p>
                    <form action="" method="post" class="flex-btn" style="width: 55%">
                        <input type="hidden" name="exam_id" value="<?= $exam_id;?>">
                        <a href="add_questions.php?get_id=<?=$exam_id?>" class="btn">اضافة سؤال </a>
                        <a href="update_exam.php?get_id=<?=$exam_id?>" class="option-btn">تعديل الامتحان</a>
                        <input type="submit" name="delete_exam" value="حذف الامتحان" class="delete-btn" onclick="return confirm('delete this exam?');">
                    </form>
                </div>
    </div>

                <br>
                <?php
                $i++;
            }
        }
        else{
            echo '<p class="empty">لم يتم اضافة امتحانات !</p>';
        }
        ?>

</section>

<!-- comments section end -->










<!-- footer section -->
<?php
include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>