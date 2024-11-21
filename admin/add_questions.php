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

$select_exam_name = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id =?");
$select_exam_name -> execute([$get_id]);
$fetch_exam_name = $select_exam_name->fetch(PDO::FETCH_ASSOC);
$exam_name = $fetch_exam_name['exam_title'];

if (isset($_POST['submit'])) {

    $question          = $_POST['question'];
    $question          = filter_var($question, FILTER_SANITIZE_STRING);

    $ch_1    = $_POST['ch_1'];
    $ch_1    = filter_var($ch_1, FILTER_SANITIZE_STRING);

    $ch_2    = $_POST['ch_2'];
    $ch_2    = filter_var($ch_2, FILTER_SANITIZE_STRING);

    $ch_3    = $_POST['ch_3'];
    $ch_3    = filter_var($ch_3, FILTER_SANITIZE_STRING);

    $ch_4    = $_POST['ch_4'];
    $ch_4    = filter_var($ch_4, FILTER_SANITIZE_STRING);

    $correct_answer    = $_POST['correct_answer'];
    $correct_answer    = filter_var($correct_answer, FILTER_SANITIZE_STRING);

    $degree    = $_POST['degree'];
    $degree    = filter_var($degree, FILTER_SANITIZE_STRING);

        
        if (!empty($_FILES['image']['name']) && !$correct_answer) {
            $image          = $_FILES['image']['name'];
            $image          = filter_var($image, FILTER_SANITIZE_STRING);
            $ext            = pathinfo($image, PATHINFO_EXTENSION);
            $rename         = create_unique_id().'.'.$ext;
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_size     = $_FILES['image']['size'];
            $image_folder   = '../uploaded_files/'.$rename;

            $add_question = $conn->prepare("INSERT INTO `exam_questions` (exam_id, img, degree) VALUES (?,?,?)");

            $add_question -> execute([ $get_id,$rename, $degree]);
            move_uploaded_file($image_tmp_name, $image_folder);
        }
        elseif(!empty($_FILES['image']['name']) && $correct_answer){
            $image          = $_FILES['image']['name'];
            $image          = filter_var($image, FILTER_SANITIZE_STRING);
            $ext            = pathinfo($image, PATHINFO_EXTENSION);
            $rename         = create_unique_id().'.'.$ext;
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_size     = $_FILES['image']['size'];
            $image_folder   = '../uploaded_files/'.$rename;

            $add_question = $conn->prepare("INSERT INTO `exam_questions` (exam_id, img,ch_1, ch_2, ch_3, ch_4, correct_answer, degree) VALUES (?,?,?,?,?,?,?,?)");

            $add_question -> execute([ $get_id,$rename, $ch_1, $ch_2, $ch_3, $ch_4, $correct_answer, $degree]);
            move_uploaded_file($image_tmp_name, $image_folder);
        }
        else{
            $add_qyestion = $conn->prepare("INSERT INTO `exam_questions` (exam_id, exam_question, ch_1, ch_2, ch_3, ch_4, correct_answer , degree)
                VALUES (?,?,?,?,?,?,?,?)");
                $add_qyestion -> execute([ $get_id, $question, $ch_1, $ch_2, $ch_3, $ch_4, $correct_answer, $degree]);
        }
        $message[] = 'تم اضافة سؤال !';

}
if (isset($_POST['delete_question'])) {
    $delete_id = $_POST['delete_question_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_question = $conn->prepare("SELECT * FROM `exam_questions` WHERE question_id =?");
    $verify_question -> execute([$delete_id]);

    if ($verify_question->rowCount() > 0) {
        $delete_question = $conn->prepare("DELETE FROM `exam_questions` WHERE question_id= ? ");
        $delete_question->execute([$delete_id]);
        $message[] = 'تم حذف السؤال !';
    }else{
        $message[] = 'السؤال موجود بالفعل !';
    }
}

$count_questions = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ?");
$count_questions->execute([$get_id]);
$total_questions = $count_questions->rowCount();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضافة سؤال</title>
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
    <h1 class="heading">اضافة سؤال</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p>عنوان السؤال </p>
        <input type="text" name="question" placeholder="عنوان السؤال" class="box">

        <p>الاختيار A</p>
        <input type="text" name="ch_1" placeholder=" الاختيار A " class="box">

        <p>الاختيار B</p>
        <input type="text" name="ch_2" placeholder=" الاختيار B " class="box">

        <p>الاختيار C</p>
        <input type="text" name="ch_3" placeholder=" الاختيار C " class="box">

        <p>الاختيار D</p>
        <input type="text" name="ch_4" placeholder=" الاختيار D " class="box">

        <p>رفع صورة</p>
        <input type="file" name="image" class="box" accept="image/*">
        
        <p>الاجابة الصحيحة</p>
        <input type="text" name="correct_answer" placeholder="الاجابة الصحيحة " class="box">
        <p>درجة السؤال <span>*</span></p>
        <input type="number" min="1" name="degree" placeholder="درجة السؤال" class="box" required>
        <input type="submit" name="submit"  value="اضافة سؤال" class="btn">
    </form>
</section>

<section class="questions" >
<div class="box-container"  >
    <div class="box" >
        <div class="flex">
            <div>
                <h3 style="font-size: 1.8rem; color: #f39c12"><?=$exam_name?></h3>
            </div>
            <div>
                <span> اسئلة الامتحان: <span style="color: #f39c12"> <?= $total_questions?></span> </span>
            </div>
        </div>
        <br>
            <div style="display: flex; align-items: center;">
                <span style="font-weight: bold; margin-right: 5px;"></span>
                <div style="height: 2px; background-color:#8e44ad; flex-grow: 1;"></div>
            </div>
        <br>
        <?php
        $select_questions = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id =?");
        $select_questions -> execute([$get_id]);
        $i=1;
        if ($select_questions->rowCount() > 0) {
        while($fetch_questions = $select_questions->fetch(PDO::FETCH_ASSOC)){
            if($fetch_questions['correct_answer'] == $fetch_questions['ch_1']){
                $ch_1_color = '#0cd20c';
                $ch_2_color ='';
                $ch_3_color ='';
                $ch_4_color ='';
            }elseif($fetch_questions['correct_answer'] == $fetch_questions['ch_2']){
                $ch_2_color = '#0cd20c';
                $ch_1_color ='';
                $ch_3_color ='';
                $ch_4_color ='';
            }elseif($fetch_questions['correct_answer'] == $fetch_questions['ch_3']){
                $ch_3_color = '#0cd20c';
                $ch_2_color ='';
                $ch_1_color ='';
                $ch_4_color ='';
            }elseif($fetch_questions['correct_answer'] == $fetch_questions['ch_4']){
                $ch_4_color = '#0cd20c';
                $ch_2_color ='';
                $ch_3_color ='';
                $ch_1_color ='';
            }
        ?>

        <?php if ( !$fetch_questions['img'] && !$fetch_questions['correct_answer'] && $fetch_questions['exam_question']) { ?>
            <div >
                <div>
                    <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.) <?=$fetch_questions['exam_question']?></h3>
                    <p style="color:red">سؤال مقالي</p>
                </div>
                    <form method="post">
                        <div style="display: flex; justify-content: space-between;" >
                            <input type="hidden" name="delete_question_id" value="<?=$fetch_questions['question_id']?>" >
                            <a  style="width: 45%;" href="update_question.php?get_id=<?=$fetch_questions['question_id']?>" style="width: 50%; margin-right: 5px" class="option-btn"> تعديل</a>
                            <input style="width: 45%;" type="submit" name="delete_question" value="حذف" style="width: 50%" class="delete-btn" onclick="return confirm('delete this question?');">
                        </div>
                    </form>
            </div>
            <br>

        <?php }elseif ((!$fetch_questions['img']) && $fetch_questions['ch_1'] && $fetch_questions['ch_2'] && $fetch_questions['ch_3']  && $fetch_questions['ch_4']) { ?>

            <div >
                <div>
                    <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.) <?=$fetch_questions['exam_question']?></h3>
                    
                    <p style="color: <?=$ch_1_color?>">a- <?=$fetch_questions['ch_1']?></p>
                    <p style="color: <?=$ch_2_color?>">b- <?=$fetch_questions['ch_2']?></p>
                    <p style="color: <?=$ch_3_color?>">c- <?=$fetch_questions['ch_3']?></p>
                    <p style="color: <?=$ch_4_color?>">d- <?=$fetch_questions['ch_4']?></p>
                </div>
                    <form method="post">
                        <div style="display: flex; justify-content: space-between;" >
                            <input type="hidden" name="delete_question_id" value="<?=$fetch_questions['question_id']?>">
                            <a style="width: 45%;" href="update_question.php?get_id=<?=$fetch_questions['question_id']?>" style="width: 50%; margin-right: 5px" class="option-btn"> تعديل</a>
                            <input style="width: 45%;" type="submit" name="delete_question" value="حذف" style="width: 50%" class="delete-btn" onclick="return confirm('delete this question?');">
                        </div>
                    </form>
            </div>
            <br>
            <?php }elseif ( !$fetch_questions['img'] && $fetch_questions['correct_answer'] && $fetch_questions['exam_question']) { ?>
            <div >
                <div>
                    <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.) <?=$fetch_questions['exam_question']?></h3>
                    <p>الاجابة الصحيحة: <span style="color: #0cd20c;"><?=$fetch_questions['correct_answer']?></span></p>

                </div>
                    <form method="post">
                        <div style="display: flex; justify-content: space-between;" >
                            <input type="hidden" name="delete_question_id" value="<?=$fetch_questions['question_id']?>">
                            <a style="width: 45%;" href="update_question.php?get_id=<?=$fetch_questions['question_id']?>" style="width: 50%; margin-right: 5px" class="option-btn"> تعديل</a>
                            <input style="width: 45%;" type="submit" name="delete_question" value="حذف" style="width: 50%" class="delete-btn" onclick="return confirm('delete this question?');">
                        </div>
                    </form>
            </div>
            <br>
        <?php
        }else{
        ?>
            <div >
                <div>
                    <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.)
                    <img src="../uploaded_files/<?=$fetch_questions['img']?>" alt="" style="width:100%;">
                    </h3>
                    <p>الاجابة الصحيحة: <span style="color: #0cd20c;"><?=$fetch_questions['correct_answer']?></span></p>
                </div>
                
                    <form method="post">
                        <div style="display: flex; justify-content: space-between;" >
                            <input type="hidden" name="delete_question_id" value="<?=$fetch_questions['question_id']?>">
                            <a style="width: 45%;" href="update_question.php?get_id=<?=$fetch_questions['question_id']?>" style="width: 50%; margin-right: 5px" class="option-btn"> تعديل</a>
                            <input style="width: 45%;" type="submit" name="delete_question" value="حذف" style="width: 50%" class="delete-btn" onclick="return confirm('delete this question?');">
                        </div>
                    </form>
            </div>
            <br>
        <?php    
            }
            $i++;
        }
            }
        $ch_1_color ='';

        ?>
    </div>
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