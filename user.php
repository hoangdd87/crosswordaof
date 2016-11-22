<?php
/**
 * Created by PhpStorm.
 * User: HoangDD
 * Date: 10/16/16
 * Time: 12:21 PM
 */

//Check if the user logon already
include_once __DIR__ . '/resource/SESSTION.php';
include_once __DIR__ . '/util/PDOHelper.php';
include_once __DIR__ . '/model/User_Answer.php';
include_once __DIR__ . '/model/User_Answer_Factory.php';
include_once __DIR__.'/util/Timer.php';

$mysession = new SESSTION();
$pdoHelper = new PDOHelper();

if ($mysession->is_User_logon_empty()) {
    header('location: login.php');
    exit;
}
$user_logon = $mysession->get_User_Logon();
if ($user_logon->role <> "user") {
    header('location: logout.php');
    exit;
}

?>
<h2>
    <?php echo "Hello $user_logon->user_name" ?>
</h2>
<p style="text-align:right">
    <a href="logout.php">Logout</a>
</p>

<?php

if (isset($_POST['btnSubmit'])) {

    $answser = $_POST['answer'];

    $currentTimeString = Timer::getStringCurrentTimeWithMilisecond();
    $user_answer = User_Answer_Factory::create($user_logon->user_name, $currentTimeString, $answser, 0);
    $pdoHelper->insert_User_Answer($user_answer);
    $question=$pdoHelper->get_Question_From_User_Answer($user_answer);
}
?>
<?php
$user_answers = $pdoHelper->get_All_User_Answer($user_logon->user_name);
for ($i = 0; $i <= count($user_answers) - 1; $i++) {
    $user_answer = $user_answers[$i];
    $question=$pdoHelper->get_Question_From_User_Answer($user_answer);
    $time_cost=!empty($question)?Timer::getDiff($question->time_begin,$user_answer->time_answer):0;
    if ($i < count($user_answers) - 1) {
        echo "<p>You answered: <span style=\"color:#FF0000;\">$user_answer->answer</span>  at $time_cost s (Question $question->question_id)</p>";
    }
    else{
        echo "<p><b>Your last answer: <span style=\"color:#1B0CFF;\">$user_answer->answer</span>  at $time_cost s (Question $question->question_id)</b></b></p>";
    }

}
echo '<p id="clientTime"> </p>'

?>
<form action="" method="post">
    <label>Enter your answer :</label>
    <input type="text" name="answer" class="form-control" autofocus required>

    <button type="submit" name="btnSubmit" class="btn btn-primary pull-right" onclick="showClientTime()" >Submit</button>

</form>
<script>
    function showClientTime() {
        currentTime=new Date();
        current_hour=currentTime.getHours();
        current_minute=currentTime.getMinutes();
        current_second=currentTime.getSeconds();
        currentTimeString=current_hour.toString()+"H "+current_minute.toString()+"m"+current_second.toString()+"s";
}
</script>


