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
if (isset($_POST['delete'])) {
    $homework = $_POST['homework'];
    $delete_homework = $conn->prepare("DELETE FROM `homework` WHERE id= ?");
    $delete_homework->execute([$homework]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الواجب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        @media (max-width:450px) {
            
            td iframe{
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <!--header section -->
<?php
include '../components/admin_header.php';
?>

<section class="comments">
    <h1 class="heading">الواجب</h1>
    <div class="box-container">
        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>المحتوي</th>
                        <th>واجب الطالب</th>
                        <th>السنة</th>
                        <th>الدرجة</th>
                        <th>تعديل</th>
                        <th>حذف</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                        $homework = $conn->prepare("SELECT * FROM `homework`");
                        $homework->execute();
                        while( $fetch_homework = $homework->fetch(PDO::FETCH_ASSOC)){
                                $select_user =  $conn->prepare("SELECT * FROM `users` WHERE id=?");
                                $select_user->execute([$fetch_homework['user_id']]);
                                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                                
                                $select_content =  $conn->prepare("SELECT * FROM `content` WHERE id=?");
                                $select_content->execute([$fetch_homework['content_id']]);
                                $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$fetch_user['name']?></td>
                        <td><?=$fetch_content['title']?></td>
                        <?php if($fetch_homework['homework']){?>
                        <!-- <td> <img onclick='openModal(this.src)' src="../uploaded_files/<?=$fetch_homework['homework']?>" class="thumbnail" alt="">
                            <div id="myModal" class="modal">
                                <span  class="close" onclick="closeModal()">&times;</span>
                                <img class="modal-content" id="img01">
                            </div>
                        </td> -->
                        <td class="ifr"><iframe src="../uploaded_files/<?=$fetch_homework['homework']?>" frameborder="0"></iframe></td>
                        <?php } else{
                            echo "<td style='color:red;'>لم يتم تسليم الواجب</td>";
                            }
                        ?>
                        <td><?php if($fetch_user['year'] == 1) echo'الصف الاول'; elseif($fetch_user['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td><?=$fetch_homework['degree'];?></td>
                        <td><a href="edit_homework.php?get_id=<?=$fetch_homework['id']?>&s_id=<?=$fetch_user['id']?>" class="option-btn">تعديل</a></td>
                        
                        <td>
                            <form action="" method="post">
                                    <input type="hidden" name="homework" value="<?=$fetch_homework['id']?>">
                                    <input type="submit" class="delete-btn" value="حذف" name="delete" onclick="return confirm('delete this homework?');">
                            </form>
                        </td>
                    </tr>
                    <?php 
                        $i++;
                    }
                    ?>
                
                </tbody>
            </table>
        </div>
       
    </div>
</section>

<!-- footer section -->
<?php
include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
<!-- <script>
        function openModal(imgSrc) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("img01");
            modal.style.display = "block";
            modalImg.src = imgSrc;
        }

        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
</script> -->
</body>
</html>