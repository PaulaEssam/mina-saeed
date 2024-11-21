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
    if (isset($_POST['show'])) {
        $user_id = $_POST['user_id'];
        $exam_id = $_POST['exam_id'];
        $show_degree = $conn->prepare("UPDATE `exam_results` SET show_degree=? WHERE user_id=? AND  exam_id=?");
        $show_degree->execute([1,$user_id, $exam_id]);
    }
    if (isset($_POST['hide'])) {
        $user_id = $_POST['user_id'];
        $exam_id = $_POST['exam_id'];
        $show_degree = $conn->prepare("UPDATE `exam_results` SET show_degree=? WHERE user_id=? AND  exam_id=?");
        $show_degree->execute([0,$user_id, $exam_id]);
    }
    if (isset($_POST['marked'])) {
        $user_id = $_POST['user_id'];
        $exam_id = $_POST['exam_id'];
        $show_degree = $conn->prepare("UPDATE `exam_results` SET Q_is_marked=? WHERE user_id=? AND  exam_id=?");
        $show_degree->execute([1,$user_id, $exam_id]);
    }
    if (isset($_POST['un_marked'])) {
        $user_id = $_POST['user_id'];
        $exam_id = $_POST['exam_id'];
        $show_degree = $conn->prepare("UPDATE `exam_results` SET Q_is_marked=? WHERE user_id=? AND  exam_id=?");
        $show_degree->execute([0,$user_id, $exam_id]);
    }
    if (isset($_POST['delete'])) {
        $exam_result_id = $_POST['exam_result_id'];
        $exam = $conn->prepare("DELETE FROM `exam_results` WHERE exam_result_id= ?");
        $exam->execute([$exam_result_id]);

        $exam_id = $_POST['exam_id'];
        $user_id = $_POST['user_id'];
        $delete_answer = $conn->prepare("DELETE FROM `exam_answers` WHERE user_id=? AND exam_id= ?");
        $delete_answer->execute([$user_id, $exam_id]);
    }
    if (isset($_POST['show_all'])) {
        $show_degree = $conn->prepare("UPDATE `exam_results` SET show_degree=? ");
        $show_degree->execute([1]);
    }
    if (isset($_POST['hide_all'])) {
        $show_degree = $conn->prepare("UPDATE `exam_results` SET show_degree=? ");
        $show_degree->execute([0]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الامتحانات المأخوذة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<style>
        .flex{
            display: flex;
            justify-content: space-between;
        }
        .search input{    
            text-align: right;
            height: 40px;
            width: 250px;
            margin: 5px;
            padding: 15px;
            font-size: 18px;
            border-radius: 5px;
            background: var(--white);
        }
        .search button{
            font-size: 18px;
            color: var(--main-color);
            cursor: pointer;
            background-color: transparent;
        }
        .highlight {
            background-color: var(--orange) !important;
            color: white;
        }
        @media (max-width:450px) {
            button.inline-option-btn.ex{
                width: 65px !important;
                height: 30px;
            }
            th, td{
                font-size: 4px !important;
            }
            td form input ,
            td a,
            td p{
                font-size: 4px !important;
            }
            .searchFlex{
                display: block !important;
            }
        }
    </style>
<body>
<!--header section -->
<?php 
    include '../components/admin_header.php';
?>
<h2 class="heading" style="padding: 20px;"> ابحث عن الطلاب والامتحانات </h2>
<div style="display: flex; gap:50px;" class="searchFlex">
    <form method="get" class="search">
        <input type="text" class="box searchInput" name="studentName" required placeholder="ابحث باسم الطالب !!" value="<?=!empty($_GET['studentName']) ? $_GET['studentName']: '' ?>"/>
        <button type="submit" name="ser" class="fas fa-search"></button>
    </form>

    <form method="get" class="search">
        <input type="text" class="box searchInput" name="examName" required placeholder="ابحث باسم الامتحان !!" value="<?=!empty($_GET['examName']) ? $_GET['examName']: '' ?>"/>
        <button type="submit" name="ser" class="fas fa-search"></button>
    </form>
</div>


<section class="comments">
    <h1 class="heading">الامتحانات المأخوذة</h1>
    <div class="box-container">
        <div class="box">
            <div style="display: flex; gap: 10px;">
                <form action="" method="post">
                    <button class="inline-btn" name="show_all">اظهار الكل </button>
                </form>
                <form action="" method="post">
                    <button class="delete-btn" name="hide_all">اخفاء الكل </button>
                </form>
            </div>
            <br><br>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الطالب</th>
                        <th>النوع</th>
                        <th>السنة</th>
                        <th>عنوان الامتحان</th>
                        <th>عرض الامتحان</th>
                        <th>الدرجة</th>
                        <th>تعديل</th>
                        <th>اظهار الدرجة</th>
                        <th>تصحيح السؤال</th>
                        <th>حذف</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                        $select_take_exams = $conn->prepare("SELECT exam_results.* FROM `exam_results` 
                                                join `exam_name` on exam_results.exam_id = exam_name.exam_id 
                                                join `users` on exam_results.user_id = users.id
                                                order by exam_name.exam_title asc, users.name asc" );

                        $select_take_exams->execute();
                        if(!empty($_GET['studentName'])){
                            $name = $_GET['studentName'] ;
                            $select_take_exams = $conn->prepare("SELECT exam_results.* FROM `exam_results` 
                                                join `exam_name` on exam_results.exam_id = exam_name.exam_id 
                                                join `users` on exam_results.user_id = users.id
                                                WHERE users.name LIKE '%$name%'
                                                order by exam_name.exam_title asc, users.name asc");
                            $select_take_exams->execute();
                        }
                        
                        if(!empty($_GET['examName'])){
                            $exam_name = $_GET['examName'] ;
                            $select_take_exams = $conn->prepare("SELECT exam_results.* FROM `exam_results` 
                                                join `exam_name` on exam_results.exam_id = exam_name.exam_id 
                                                join `users` on exam_results.user_id = users.id
                                                WHERE exam_name.exam_title LIKE '%$exam_name%'
                                                order by exam_name.exam_title asc, users.name asc");
                            $select_take_exams->execute();
                        }

                        while( $fetch_take_exams = $select_take_exams->fetch(PDO::FETCH_ASSOC)){
                            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                            $select_user->execute([$fetch_take_exams['user_id']]);
                            
                            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                            
                            $select_exam = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ? ");
                            $select_exam->execute([$fetch_take_exams['exam_id']]);
                            $fetch_exam = $select_exam->fetch(PDO::FETCH_ASSOC);
                            
                            $score = $conn->prepare("SELECT * FROM `exam_questions` WHERE exam_id = ?");
                            $score -> execute([$fetch_take_exams['exam_id']]);
                            $exam_degree = 0 ;
                            $user_degree = 0 ;
                            while($fetch_result = $score->fetch(PDO::FETCH_ASSOC)){
                                $exam_degree += $fetch_result['degree'];
                            }

                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$fetch_user['name']?></td>
                        <td><?=$fetch_user['gender']?></td>
                        <td><?php if($fetch_user['year'] == 1) echo'الصف الاول'; elseif($fetch_user['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td> <a href="mark_questions.php?get_id=<?=$fetch_exam['exam_id']?>&s_id=<?=$fetch_take_exams['user_id']?>" style="color: var(--orange);"><?=$fetch_exam['exam_title']?></a></td>
                        <td> <a href="show_exam.php?get_id=<?=$fetch_exam['exam_id']?>&s_id=<?=$fetch_take_exams['user_id']?>" style="color: var(--main-color);"><?=$fetch_exam['exam_title']?></a></td>
                        <td><?=$fetch_take_exams['degree'] . ' / ' . $exam_degree?></td>
                        <td class="edit-delete"><a href="edit_degree.php?get_id=<?=$fetch_exam['exam_id']?>&s_id=<?=$fetch_take_exams['user_id']?>" class="option-btn">تعديل</a></td>
                        <td class="edit-delete" >
                            <?php if(!$fetch_take_exams['show_degree']){?>
                                <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_take_exams['user_id']?>">
                                    <input type="hidden" name="exam_id" value="<?=$fetch_exam['exam_id']?>">
                                    <input type="submit" value="اظهار" class="btn" name="show">
                                </form>
                            <?php }else{?>
                                <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_take_exams['user_id']?>">
                                    <input type="hidden" name="exam_id" value="<?=$fetch_exam['exam_id']?>">
                                    <input type="submit" value="اخفاء" class="delete-btn" name="hide">
                                </form>
                            <?php }?>
                        </td>
                        <td>
                            <?php if(!$fetch_take_exams['Q_is_marked']){?>
                                <form action="" method="post"> 
                                    <input type="hidden" name="exam_id" value="<?=$fetch_exam['exam_id']?>">
                                    <input type="hidden" name="user_id" value="<?=$fetch_take_exams['user_id']?>">
                                    <input type="submit" name="marked" class="delete-btn" value="غير مصحح">
                                </form>  
                            <?php }else{?>
                                <form action="" method="post"> 
                                    <input type="hidden" name="exam_id" value="<?=$fetch_exam['exam_id']?>">
                                    <input type="hidden" name="user_id" value="<?=$fetch_take_exams['user_id']?>">
                                    <input type="submit" name="un_marked" class="btn" value="مصحح">
                                </form>   
                            <?php }?>

                        </td>
                        <td>
                            <form action="" method="post">
                                    <input type="hidden" name="exam_result_id" value="<?=$fetch_take_exams['exam_result_id']?>">
                                    <input type="hidden" name="exam_id" value="<?=$fetch_exam['exam_id']?>">
                                    <input type="hidden" name="user_id" value="<?=$fetch_take_exams['user_id']?>">
                                    <input type="submit" class="delete-btn" value="حذف" name="delete" onclick="return confirm('delete this exam?');">
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

</body>
</html>