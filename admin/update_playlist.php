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
        $status        = $_POST['status'];
        $status        = filter_var($status, FILTER_SANITIZE_STRING);
        $title         = $_POST['title'];
        $title         = filter_var($title, FILTER_SANITIZE_STRING);
        $description   = $_POST['description'];
        $description   = filter_var($description, FILTER_SANITIZE_STRING);
        
        $year        = $_POST['year'];
        $year        = filter_var($year, FILTER_SANITIZE_STRING);

        $update_playlist = $conn->prepare("UPDATE `playlist` SET title=? , description=?, year=?, status=? WHERE id=?");
        $update_playlist->execute([$title, $description, $year, $status, $get_id]);
        $message[] = 'تم تحديث الوحدة !';

        $old_thumb      = $_POST['old_thumb'];
        $old_thumb      = filter_var($old_thumb, FILTER_SANITIZE_STRING);
        $thumb          = $_FILES['thumb']['name'];
        $thumb          = filter_var($thumb, FILTER_SANITIZE_STRING);
        $ext            = pathinfo($thumb, PATHINFO_EXTENSION);
        $rename         = create_unique_id().'.'.$ext;
        $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
        $thumb_size     = $_FILES['thumb']['size'];
        $thumb_folder   = '../uploaded_files/'.$rename;

        if (!empty($thumb)) {
            if ($thumb_size > 2000000) {
                $message[] = 'برجاء اختيار صورة اصغر حجما!';
            }
            else{
                $update_thumb = $conn->prepare("UPDATE `playlist` SET `thumb`=? WHERE id=?");
                $update_thumb->execute([$rename, $get_id]);
                move_uploaded_file($thumb_tmp_name, $thumb_folder);
                if ($old_thumb != '' && $old_thumb != $rename) {
                    unlink('../uploaded_files/'.$old_thumb);
                }
                
            }
        }
    }

    if (isset($_POST['delete_playlist'])) {
        $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id =?");
        $verify_playlist -> execute([$get_id]);

        if ($verify_playlist->rowCount() > 0) {
            $fetch_thumb = $verify_playlist->fetch(PDO::FETCH_ASSOC);
            $prev_thumb  = $fetch_thumb['thumb'];
            if ($prev_thumb != '') {
                unlink('../uploaded_files/' . $prev_thumb);
            }
            $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id= ? ");
            $delete_bookmark->execute([$get_id]);
            
            $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id= ? ");
            $delete_playlist->execute([$get_id]);

            $delete_content = $conn->prepare("DELETE FROM `content` WHERE playlist_id= ?  ");
            $delete_content->execute([$get_id]);
            // header("Location: playlists.php");
        }else{
            $message[] = 'الوحدة محذوفة بالفعل !';
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الوحدة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>




<!-- update playlist section start -->
<section class="crud-form">
    <h1 class="heading">تعديل الوحدة</h1>
    <?php
        $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id =?");
        $select_playlist -> execute([$get_id]);

        if ($select_playlist->rowCount() > 0) {
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
                $playlist_id   = $fetch_playlist['id'];
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="old_thumb" value="<?=$fetch_playlist['thumb'];?>">
        <p>الحالة </p>
        
        <select name="status" class="box"  >
            <option value="<?=$fetch_playlist['status'];?>" selected><?php echo $fetch_playlist['status'] == 'active' ? 'مفعل' : 'غير مفعل';?></option>
            <option value="active">مفعل</option>
            <option value="deactive">غير مفعل</option>
        </select>
        
        <p>العنوان </p>
        <input type="text" name="title" value="<?=$fetch_playlist['title'];?>" class="box" maxlength="100" >
        
        <p>الوصف </p>
        <textarea name="description"  class="box"  maxlength="1000"  cols="30" rows="10"><?=$fetch_playlist['description'];?></textarea>
    
        <p>الصورة </p>
        <img src="../uploaded_files/<?=$fetch_playlist['thumb'];?>" alt="" class="media">
        <input type="file" name="thumb"  class="box"  accept="image/*" >
        <p>السنة</p>
        <select name="year" class="box">
        <option value="<?=$fetch_playlist['year'];?>" selected><?php if($fetch_playlist['year'] == 1) echo'الاول'; elseif($fetch_playlist['year']==2)echo 'الثاني'; else echo'الثالث'; ?></option>
            <option value="1">الاول</option>
            <option value="2">الثاني</option>
            <option value="3">الثالث</option>
        </select>
        <input type="submit" name="update"  value="تعديل الوحدة" class="btn">
    
        <div class="flex-btn">
            <input type="submit" value="حذف" name="delete_playlist" class="delete-btn">
            <a href="view_playlist.php?get_id=<?=$playlist_id?>" class="option-btn">عرض الوحدة</a>
        </div>
    </form>
    <?php        
            }
        }else{
            echo '<p class="empty">لا توجد واحدات !</p>';
        }
        
        ?>
</section>
<!-- update playlist section end -->








<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>