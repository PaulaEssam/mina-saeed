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
    $status         = $_POST['status'];
    $status         = filter_var($status, FILTER_SANITIZE_STRING);

    $title          = $_POST['title'];
    $title          = filter_var($title, FILTER_SANITIZE_STRING);

    $description    = $_POST['description'];
    $description    = filter_var($description, FILTER_SANITIZE_STRING);

    $playlist_id    = $_POST['playlist'];
    $playlist_id    = filter_var($playlist_id, FILTER_SANITIZE_STRING);

    $exam_time    = $_POST['exam_time'];
    $exam_time    = filter_var($exam_time, FILTER_SANITIZE_STRING);

    $year    = $_POST['year'];
    $year    = filter_var($year, FILTER_SANITIZE_STRING);


    $verify_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE tutor_id =? AND exam_title =? AND exam_description =? AND year =? ");
    $verify_exam -> execute([$tutor_id, $title, $description, $year]);

    if ($verify_exam->rowCount() > 0) {
        $message[] = 'الامتحان موجود بالفعل !';
    }

        else{
            $add_exam = $conn->prepare("INSERT INTO `exam_name` (tutor_id, playlist_id, exam_title, exam_description, exam_time, year, status)
                VALUES (?,?,?,?,?,?,?)");                     
            $add_exam -> execute([ $tutor_id, $playlist_id, $title, $description, $exam_time, $year, $status]);
            $message[] = 'تم اضافة امتحان بنجاح!';
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضافة امتحان</title>
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
    <h1 class="heading">اضافة امتحان</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p>الحالة <span>*</span></p>

        <select name="status" class="box" required>
            <option value="deactive">غير مفعل</option>
            <option value="active">مفعل</option>
        </select>

        <p>عنوان الامتحان <span>*</span></p>
        <input type="text" name="title" placeholder="ادخل عنوان الامتحان" class="box" maxlength="100" required>

        <p>وصف الامتحان </p>
        <textarea name="description" placeholder="ادخل وصف الامتحان" class="box" maxlength="1000"  cols="30" rows="10"></textarea>

        <select name="playlist" class="box" required>
            <option value="" disabled selected>--اختار وحدة</option>
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
                echo '<option value="" disabled> لم يتم اضافة وحدات حتي الان! </option>';
            }
            ?>
        </select>

        <select name="exam_time" class="box" required>
            <option value="" disabled selected>--اختار وقت الامتحان</option>
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

        <select name="year" class="box" required>
            <option value="" disabled selected>-- السنة الدراسية</option>
            <option value="1" >الصف الاول</option>
            <option value="2" >الصف الثاني</option>
            <option value="3" >الصف الثالث</option>
        </select>
        <input type="submit" name="submit"  value="اضافة امتحان" class="btn">

    </form>
</section>

<!-- add contensection end -->









<!-- footer section -->
<?php
include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
</body>
</html>