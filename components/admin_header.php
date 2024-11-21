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


?>
<!-- header section starts -->
<header class="header">
    <section class="flex">
        <a href="dashboard.php" class="logo">المسؤل</a>

        <!-- <form action="search_page.php" method="post" class="search-form">
            <input type="text" name="search_box" placeholder="بحث..." 
                maxlen="100" required/>
            <button type="submit" class="fas fa-search" name="search_btn"> </button>
        </form> -->
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="toggle-btn" class="fas fa-moon"></div>
        </div>

        <div class="profile">
            <?php
                $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id=?");
                $select_profile->execute([$tutor_id]);
                if($select_profile->rowCount() > 0){
                    $fetch_profile =  $select_profile->fetch(PDO::FETCH_ASSOC) ;
                
            ?>
            <img src="../uploaded_files/<?= $fetch_profile['image'];?>" alt="">
            <h3><?= $fetch_profile['name'];?></h3>
            <span><?= $fetch_profile['profession'];?></span>
            <a href="profile.php" class="btn">عرض البروفايل</a>
            <!-- <div class="flex-btn">
                <a href="login.php" class="option-btn">login</a>
                <a href="register.php" class="option-btn">register</a>
            </div> -->
            <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?')" class="delete-btn">تسجيل خروج</a>

            <?php 
            
                }else{
            ?>    
                    <h3>سجل دخول اولا</h3>
                    <div class="flex-btn">
                        <a href="index.php" class="option-btn">تسجيل دخول</a>
                        <a href="register.php" class="option-btn">انشاء حساب</a>
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
                $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id=?");
                $select_profile->execute([$tutor_id]);
                if($select_profile->rowCount() > 0){
                    $fetch_profile =  $select_profile->fetch(PDO::FETCH_ASSOC) ;
                
            ?>
            <img src="../uploaded_files/<?= $fetch_profile['image'];?>" alt="">
            <h3><?= $fetch_profile['name'];?></h3>
            <span><?= $fetch_profile['profession'];?></span>
            <a href="profile.php" class="btn">عرض البروفايل</a>
            

            <?php 
            
                }else{
            ?>    
                    <h3>سجل دخول اولا</h3>
                    <div class="flex-btn">
                        <a href="index.php" class="option-btn">تسجيل دخول</a>
                        <a href="register.php" class="option-btn">انشاء حساب</a>
                    </div>
            
            <?php 
                
                }
            ?>
            <?php
                $message = $conn->prepare("SELECT * FROM `contact` WHERE see !=1"); 
                $message->execute();
                $count_messages = $message->rowCount();
            ?>
            <?php
                $comment = $conn->prepare("SELECT * FROM `comments` WHERE approve !=1"); 
                $comment->execute();
                $count_comments = $comment->rowCount();
            ?>
        </div>
        <nav class="navbar">
            <a href="dashboard.php"><i class="fas fa-home"></i><span>الصفحة الرئيسية</span></a>
            <a href="playlists.php"><i class="fas fa-bars-staggered"></i><span>الوحدات</span></a>
            <a href="contents.php"><i class="fas fa-graduation-cap"></i><span>الدروس (االمحتوي)</span></a>
            <a href="comments.php" style="position: relative; color:var(--light-color);">
                <i class="fas fa-comment">
                </i>
                <?php if($count_comments)
                    {
                ?>
                <span style="background-color: red; position:absolute; font-size:12px; right: -1px;  top: 3px; color: #fff; padding: 2px 5px; border-radius: 50%;">
                    <?=$count_comments ?>
                </span>
                <?php } ?>
                التعليقات
            </a>
            <a href="messages.php" style="position: relative; color:var(--light-color);">
                <i class="fa-solid fa-message"></i>
                <?php if($count_messages)
                    {
                ?>
                <span style="background-color: red; position:absolute; font-size:12px; right: -1px;  top: 3px; color: #fff; padding: 2px 5px; border-radius: 50%;">
                    <?=$count_messages ?>
                </span>
                <?php } ?>
                الرسائل
            </a>
            <a href="exams.php"><i class="fa-solid fa-book"></i><span>الامتحانات</span></a>
            <a href="slider.php"><i class="fa-regular fa-image"></i><span>اضافة صور</span></a>

            <a href="registerStudent.php"><i class="fa-solid fa-user-plus"></i><span>تسجيل الدخول للطالب</span></a>

            <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?')"><i class="fas fa-right-from-bracket"></i><span>تسجيل خروج</span></a>
        </nav>
</div>
<!-- side bar section end -->