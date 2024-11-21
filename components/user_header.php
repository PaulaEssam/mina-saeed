<?php
if (isset($message)) {
    foreach($message as $msg){
        echo '
            <div class="message">
                <span>'.$msg.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
        
        ';
    }
}
$check = false ;

?>
<!-- header section starts -->
<header class="header">
    <section class="flex">
        <a href="index.php" class="logo"><img style="width: 80px; height: 80px; border-radius: 50%;" src="imgs/logo.jpeg" alt=""></a>
        

        <form action="search_course.php" method="post" class="search-form">
            <input type="text" name="search_box" placeholder="بحث..." 
                maxlen="100" required/>
            <button type="submit" class="fas fa-search" name="search_btn"> </button>
        </form>
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="toggle-btn" class="fas fa-moon"></div>
        </div>

        <div class="profile">
            <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id=?");
                $select_profile->execute([$user_id]);
                if($select_profile->rowCount() > 0){
                    $fetch_profile =  $select_profile->fetch(PDO::FETCH_ASSOC) ;
                
            ?>
            <h3><?= $fetch_profile['name'];?></h3>
            <span>طالب: </span>
            <span><?php if($fetch_profile['year'] == 1 ) echo "الصف الاول"; elseif($fetch_profile['year'] == 2 ) echo "الصف الثاني"; else echo "الصف الثالث";?></span>
            <a href="profile.php" class="btn">عرض البروفايل</a>
            <div class="flex-btn">
                <!-- <a href="login.php" class="option-btn">login</a> -->
                <!-- <a href="register.php" class="option-btn">register</a> -->
                <a href="components/user_logout.php" onclick="return confirm('هل تريد تسجيل الخروج ؟ ')" class="delete-btn">تسجيل خروج</a>
            </div>
            

            <?php 
            
                }else{
            ?>    
                    <h3>سجل دخول اولا</h3>
                    <div class="flex-btn">
                        <a style="font-size: 14px; font-weight: bold;" href="login.php" class="option-btn">تسجيل دخول</a>
                        <a style="font-size: 14px; font-weight: bold;" href="register.php" class="option-btn">انشاء حساب </a>
                    </div>
            
            <?php 
                

                }
            ?>
        </div>
    </section>
</header>
<!-- header section ends -->


<!-- side bar section start -->
<div class="side-bar">
        <div class="close-side-bar">
            <i class="fas fa-times"></i>
        </div>
        <div class="profile">
            <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id=?");
                $select_profile->execute([$user_id]);
                if($select_profile->rowCount() > 0){
                    $fetch_profile =  $select_profile->fetch(PDO::FETCH_ASSOC) ;
                $check = true ;
            ?>
            <?php if($fetch_profile['image']){ ?>
                <img src="uploaded_files/<?= $fetch_profile['image'];?>" alt="">
            <?php }else{ ?>
                <img src="imgs/avatar.jpg" alt="">
            <?php } ?>
            <h3><?= $fetch_profile['name'];?></h3>
            <span>طالب: </span>
            <span><?php if($fetch_profile['year'] == 1 ) echo "الصف الاول"; elseif($fetch_profile['year'] == 2 ) echo "الصف الثاني"; else echo "الصف الثالث";?></span>
            <a href="profile.php" class="btn">عرض البروفايل</a>
            

            <?php 
            
                }else{
            ?>    
                    <h3>سجل دخول اولا</h3>
                    <div class="flex-btn">
                        <a style="font-size: 14px; font-weight: bold;" href="login.php" class="option-btn">تسجيل دخول</a>
                        <a style="font-size: 14px; font-weight: bold;" href="register.php" class="option-btn">انشاء حساب </a>
                    </div>
            
            <?php 
                

                }
            ?>
        </div>
        <nav class="navbar1">
            <a href="index.php" ><i class="fas fa-home"></i><span>الصفحة الرئيسية</span></a>
            <a href="about.php" ><i class="fas fa-question"></i><span>من نحن</span></a>
            <a href="courses.php" ><i class="fas fa-graduation-cap"></i><span>الكورسات </span></a>
            <a href="teachers.php" ><i class="fas fa-chalkboard-user"></i><span>المدرس</span></a>
            <a href="contact.php" ><i class="fas fa-headset"></i><span>تواصل معنا</span></a>
            <?php if ($check){?>
            <a href="components/user_logout.php" onclick="return confirm('هل تريد تسجيل الخروج ؟ ')"><i class="fas fa-right-from-bracket"></i><span>تسجيل خروج</span></a>
<?php }?>
        </nav>
</div>
<!-- side bar section end -->