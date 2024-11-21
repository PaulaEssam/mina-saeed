<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
    header("Location: index.php");
}

if (isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_like = $conn->prepare("SELECT * FROM `likes` WHERE user_id=? AND content_id =? ");
    $verify_like->execute([$user_id, $delete_id]);

    if ($verify_like->rowCount() > 0 ) {
        $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id=? AND content_id = ? ");
        $remove_likes->execute([$user_id, $delete_id]);
        $message[] = 'تم الازالة من الاعجاب!';
    }
    else{
        $message[] = 'تمت الازالة من قبل !';
    }
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
    <title>جميع التفاعلات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- courses section start -->
<section class="courses">
    <h1 class="heading">الفيديوهات التي حاظت علي اعجباك</h1>
    <div class="box-container">
        <?php
            $select_likes =  $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? ");
            $select_likes->execute([$user_id]);
            if ($select_likes->rowCount() > 0) {
                while($fetch_likes = $select_likes->fetch(PDO::FETCH_ASSOC)){
                    $select_courses = $conn->prepare("SELECT * FROM `content` WHERE id=? AND status = ? ORDER BY date DESC");
                    $select_courses->execute([$fetch_likes['content_id'], 'active']);
                    if ($select_courses->rowCount() > 0) {
                        while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)) {
                            $course_id = $fetch_course['id'];

                            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                            $select_tutor->execute([$fetch_course['tutor_id']]);
                            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
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
                                    <img src="uploaded_files/<?=$fetch_course['thumb']?>" alt="">
                                </div>
                                <h3 class="title"><?=$fetch_course['title']?></h3>
                                <form action="" method="post" class="flex-btn">
                                    <input type="hidden" name="delete_id" value="<?=$fetch_likes['content_id']?>">
                                    <a href="watchVideo.php?get_id=<?=$course_id?>" class="inline-btn">عرض الفيديو</a>
                                    <input type="submit" name="delete" value="ازالة" class="inline-delete-btn">
                                </form>
                            </div>
                
        <?php
            }
            } else{
                echo "<p class='empty'>لم يتم اضافة كورسات !</p>";
            }
        }
    }
        else{
            echo "<p class='empty'>لم يتم اضافة كورسات الي الاعجابات !</p>";
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