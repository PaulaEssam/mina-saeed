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
    if (isset($_GET['get_id'])) {
        $get_id = $_GET['get_id'];
    }else{
        $get_id = '';
        header("Location: playlists.php");
        exit();
    }

    if (isset($_POST['delete_content'])) {
        $delete_id = $_POST['content_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_content = $conn->prepare("SELECT * FROM `content` WHERE id=?");
        $verify_content->execute([$delete_id]);
        if($verify_content->rowCount() > 0){
            $fetch_content = $verify_content->fetch(PDO::FETCH_ASSOC);
            unlink('../uploaded_files/' . $fetch_content['thumb']);
            unlink('../uploaded_files/' . $fetch_content['video']);
            $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE content_id= ? ");
            $delete_comment->execute([$delete_id]);
            
            $delete_like = $conn->prepare("DELETE FROM `likes` WHERE content_id= ? ");
            $delete_like->execute([$delete_id]);

            $delete_content = $conn->prepare("DELETE FROM `content` WHERE id= ? ");
            $delete_content->execute([$delete_id]);
            header("Location: contents.php");
        }else{
        $message[] = 'المحتوي محذوف بالفعل !';
        }
    }

    if (isset($_POST['delete_comment'])) {
        $delete_id = $_POST['comment_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
        $verify_comment->execute([$delete_id]);

        if ($verify_comment->rowCount() > 0) {
            $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
            $delete_comment->execute([$delete_id]);
            $message[] = 'تم حذف التعليق بنجاح !';
        }
        else{
            $message[] = ' التعليق محذوف بالفعل !';
        }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المحتوي </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>

<!-- view content section start -->
<section class="view-content">
    <?php
        $select_content = $conn->prepare("SELECT * FROM `content` WHERE id=?");
        $select_content->execute([$get_id]);
        if($select_content->rowCount()>0){
        while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $content_id = $fetch_content['id'];
            $pdf = $fetch_content['pdf'];

            $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ? AND content_id=?");
            $count_likes->execute([$tutor_id, $content_id]);
            $total_likes = $count_likes->rowCount();
            
            $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ? AND content_id=?");
            $count_comments->execute([$tutor_id, $content_id]);
            $total_comments = $count_comments->rowCount();
    ?>

    <div class="content">
        <?php if($fetch_content['video']) {?>
            <a href="<?=$fetch_content['video'];?>"><img src="../uploaded_files/<?=$fetch_content['thumb'];?>"></a>
        <?php } elseif( $fetch_content['audio']) {?>
            <audio controls>
                <source src="../uploaded_files/<?=$fetch_content['audio'];?>" type="audio/mpeg">
                <source src="../uploaded_files/<?=$fetch_content['audio'];?>" type="audio/ogg">
                <source src="../uploaded_files/<?=$fetch_content['audio'];?>" type="audio/wav">
            </audio>
        <?php }?>    
        <div class="date">
            <i class="fas fa-calendar"><span><?=$fetch_content['date'];?></span></i>
        </div>

        <h3 class="title"><?=$fetch_content['title'];?></h3>

        <div class="flex">
            <div>
                <i class="fas fa-heart"><span><?=$total_likes;?></span></i>    
            </div>
            <div>
                <i class="fas fa-comment"><span><?=$total_comments;?></span></i>    
            </div>
        </div>

        <p class="description"><?=$fetch_content['description'];?></p>

        <form action="" method="post" class="flex-btn">
            <input type="hidden" name="content_id" value="<?=$content_id;?>">
            <input type="submit" name="delete_content" value="حذف المحتوي " class="delete-btn">
            <a href="update_content.php?get_id=<?=$content_id;?>" class="option-btn">تعديل المحتوي </a>
        </form>
    </div>
    <?php
            }
        }else{
            echo '<p class="empty">المحتوي غير موجود !</p>';
        }
    ?>
</section>
<!-- view content section end -->

<section class="comment-form">
    <h1 class="heading">المرفقات </h1>
    <?php 
            if($pdf){
        ?>
    <!-- <embed src="../uploaded_files/<?=$pdf;?>"  width="100%" height="500px" /> -->
    <iframe src="../uploaded_files/<?=$pdf;?>"  width="100%" height="500px" frameborder="0"></iframe>

    <?php
            }
            else{
                echo '<p class="empty">لا يوجد مرفقات !</p>';
            }
            ?>
</section>

<!-- comments section start -->
<section class="comments">
    <h1 class="heading">تعليقات الطلاب </h1>
    <div class="box-container">
        <?php
            $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id=? AND tutor_id = ?");
            $select_comments->execute([$get_id, $tutor_id]);
            if ($select_comments->rowCount() > 0) {
                while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){
                    $comment_id = $fetch_comment['id'];
                    $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id=?");
                    $select_commentor->execute([$fetch_comment['user_id']]);
                    $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="box">
                <div class="user">
                    <!-- <img src="../uploaded_files/<?=$fetch_commentor['image'];?>" alt=""> -->
                    <div>
                        <h3><?=$fetch_commentor['name'];?></h3>
                        <span><?=$fetch_comment['date'];?></span>
                    </div>
                </div>
                <p class="comment-box"><?= $fetch_comment['comment'];?></p>
                <form action="" method="post">
                    <input type="hidden" name="comment_id" value="<?= $fetch_comment['id'];?>">
                    <input type="submit" name="delete_comment" value="حذف التعليق" class="inline-delete-btn" onclick="return confirm('delete this comment?');">
                </form>
            </div>
            <?php
                }
            }   
            else{
                echo '<p class="empty">لا توجد تعليقات !</p>';
            }
        ?>
    </div>
</section>

<!-- comments section end -->






<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>