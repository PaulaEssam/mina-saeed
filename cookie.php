<?php
// include 'components/connect.php';

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];

    // $select_exam_time = $conn->prepare("SELECT * FROM `exam_name` WHERE exam_id = ?");
    // $select_exam_time->execute([$get_id]);
    // $fetch_exam_time = $select_exam_time->fetch(PDO::FETCH_ASSOC);
    // $exam_time = $fetch_exam_time['exam_time'] ;

    setcookie('login_time', time());
    header("Location: exam.php?get_id=$get_id");

}
else{
    $get_id = '';
    header("Location: courses.php");
}
