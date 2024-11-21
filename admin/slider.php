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

    $image          = $_FILES['image']['name'];
    $image          = filter_var($image, FILTER_SANITIZE_STRING);
    
    $ext            = pathinfo($image, PATHINFO_EXTENSION);
    $rename         = create_unique_id().'.'.$ext;
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size     = $_FILES['image']['size'];
    $image_folder   = '../uploaded_files/'.$rename;

            $add_exam = $conn->prepare("INSERT INTO `slider` (img)
                VALUES (?)");                     
            $add_exam -> execute([$rename]);
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'تم اضافة صورة جديدة!';
}

if (isset($_POST['delete_image'])) {
    $delete_id = $_POST['delete_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_image = $conn->prepare("SELECT * FROM `slider` WHERE id =?");
    $verify_image -> execute([$delete_id]);

    if ($verify_image->rowCount() > 0) {
        $fetch_thumb = $verify_image->fetch(PDO::FETCH_ASSOC);
        $prev_thumb  = $fetch_thumb['img'];
        if ($prev_thumb != '') {
            unlink('../uploaded_files/' . $prev_thumb);
        }
        
        $delete_playlist = $conn->prepare("DELETE FROM `slider` WHERE id= ? ");
        $delete_playlist->execute([$delete_id]);
        $message[] = 'تم حذف الصورة !';
    }else{
        $message[] = 'الصورة محذوفة بالفعل !';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضفافة صورة</title>
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
    <h1 class="heading">اضافة صورة</h1>
    <form action="" method="post" enctype="multipart/form-data">

        <p>رفع صورة <span>*</span></p>
        <input type="file" name="image" class="box" accept="image/*" required>
        <input type="submit" name="submit"  value="اضافة صورة" class="btn">

    </form>
</section>

<section class="playlists">
    <h1 class="heading">الصور</h1>
    <div class="box-container">
    <?php
            $select_image = $conn->prepare("SELECT * FROM `slider`");
            $select_image -> execute();

            if ($select_image->rowCount() > 0) {
                while($fetch_image = $select_image->fetch(PDO::FETCH_ASSOC)){
        ?>
        <div class="box">
            <div >
                <img style="width: 100%; height:50%; border-radius: 5px;" src="../uploaded_files/<?= $fetch_image['img']; ?>" >
                <form action="" method="POST" class="flex-btn">
                    <input type="hidden" name="delete_id" value="<?=$fetch_image['id'];?>">
                    <input type="submit" value="حذف" name="delete_image" class="delete-btn" onclick="return confirm('delete this image?');">
                </form>
            </div>
        </div>

    <?php 
        }
    }
    ?>
    </div>
</section>

<!-- add contensection end -->




<!-- footer section -->
<?php
include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>