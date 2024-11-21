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
    if (isset($_POST['update'])) {
        $select_old_degree = $conn->prepare("SELECT * FROM `exam_results` WHERE user_id=? AND exam_id=?");
        $select_old_degree->execute([$s_id, $get_id]);
        $row = $select_old_degree->fetch(PDO::FETCH_ASSOC);

        $new_degree        = $_POST['totalScore'];
        $new_degree        = filter_var($new_degree, FILTER_SANITIZE_STRING);
    
        $new_degree +=$row['degree'];

        $update_degree = $conn->prepare("UPDATE `exam_results` SET degree=? WHERE user_id=? AND exam_id=?");
        $update_degree->execute([$new_degree, $s_id, $get_id]);

        $message[] = 'تم تعديل الدرجة !';
        header("Location: taken_exams.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تصحيح الاسئلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 20px;
        }
        th {
            background-color: black;
            color: white;
        }
        tr:nth-child(even) {
            background-color: white;
        }
        tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

    
</style>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>


<section class="comments">
    <h1 class="heading">تصحيح الاسئلة</h1>
    <div class="box-container">
        <div class="box">
        <form id="scoreForm" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الطالب</th>
                        <th>النوع</th>
                        <th>السنة</th>
                        <th>عنوان الامتحان</th>
                        <th>السؤال</th>
                        <th>اجابة الطالب</th>
                        <th>اضافة درجة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                        $select_question = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ?");
                        $select_question->execute([$get_id]);
                        while( $fetch_question = $select_question->fetch(PDO::FETCH_ASSOC)){
                            
                            $select_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
                            $select_exam->execute([$get_id]);
                            $fetch_exam = $select_exam->fetch(PDO::FETCH_ASSOC);

                            if (($fetch_question['img'] && !$fetch_question['correct_answer']) || (!$fetch_question['img'] && !$fetch_question['correct_answer']) ) {
                                
                                $select_user_answer = $conn->prepare("SELECT * FROM `exam_answers` WHERE exam_id = ?");
                                $select_user_answer->execute([$get_id]);
                                while($fetch_user_answer = $select_user_answer->fetch(PDO::FETCH_ASSOC)){
                                    $select_user_name = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                                    $select_user_name->execute([$fetch_user_answer['user_id']]);
                                    $fetch_user_name = $select_user_name->fetch(PDO::FETCH_ASSOC);

                                    if(($fetch_user_answer['question_id'] == $fetch_question['question_id']) && ($fetch_user_answer['user_id'] == $s_id) ){
                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$fetch_user_name['name']?></td>

                        <td><?=$fetch_user_name['gender']?></td>
                        <td><?php if($fetch_user_name['year'] == 1) echo'الصف الاول'; elseif($fetch_user_name['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td> <a href="mark_questions.php?get_id=<?=$fetch_exam['exam_title']?>" style="color: var(--orange);"><?=$fetch_exam['exam_title']?></a></td>
                        <td>
                            <?php 
                                if  ($fetch_question['img'] && !$fetch_question['correct_answer']) {
                                    echo "<img  src='../uploaded_files/$fetch_question[img]' alt='image' style='width: 100px; height: 100px;'>" ;
                                }
                            ?>
                            <?=$fetch_question['exam_question']?>
                        </td>

                        <td>
                            <?php
                                    $thumb_ext= pathinfo($fetch_user_answer['user_answer'], PATHINFO_EXTENSION);
                                    if ($thumb_ext) {
                                        echo "
                                                <img class='thumbnail' onclick='openModal(this.src)' src='../uploaded_files/$fetch_user_answer[user_answer]' alt='image' style='width: 100px; height: 100px;'>
                                                " ;
                                            echo '  <div id="myModal" class="modal">
                                                        <span style="width:300px;" class="close" onclick="closeModal()">&times;</span>
                                                        <img class="modal-content" id="img01">
                                                    </div>';
                                        $fetch_user_answer['user_answer'] = '';
                                    }
                            ?>

                                <?= $fetch_user_answer['user_answer']?>
                        </td>
                        <td>
                            <input type="text" name="add_degree" placeholder="اضافة درجة" class="grade" style="width: 80px; height: 30px; background-color: #80808045; font-size: 16px; border-radius: 5px; padding: 5px;">
                        </td>
                    
                    </tr>
                    <?php 
                        $i++;
                    }
                }
            }
        }
                    ?>
                
                </tbody>
            </table>
            <input type="hidden" id="totalScore" name="totalScore" value="0" />
            <button id="calculate"name="update" class="inline-btn"> اضافة درجة الطالب</button>
            
        </form>
            
        </div>
        <div> 
            <script>
                document.getElementById('calculate').onclick = function() {
                    let total = 0;
                    const grades = document.querySelectorAll('.grade');

                    grades.forEach(input => {
                        total += parseFloat(input.value) || 0; // جمع الدرجات مع التعامل مع القيم الفارغة
                    });

                    document.getElementById('totalScore').value = total; // تخزين المجموع في حقل مخفي
                };
            </script>
        </div>
        
    </div>
</section>

<!-- footer section -->
<?php 
        include '../components/footer.php';
?>
<script src="../js/admin_script.js"></script>
<script>
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
        
</script>
</body>
</html>