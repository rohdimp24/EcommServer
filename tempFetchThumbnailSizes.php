<?php
/**
 * Created by PhpStorm.
 * User: rohitagarwal
 * Date: 9/6/15
 * Time: 5:59 AM
 */


require_once ('login.php');


$query="Select EbayItemId,thumbnail from EbayProductsForTx";
$result=mysql_query($query);
$rowsnum=mysql_num_rows($result);
for($i=0;$i<$rowsnum;$i++)
{

    $row=mysql_fetch_row($result);
    $thumbnail=$row[1];
    $size=getimagesize($thumbnail);
    $width=$size[0];
    $height=$size[1];

    //update db
    $updateQuery="UPDATE `EbayProductsForTx` SET `thumbnailWidth`='".$width."',`thumbnailHeight`='".$height."' WHERE `EbayItemId`='".$row[0]."'";
    $resultUpdate=mysql_query($updateQuery);
    if(!$resultUpdate)
    {
        echo "could not update for ".$updateQuery."<br/>";
    }


}