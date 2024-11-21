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

    if (isset($_POST['update'])) {
        $status         = $_POST['status'];
        $status         = filter_var($status, FILTER_SANITIZE_STRING);
        
        $title          = $_POST['title'];
        $title          = filter_var($title, FILTER_SANITIZE_STRING);
        
        $description    = $_POST['description'];
        $description    = filter_var($description, FILTER_SANITIZE_STRING);
        
        $playlist_id    = $_POST['playlist'];
        $playlist_id    = filter_var($playlist_id, FILTER_SANITIZE_STRING);

        $update_content = $conn->prepare("UPDATE `content` SET title=?, description=?, status=? WHERE id=?");
        $update_content->execute([$title, $description, $status, $get_id]);

        if (!empty($playlist_id)) {
            $update_playlist = $conn->prepare("UPDATE `content` SET playlist_id=? WHERE id=?");
            $update_playlist->execute([$playlist_id, $get_id]);
        }
        
        $old_thumb      = $_POST['old_thumb'];
        $old_thumb      = filter_var($old_thumb, FILTER_SANITIZE_STRING);
        $thumb          = $_FILES['thumb']['name'];
        $thumb          = filter_var($thumb, FILTER_SANITIZE_STRING);
        $thumb_ext      = pathinfo($thumb, PATHINFO_EXTENSION);
        $rename_thumb   = create_unique_id().'.'.$thumb_ext;
        $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
        $thumb_size     = $_FILES['thumb']['size'];
        $thumb_folder   = '../uploaded_files/'.$rename_thumb;
        if (!empty($thumb)) {
            if ($thumb_size > 2000000) {
                $message[] = 'برجاء اختيار صورة اسغر حجما!';
            }
            else{
                $update_thumb = $conn->prepare("UPDATE `content` SET thumb=? WHERE id=?");
                $update_thumb->execute([$rename_thumb, $get_id]);
                move_uploaded_file($thumb_tmp_name, $thumb_folder);
                if ($old_thumb != '') {
                    unlink('../uploaded_files/'.$old_thumb);
                }
            }
        }


        // $old_video      = $_POST['old_video'];
        // $old_video      = filter_var($old_video, FILTER_SANITIZE_STRING);
        // $video          = $_FILES['video']['name'];
        // $video          = filter_var($video, FILTER_SANITIZE_STRING);
        // $video_ext      = pathinfo($video, PATHINFO_EXTENSION);
        // $rename_video   = create_unique_id().'.'.$video_ext;
        // $video_tmp_name = $_FILES['video']['tmp_name'];
        // $video_folder   = '../uploaded_files/'.$rename_video;
        // if (!empty($video)) {
        //     $update_video = $conn->prepare("UPDATE `content` SET video=? WHERE id=?");
        //     $update_video->execute([$rename_video, $get_id]);
        //     move_uploaded_file($video_tmp_name, $video_folder);
        //     if ($old_video != '') {
        //         unlink('../uploaded_files/'.$old_video);
        //     }
        // }

        $video             = $_POST['video'];
        $video             = filter_var($video, FILTER_SANITIZE_STRING);
        if (!empty($video)) {
            $update_video = $conn->prepare("UPDATE `content` SET video=? WHERE id=?");
            $update_video->execute([$video, $get_id]);
        }
        
        $old_pdf      = $_POST['old_pdf'];
        $old_pdf      = filter_var($old_pdf, FILTER_SANITIZE_STRING);
        $pdf          = $_FILES['pdf']['name'];
        $pdf          = filter_var($pdf, FILTER_SANITIZE_STRING);
        $pdf_ext      = pathinfo($pdf, PATHINFO_EXTENSION);
        $rename_pdf   = create_unique_id().'.'.$pdf_ext;
        $pdf_tmp_name = $_FILES['pdf']['tmp_name'];
        $pdf_folder   = '../uploaded_files/'.$rename_pdf;
        if (!empty($pdf)) {
            $update_pdf = $conn->prepare("UPDATE `content` SET pdf=? WHERE id=?");
            $update_pdf->execute([$rename_pdf, $get_id]);
            move_uploaded_file($pdf_tmp_name, $pdf_folder);
            if ($old_pdf != '') {
                unlink('../uploaded_files/'.$old_pdf);
            }
        }
        $message[] = 'تم تعديل المحتوي !';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المحتوي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>

<!-- update content section start-->
<section class="crud-form">
    <h1 class="heading">تعديل المحتوي</h1>

    <?php
        $select_content = $conn->prepare("SELECT * FROM `content` WHERE id=?");
        $select_content->execute([$get_id]);
        if($select_content->rowCount()>0){
            while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
                $pdf = $fetch_content['pdf'];

    ?>

    <form action="" method="post" enctype="multipart/form-data">
        
        <input type="hidden" name="content_id" value="<?=$fetch_content['id']?>">
        <input type="hidden" name="old_video" value="<?=$fetch_content['video']?>">
        <input type="hidden" name="old_thumb" value="<?=$fetch_content['thumb']?>">
        <input type="hidden" name="old_pdf" value="<?=$fetch_content['pdf']?>">
        
        <p>الحالة </p>
        <select name="status" class="box" >
            <option value="<?=$fetch_content['status']?>" selected><?=$fetch_content['status']?></option>
            <option value="active">مفعل</option>
            <option value="deactive">غير مفعل</option>
        </select>
        
        <p>عنوان المحتوي</p>
        <input type="text" name="title" placeholder="عنوان المحتوي" class="box" maxlength="100" value="<?=$fetch_content['title']?>">
        
        <p>وصف المحتوي</p>
        <textarea name="description" placeholder="وصف المحتوي" class="box" maxlength="1000"  cols="30" rows="10"><?=$fetch_content['description']?></textarea>
        
        <select name="playlist" class="box" >
            <!-- <option value="<?=$fetch_content['playlist_id']?>"  selected>--select playlist</option> -->
            <?php
                $select_playlist = $conn-> prepare("SELECT * FROM `playlist` WHERE tutor_id = ? ");
                $select_playlist->execute([$tutor_id]);
                if($select_playlist->rowCount()>0){
                    while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
                        $selected = '';
                        if ($fetch_content['playlist_id'] == $fetch_playlist['id']){
                            $selected = 'selected';
                        }
            ?>
                <option <?= $selected?> value="<?= $fetch_playlist['id'];?>"><?= $fetch_playlist['title'];?></option>
            <?php
                    }
                }
                else{
                    echo '<option value="" disabled> لم يتم اضافة اي وحدة حتي الان !</option>';
                }
            ?> 
        </select>
        
        <p>تعديل الصورة</p>
        <img src="../uploaded_files/<?=$fetch_content['thumb']?>" class="media" alt="">
        <input type="file" name="thumb"  class="box" accept="image/*" >
        <p>تعديل الفيديو</p>
        <input type="text" name="video" placeholder="تعديل الفيديو" class="box" maxlength="100" value="<?=$fetch_content['video']?>">

        <p>تعديل pdf</p>
        <?php if($fetch_content['pdf']){ ?>
        <embed src="../uploaded_files/<?=$fetch_content['pdf'];?>" class="media" width="100%" height="400px"/>
        <?php 
            }
            else{
                echo '<p class="empty" style="color:red">لم يتم اضافة اي ملفات pdf !</p>';
                }
        ?>
        <input type="file" name="pdf"  class="box" accept="application/pdf">

        <p>تعديل الملف الصوتي </p>
        <?php if($fetch_content['audio']){ ?>
            <audio controls class="media" >
                <source src="../uploaded_files/<?=$fetch_content['audio'];?>" type="audio/mpeg">
                <source src="../uploaded_files/<?=$fetch_content['audio'];?>" type="audio/ogg">
                <source src="../uploaded_files/<?=$fetch_content['audio'];?>" type="audio/wav">
            </audio>
        <?php 
            }
            else{
                echo '<p class="empty" style="color:red">لم يتم اضافة اي ملفات صوتية!</p>';
                }
        ?>
        <input type="file" name="audio_file" class="box">

        <input type="submit" name="update"  value="تعديل المحتوي " class="btn">
        <div class="flex-btn">
            <a href="view_content.php?get_id=<?=$get_id;?>" class="option-btn">عرض المحتوي</a>
            <input type="submit" value="حذف المحتوي" name="delete_content" class="delete-btn">
        </div>
    </form>
    <?php
        }
    }else{
        echo '<p class="empty">المحتوي غير موجود !</p>';
    }
    ?>
</section>

<!-- update content section end -->










<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>