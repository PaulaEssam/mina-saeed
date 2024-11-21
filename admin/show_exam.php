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
    }
    else{
        $get_id = '';
        header("Location: taken_exams.php");
    }
    if (isset($_GET['s_id'])) {
        $s_id = $_GET['s_id'];
    }
    else{
        $s_id = '';
        header("Location: taken_exams.php");
    }
    


    $select_exam_name = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id =?");
    $select_exam_name -> execute([$get_id]);
    $fetch_exam_name = $select_exam_name->fetch(PDO::FETCH_ASSOC);
    $exam_name = $fetch_exam_name['exam_title'];

    $select_user_name = $conn->prepare("SELECT * FROM `users` WHERE id =?");
    $select_user_name -> execute([$s_id]);
    $fetch_user_name = $select_user_name->fetch(PDO::FETCH_ASSOC);
    $user_name = $fetch_user_name['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> عرض امتحان الطالب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>

<section class="questions" >
    <div class="box-container">
        <div class="box" >
            <div class="flex">
                    <p>اسم الامتحان: <b style="color: var(--orange);"><?=$exam_name?></b></p>
                    <p>اسم الطالب: <b style="color: var(--orange);"><?=$user_name?></b></p>
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
                $select_user_answer = $conn->prepare('SELECT * FROM exam_answers WHERE user_id = ? AND exam_id = ? AND question_id = ?');
                $select_user_answer->execute([$s_id, $get_id, $fetch_questions['question_id']]);
                $fetch_user_answer = $select_user_answer->fetch(PDO::FETCH_ASSOC);
            
            
            ?>

            <?php if ( !$fetch_questions['img'] && !$fetch_questions['correct_answer'] && $fetch_questions['exam_question']) { ?>
                <div >
                    <div>
                        <div class="flex">
                            <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.) <?=$fetch_questions['exam_question']?></h3>
                            <span>درجة السؤال: <?=$fetch_questions['degree']?></span>
                        </div>
                        <br>
                        <h2 style="background-color: var( --light-color);font-size: 18px; padding: 5px; border-radius: 3px; width: 100%;"> اجابة الطالب: <span style="color: var(--main-color);"><?=$fetch_user_answer['user_answer']?></span></h2>
                    </div>
                    
                </div>
                <br>

            <?php }elseif ((!$fetch_questions['img']) && $fetch_questions['ch_1'] && $fetch_questions['ch_2'] && $fetch_questions['ch_3']  && $fetch_questions['ch_4']) { ?>

                <div >
                    <div>
                        <div class="flex">
                            <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.) <?=$fetch_questions['exam_question']?></h3>
                            <span>درجة السؤال: <?=$fetch_questions['degree']?></span>
                        </div>
                        <p style="color: <?=$ch_1_color?>">a- <?=$fetch_questions['ch_1']?></p>
                        <p style="color: <?=$ch_2_color?>">b- <?=$fetch_questions['ch_2']?></p>
                        <p style="color: <?=$ch_3_color?>">c- <?=$fetch_questions['ch_3']?></p>
                        <p style="color: <?=$ch_4_color?>">d- <?=$fetch_questions['ch_4']?></p>
                        <br>
                        <h2 style="background-color: var( --light-color);font-size: 18px; padding: 5px; border-radius: 3px; width: 100%;">اجابة الطالب: <span style="color: var(--main-color);"><?=$fetch_user_answer['user_answer']?></span></h4>
                    </div>
    
                </div>
                <br>
                <?php }elseif ( !$fetch_questions['img'] && $fetch_questions['correct_answer'] && $fetch_questions['exam_question']) { ?>
                <div >
                    <div>
                        <div class="flex">
                            <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.) <?=$fetch_questions['exam_question']?></h3>
                            <span>درجة السؤال: <?=$fetch_questions['degree']?></span>
                        </div>
                        <p>الاجابة الصحيحة: <span style="color: #0cd20c;"><?=$fetch_questions['correct_answer']?></span></p>

                    </div>

                </div>
                <br>
            <?php
            }elseif (($fetch_questions['img']) && $fetch_questions['ch_1'] && $fetch_questions['ch_2'] && $fetch_questions['ch_3']  && $fetch_questions['ch_4']){
            ?>
                <div >
                    <div>
                        <div class="flex">
                            <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.)</h3>
                            <span>درجة السؤال: <?=$fetch_questions['degree']?></span>
                        </div>
                        <br>
                        <img src="../uploaded_files/<?=$fetch_questions['img']?>" alt="" style="width:100%;">

                        <p>الاجابة الصحيحة: <span style="color: #0cd20c;"><?=$fetch_questions['correct_answer']?></span></p>
                        <br>
                        <h2 style="background-color: var( --light-color);font-size: 18px; padding: 5px; border-radius: 3px; width: 100%;">اجابة الطالب: <span style="color: var(--main-color);"><?=$fetch_user_answer['user_answer']?></span></h2>
                    </div>
                </div>
                <br>
            <?php    
                
                }elseif($fetch_questions['img'] && !$fetch_questions['correct_answer']){
                
                ?>
                <div >
                    <div>
                        <div class="flex">
                            <h3 style="color: var(--black); font-size: 1.8rem"><?=$i?>.)</h3>
                            <span>درجة السؤال: <?=$fetch_questions['degree']?></span>
                        </div>
                        <br>
                        <img src="../uploaded_files/<?=$fetch_questions['img']?>" alt="" style="width:100%;">
                        <!-- <p>الاجابة الصحيحة: <span style="color: #0cd20c;"><?=$fetch_questions['correct_answer']?></span></p> -->
                        <br>
                        <?php
                            if($fetch_user_answer['user_answer']){
                        ?>
                        <h2 style="background-color: var( --light-color);font-size: 18px; padding: 5px; border-radius: 3px; width: 100%;">اجابة الطالب: 👇🏻</h2>
                            <img style="width: 100%; height: 20%;" src="../uploaded_files/<?=$fetch_user_answer['user_answer']?>" alt="">
                        <br>
                        <?php 
                            }
                            else {
                                echo '<h2 style="background-color: red; color:#fff; font-size: 18px; width: 100%; padding:5px; border-radius: 3px;">لا توجد اجابة للطالب علي هذا السؤال </h2>';
                            }
                        ?>
                    </div>
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

<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>

</body>
</html>