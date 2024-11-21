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
    header("Location: teachers.php");
}
$check = false;
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
    <title>حساب المستر </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->

<!-- teachers profile section start -->
<section class="tutor-profile">
    <h1 class="heading">حساب المستر </h1>
    <?php
        $select_tutors = $conn->prepare("SELECT * FROM `tutors` WHERE email = ? LIMIT 1");
        $select_tutors->execute([$get_id]);
        if ($select_tutors->rowCount() > 0) {
            while($fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC)){
                $tutor_id = $fetch_tutor['id'];

                $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
                $count_likes ->execute([$tutor_id]);
                $total_likes = $count_likes->rowCount();

                $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
                $count_comments ->execute([$tutor_id]);
                $total_comments = $count_comments->rowCount();

                $count_content = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
                $count_content ->execute([$tutor_id]);
                $total_content = $count_content->rowCount();

                $count_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
                $count_playlist ->execute([$tutor_id]);
                $total_playlist = $count_playlist->rowCount();
    ?>
        <div class="details">
            <div class="tutor">
                <img src="uploaded_files/<?=$fetch_tutor['image']?>" alt="">
                <div>
                    <h3><?=$fetch_tutor['name']?></h3>
                    <span><?=$fetch_tutor['profession']?></span>
                    <p><?=$fetch_tutor['email']?></p>
                </div>
            </div>
            <div class="flex">
                <p>عدد الوحدات : <span><?=$total_playlist?></span></p>
                <p>عدد الفيديوهات :    <span><?=$total_content?></span></p>
                <p>عدد التفاعلات :     <span><?=$total_likes?></span></p>
                <p>عدد التعليقات :  <span><?=$total_comments?></span></p>
            </div>
    <?php
            }
        }
        else{
            echo '<p class="empty"> لا يوجد مدرسين !</p>';
        }
    ?>
        </div>
</section>


<!-- teachers profile section end -->


<!-- courses section start -->
<section class="courses">
    <h1 class="heading">كورسات المستر </h1>
    <div class="box-container">
        <?php
            $select_tutor_email = $conn->prepare("SELECT * FROM `tutors` WHERE email = ? LIMIT 1");
            $select_tutor_email->execute([$get_id]);
            $fetch_tutor_id = $select_tutor_email->fetch(PDO::FETCH_ASSOC);

            $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE  tutor_id =? AND status = ? ORDER BY date DESC");
            $select_courses->execute([$fetch_tutor_id['id'], 'active']);
            if ($select_courses->rowCount() > 0) {
                while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)) {
                    $course_id = $fetch_course['id'];
                    $course_year = $fetch_course['year']; 

                    $count_course = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
                    $count_course->execute([$course_id]);
                    $total_courses = $count_course->rowCount();

                    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                    $select_tutor->execute([$fetch_course['tutor_id']]);
                    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
                    if(isset($fetch_profile) && $fetch_profile['year'] == $course_year) {
                        $check = true;

        ?>
                    <div class="box">
                        <div class="tutor">
                            <img src="uploaded_files/<?=$fetch_tutor['image']?>" alt="">
                            <div>
                                <h3><?=$fetch_tutor['name']?></h3>
                                <span><?=$fetch_course['date']?></span>
                            </div>
                        </div>
                        
                        <div class="thumb">
                            <span><?=$total_courses?></span>
                            <img src="uploaded_files/<?=$fetch_course['thumb']?>" alt="">
                        </div>
                        <h3 class="title"><?=$fetch_course['title']?></h3>
                        <a href="playlist.php?get_id=<?=$course_id?>" class="inline-btn">عرض الكورس </a>
                    </div>
        
        <?php
                    }
                    else{
                        $check = false;
                    }
                
                }
            } else{
                echo "<p class='empty'>لا يوجد كورسات!</p>";
            }  

            if(!$check){
               
                    echo "<p class='empty'>غير مسموح لك بمشاهدة هذه الكورسات 😊 </p>";
                
            }
        ?>

    </div>

</section>
<!-- courses section end -->













<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->
<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>