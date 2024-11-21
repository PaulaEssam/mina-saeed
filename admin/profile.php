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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البروفايل</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>


<!-- profile section start -->
<section class="profile">

    <h1 class="heading">التفاصيل</h1>
    <div class="details">
        
        <div class="tutor">
            <img src="../uploaded_files/<?=$fetch_profile['image'];?>" alt="">
            <h3><?=$fetch_profile['name'];?></h3>
            <p><?=$fetch_profile['profession'];?></p>
            <a href="update.php" class="inline-btn">تعديل البروفايل</a>
        </div>

        <div class="box-container">
            <div class="box">
                <h3><?= $total_contents ?></h3>
                <p>عدد المحتويات</p>
                <a href="contents.php" class="btn">عرض المحتوي</a>
            </div>
            
            <div class="box">
                <h3><?= $total_playlists ?></h3>
                <p>عدد الوحدات </p>
                <a href="playlists.php" class="btn">عرض الوحدات</a>
            </div>

            <div class="box">
                <h3><?= $total_likes ?></h3>
                <p>عدد التفاعلات</p>
                <a href="contents.php" class="btn">عرض</a>
            </div>

            <div class="box">
                <h3><?= $total_comments ?></h3>
                <p>عدد التعليقات</p>
                <a href="comments.php" class="btn">عرض</a>
            </div>

        </div>
    </div>
</section>
<!-- profile section end -->









<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>