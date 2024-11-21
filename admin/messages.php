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

    if (isset($_POST['delete_message'])) {
        $delete_id = $_POST['message_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_message = $conn->prepare("SELECT * FROM `contact` WHERE id = ?");
        $verify_message->execute([$delete_id]);

        if ($verify_message->rowCount() > 0) {
            $delete_message = $conn->prepare("DELETE FROM `contact` WHERE id = ?");
            $delete_message->execute([$delete_id]);
            $message[] = 'تم حذف الرسالة!';
        }
        else{
            $message[] = 'الرسالة محذوفة بالفعل!';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الرسائل</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>

<!-- messages section start -->
<section class="comments">
    <h1 class="heading">رسائل الطلاب</h1>
    <div class="box-container">
    
    <?php
            $update_mesg = $conn->prepare("UPDATE `contact` SET see = 1 ");
            $update_mesg->execute();


            $select_messages = $conn->prepare("SELECT * FROM `contact` ORDER BY id DESC");
            $select_messages->execute();
            if ($select_messages->rowCount() > 0) {
                while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){
                    // $message_id = $fetch_message['id'];
                    $user_name = $fetch_message['name'];
                    
                    $select_sender = $conn->prepare("SELECT * FROM `users`");
                    $select_sender->execute();
                    $fetch_sender = $select_sender->fetch(PDO::FETCH_ASSOC);
                    
                    
    ?>
            <div class="box">
                <div class="user">
                    <div>
                        <h3><?=$user_name;?></h3>
                    </div>
                </div>
                <p class="comment-box"><?= $fetch_message['message'];?></p>
                <form action="" method="post">
                    <input type="hidden" name="message_id" value="<?= $fetch_message['id'];?>">
                    <input type="submit" name="delete_message" value="حذف الرسالة" class="inline-delete-btn" onclick="return confirm('delete this message?');">
                </form>
            </div>
    <?php
                }
            }   
            else{
                echo '<p class="empty">لا توجد رسائل !</p>';
            }
        ?>
    </div>
</section>

<!-- messages section end -->


<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>