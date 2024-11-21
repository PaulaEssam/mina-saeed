<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}
else{
    $user_id = '';
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<!-- header section start -->
<?php include 'components/user_header.php';?>
<!-- header section end -->


<!-- about section start -->
<section class="about">
    <div class="row">
        <div class="image">
            <img src="imgs/about-img.png" alt="">
        </div>

        <div class="content">
            <h3>لماذا نحن ؟ </h3>
            <p>
                المنصة الاولي الرائدة في تعلم الرياضيات 
                <br>
                <span style="font-weight:bold; color:var(--orange); font-size:20px;">الرياضيات تختلف مع معلم محترف </span>
            </p>
            <a href="courses.php" class="inline-btn">كورساتنا</a>
        </div>
    </div>
    <div class="box-container">
        <div class="box">
            <i class="fas fa-graduation-cap"></i>
            <div>
                <h3>+10k</h3>
                <span>كورسات اونلاين</span>
            </div>
        </div>
        
        <div class="box">
            <i class="fas fa-user-graduate"></i>
            <div>
                <h3>+25k</h3>
                <span>طالب</span>
            </div>
        </div>

        <!-- <div class="box">
            <i class="fas fa-chalkboard-user"></i>
            <div>
                <h3>+5k</h3>
                <span>expert teachers</span>
            </div>
        </div>

        <div class="box">
            <i class="fas fa-briefcase"></i>
            <div>
                <h3>100%</h3>
                <span>job placement</span>
            </div>
        </div> -->

    </div>
</section>
<!-- about section end -->


<!-- reviews section start -->
<section class="reviews">
    <h1 class="heading">الرياضيات مفتاح الابداع </h1>
    <div class="box-container">
        <div class="box">
            
            <ul><h3 class="heading" style="font-size: 20px;">تقدم منصة الرياضيات مفتاح الابداع  لطلاب المرحلة الاعدادية في مادة الرياضيات :</h3>
                <li style="font-size: 18px;"><p>شرح وتبسيط المعلومات: يتميز المحتوى المقدم على المنصة بالشرح الواضح والبسيط للمفاهيم الصعبة، مما يسهل على الطلاب استيعاب المادة العلمية</p>.</li>
                <li style="font-size: 18px;"><p>تسجيل الواجبات وشرحها: تتيح المنصة للطلاب تسجيل واجباتهم وتوفر لهم شروحات مفصلة لمساعدتهم في إنجازها.</p> </li>
                <li style="font-size: 18px;"><p>امتحانات دورية علي كل حصة وامتحانات شاملة علي كل وحدة لتقييم وتعزيز مستوي الطلاب</p></li>
                <li style="font-size: 18px;"> <p>متابعة الطلاب: تُتيح المنصة للمعلم إمكانية متابعة تقدم الطلاب وأدائهم من خلال المنصة وقنوات التواصل الاجتماعي.</p></li>
                <li style="font-size: 18px;"> <p>تكريم الأوائل: تحفز المنصة الطلاب المتميزين من خلال تكريمهم وتقديم الجوائز لهم.</p></li>
                <li style="font-size: 18px;"> <p>محتوى تفاعلي: تضم المنصة محتوى تفاعلي كالعروض التقديمية والرسوم البيانية لتعزيز استيعاب الطلاب للمادة العلمية.</p></li>
                <li style="font-size: 18px;"><p>  الدعم اللازم للمعلمين والطلاب على حد سواء من خلال الأدوات والميزات المتنوعة.</p></li>
                <li style="font-size: 18px;"> <p>واجهة سهلة الاستخدام: تتميز المنصة بواجهة استخدام بسيطة وسلسة، مما يسهل على الطلاب التعامل معها.</p></li>
                <li style="font-size: 18px;"><p> محتوى متنوع ومتجدد: تضم المنصة محتوى تعليمي متنوع ومتجدد بشكل مستمر لمواكبة احتياجات الطلاب.</p></li>
                <li style="font-size: 18px;"><p> مع الرياضيات مفتاح الابداع  انت في أمان </p></li>
                <li style="font-size: 18px;"><p> متجاوبة مع جميع الاجهزة </p></li>
            </ul>
        </div>

        
        </div>
</section>
<!-- reviews section end -->










<!-- footer section start -->
<?php include 'components/footer.php';?>
<!-- footer section end -->

<script src="js/script.js"></script>
<script>document.addEventListener('contextmenu', function(e) {e.preventDefault();});</script>
</body>
</html>