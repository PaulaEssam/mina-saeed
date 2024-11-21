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

    if (isset($_POST['submit'])) {
        $id             = create_unique_id();
    
        $status         = $_POST['status'];
        $status         = filter_var($status, FILTER_SANITIZE_STRING);
        
        $title          = $_POST['title'];
        $title          = filter_var($title, FILTER_SANITIZE_STRING);
        
        $description    = $_POST['description'];
        $description    = filter_var($description, FILTER_SANITIZE_STRING);
        
        $playlist_id    = $_POST['playlist'];
        $playlist_id    = filter_var($playlist_id, FILTER_SANITIZE_STRING);

        $thumb          = $_FILES['thumb']['name'];
        $thumb          = filter_var($thumb, FILTER_SANITIZE_STRING);
        $thumb_ext            = pathinfo($thumb, PATHINFO_EXTENSION);
        $rename_thumb         = create_unique_id().'.'.$thumb_ext;
        $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
        $thumb_size     = $_FILES['thumb']['size'];
        $thumb_folder   = '../uploaded_files/'.$rename_thumb;

        $video             = $_POST['video'];
        $video             = filter_var($video, FILTER_SANITIZE_STRING);

        $audio = $_FILES['audio_file']['name'];
        $audio = filter_var($audio, FILTER_SANITIZE_STRING);
        $audio_ext            = pathinfo($audio, PATHINFO_EXTENSION);
        $rename_audio         = create_unique_id().'.'.$audio_ext;
        $audio_tmp_name = $_FILES['audio_file']['tmp_name'];
        $audio_size     = $_FILES['audio_file']['size'];
        $audio_folder   = '../uploaded_files/'.$rename_audio;


        $file_name = $_FILES['pdf']['name'];
        $file_name = filter_var($file_name, FILTER_SANITIZE_STRING);
        // $exe = explode('.',$file_name);
        // $exetension = end($exe) ;
        $file_ext            = pathinfo($file_name, PATHINFO_EXTENSION);
        $rename_file       = create_unique_id().'.'.$file_ext;
        $file_tmp_name = $_FILES['pdf']['tmp_name'];
        $file_size     = $_FILES['pdf']['size'];
        $file_folder   = '../uploaded_files/'.$rename_file;

        // $video          = $_FILES['video']['name'];
        // $video          = filter_var($video, FILTER_SANITIZE_STRING);
        // $video_ext            = pathinfo($video, PATHINFO_EXTENSION);
        // $rename_video         = create_unique_id().'.'.$video_ext;
        // $video_tmp_name = $_FILES['video']['tmp_name'];
        // $video_folder   = '../uploaded_files/'.$rename_video;

        $verify_content = $conn->prepare("SELECT * FROM `content` WHERE tutor_id =? AND title =? AND description =? ");
        $verify_content -> execute([$tutor_id, $title, $description]);

        if ($verify_content->rowCount() > 0) {
            $message[] = 'المحتوي موجود بالفعل!';
        }
        else{
            if ($thumb_size > 2000000) {
                $message[] = "برجاء رفع صورة اصغر حجما!";
            }
            elseif( $audio_ext!='' && $file_ext!=''){
                $add_content = $conn->prepare("INSERT INTO `content` (id, tutor_id, playlist_id, title, description, video, audio, thumb, pdf, status)
                VALUES (?,?,?,?,?,?,?,?,?,?)");
                $add_content -> execute([$id, $tutor_id, $playlist_id, $title, $description, $video, $rename_audio, $rename_thumb, $rename_file, $status]);            
                move_uploaded_file($thumb_tmp_name, $thumb_folder);
                move_uploaded_file($audio_tmp_name, $audio_folder);
                move_uploaded_file($file_tmp_name, $file_folder);
                //move_uploaded_file($video_tmp_name, $video_folder);
                $message[] = 'تم اضافة المحتوي بنجاح!';
            }
            elseif( $audio_ext!='' && $file_ext==''){
                $add_content = $conn->prepare("INSERT INTO `content` (id, tutor_id, playlist_id, title, description, video, audio, thumb, pdf, status)
                VALUES (?,?,?,?,?,?,?,?,?,?)");
                $add_content -> execute([$id, $tutor_id, $playlist_id, $title, $description, $video, $rename_audio, $rename_thumb, null, $status]);            
                move_uploaded_file($thumb_tmp_name, $thumb_folder);
                move_uploaded_file($audio_tmp_name, $audio_folder);
                move_uploaded_file($file_tmp_name, $file_folder);
                //move_uploaded_file($video_tmp_name, $video_folder);
                $message[] =  'تم اضافة المحتوي بنجاح!';
            }
            elseif( $audio_ext=='' && $file_ext!=''){
                $add_content = $conn->prepare("INSERT INTO `content` (id, tutor_id, playlist_id, title, description, video, audio, thumb, pdf, status)
                VALUES (?,?,?,?,?,?,?,?,?,?)");
                $add_content -> execute([$id, $tutor_id, $playlist_id, $title, $description, $video, null, $rename_thumb, $rename_file, $status]);            
                move_uploaded_file($thumb_tmp_name, $thumb_folder);
                move_uploaded_file($audio_tmp_name, $audio_folder);
                move_uploaded_file($file_tmp_name, $file_folder);
                //move_uploaded_file($video_tmp_name, $video_folder);
                $message[] =  'تم اضافة المحتوي بنجاح!';
            }
            else{
                $add_content = $conn->prepare("INSERT INTO `content` (id, tutor_id, playlist_id, title, description, video, audio, thumb, pdf, status)
                VALUES (?,?,?,?,?,?,?,?,?,?)");
                $add_content -> execute([$id, $tutor_id, $playlist_id, $title, $description, $video, null, $rename_thumb, null, $status]);            
                move_uploaded_file($thumb_tmp_name, $thumb_folder);
                // move_uploaded_file($audio_tmp_name, $audio_folder);
                // move_uploaded_file($file_tmp_name, $file_folder);
                //move_uploaded_file($video_tmp_name, $video_folder);
                $message[] =  'تم اضافة المحتوي بنجاح!';
            }

        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضافة محتوي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>
<!-- add contensection start-->
<section class="crud-form">
    <h1 class="heading">اضافة محتوي</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p>الحالة <span>*</span></p>
        
        <select name="status" class="box" required>
            <option value="active">مفعل</option>
            <option value="deactive">غير مفعل</option>
        </select>
        
        <p>عنوان المحتوي <span>*</span></p>
        <input type="text" name="title" placeholder="اكتب عنوان المحتوي" class="box" maxlength="100" required>
        
        <p>وصف المحتوي</p>
        <textarea name="description" placeholder="اكتب وصف المحتوي" class="box" maxlength="1000"  cols="30" rows="10"></textarea>
        
        <select name="playlist" class="box" required>
            <option value="" disabled selected>--اختار وحدة </option>
            <?php
                $select_playlist = $conn-> prepare("SELECT * FROM `playlist` WHERE tutor_id = ? ");
                $select_playlist->execute([$tutor_id]);
                if($select_playlist->rowCount()>0){
                    while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){

            ?>
                <option value="<?= $fetch_playlist['id'];?>"><?= $fetch_playlist['title'];?></option>
            <?php
                    }
                }
                else{
                    echo '<option value="" disabled> لم يتم اضافة وحدات حتي الان!</option>';
                }
            ?> 
        </select>
        
        <p>اختار صورة المحتوي <span>*</span></p>
        <input type="file" name="thumb"  class="box" accept="image/*" required>
        
        <p>ادخل رابط الفيديو </p>
        <input type="text" name="video"  class="box"  placeholder="ادخل رابط الفيديو">

        <p>رفع ملف صوتي </p>
        <input type="file" name="audio_file" class="box" accept="audio/*">

        <!-- <p>select video <span>*</span></p>
        <input type="file" name="video"  class="box" accept="video/*" required> -->

        <!-- <input type="text" name="video"  class="box" placeholder="enter the video link">  -->
        <p>رفع pdf</p>
        <input type="file" name="pdf" class="box" accept="application/pdf">
        <input type="submit" name="submit"  value="اضافة محتوي" class="btn">
    
    </form>

</section>



<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>