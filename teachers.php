<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
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
    <title>المدرسين</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->

<!-- teachers section start -->
<section class="teachers">
    <h1 class="heading">المدرسين</h1>

    <div class="box-container">
        <?php
            $select_tutors = $conn->prepare("SELECT * FROM `tutors`");
            $select_tutors->execute();
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

        <div class="box">
            <div class="tutor">
                <img src="uploaded_files/<?=$fetch_tutor['image']?>" alt="">
                <div>
                    <h3><?=$fetch_tutor['name']?></h3>
                    <span><?=$fetch_tutor['profession']?></span>
                </div>
            </div>
                <p>عدد الوحدات : <span><?=$total_playlist?></span></p>
                <p>عدد الفيديوهات :    <span><?=$total_content?></span></p>
                <p>عدد التفاعلات :     <span><?=$total_likes?></span></p>
                <p>عدد التعليقات :  <span><?=$total_comments?></span></p>
            <a href="teacherProfile.php?get_id=<?=$fetch_tutor['email']?>"><button  class="inline-btn">عرض البروفايل</button></a>
        </div>

        <?php
            }
        }
        else{
            echo '<p class="empty"> المستر غير موجود !</p>';
        }
    ?>
    </div>
</section>
<!-- teachers section end -->










<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>