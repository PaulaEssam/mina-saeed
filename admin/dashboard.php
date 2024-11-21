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

    $count_content = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
    $count_content->execute([$tutor_id]);
    $total_contents = $count_content->rowCount();
    
    $count_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
    $count_playlist->execute([$tutor_id]);
    $total_playlists = $count_playlist->rowCount();
    
    $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
    $count_likes->execute([$tutor_id]);
    $total_likes = $count_likes->rowCount();
    
    $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
    $count_comments->execute([$tutor_id]);
    $total_comments = $count_comments->rowCount();

    $count_exams = $conn->prepare("SELECT * FROM `exam_name` WHERE tutor_id = ?");
    $count_exams->execute([$tutor_id]);
    $total_exams = $count_exams->rowCount();

    $count_males = $conn->prepare("SELECT * FROM `users` WHERE gender = ?");
    $count_males->execute(['male']);
    $total_males = $count_males->rowCount();

    $count_females = $conn->prepare("SELECT * FROM `users` WHERE gender = ?");
    $count_females->execute(['female']);
    $total_females = $count_females->rowCount();

    $taken_exams = $conn->prepare("SELECT * FROM `exam_results`");
    $taken_exams->execute();
    $taken_exams = $taken_exams->rowCount();

    $home_work = $conn->prepare("SELECT * FROM `homework`");
    $home_work->execute();
    $home_work = $home_work->rowCount();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>

<!--dashboard section start-->
<section class="dashboard">
    <h1 class="heading">لوحة التحكم</h1>
    <div class="box-container">
        <div class="box">
            <h3>مرحبا!</h3>
            <p><?= $fetch_profile['name'];?></p>
            <a href="profile.php" class="btn">عرض البروفايل </a>
        </div>

        <div class="box">
            <h3><?= $total_contents;?></h3>
            <p>رفع الدروس (المحتوي)</p>
            <a href="add_content.php" class="btn">اضافة محتوي </a>
        </div>

        <div class="box">
            <h3><?= $total_playlists;?></h3>
            <p>رفع الوحدات</p>
            <a href="add_playlist.php" class="btn">اضافة وحدة </a>
        </div>

        <div class="box">
            <h3><?= $total_likes;?></h3>
            <p>جميع التفاعلات</p>
            <a href="contents.php" class="btn">مشاهدة المحتوي</a>
        </div>
        
        <div class="box">
            <h3><?= $total_comments;?></h3>
            <p>جميع التعليقات</p>
            <a href="comments.php" class="btn">مشاهدة التعليقات</a>
        </div>

        <div class="box">
            <h3><?= $total_exams;?></h3>
            <p>اضافة امتحان</p>
            <a href="add_exam.php" class="btn">اضافة</a>
        </div>

        <div class="box">
            <h3><?= $total_males;?></h3>
            <p>الطلاب الذكور</p>
            <a href="students.php?gender=male" class="btn">عرض</a>
        </div>

        <div class="box">
            <h3><?= $total_females;?></h3>
            <p>الطالبات الاناث</p>
            <a href="students.php?gender=female" class="btn">عرض</a>
        </div>
        
        <div class="box">
            <h3><?= $taken_exams;?></h3>
            <p>الامتحانات الممتحنة</p>
            <a href="taken_exams.php" class="btn">عرض</a>
        </div>

        <div class="box">
            <h3><?= $home_work;?></h3>
            <p>الواجبات</p>
            <a href="homework.php" class="btn">عرض</a>
        </div>

        <div class="box">
            <h3>التقارير</h3>
            <p>تقارير الطلاب</p>
            <a href="reports.php" class="btn">عرض</a>
        </div>
        
        
        <!-- <div class="box">
            <h3>Exams</h3>
            <p>add exam</p>
            <a href="exams.php" class="btn">add exam</a>
        </div> -->

        <!-- <div class="box">
            <h3>quick links</h3>
            <p>login | register</p>
            <div class="flex-btn">
                <a href="login.php" class="option-btn">login</a>
                <a href="register.php" class="option-btn">register</a>
            </div>
    </div>  -->

</section>
<!--dashboard section end-->










<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>