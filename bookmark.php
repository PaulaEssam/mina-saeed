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
    if ($user_id != '') {
            $delete_id = $_POST['delete_id'];
            $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

            $verify_list = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
            $verify_list->execute([$user_id, $delete_id]);

            if ($verify_list->rowCount() > 0) {
                $remove_list = $conn->prepare("DELETE FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
                $remove_list->execute([$user_id, $delete_id]);
                $message[] = 'تم ازالة الوحدة!';
            }
            else{
                $message[] = 'تم ازالة الوحدة!';
            }

    }
    else{
        $message[] = 'سجل دخول اولا!';
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
    <title>المحفوظات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- courses section start -->
<section class="courses">
    <h1 class="heading">الوحدات المحفوظة</h1>
    <div class="box-container">
        <?php
            $select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id=?");
            $select_bookmark->execute([$user_id]);
            if ($select_bookmark->rowCount()>0) {
                while($fetch_bookmark=$select_bookmark->fetch(PDO::FETCH_ASSOC)) {
            
                    $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE id=? AND status = ? ORDER BY date DESC");
                    $select_courses->execute([$fetch_bookmark['playlist_id'], 'active']);
                    if ($select_courses->rowCount() > 0) {
                        while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)) {
                            $course_id = $fetch_course['id'];

                            $count_course = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
                            $count_course->execute([$course_id]);
                            $total_courses = $count_course->rowCount();

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
                                    <span><?=$total_courses?></span>
                                    <img src="uploaded_files/<?=$fetch_course['thumb']?>" alt="">
                                </div>
                                <h3 class="title"><?=$fetch_course['title']?></h3>
                                <form action="" method="post" class="flex-btn">
                                    <input type="hidden" name="delete_id" value="<?=$course_id?>">
                                    <a href="playlist.php?get_id=<?=$course_id?>" class="inline-btn">عرض الوحدة</a>
                                    <input type="submit" name="delete" value="ازالة" class="inline-delete-btn" onclick="return confirm('remove from bookmarked?');">
                                </form>
                            </div>
        
        <?php
                            }
                        } else{
                            echo "<p class='empty'>لا توجد كورسات مضافة !</p>";
                        }
                    }
            }
            else{
                echo "<p class='empty'>لا توجد محفوظات !</p>";
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