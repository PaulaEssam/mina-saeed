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
    header("Location: exams.php");
    exit();
}

if (isset($_POST['update'])) {
    $status        = $_POST['status'];
    $status        = filter_var($status, FILTER_SANITIZE_STRING);

    $title         = $_POST['title'];
    $title         = filter_var($title, FILTER_SANITIZE_STRING);

    $description   = $_POST['description'];
    $description   = filter_var($description, FILTER_SANITIZE_STRING);

    $playlist_id        = $_POST['playlist_id'];
    $playlist_id        = filter_var($playlist_id, FILTER_SANITIZE_STRING);

    $exam_time        = $_POST['exam_time'];
    $exam_time        = filter_var($exam_time, FILTER_SANITIZE_STRING);

    $year        = $_POST['year'];
    $year        = filter_var($year, FILTER_SANITIZE_STRING);


    $update_playlist = $conn->prepare("UPDATE `exam_name` SET exam_title=?, exam_description=?, playlist_id=?, exam_time=?, year=?, status=? WHERE exam_id=?");
    $update_playlist->execute([$title, $description, $playlist_id, $exam_time, $year, $status, $get_id]);
    $message[] = 'تم تعديل الامتحان !';

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الامتحان </title>
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
    <h1 class="heading">تعديل الامتحان </h1>
    <?php
    $select_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id =?");
    $select_exam -> execute([$get_id]);

    if ($select_exam->rowCount() > 0) {
        while($fetch_exam = $select_exam->fetch(PDO::FETCH_ASSOC)){
            $exam_id   = $fetch_exam['exam_id'];
            if ($fetch_exam['year'] == 1){
                $year = 'الاول';
            } elseif ($fetch_exam['year'] == 2){
                $year = 'الثاني';
            }elseif ($fetch_exam['year'] == 3){
                $year = 'الثالث';
            }
            

            $playlist_id = $fetch_exam['playlist_id'];
            $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
            $select_playlist->execute([$playlist_id]);
            $fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);
            ?>
            <form action="" method="post" enctype="multipart/form-data">
                <p>الحالة </p>
                <select name="status" class="box"  >
                    <option value="<?=$fetch_exam['status'];?>" selected><?php  echo $fetch_exam['status'] == 'active' ? 'مفعل' : 'غير مفعل';?></option>

                    <option value="active">مفعل</option>
                    <option value="deactive">غير مفعل</option>
                </select>

                <p>العنوان </p>
                <input type="text" name="title" value="<?=$fetch_exam['exam_title'];?>" class="box" maxlength="100" >

                <p>الوصف </p>
                <textarea name="description"  class="box"  maxlength="1000"  cols="30" rows="10"><?=$fetch_exam['exam_description'];?></textarea>
                <p>اختار وحدة </p>

                <select name="playlist_id" class="box">
                    <?php
                    $select_playlist = $conn-> prepare("SELECT * FROM `playlist` WHERE tutor_id = ? ");
                    $select_playlist->execute([$tutor_id]);
                    if($select_playlist->rowCount()>0){
                        while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
                            $selected = '';
                            if ($fetch_exam['playlist_id'] == $fetch_playlist['id']){
                                $selected = 'selected';
                            }
                            ?>
                                <option <?= $selected?> value="<?= $fetch_playlist['id'];?>"><?= $fetch_playlist['title'];?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <p>اختار وقت الامتحان </p>

                <select name="exam_time" class="box" >
                    <option value="<?=$fetch_exam['exam_time']?>"  selected> <?=($fetch_exam['exam_time'] * 50 / 60)?> </option>
                    <option value="6" >5 دقيقة</option>
                    <option value="12" >10 دقيقة</option>
                    <option value="18" >15 دقيقة</option>
                    <option value="24" >20 دقيقة</option>
                    <option value="30" >25 دقيقة</option>
                    <option value="36" >30 دقيقة</option>
                    <option value="42" >35 دقيقة</option>
                    <option value="48" >40 دقيقة</option>
                    <option value="54" >45 دقيقة</option>
                    <option value="60" >50 دقيقة</option>
                    <option value="66" >55 دقيقة</option>
                    <option value="72" >60 دقيقة</option>
                </select>
                <p>السنة الدراسية </p>

                <select name="year" class="box" required>
                    <option value="<?=$fetch_exam['year']?>"  selected> <?=$year?> </option>
                    <option value="1" >الاول</option>
                    <option value="2" >الثاني</option>
                    <option value="3" >الثالث</option>
                </select>

                <input type="submit" name="update"  value="تعديل الامتحان" class="btn">
            </form>
            <?php
        }
    }else{
        echo '<p class="empty">لا توجد امتحانات !</p>';
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