<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
    header("Location: index.php");
}

$count_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$count_bookmark->execute([$user_id]);
$total_bookmarks = $count_bookmark->rowCount();

$count_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$count_likes->execute([$user_id]);
$total_likes = $count_likes->rowCount();

$count_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$count_comments->execute([$user_id]);
$total_comments = $count_comments->rowCount();
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
    <title>البروفايل</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->

<!-- profile section start -->
<section class="profile">
    <h1 class="heading">تفاصيل البروفايل </h1>
    <div class="details">
        
        <div class="tutor">
        <?php if($fetch_profile['image']){ ?>
                <img src="uploaded_files/<?= $fetch_profile['image'];?>" alt="">
        <?php }else{ ?>
                <img src="imgs/avatar.jpg" alt="">
        <?php } ?>
            <h3><?=$fetch_profile['name'];?></h3>
            <span><?=$fetch_profile['email'];?></span>
            <p><?php if($fetch_profile['year'] == 1 ) echo "الصف الاول"; elseif($fetch_profile['year'] == 2 ) echo "الصف الثاني"; else echo "الصف الثالث";?></p>
            <a href="update.php" class="inline-btn">تحديث البروفايل</a>
        </div>

        <div class="box-container">
            
            <div class="box">
                <h3><?= $total_bookmarks ?></h3>
                <p>الوحدات المحفوظة</p>
                <a href="bookmark.php" class="btn">عرض الوحدات</a>
            </div>

            <div class="box">
                <h3><?= $total_likes ?></h3>
                <p>عدد التفاعلات </p>
                <a href="likes.php" class="btn">عرض المحتوي</a>
            </div>

            <div class="box">
                <h3><?= $total_comments ?></h3>
                <p>عدد التعليقات </p>
                <a href="comments.php" class="btn">عرض التعليقات </a>
            </div>

        </div>
    </div>
</section>
<!-- profile section end -->



<section class="comments">
    <h1 class="heading">تقارير الامتحانات</h1>
    <div class="box-container">
        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الايميل</th>
                        <th>السنة الدراسية</th>
                        <th>عنوان الامتحان</th>
                        <th>الدرجة</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                    $background ='';
                        $select_take_exams = $conn->prepare("SELECT * FROM `exam_results` WHERE user_id = ? ORDER BY degree DESC");
                        $select_take_exams->execute([$user_id]);
                        while( $fetch_take_exams = $select_take_exams->fetch(PDO::FETCH_ASSOC)){
                            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                            $select_user->execute([$fetch_take_exams['user_id']]);
                            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                            
                            $select_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
                            $select_exam->execute([$fetch_take_exams['exam_id']]);
                            $fetch_exam = $select_exam->fetch(PDO::FETCH_ASSOC);
                            
                            $score = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ?");
                            $score -> execute([$fetch_take_exams['exam_id']]);
                            $exam_degree = 0 ;
                            $user_degree = 0 ;
                            while($fetch_result = $score->fetch(PDO::FETCH_ASSOC)){
                                $exam_degree += $fetch_result['degree'];
                            }
                            // if($fetch_profile['name'] == $fetch_user['name']){
                            //     $background = '#f39c12';
                            // }
                            // else{
                            //     $background = '';
                            // }
                    ?>
                    <tr>
                        <td style="background-color: <?=$background?>;"><?=$i?></td>
                        <td style="background-color: <?=$background?>;"><?=$fetch_user['name']?></td>
                        <td style="background-color: <?=$background?>;"><?=$fetch_user['email']?></td>
                        <td style="background-color: <?=$background?>;"><?php if($fetch_user['year'] == 1) echo'الصف الاول'; elseif($fetch_user['year']==2)echo 'السف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td style="background-color: <?=$background?>;"> <?=$fetch_exam['exam_title']?></td>
                        <td style="background-color: <?=$background?>;"><?=$fetch_take_exams['degree'] . ' / ' . $exam_degree?></td>
                    
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


<section class="comments">
    <h1 class="heading">تقارير الواجبات</h1>
    <div class="box-container">
        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الايميل</th>
                        <th>السنة الدراسية</th>
                        <th>الواجب</th>
                        <th>الدرجة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                    $background ='';
                        $homework = $conn->prepare("SELECT * FROM `homework` WHERE user_id = ? ORDER BY degree DESC");
                        $homework->execute([$user_id]);
                        while( $fetch_homework = $homework->fetch(PDO::FETCH_ASSOC)){
                                $select_user =  $conn->prepare("SELECT * FROM `users` WHERE id=?");
                                $select_user->execute([$fetch_homework['user_id']]);
                                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                                
                                $select_content =  $conn->prepare("SELECT * FROM `content` WHERE id=?");
                                $select_content->execute([$fetch_homework['content_id']]);
                                $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
                                // if($fetch_profile['name'] == $fetch_user['name']){
                                //     $background = '#f39c12';
                                // }
                                // else{
                                //     $background = '';
                                // }
                    ?>
                    <tr>
                        <td style="background-color: <?=$background?>;"><?=$i?></td>
                        <td style="background-color: <?=$background?>;"><?=$fetch_user['name']?></td>
                        <td style="background-color: <?=$background?>;"><?=$fetch_user['email']?></td>
                        <td style="background-color: <?=$background?>;"><?php if($fetch_user['year'] == 1) echo'الصف الاول'; elseif($fetch_user['year']==2)echo 'السف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td style="background-color: <?=$background?>;"><?=$fetch_content['title']?></td>
                        <td style="background-color: <?=$background?>;"><?=$fetch_homework['degree'];?></td>
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


<section class="comments">
    <h1 class="heading">التقارير النهائية</h1>
    <div class="box-container">
        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم </th>
                        <th>السنة الدراسية</th>
                        <th>اجمالي الدرجات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                    $background ='';
                    // استعلام لجلب السنة الدراسية للطالب الحالي
                        $currentUserId = $fetch_profile['id']; // تأكد من أنك تحصل على معرف المستخدم بشكل صحيح
                        $stmtCurrentStudent = $conn->prepare('SELECT year FROM users WHERE id = ?');
                        $stmtCurrentStudent->execute([$currentUserId]);
                        $currentStudent = $stmtCurrentStudent->fetch();

                        $currentYear = $currentStudent['year']; // السنة الدراسية للطالب الحالي

                        // استعلام لجلب بيانات الامتحانات
                        $stmtExams = $conn->prepare('SELECT user_id, year, SUM(degree) as total_exam_degree FROM exam_results WHERE year = ? GROUP BY user_id ORDER BY total_exam_degree DESC');
                        $stmtExams->execute([$currentYear]);
                        $exams = $stmtExams->fetchAll();

                        // استعلام لجلب بيانات الواجبات
                        $stmtHomeworks = $conn->prepare('SELECT user_id, SUM(degree) as total_homework_degree FROM homework WHERE user_id IN (SELECT id FROM users WHERE year = ?) GROUP BY user_id ORDER BY total_homework_degree DESC');
                        $stmtHomeworks->execute([$currentYear]);
                        $homeworks = $stmtHomeworks->fetchAll();

                        // دمج البيانات وحساب المجموع الكلي
                        $students = [];

                        // دمج نتائج الامتحانات
                        foreach ($exams as $exam) {
                            $userId = $exam['user_id'];
                            $totalExamDegree = $exam['total_exam_degree'];

                            if (!isset($students[$userId])) {
                                $students[$userId] = ['year' => $currentYear, 'total_degree' => 0];
                            }

                            $students[$userId]['total_degree'] += $totalExamDegree;
                        }

                        // دمج نتائج الواجبات
                        foreach ($homeworks as $homework) {
                            $userId = $homework['user_id'];
                            $totalHomeworkDegree = $homework['total_homework_degree'];

                            if (!isset($students[$userId])) {
                                $students[$userId] = ['year' => $currentYear, 'total_degree' => 0];
                            }

                            $students[$userId]['total_degree'] += $totalHomeworkDegree;
                        }

                        // استعلام لجلب أسماء الطلاب
                        $stmtStudents = $conn->prepare('SELECT id, name FROM users WHERE year = ?');
                        $stmtStudents->execute([$currentYear]);
                        $studentNames = $stmtStudents->fetchAll(PDO::FETCH_KEY_PAIR);

                        foreach ($students as $userId => $data) {
                            $background = '';
                            if ($fetch_profile['name'] == $studentNames[$userId]) {
                                $background = '#f39c12';
                            }
                            else{
                                $background = '';
                            }
                        ?>
                        <tr>
                            <td style="background-color: <?=$background?>;"><?=$i?></td>
                            <td style="background-color: <?=$background?>;"><?=$studentNames[$userId]?></td>
                            <td style="background-color: <?=$background?>;"><?php if($data['year'] == 1) echo'الصف الاول'; elseif($data['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                            <td style="background-color: <?=$background?>;"><?=$data['total_degree'];?></td>
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

<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>

</body>
</html>