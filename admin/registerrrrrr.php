<?php
include '../components/connect.php';
    if (isset($_COOKIE['tutor_id'])) {
        $tutor_id = $_COOKIE['tutor_id'];
    }
    else {
        $tutor_id = '';
    }

    if (isset($_POST['submit'])) {
        // Get the data from form
        $id             = create_unique_id();

        $name           = $_POST['name'];
        $name           = filter_var($name, FILTER_SANITIZE_STRING);
        
        $profession     = $_POST['profession'];
        $profession     = filter_var($profession, FILTER_SANITIZE_STRING);
        
        $email          = $_POST['email'];
        $email          = filter_var($email, FILTER_SANITIZE_STRING);
        
        $pass           = sha1($_POST['pass']);
        $pass           = filter_var($pass, FILTER_SANITIZE_STRING);
        
        $c_pass         = sha1($_POST['c_pass']);
        $c_pass         = filter_var($c_pass, FILTER_SANITIZE_STRING);

        $image          = $_FILES['image']['name'];
        $image          = filter_var($image, FILTER_SANITIZE_STRING);
        
        $ext            = pathinfo($image, PATHINFO_EXTENSION);
        $rename         = create_unique_id().'.'.$ext;
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size     = $_FILES['image']['size'];
        $image_folder   = '../uploaded_files/'.$rename;

        // select from database************
        $select_tutor_email = $conn->prepare("SELECT * FROM `tutors` WHERE email=?");
        $select_tutor_email->execute([$email]);
        if ($select_tutor_email->rowCount() > 0) {
            $message[] = 'email already taken!';
        }
        else{
            if ($pass != $c_pass) {
                $message[] = 'password not matched!'; 
            }
            else{
                if ($image_size > 2000000) {
                    $message[] = "Image size is too large.";
                }else{
                    // insert tutor into database
                    $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, email, password, image)
                    VALUE(?,?,?,?,?,?)");
                    $insert_tutor->execute([$id, $name, $profession, $email, $c_pass, $rename]);
                    move_uploaded_file($image_tmp_name, $image_folder);
                
                    $verify_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email=? AND password =? LIMIT 1");
                    $verify_tutor->execute([$email,$c_pass]);
                    $row = $verify_tutor->fetch(PDO::FETCH_ASSOC);
                    if($insert_tutor){
                        if ($verify_tutor->rowCount() > 0) {
                                setcookie('tutor_id', $row['id'], time() + 60*60*24*30,'/');
                                header("Location: dashboard.php");
                        }else{
                            $message[] = 'something went wrong!';
    
                        }
                    }
                
                }


            }
        }
        
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body style="padding-left:0;">


<?php
if (isset($message)) {
    foreach($message as $msg){
        echo '
            <div class="message form">
                <span>'.$msg.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
        
        ';
    }
}


?>


<!-- register section start -->
<section class="form-container">
    
    <form action="" method="post" enctype="multipart/form-data">
        <h3>register as tutor</h3>
        <div class="flex">
            <div class="col">
                <p>your name <span>*</span></p>
                <input type="text" name="name" maxlength="50" required
                placeholder="enter your name" class="box">

                <p>your profession <span>*</span></p>
                <select type="text" name="profession" required class="box">
                    <option value="" disabled selected>-- select your profession</option>
                    <option value="developer">Developer</option>
                    <option value="english Teacher">English Teacher</option>
                    <option value="math Teacher">Math Teacher</option>
                    <option value="bio Teacher">Bio Teacher</option>
                    <option value="spanish Teacher">Spanish Teacher</option>
                </select>

                <p>your email <span>*</span></p>
                <input type="email" name="email" maxlength="50" required
                placeholder="enter your email" class="box">
            </div>

            <div class="col">
                <p>your password <span>*</span></p>
                    <input type="password" name="pass" maxlength="20" required
                    placeholder="enter your password" class="box">
                
                <p>confirm password <span>*</span></p>
                    <input type="password" name="c_pass" maxlength="20" required
                    placeholder="confirm your password" class="box">
                
                <p>select pic <span>*</span></p>
                    <input type="file" name="image" class="box" required
                    accept="image/*">
            </div>
        </div>
        <input type="submit" name="submit" value="register now" class="btn">
        <p class="link">already have an account? <a href="index.php">login now</a></p>
    </form>
</section>
<!-- register section end -->











<script src="../js/admin_script.js"></script>
</body>
</html>