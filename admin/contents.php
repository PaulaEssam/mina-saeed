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

    if (isset($_POST['delete_content'])) {
        $delete_id = $_POST['content_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_content = $conn->prepare("SELECT * FROM `content` WHERE id=?");
        $verify_content->execute([$delete_id]);
        if($verify_content->rowCount() > 0){
            $fetch_content = $verify_content->fetch(PDO::FETCH_ASSOC);
            unlink('../uploaded_files/' . $fetch_content['thumb']);
        //    unlink('../uploaded_files/' . $fetch_content['video']);
            $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE content_id= ? ");
            $delete_comment->execute([$delete_id]);
            
            $delete_like = $conn->prepare("DELETE FROM `likes` WHERE content_id= ? ");
            $delete_like->execute([$delete_id]);

            $delete_content = $conn->prepare("DELETE FROM `content` WHERE id= ? ");
            $delete_content->execute([$delete_id]);
            $message[] = 'تم حذف المحتوي !';
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
    <title>كل المحتوي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>



<!-- contents section start -->
<section class="contents">
    <h1 class="heading">كل المحتوي</h1>
    <div class="box-container">
        <div class="box" style="text-align:center;">
            <h3 class="title" style="padding-bottom: 1rem;">اضافة محتوي </h3>
            <a href="add_content.php" class="btn">اضافة محتوي </a>
        </div>
    <?php
        $select_content = $conn->prepare("SELECT * FROM `content` WHERE tutor_id =?");
        $select_content -> execute([$tutor_id]);

        if ($select_content->rowCount() > 0) {
            while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
    ?>
    <div class="box">
        <div class="flex">
            <p><i class="fas fa-circle-dot" style="color:<?php if($fetch_content['status'] == 'active'){echo'limegreen';}else{echo'red';}?>"></i><span style="color:<?php if($fetch_content['status'] == 'active'){echo'limegreen';}else{echo'red';}?>"><?= $fetch_content['status'];?></span></p>
            <p><i class="fas fa-calendar"></i><span><?= $fetch_content['date'];?></span></p>
        </div>
        <img src="../uploaded_files/<?= $fetch_content['thumb'];?>" alt="">
        <h3 class="title"><?= $fetch_content['title'];?></h3>
        <a href="view_content.php?get_id=<?= $fetch_content['id'];?>" class="btn">عرض المحتوي</a>
        <form action="" class="flex-btn" method="POST">
            <input type="hidden" name="content_id" value="<?=$fetch_content['id'];?>">
            <a href="update_content.php?get_id=<?= $fetch_content['id'];?>" class="option-btn">تعديل</a>
            <input type="submit" value="حذف" name="delete_content" class="delete-btn">
        </form>
    </div>
    <?php
            }
        }
        else{
            echo 
            '<p class="empty">لا يوجد محتوي !
                <a href="add_content.php" style="margin-top: 2rem;" class="btn">اضافة محتوي جديد </a>
            </p>';
        }
    ?>
    </div>
</section>
<!-- contents section end -->











<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>