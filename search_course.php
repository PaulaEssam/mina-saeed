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
    <title>Playlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->

<!-- courses section start -->
<section class="courses">
    <h1 class="heading">search results</h1>
    <div class="box-container">
        <?php
            if (isset($_POST['search_btn']) || isset($_POST['search_box']) ) {
                $search_box = $_POST['search_box'];
                $search_box     = filter_var($search_box, FILTER_SANITIZE_STRING);

                $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE '%{$search_box}%' AND status = ? ORDER BY date DESC");
                $select_courses->execute(['active']);

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
                                <a href="playlist.php?get_id=<?=$course_id?>" class="inline-btn">view course</a>
                            </div>
            
        <?php
                    }
                    else{
                        echo '<p class="empty">☝️ search something! ☝️</p>';
                    } 
                }
                } else{
                    echo "<p class='empty'>courses not found!</p>";
                }
            }
            else{
                echo '<p class="empty">☝️ search something! ☝️</p>';
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