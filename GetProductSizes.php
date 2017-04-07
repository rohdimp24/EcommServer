<?php
require_once ('login.php');

$query="Select Distinct(Size) from EbayTransactions";

$result= mysql_query($query);
$rowsnum = mysql_num_rows($result);

$arrData=array();
$count=1;

for($j=0;$j<$rowsnum;$j++)
{
    $row=mysql_fetch_row($result);
	


    $obj = new StdClass();
    $obj->size=trim($row[0]);
    /*$obj->commentingUser=$commentingUser;
    $obj->comment=$comment;
    $obj->commentTime=$commentTime;
    $obj->feedbackType=$feedbackType;
    $obj->score=$score;
    $obj->itemId=$itemId;
    $obj->itemTitle=$itemTitle;
    // print_r($obj);
    */
	array_push($arrData,$obj);


}


echo json_encode($arrData);
//return;




?>
