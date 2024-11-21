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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>التقارير</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
    <!--header section -->
<?php
include '../components/admin_header.php';
?>

<section class="comments">
    <h1 class="heading">تقارير الامتحانات</h1>
    <button class="inline-option-btn" onclick="exportTableToExcel('ExamsTable', 'ExamReports')">تصدير إلى Excel</button>
<br> <br>
    <div class="box-container">
        <div class="box">
            <table id="ExamsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الايميل</th>
                        <th>السنة</th>
                        <th>عنوان الامتحان</th>
                        <th>الدرجة</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                        $select_take_exams = $conn->prepare("SELECT * FROM `exam_results` ORDER BY degree DESC");
                        $select_take_exams->execute();
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

                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$fetch_user['name']?></td>
                        <td><?=$fetch_user['email']?></td>
                        <td><?php if($fetch_user['year'] == 1) echo'الصف الاول'; elseif($fetch_user['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td> <?=$fetch_exam['exam_title']?></td>
                        <td><?=$fetch_take_exams['degree'] . ' / ' . $exam_degree?></td>
                    
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
    <button class="inline-option-btn" onclick="exportHomeWorkToExcel('HomeWorkTable', 'HomeWorkReports')">تصدير إلى Excel</button>
    <br> <br>
    <div class="box-container">
        <div class="box">
            <table id="HomeWorkTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الايميل</th>
                        <th>السنة</th>
                        <th>الواجب</th>
                        <th>الدرجة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                        $homework = $conn->prepare("SELECT * FROM `homework` ORDER BY degree DESC");
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
                        <td><?=$fetch_user['email']?></td>
                        <td><?php if($fetch_user['year'] == 1) echo'الصف الاول'; elseif($fetch_user['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td><?=$fetch_content['title']?></td>
                        <td><?=$fetch_homework['degree'];?></td>
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
    <button class="inline-option-btn" onclick="exportFinalReportsToExcel('FinalReportsTable', 'FinalReports')">تصدير إلى Excel</button>
    <br> <br>
    <div class="box-container">
        <div class="box">
            <table id="FinalReportsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>السنة</th>
                        <th>الدرجة الكلية</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                    // استعلام لجلب بيانات الامتحانات
                        $stmtExams = $conn->prepare('SELECT user_id, year, SUM(degree) as total_exam_degree FROM exam_results GROUP BY user_id, year ORDER BY total_exam_degree DESC');
                        $stmtExams->execute();
                        $exams = $stmtExams->fetchAll();

                        // استعلام لجلب بيانات الواجبات
                        $stmtHomeworks = $conn->prepare('SELECT user_id, SUM(degree) as total_homework_degree FROM homework GROUP BY user_id ORDER BY total_homework_degree DESC');
                        $stmtHomeworks->execute();
                        $homeworks = $stmtHomeworks->fetchAll();

                        // دمج البيانات وحساب المجموع الكلي
                        $students = [];

                        foreach ($exams as $exam) {
                            $userId = $exam['user_id'];
                            $year = $exam['year'];
                            $totalExamDegree = $exam['total_exam_degree'];

                            if (!isset($students[$userId])) {
                                $students[$userId] = ['year' => $year, 'total_degree' => 0];
                            }

                            $students[$userId]['total_degree'] += $totalExamDegree;
                        }

                        foreach ($homeworks as $homework) {
                            $userId = $homework['user_id'];
                            $totalHomeworkDegree = $homework['total_homework_degree'];

                            if (!isset($students[$userId])) {
                                $students[$userId] = ['year' => $year, 'total_degree' => 0];
                            }

                            $students[$userId]['total_degree'] += $totalHomeworkDegree;
                        }

                        // استعلام لجلب أسماء الطلاب
                        $stmtStudents = $conn->prepare('SELECT id, name FROM users');
                        $stmtStudents->execute();
                        $studentNames = $stmtStudents->fetchAll(PDO::FETCH_KEY_PAIR);
                        foreach ($students as $userId => $data) {

                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$studentNames[$userId]?></td>
                        <td><?php if($data['year'] == 1) echo'الصف الاول'; elseif($data['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td><?=$data['total_degree'];?></td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script>
//////////////////////////  EXCEL /////////////////////////////////
    function exportTableToExcel(tableID, filename = ''){
        var table = document.getElementById(tableID);
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

        var blob = new Blob([s2ab(wbout)], {type: "application/octet-stream"});

        var link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename ? filename + '.xlsx' : 'ExamReports.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportHomeWorkToExcel(tableID, filename = ''){
        var table = document.getElementById(tableID);
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

        var blob = new Blob([s2ab(wbout)], {type: "application/octet-stream"});

        var link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename ? filename + '.xlsx' : 'HomeWorkReports.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportFinalReportsToExcel(tableID, filename = ''){
        var table = document.getElementById(tableID);
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

        var blob = new Blob([s2ab(wbout)], {type: "application/octet-stream"});

        var link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename ? filename + '.xlsx' : 'FinalReports.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }


</script>
<script src="../js/admin_script.js"></script>

</body>
</html>