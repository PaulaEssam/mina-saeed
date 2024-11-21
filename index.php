<?php
session_start();

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
}

$count_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$count_likes ->execute([$user_id]);
$total_likes = $count_likes->rowCount();

$count_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$count_comments ->execute([$user_id]);
$total_comments = $count_comments->rowCount();

$count_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$count_bookmark ->execute([$user_id]);
$total_bookmark = $count_bookmark->rowCount();

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة الرئيسية</title>
    <link rel="icon" href="imgs/m1.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .view:hover{
            text-decoration: none;
        }
    
    </style>
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- quick select section start -->
<section class="quick-select">
    <h1 class="heading" style="color:var(--orange);">الرياضيات مفتاح الابداع </h1>
    <div class="box-container">
        
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="imgs/m1.jpeg" alt="First slide" >
                </div>
            <?php
                $select_image = $conn->prepare("SELECT * FROM `slider`");
                $select_image->execute();  
            
                    if ($select_image->rowCount() > 0) {
                    while($fetch_image = $select_image->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <div class="carousel-item">
                    <img class="d-block w-100" src="uploaded_files/<?=$fetch_image['img']?>" alt="First slide">
                </div>
                <?php 
            }} ?>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</section>
<!-- quick select section end -->

<!-- courses section start -->
<section class="courses">
    <h1 class="heading" >احدث الكورسات </h1>
    <div class="box-container">
        <?php
            $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC LIMIT 6");
            $select_courses->execute(['active']);  
        
            if ($select_courses->rowCount() > 0) {
                $check = false;
                while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)) {
                    $course_id   = $fetch_course['id'];
                    $course_year = $fetch_course['year']; 

                    $count_course = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ? AND status = ?");
                    $count_course->execute([$course_id, 'active']);
                    $total_courses = $count_course->rowCount();

                    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                    $select_tutor->execute([$fetch_course['tutor_id']]);
                    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
                    if(isset($fetch_profile) && $fetch_profile['year'] == $course_year) {
                        $check = true ;
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
                                <a href="playlist.php?get_id=<?=$course_id?>" class="inline-btn view">عرض الكورس </a>
                    </div>
        
        <?php
                    }
                    // else{
                    //     $check = false;
                    // }  
                }
            } 
            else{
                // echo "<p class='empty'>no courses added yet!</p>";
                echo "<p class='empty'>لا توجد كورسات !</p>";
                // $check = false;
            }  
        ?>
    </div>
    <?php
        if ($user_id == '' && $select_courses->rowCount() > 0) {
            echo "<p class='empty'>ان كنت مشترك قم بتسجيل الدخول او اشترك بالمنصة وقم بانشاء حساب!</p>" ;

            echo' <div class="flex-btn">
                <a style="font-size: 14px; font-weight: bold;" href="login.php" class="option-btn">تسجيل دخول</a>
                <a style="font-size: 14px; font-weight: bold;" href="register.php" class="option-btn">انشاء حساب </a>
            </div>';
        }
        if($select_courses->rowCount() > 0 && !$check && $user_id){
    ?>
            <p class='empty'>لا توجد كورسات لهذه المرحلة !</p>
    
    <?php
        }
        elseif($select_courses->rowCount() > 0 && $check){
            echo '  <div style="margin-top: 2rem; text-align:center;">
                        <a href="courses.php" class="inline-option-btn view">عرض الكل </a>
                    </div>';
        }
    ?>
</section>
<!-- courses section end -->


<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script src="js/popper.min.js"></script>
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/bootstrap.js"></script>
    
    <script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>
</body>
</html>