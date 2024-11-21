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
if (isset($_POST['active'])) {
    $user_id = $_POST['user_id'];
    $activ_user = $conn->prepare("UPDATE `users` SET status=? WHERE id=?");
    $activ_user->execute([0,$user_id]);
}
if (isset($_POST['unactive'])) {
    $user_id = $_POST['user_id'];
    $unactive = $conn->prepare("UPDATE `users` SET last_login=? , status=? WHERE id=? ");
    $unactive->execute([time(), 1, $user_id]);
}
if (isset($_POST['logout'])) {
    $user_id = $_POST['user_id'];
    $unactive = $conn->prepare("UPDATE `users` SET is_logged_in=? , session_id=? WHERE id=? ");
    $unactive->execute([0, null, $user_id]);
}
if (isset($_POST['delete'])) {

    $user_id = $_POST['user_id'];
    $user = $conn->prepare("DELETE FROM `users` WHERE id= ?");
    $user->execute([$user_id]);
}
if (isset($_POST['show_all'])) {
    $activ_user = $conn->prepare("UPDATE `users` SET status=?");
    $activ_user->execute([1]);
}
if (isset($_POST['hide_all'])) {
    $unactive = $conn->prepare("UPDATE `users` SET last_login=? , status=? ");
    $unactive->execute([time(), 0]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الطلاب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
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
        }
    </style>
</head>
<body>
    <!--header section -->
<?php
include '../components/admin_header.php';
?>

<h2 class="heading">بحث عن الطلاب</h2>
<form method="POST" class="search">
    <input type="text" class="box searchInput" name="studentName" required placeholder="ابحث بالاسم فقط !!"/>
    <button type="submit" name="ser" class="fas fa-search"></button>
</form>

<section class="comments">
    <h1 class="heading">الطلاب</h1>
    <div class="flex">
        <button class="inline-option-btn ex" onclick="exportTableToExcel('studentTable', 'students')">Excel</button>
        
        <!-- <div class="search">
            <input type="text" class="box searchInput" name="search_box" placeholder="ابحث بالاسم فقط !!" maxlen="100" required/>
            <button onclick="searchStudent()" type="submit" class="fas fa-search" name="search_btn"> </button>
        </div> -->
    </div>
    <div class="box-container">
        <?php if($_GET['gender'] == 'male') {
        ?>
        <div class="box">
        <div style="display: flex; gap: 10px;">
                <form action="" method="post">
                    <button class="inline-btn" name="show_all">تفعيل الكل </button>
                </form>
                <form action="" method="post">
                    <button class="delete-btn" name="hide_all">الغاء تفعيل الكل </button>
                </form>
        </div>
            <br><br>
            <table id="studentTable" class="studentsTable">
                <thead>
                    <tr>
                        <th style="font-size: 12px;">#</th>
                        <th style="font-size: 12px;">الصورة</th>
                        <th style="font-size: 12px;">الاسم</th>
                        <th style="font-size: 12px;">الايميل</th>
                        <th style="font-size: 12px;">رقم الهاتف</th>
                        <th style="font-size: 12px;">رقم ولي الامر</th>
                        <th style="font-size: 12px;">السنة</th>
                        <th style="font-size: 12px;">الحضور</th>
                        <th style="font-size: 12px;">المحافظة</th>
                        <th style="font-size: 12px;">تعديل</th>
                        <th style="font-size: 12px;">الحالة</th>
                        <th style="font-size: 12px;">تسجيل خروج</th>
                        <th style="font-size: 12px;">حذف</th>
                        <th style="font-size: 12px;">اضافة نقاط</th>
                        <th style="font-size: 12px;">مجموع النقاط</th>
                    </tr>
                </thead> 
                <tbody>
                    <?php
                    $i=1;
                    
                        $select_males = $conn->prepare("SELECT * FROM `users` WHERE gender = ? ORDER BY name ASC");
                        $select_males->execute(['male']);
                        if(!empty($_POST['studentName'])){
                            $name = $_POST['studentName'] ;
                            $select_males = $conn->prepare("SELECT * FROM users WHERE name LIKE '%$name%'");
                            $select_males->execute();
                        }
                        while( $fetch_males = $select_males->fetch(PDO::FETCH_ASSOC)){

                    ?>
                    <tr>
                        <td style="font-size: 12px;"><?=$i?></td>
                        <?php
                        if($fetch_males['image']){
                        ?>
                        <td><img src="../uploaded_files/<?=$fetch_males['image']?>" style="width: 100px; height: 100px;" alt="<?=$fetch_males['name']?>"></td>
                        <?php
                        }else{
                            echo '<td style="color:red;">لا توجد صورة</td>';
                        }
                        ?>
                        <td style="font-size: 12px;"><?=$fetch_males['name']?></td>
                        <td style="font-size: 12px;"><?=$fetch_males['email']?></td>
                        <td style="font-size: 12px;"><?=$fetch_males['number']?></td>
                        <td style="font-size: 12px;"><?=$fetch_males['parent_num']?></td>
                        <td style="font-size: 12px;"><?php if($fetch_males['year'] == 1) echo'الصف الاول'; elseif($fetch_males['year']==2)echo 'الصف الثاني'; else echo'الصف الثالث'; ?></td>
                        <td style="font-size: 12px;"><?=$fetch_males['register'];?></td>
                        <td style="font-size: 12px;"><?=$fetch_males['governorate'];?></td>
                        <td style="font-size: 12px;"><a href="edit_student.php?s_id=<?=$fetch_males['id']?>" class="option-btn">تعديل</a></td>
                        <td style="font-size: 12px;">
                        <?php if($fetch_males['status']){?>
                                <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_males['id']?>">
                                    <input type="submit" value="مفعل" class="btn" name="active">
                                
                                </form>
                            <?php 
                                
                        }else{?>
                                <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_males['id']?>">
                                    <input type="submit" class="delete-btn" value="غير مفعل" name="unactive" >
                                </form>
                                
                            <?php }  ?>

                        </td>
                        <td style="font-size: 12px;">
                        <?php if($fetch_males['is_logged_in']){?>                                
                            <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_males['id']?>">
                                    <input type="submit" class="delete-btn" value="خروج" name="logout" >
                            </form>
                        <?php } else {?>
                            <p>الطالب لم يسجل بعد </p>
                        <?php }  ?>
                        </td>
                        <td style="font-size: 12px;">
                            <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_males['id']?>">
                                    <input type="submit" class="delete-btn" value="حذف" name="delete" onclick="return confirm('delete this student?');">
                            </form>
                        </td >
                        <td style="font-size: 12px;"><a href="addPoints.php?s_id=<?=$fetch_males['id']?>" class="btn">اضافة</a></td>
                        <td style="font-size: 12px;"><?=$fetch_males['points'];?></td>
                    </tr>
                    <?php 
                        $i++;
                    }
                    ?>
                
                </tbody>
            </table>
        </div>
        <?php } elseif($_GET['gender'] == 'female'){?>
            <div class="box">
            <div style="display: flex; gap: 10px;">
                <form action="" method="post">
                    <button class="inline-btn" name="show_all">تفعيل الكل </button>
                </form>
                <form action="" method="post">
                    <button class="delete-btn" name="hide_all">الغاء تفعيل الكل </button>
                </form>
            </div>
            <br><br>
            <table id="studentTable" class="studentsTable">
                <thead>
                    <tr>
                        <th style="font-size: 12px;">#</th>
                        <th style="font-size: 12px;">الصورة</th>
                        <th style="font-size: 12px;">الاسم</th>
                        <th style="font-size: 12px;">الايميل</th>
                        <th style="font-size: 12px;">رقم الهاتف</th>
                        <th style="font-size: 12px;">رقم ولي الامر</th>
                        <th style="font-size: 12px;">السنة</th>
                        <th style="font-size: 12px;">الحضور</th>
                        <th style="font-size: 12px;">المحافظة</th>
                        <th style="font-size: 12px;">تعديل</th>
                        <th style="font-size: 12px;">الحالة</th>
                        <th style="font-size: 12px;">تسجيل خروج</th>
                        <th style="font-size: 12px;">حذف</th>
                        <th style="font-size: 12px;">اضافة نقاط</th>
                        <th style="font-size: 12px;">مجموع النقاط</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                        $select_females = $conn->prepare("SELECT * FROM `users` WHERE gender = ? ORDER BY name ASC");
                        $select_females->execute(['female']);
                        if(!empty($_POST['studentName'])){
                            $name = $_POST['studentName'] ;
                            $select_females = $conn->prepare("SELECT * FROM users WHERE name LIKE '%$name%'");
                            $select_females->execute();
                        }
                        while( $fetch_females = $select_females->fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <tr>
                        <td style="font-size: 12px;"><?=$i?></td>
                        <?php
                        if($fetch_females['image']){
                        ?>
                        <td><img src="../uploaded_files/<?=$fetch_females['image']?>" style="width: 100px; height: 100px;" alt="<?=$fetch_females['name']?>"></td>
                        <?php
                        }else{
                            echo '<td style="color:red;">لا توجد صورة</td>';
                        }
                        ?>
                        <td style="font-size: 12px;"><?=$fetch_females['name']?></td>
                        <td style="font-size: 12px;"><?=$fetch_females['email']?></td>
                        <td style="font-size: 12px;"><?=$fetch_females['number']?></td>
                        <td style="font-size: 12px;"><?=$fetch_females['parent_num']?></td>
                        <td style="font-size: 12px;"><?php if($fetch_females['year'] == 1) echo'الصف الاول'; elseif($fetch_females['year']==2)echo 'الصف الثاني'; else echo'االصف الثالث'; ?></td>
                        <td style="font-size: 12px;"><?=$fetch_females['register'];?></td>
                        <td style="font-size: 12px;"><?=$fetch_females['governorate'];?></td>
                        <td style="font-size: 12px;"><a href="edit_student.php?s_id=<?=$fetch_females['id']?>" class="option-btn">تعديل</a></td>
                        <td style="font-size: 12px;">
                        <?php if($fetch_females['status']){?>
                                <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_females['id']?>">
                                    <input type="submit" value="مفعل" class="btn" name="active">
                                
                                </form>
                            <?php }else{?>
                                <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_females['id']?>">
                                    <input type="submit" class="delete-btn" value="غير مفعل" name="unactive" >
                                </form>
                            <?php }?>
                        </td>
                        <td style="font-size: 12px;">
                        <?php if($fetch_females['is_logged_in']){?>                                
                            <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_females['id']?>">
                                    <input type="submit" class="delete-btn" value="خروج" name="logout" >
                            </form>
                        <?php } else {?>
                            <p>الطالب لم يسجل بعد </p>
                        <?php }  ?>
                        </td>
                        <td style="font-size: 12px;">
                            <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?=$fetch_females['id']?>">
                                    <input type="submit" class="delete-btn" value="حذف" name="delete" onclick="return confirm('delete this student?');">
                            </form>
                        </td>
                        <td style="font-size: 12px;"><a href="addPoints.php?s_id=<?=$fetch_females['id']?>" class="btn">اضافة</a></td>
                        <td style="font-size: 12px;"><?=$fetch_females['points'];?></td>
                    </tr>
                    <?php 
                        $i++;
                    }
                    ?>
                
                </tbody>
            </table>
        </div>
        <?php }?>
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
    link.download = filename ? filename + '.xlsx' : 'students.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
    
        // function searchStudent() {
        //     const input = document.querySelector('.searchInput').value.toLowerCase();
        //     const table = document.querySelector('.studentsTable');
        //     const rows = table.getElementsByTagName('tr');
        //     let found = false;

        //     // إزالة التمييز عن جميع الأسماء
        //     for (let i = 1; i < rows.length; i++) {
        //         rows[i].classList.remove('highlight');
        //     }

        //     for (let i = 1; i < rows.length; i++) {
        //         const cell = rows[i].getElementsByTagName('td')[1]; // العمود الأول (الاسم)
        //         if (cell) {
        //             const cellText = cell.textContent || cell.innerText;
        //             if (cellText.toLowerCase().includes(input)) {
        //                 rows[i].classList.add('highlight');
        //                 rows[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
        //                 found = true;
        //                 break; // قم بإيقاف البحث بعد العثور على الاسم
        //             }
        //         }
        //     }

        //     if (!found) {
        //         alert('لم يتم العثور على الطالب.');
        //     }
        // }
    </script>
<script src="../js/admin_script.js"></script>
</body>
</html>