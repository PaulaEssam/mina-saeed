<?php
session_start();
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
}
if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
}
else{
    $get_id = '';
    header("Location: courses.php");
}

$select_users = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_users->execute([$user_id]);
$fetch_user = $select_users->fetch(PDO::FETCH_ASSOC);
$gender = $fetch_user['gender'];
$year = $fetch_user['year'];


$select_exam_time = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
$select_exam_time->execute([$get_id]);
$fetch_exam_time = $select_exam_time->fetch(PDO::FETCH_ASSOC);
$exam_time = $fetch_exam_time['exam_time'] ;
$timer = $exam_time ;

$end_exam = $_COOKIE['login_time'] + $exam_time * 60 ;

// echo $exam_time;
###########################################################################
$givenTimestamp = date('Y-m-d H:i:s',$end_exam);
$givenDateTime = new DateTime($givenTimestamp);
$currentDateTime = new DateTime();
$interval = $currentDateTime->diff($givenDateTime);
$timeInMinutes =  $interval->format('%i');
###########################################################################
$checkSubmit = $conn->prepare("SELECT * FROM `exam_answers` WHERE user_id=? AND exam_id=?");
        $checkSubmit->execute([$user_id, $get_id]);
        // echo $checkSubmit->rowCount() ;


if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    if( !(time() >= $end_exam)){
        
    $select_exams = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
    $select_exams->execute([$get_id]);
    $fetch_exam = $select_exams->fetch(PDO::FETCH_ASSOC);
    $exam_id = $fetch_exam['exam_id'];
    $exam_time = $fetch_exam['exam_time'];
    
    $select_questions = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ?");
    $select_questions->execute([$get_id]);
    $total_questions = $select_questions->rowCount();

    for ($i=1; $i <= $total_questions ; $i++) {
        $fetch_question = $select_questions->fetch(PDO::FETCH_ASSOC);
        $Q_id = $fetch_question['question_id'];
        $question_id = "answer" . $Q_id;
        if(empty($_FILES[$question_id]['name'])){
            if($checkSubmit->rowCount() > 0 ){
                $message[] = "تم ارساال الامتحان من قبل."; //الرسالة مش هتتكب انا عاملها ف الكونديشن عشان ميبعتش اكتر من مرة
            }else{
                $user_answer = $_POST[$question_id];
                $insert_answer = $conn->prepare("INSERT INTO `exam_answers` (user_id, exam_id, question_id, user_answer) VALUES (?,?,?,?)");
                $insert_answer->execute([$user_id, $exam_id, $Q_id, $user_answer]);
            }
            // $save_result = $conn->prepare("INSERT INTO `exam_results` (user_id, gender, exam_id, year, degree) VALUES (?,?,?,?,?)");
            // $save_result->execute([$user_id, $gender, $get_id, $year, $user_degree]);
        }
        if (!empty($_FILES[$question_id]['name'])) {
            $user_answer = $_POST[$question_id];
            $image          = $_FILES[$question_id]['name'];
            $image          = filter_var($image, FILTER_SANITIZE_STRING);
            $ext            = pathinfo($image, PATHINFO_EXTENSION);
            $rename         = create_unique_id().'.'.$ext;
            $image_tmp_name = $_FILES[$question_id]['tmp_name'];
            $image_size     = $_FILES[$question_id]['size'];
            $image_folder   = 'uploaded_files/'.$rename;
            if($checkSubmit->rowCount() > 0 ){
                $message[] = "تم ارسال الامتحان من قبل."; //الرسالة مش هتتكب انا عاملها ف الكونديشن عشان ميبعتش اكتر من مرة
            }else{
                $insert_answer = $conn->prepare("INSERT INTO `exam_answers` (user_id, exam_id, question_id, user_answer) VALUES (?,?,?,?)");
                $insert_answer->execute([$user_id, $exam_id, $Q_id, $rename]);
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        }
    }

    $message[] = 'answers have been sent successfully';
    // $update_exam = $conn->prepare("UPDATE `exam_name` SET taken=? WHERE exam_id=? AND user_id=?");
    // $update_exam -> execute(["1",$get_id, $user_id]);
    header("Location: courses.php");
######################################################user degree#######################################################
        $score = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ?");
        $score -> execute([$get_id]);
        $exam_degree = 0 ;
        $user_degree = 0 ;
        while($fetch_result = $score->fetch(PDO::FETCH_ASSOC)){
            $exam_degree += $fetch_result['degree'];
            
            $user_answer = $conn->prepare("SELECT * FROM `exam_answers` WHERE exam_id=? AND user_id=?");
            $user_answer->execute([$get_id, $user_id]);
            while($fetch_user_answer = $user_answer->fetch(PDO::FETCH_ASSOC)){
                if($fetch_result['correct_answer'] == $fetch_user_answer['user_answer'] && $fetch_result['question_id'] ==  $fetch_user_answer['question_id'] && $fetch_result['correct_answer']){
                    $user_degree += $fetch_result['degree'];
                    //echo $fetch_user_answer['user_answer'] ."</br>";
                }
            }
        }
        if($checkSubmit->rowCount() > 0 ){
            $message[] = "تم ارسال الامتحان من قبل."; //الرسالة مش هتتكب انا عاملها ف الكونديشن عشان ميبعتش اكتر من مرة
        }else{
            $save_result = $conn->prepare("INSERT INTO `exam_results` (user_id, gender, exam_id, year, degree) VALUES (?,?,?,?,?)");
            $save_result->execute([$user_id, $gender, $get_id, $year, $user_degree]);
        }
    }
    
########################################################################################################################

}
############################################active student############################################################
$active_student = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$active_student->execute([$user_id]);
$fetch_student = $active_student->fetch(PDO::FETCH_ASSOC);
    // $days_since_last_login = ($current_timestamp - $last_login_timestamp) / (60 * 60 * 24);
    if($active_student->rowCount()>0){
    $days = (time() - $fetch_student['last_login']) / (60*60*24);
    if ($days > 30 || $fetch_student['status'] == 0){
        $update = $conn->prepare("UPDATE `users` SET `status` =?  WHERE id = ?");
        $update->execute([0,$user_id]);
        $user_id = '';
        header("Location: components/user_logout.php");
    }
}
#######################################################################################################################
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الامتحان</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- comments section start -->
<section class="comments">
    <h1 class="heading">الامتحان</h1>

        <?php
            $select_exams = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
            $select_exams->execute([$get_id]);

            if ($select_exams->rowCount() > 0) {
                while($fetch_exam = $select_exams->fetch(PDO::FETCH_ASSOC)){
                
        ?>
            <div class="box-container">
                <div class="box">
                    <div class="comment-content">
                        <p>
                            <?= $fetch_exam['exam_title'];?>
                        </p>
                        <div>
                            <!-- <?php 
                                    // $date = new DateTime("@$end_exam");
                                    // $date->setTimezone(new DateTimeZone('Africa/Cairo'));
                                    // echo $date->format('H:i:s');
                            ?> -->
                            <p style="display: inline;">وقت الانتهاء :</p> 
                            <span id="timer" style="color:#f39c12">
                                <?php 
                                    $exatlyExamTime = (($exam_time * 50 )/ 60)*60; 
                                    $end_exam_time = $exatlyExamTime + $_COOKIE['login_time'] ;
                                    $date = new DateTime("@$end_exam_time");
                                    $date->setTimezone(new DateTimeZone('Africa/Cairo'));
                                    echo $date->format('H:i:s');
                                ?>

                            </span>
                            <div style="color: var(--light-bg);">الزمن المتبقي: 
                                <span id="countdown" style="color: red; font-size: 24px;"></span>
                            </div>
                        </div>
                    </div>
                    <form id="quizForm" action="" method="POST" enctype="multipart/form-data">

                    <?php
                        $select_questions = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ? ORDER BY RAND()");
                        $select_questions->execute([$get_id]);

                        if ($select_questions->rowCount() > 0) {
                            while($fetch_question = $select_questions->fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <!-- مقالي -->
                    <?php if ( !$fetch_question['img'] && !$fetch_question['correct_answer'] && $fetch_question['exam_question']) { ?>
                        <div class="comment-box">
                            <p ><?= $fetch_question['exam_question'];?></p>
                            <textarea name="answer<?=$fetch_question['question_id']?>" style="width: 100%; height: 100px; resize: none;"></textarea>
                        </div>
                        <!-- اختياري -->
                    <?php }elseif ((!$fetch_question['img']) && $fetch_question['ch_1'] && $fetch_question['ch_2'] && $fetch_question['ch_3']  && $fetch_question['ch_4']) { ?>

                        <div class="comment-box">
                            <p ><?= $fetch_question['exam_question'];?></p>
                            <div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_1']?>" id="<?=$fetch_question['ch_1']?>" >
                                    <label for="<?=$fetch_question['ch_1']?>"><?=$fetch_question['ch_1']?></label>
                                </div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_2']?>" id="<?=$fetch_question['ch_2']?>" >
                                    <label for="<?=$fetch_question['ch_2']?>"><?=$fetch_question['ch_2']?></label>
                                </div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_3']?>" id="<?=$fetch_question['ch_3']?>" >
                                    <label for="<?=$fetch_question['ch_3']?>"><?=$fetch_question['ch_3']?></label>
                                </div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_4']?>" id="<?=$fetch_question['ch_4']?>" >
                                    <label for="<?=$fetch_question['ch_4']?>"><?=$fetch_question['ch_4']?></label>
                                </div>
                            </div>
                        </div>
                        <!-- الطالب يرفع صورة -->
                        <?php
                        }elseif($fetch_question['img'] && !$fetch_question['correct_answer']){
                        ?>
                        <div class="comment-box">
                            <img src="uploaded_files/<?=$fetch_question['img']?>" style="width: 100%;">
                            <input type="file" name="answer<?=$fetch_question['question_id']?>" accept="image/*" style="padding: 1.4rem; font-size: 1.8rem;">
                        </div>
                        <!-- سؤال صورة اختياري  -->
                        <?php
                        }elseif($fetch_question['img'] && $fetch_question['correct_answer']){ ?>
                        <div class="comment-box">
                            <img src="uploaded_files/<?=$fetch_question['img']?>" style="width: 100%;">
                            <div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_1']?>" id="<?=$fetch_question['ch_1']?>" >
                                    <label for="<?=$fetch_question['ch_1']?>"><?=$fetch_question['ch_1']?></label>
                                </div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_2']?>" id="<?=$fetch_question['ch_2']?>" >
                                    <label for="<?=$fetch_question['ch_2']?>"><?=$fetch_question['ch_2']?></label>
                                </div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_3']?>" id="<?=$fetch_question['ch_3']?>" >
                                    <label for="<?=$fetch_question['ch_3']?>"><?=$fetch_question['ch_3']?></label>
                                </div>
                                <div style="display: flex;">
                                    <input  type="radio" name="answer<?=$fetch_question['question_id']?>" value="<?=$fetch_question['ch_4']?>" id="<?=$fetch_question['ch_4']?>" >
                                    <label for="<?=$fetch_question['ch_4']?>"><?=$fetch_question['ch_4']?></label>
                                </div>
                            </div>                        
                        </div>
                        <!-- مقالي -->
                    <?php
                        } elseif( !$fetch_question['img'] && $fetch_question['correct_answer'] && $fetch_question['exam_question']) { ?>
                        <div class="comment-box">
                            <p ><?= $fetch_question['exam_question'];?></p>
                            <textarea name="answer<?=$fetch_question['question_id']?>" style="width: 100%; height: 100px; resize: none;"></textarea>
                        </div>
                    <?php
                        }
                            }
                        }
                    ?>
                        <input type="submit" name="submit_exam" value="ارسال" class="inline-btn" onclick="return confirm('هل تريد ارسال الامتحان ؟');">
                    </form>
                </div>
            </div>

                <br>
                <?php
                }
        }
        else{
            echo '<p class="empty">لا يوجد امتحانات !</p>';
        }
        ?>

</section>

<!-- comments section end -->

<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
        
<script>
        function submitQuiz() {
            // let formData = $("#quizForm").serialize();
            let formData = new FormData($('#quizForm')[0]); 
            $.ajax({
                type: "POST",
                url: "exam.php?get_id=<?=$_GET['get_id']?>",
                data: formData,
                processData: false, // لا تعالج البيانات
                contentType: false, // لا تحدد نوع المحتوى
                success: function(response) {
                    alert("تم ارسال الامتحان بنجاح !");
                    window.location.href = 'courses.php';
                    $("#quizForm").trigger("reset");
                    disableForm();
                }
            });
        }

        $(document).ready(function() {
            
            setTimeout(submitQuiz, <?=($timeInMinutes+1) ?> * 50 * 1000); // 10 minutes in milliseconds
        });

        alert('تحذير هاام!!!  يجب عدم تحديث الصفحة(ريفريش) اثناء الامتحان لتجنب عدم حفظ الاجابات');
    </script>
        <script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>
        <script>
        // هنا نقوم بتعيين الوقت المتبقي (مثال: 10 دقائق من الآن)
        const examEndTime = new Date(Date.now() + <?=(($timeInMinutes+1) * 50) / 60?> * 60 * 1000).getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = examEndTime - now;

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerHTML =
                (hours < 10 ? "0" : "") + hours + ":" +
                (minutes < 10 ? "0" : "") + minutes + ":" +
                (seconds < 10 ? "0" : "") + seconds;

            // إذا انتهى الوقت
            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("countdown").innerHTML = "انتهى الوقت!";
            }
        }

        // تحديث العد التنازلي كل ثانية
        const timer = setInterval(updateCountdown, 1000);
    </script>
</body>
</html>