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
    header("Location: courses.php");
}

if (isset($_POST['like_content'])) {
    if ($user_id != '' ) {
        $like_id = $_POST['content_id'];
        $like_id = filter_var($like_id, FILTER_SANITIZE_STRING);

        $get_content = $conn->prepare("SELECT * FROM `content` WHERE id =? LIMIT 1");
        $get_content->execute([$like_id]);
        $fetch_get_content = $get_content->fetch(PDO::FETCH_ASSOC);

        $tutor_id = $fetch_get_content['tutor_id'];

        $verify_like = $conn->prepare("SELECT * FROM `likes` WHERE user_id =? AND content_id=?");
        $verify_like->execute([$user_id, $like_id]);

        if ($verify_like->rowCount() > 0 ) {
            $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
            $remove_likes->execute([$user_id, $like_id]);
            $message[] = 'تمت الازلة من التفاعلات!';
        }
        else{
            $add_likes = $conn->prepare("INSERT INTO `likes` (user_id, tutor_id, content_id) VALUES (?,?,?) ");

            $add_likes->execute([$user_id, $tutor_id ,$like_id]);
            $message[] = 'تمت الاضافة لدي الاعجاب!';
        }
    }
    else{
        $message[] = 'سجل دخول اولا !';
    }
}

if(isset($_POST['add_comment'])){

    if($user_id != ''){

        $id = create_unique_id();
        $comment_box = $_POST['comment_box'];
        $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
        $content_id = $_POST['content_id'];
        $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

       $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
        $select_content->execute([$content_id]);
        $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

        $tutor_id = $fetch_content['tutor_id'];

        if($select_content->rowCount() > 0){

          $select_comment = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ? AND user_id = ? AND tutor_id = ? AND comment = ?");
            $select_comment->execute([$content_id, $user_id, $tutor_id, $comment_box]);

            if($select_comment->rowCount() > 0){
                $message[] = 'تمت اضافة التعليق من  قبل !';
            }else{
                $insert_comment = $conn->prepare("INSERT INTO `comments`(id, content_id, user_id, tutor_id, comment) VALUES(?,?,?,?,?)");
                $insert_comment->execute([$id, $content_id, $user_id, $tutor_id, $comment_box]);
                $message[] = 'تم اضافة تعليق جديد!';
            }

        }else{
            $message[] = 'شئ ما حدث خطأ!';
        }

    }else{
        $message[] = 'سجل دخول اولا !';
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
        $message[] = 'تم حذف التعليق !';
    }
    else{
        $message[] = 'التعليق محذوف من قبل !';
    }
}

if (isset($_POST['edit_comment'])) {
    $edit_id = $_POST['edit_id'];
    $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
    
    $comment_box = $_POST['comment_box'];
    $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);

    $verify_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id=? AND comment=?");
    $verify_edit_comment->execute([$edit_id, $comment_box]);
    if ($verify_edit_comment->rowCount() >0) {
        $message[] = 'تمت اضافة التعليق من قبل !';
    }
    else{
        $update_comment = $conn->prepare('UPDATE `comments` SET comment = ? WHERE id = ?');
        $update_comment->execute([$comment_box, $edit_id]);
        $message[] = 'تم تحديث التعليق !';
    }
}
if (isset($_POST['upload_homework'])) {
    if($user_id != ''){
        $content_id = $_POST['content_id'];
        $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);
        // $img          = $_FILES['img']['name'];
        // $img          = filter_var($img, FILTER_SANITIZE_STRING);
        
        // $ext            = pathinfo($img, PATHINFO_EXTENSION);
        // $rename         = create_unique_id().'.'.$ext;
        // $img_tmp_name = $_FILES['img']['tmp_name'];
        // $img_size     = $_FILES['img']['size'];
        // $img_folder   = 'uploaded_files/'.$rename;
        $file_name = $_FILES['pdf']['name'];
        $file_name = filter_var($file_name, FILTER_SANITIZE_STRING);
        $file_ext            = pathinfo($file_name, PATHINFO_EXTENSION);
        $rename_file       = create_unique_id().'.'.$file_ext;
        $file_tmp_name = $_FILES['pdf']['tmp_name'];
        $file_size     = $_FILES['pdf']['size'];
        $file_folder   = 'uploaded_files/'.$rename_file;
        
        $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
        $select_content->execute([$content_id]);
        $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

        $tutor_id = $fetch_content['tutor_id'];

        if($select_content->rowCount() > 0){

          $select_img = $conn->prepare("SELECT * FROM `homework` WHERE content_id = ? AND user_id = ?  AND homework = ?");
            $select_img->execute([$content_id, $user_id, $rename_file]);

            if($select_img->rowCount() > 0){
                $message[] = 'تمت اضافة الواجب من قبل !';
            }else{
                $insert_img = $conn->prepare("INSERT INTO `homework`( content_id, user_id, homework) VALUES(?,?,?)");
                $insert_img->execute([ $content_id, $user_id, $rename_file]);
                // move_uploaded_file($img_tmp_name, $img_folder);
                move_uploaded_file($file_tmp_name, $file_folder);
                $message[] = 'تم ارسال الواجب بنجاح !';
            }

        }else{
            $message[] = 'شئ ما حدث خطأ!';
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
    <title>مشاهدة الفيديو</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- header section start -->
    <?php include 'components/user_header.php';?>
    <!-- header section end -->

<?php
    if(isset($_POST['update_comment'])){
        $update_id = $_POST['comment_id'];
        $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);

        $select_update_comment = $conn->prepare("SELECT * FROM `comments` WHERE id =? LIMIT 1 ");
        $select_update_comment->execute([$update_id]);
        $fetch_update_comment = $select_update_comment->fetch(PDO::FETCH_ASSOC);
?>
<section class="comment-form">
    <h1 class="heading">تعديل التعليق </h1>
    <form action="" method="POST">
    <input type="hidden" name="edit_id" value="<?= $fetch_update_comment['id']; ?>">
        <textarea name="comment_box" class="box" required maxlength="1000" cols="30" row="10" placeholder="كتابة تعليق...">
            <?= $fetch_update_comment['comment']?>
        </textarea>
        <input type="submit" value="تعديل التعليق " name="edit_comment" class="inline-btn">
    </form>
</section>
<?php
}
?>
<!-- watch video sction start -->
<section class="watch-video">
    <?php
        $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
        $select_content->execute([$get_id, 'active']);
        if ($select_content->rowCount() > 0) {
            while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){ 
                $content_id = $fetch_content['id'];
                $pdf = $fetch_content['pdf'];
                $video = $fetch_content['video'];
                $audio = $fetch_content['audio'];
                $date = $fetch_content['date'];
                    $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ?");
                    $select_likes->execute([$content_id]);
                    $total_likes = $select_likes->rowCount();
                    
                    $user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
                    $user_likes->execute([$user_id, $content_id]);

                    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? ");
                    $select_tutor->execute([$fetch_content['tutor_id']]);
                    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

    ?>
    <div class="video-details">
    <?php if($fetch_content['video']) {?>
        <a href="<?=$fetch_content['video'];?>"><img src="uploaded_files/<?=$fetch_content['thumb'];?>"></a>
        <?php } elseif( $fetch_content['audio']) {?>
            <audio controls>
                <source src="uploaded_files/<?=$fetch_content['audio'];?>" type="audio/mpeg">
                <source src="uploaded_files/<?=$fetch_content['audio'];?>" type="audio/ogg">
                <source src="uploaded_files/<?=$fetch_content['audio'];?>" type="audio/wav">
            </audio>
        <?php }?> 
        <h3 class="title"><?=$fetch_content['title'];?></h3>
        
        <div class="info">
            <p><i class="fas fa-calendar"></i><span><?=$fetch_content['date'];?></span></p>
            <p><i class="fas fa-heart"></i><span><?=$total_likes?></span></p>
        </div>

        <div class="tutor">
            <img src="uploaded_files/<?=$fetch_tutor['image'];?>" alt="">
            <div>
                <h3><?=$fetch_tutor['name'];?></h3>
                <span><?=$fetch_tutor['profession'];?></span>
            </div>
        </div>

        <form action="" method="POST" class="flex">
            <input type="hidden" name="content_id" value="<?=$content_id;?>">
            <a href="playlist.php?get_id=<?=$fetch_content['playlist_id']?>" class="inline-btn">عرض الوحدة</a>
            <?php if($user_likes->rowCount() > 0) { ?>
                <button name="like_content" type="submit"><i class="fas fa-heart"></i><span>اعجبك</span></button>
            <?php } else{?>
                <button name="like_content" type="submit"><i class="far fa-heart"></i><span>اعجاب</span></button>
            <?php }?>        
        </form>

            <div class="description">
                <p><?=$fetch_content['description']?></p>
            </div>
    </div>
    <?php
            }
        }else{
            echo '<p class="empty">لا يوجد محتوي !</p>';
            }
        ?>
</section>
<!-- watch video sction end -->

<section class="comment-form">
    <h1 class="heading">المرفقات</h1>
        <?php 
            if($pdf){
        ?>
                <!-- <embed src="uploaded_files/<?=$pdf;?>"  width="100%" height="500px" /> -->
                <iframe src="uploaded_files/<?=$pdf;?>"  width="100%" height="500px" frameborder="0"></iframe>
        <?php
            }
            else{
                echo '<p class="empty">لا يوجد مرفقات!</p>';
            }
        ?>
</section>

<?php  if($pdf && !$video && !$audio){
       // echo date('y-m-d h:i:s a',strtotime($date)) ;
        $current_date = date("Y-m-d");
        $form_expiry_date = date("Y-m-d", strtotime("+7 days", strtotime($date)));
            //echo date('Y-m-d h-i-s a',strtotime($form_expiry_date));
        if ($current_date <= $form_expiry_date) {
            $select_homework = $conn->prepare("SELECT * FROM `homework` WHERE user_id = ? AND content_id = ?");
            $select_homework->execute([$user_id, $content_id]);
            $num = $select_homework->rowCount();
            if($num == 0){
?>
            <section class="comment-form">
                <h1 class="heading">رفع الواجب</h1>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="content_id" value="<?= $get_id; ?>">
                    <!-- <input type="file" name="img" required accept="image/*" 
                            style="width: 100%;
                            border-radius: .5rem;
                            margin: 1rem 0;
                            font-size: 1.8rem;
                            color: var(--black);
                            padding: 1.4rem;
                            background-color: var(--light-bg);"> <br> -->
                    <input type="file" name="pdf" class="box" accept="application/pdf" 
                            style="width: 100%;
                            border-radius: .5rem;
                            margin: 1rem 0;
                            font-size: 1.8rem;
                            color: var(--black);
                            padding: 1.4rem;
                            background-color: var(--light-bg);"> <br>

                    <input type="submit" value="ارسال " name="upload_homework" class="inline-btn">
                </form>
            </section>
<?php }
    }
    else{
        // التأكد من ان البيانات ارسلت مرة واحدة فقط 
        $select_img = $conn->prepare("SELECT * FROM `homework` WHERE content_id=? AND user_id=? AND degree=?");
        $select_img->execute([ $content_id, $user_id, 0]);
        if(!$select_img->rowCount()>0){
            $insert_img = $conn->prepare("INSERT INTO `homework`( content_id, user_id, degree) VALUES(?,?,?)");
            $insert_img->execute([ $content_id, $user_id, 0]);
        }
        echo '<p class="empty">انتهت مدة رفع الواجب</p>';
}
}
?>

<section class="comment-form">
    <h1 class="heading">اضافة تعليق </h1>
    <form action="" method="POST">
    <input type="hidden" name="content_id" value="<?= $get_id; ?>">
        <textarea name="comment_box" class="box" required maxlength="1000" cols="30" row="10" placeholder="كتابة تعليق ..."></textarea>
        <input type="submit" value="اضف تعليق" name="add_comment" class="inline-btn">
    </form>
</section>

<!-- comments section start -->
<section class="comments">
    <h1 class="heading">تعليقات الطلاب </h1>
    <div class="show-comments">
        <?php
            $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id=? AND approve = 1");
            $select_comments->execute([$get_id]);
            if ($select_comments->rowCount() > 0) {
                while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){
                    $comment_id = $fetch_comment['id'];
                    $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id=?");
                    $select_commentor->execute([$fetch_comment['user_id']]);
                    $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="box"<?php if($fetch_comment['user_id'] == $user_id) {echo 'style="order:-1";'; }?> >
                <div class="user">
                    <div>
                        <h3><?=$fetch_commentor['name'];?></h3>
                        <span><?=$fetch_comment['date'];?></span>
                    </div>
                </div>
                <p class="text"><?= $fetch_comment['comment'];?></p>
                <?php if($fetch_comment['user_id'] == $user_id) {?>
                    <form action="" method="post">
                        <input type="hidden" name="comment_id" value="<?= $fetch_comment['id'];?>">
                        <input type="submit" name="update_comment" value="تعديل التعليق" class="inline-option-btn">
                        <input type="submit" name="delete_comment" value="حذف التعليق" class="inline-delete-btn" onclick="return confirm('حذف التعليق ؟');">
                    </form>
                <?php } ?>
            </div>
            <?php
                }
            }   
            else{
                echo '<p class="empty">لا يوجد تعليقات !</p>';
            }
        ?>
    </div>
</section>

<!-- comments section end -->







<!-- footer section start -->

<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<!-- <script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script> -->

</body>
</html>