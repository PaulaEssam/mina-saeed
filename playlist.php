<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
    header('Location: login.php');
    exit ;
}
if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
}
else{
    $get_id = '';
    header("Location: courses.php");
}
if (isset($_POST['save_list'])) {
    if ($user_id != '') {
            $list_id = $_POST['list_id'];
            $list_id = filter_var($list_id, FILTER_SANITIZE_STRING);

            $verify_list = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
            $verify_list->execute([$user_id, $list_id]);

            if ($verify_list->rowCount() > 0) {
                $remove_list = $conn->prepare("DELETE FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
                $remove_list->execute([$user_id, $list_id]);
                $message[] = 'تم ازلة الوحدة من المحفوظات!';
            }
            else{
                $add_list = $conn->prepare("INSERT INTO `bookmark`  (user_id , playlist_id) VALUES (?, ?)");
                $add_list->execute([$user_id, $list_id]);
                $message[] = 'تم حفظ الوحدة ';
            }

    }
    else{
        $message[] = 'سجل دخول اولا !';
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
    <title>الوحدة </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- view playlist section start -->
<section class="playlist">
    <h1 class="heading">تفاصيل الوحدة </h1>
    <div class="row">
        <?php
        $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND status = ? LIMIT 1");
        $select_playlist->execute([$get_id, 'active']);
        if ($select_playlist->rowCount() > 0) {
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){ 
                $playlist_id = $fetch_playlist['id'];

                    $count_content = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ? AND status = ?");
                    $count_content->execute([$playlist_id, 'active']);
                    $total_content = $count_content->rowCount();

                    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
                    $select_tutor->execute([$fetch_playlist['tutor_id']]);
                    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

                    $select_bookmark =  $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
                    $select_bookmark->execute([$user_id, $playlist_id]);

        ?>
            <div class="col">
                <form action="" method="post" class="save-list">
                    <input type="hidden" name="list_id" value="<?=$playlist_id?>" />
                    <?php if($select_bookmark->rowCount()>0){?>
                        <button type="submit" name="save_list">
                            <i class="far fa-bookmark">
                                <span>محفوظة</span>
                            </i>
                        </button>
                    <?php } else{?>
                        <button type="submit" name="save_list">
                            <i class="far fa-bookmark">
                                <span>حفظ الوحدة</span>
                            </i>
                    </button>
                    <?php }?>
                </form>

                <div class="thumb">
                    <span><?=$total_content;?></span>
                    <img src="uploaded_files/<?=$fetch_playlist['thumb'];?>" alt="">
                </div>
            </div>

            <div class="col">
                <div class="tutor">
                    <img src="uploaded_files/<?=$fetch_tutor['image'];?>" alt="">
                    <div>
                        <h3><?=$fetch_tutor['name'];?></h3>
                        <span><?=$fetch_tutor['profession'];?></span>
                    </div>
                </div>

                <div class="details">
                    <h3><?=$fetch_playlist['title'];?></h3>
                    <p><?=$fetch_playlist['description'];?></p>
                    <div class="date">
                        <i class="fas fa-calendar"></i>
                        <span><?=$fetch_playlist['date'];?></span>    
                    </div>
                </div>
            </div>
        <?php
            }
        }else{
            echo '<p class="empty">الوحدة غير موجودة !</p>';
            }
        ?>
    </div>
</section>
<!-- view playlist section end -->



<!-- videos section start -->
<section class="video-container">
    <h1 class="heading">حصص الوحدة </h1>
    <div class="box-container">
        <?php
            $select_content = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ? AND status = ? ORDER BY title asc");
            $select_content->execute([$get_id, 'active']);
            if ($select_content->rowCount() > 0) {
                while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){ 
                
        ?>
        <a href="watchVideo.php?get_id=<?=$fetch_content['id'];?>" class="box">
            <i class="fas fa-play"></i>
            <img src="uploaded_files/<?=$fetch_content['thumb'];?>" alt="">
            <h3><?=$fetch_content['title'];?></h3>
        </a>
                
        <?php
            
                }
            }else{
                echo '<p class="empty">لا يوجد محتوي !</p>';
            }
        ?>
    </div>
    <?php
        $select_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE playlist_id=? AND status=? order by exam_title asc");
        $select_exam->execute([$playlist_id, 'active' ]);
        if ($select_exam->rowCount() > 0) {
            while($fetch_exam = $select_exam->fetch(PDO::FETCH_ASSOC)){
                $select_result = $conn->prepare("SELECT * FROM `exam_results` WHERE user_id = ? AND exam_id=?");
                $select_result->execute([$user_id, $fetch_exam['exam_id']]);
            
                $exam_year  = $fetch_exam['year'];
                if ($exam_year == $fetch_profile['year']){
    ?>

    <div style="margin-top: 2rem; text-align:center;">
        <?php if ($select_result->rowCount() > 0) {?>
            <a href="exam_result.php?get_id=<?=$fetch_exam['exam_id'];?>" class="inline-btn"><?= $fetch_exam['exam_title'].' (مأخوذ)'?></a>

        <?php } else {?>
            <a href="cookie.php?get_id=<?=$fetch_exam['exam_id'];?>" class="inline-option-btn" onclick=" return confirm('هل انت متأكد من دخول الامتحان ؟')"><?= $fetch_exam['exam_title'].' (جديد)'?></a>
            <!-- <a href="exam.php?get_id=<?=$fetch_exam['exam_id'];?>" class="inline-option-btn" onclick=" return confirm('are you sure')"><?= $fetch_exam['exam_title'].' (exam)'?></a> -->

        <?php }?>
    </div>
    <?php
                }
            }
        } 
    ?>
</section>
<!-- videos section end -->





<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>