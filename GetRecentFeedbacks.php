<?php
require_once ('login.php');

$query="select `FeedbackID`, `FeedbackUser`, `Comment`, `CommentTime`, `FeedbackType`,
        `FeedbackScore`, `ItemId`, `ItemTitle` from EbayFeedbacks order by CommentTime DESC LIMIT 500";

$result= mysql_query($query);
$rowsnum = mysql_num_rows($result);

if($rowsnum>1000)
    $rowsnum=1000;

$arrData=array();
$count=1;

for($j=0;$j<$rowsnum;$j++)
{
    $row=mysql_fetch_row($result);
    $feedbackId=$row[0];
    $commentingUser=$row[1];
    $comment=$row[2];
    $commentTime=$row[3];
    $feedbackType=$row[4];
    $score=$row[5];
    $itemId=$row[6];
    $itemTitle=$row[7];



    $obj = new StdClass();
    $obj->feedbackId=$feedbackId;
    $obj->commentingUser=$commentingUser;
    $obj->comment=$comment;
    $obj->commentTime=$commentTime;
    $obj->feedbackType=$feedbackType;
    $obj->score=$score;
    $obj->itemId=$itemId;
    $obj->itemTitle=$itemTitle;
    // print_r($obj);
    array_push($arrData,$obj);


}


echo json_encode($arrData);
//return;




?>
