<?php
require_once ('login.php');



//echo "hi i am in ".$month;
//$lines.="<H2>Displaying top results for ".$month ."</H2><br/>";
$arrData=array();
// $lines='';
$todaysDate=date('Y-m-d');

//check for the products which have been discontinued..we need to remove them. also need to see what to do for the products
//which are not in the ebay table..need to see if they are valid or not.
$time_start = microtime(true);
$queryInactiveProducts="Select EbayItemId from EbayProductsForTx where continueDiscontinue=1";
$resultInactiveProducts=mysql_query($queryInactiveProducts);
$rowsnumInactive=mysql_num_rows($resultInactiveProducts);
$arrayInactiveProducts=array();
for($j=0;$j<$rowsnumInactive;$j++)
{
    $row=mysql_fetch_row($resultInactiveProducts);
     array_push($arrayInactiveProducts, $row[0]);    
}



//this is taking about 0.03 sec
//get the items which are not existing enaymore in ebay store, but we have done the sales earlier
$queryNonExisting="SELECT distinct(ItemId) from EbayTransactions where itemId not in (select EbayItemId from EbayProductsForTx)";
$resultNonExisting=mysql_query($queryNonExisting);
$rowsnumNonExisting=mysql_num_rows($resultNonExisting);
$arrayNonExisting=array();
for($j=0;$j<$rowsnumNonExisting;$j++)
{
    $row=mysql_fetch_row($resultNonExisting);
     array_push($arrayNonExisting, $row[0]);    
}


//print_r($arrayNonExisting);




$query="SELECT ItemId,Title,SKU,max(CreationDate), min(DATEDIFF('".$todaysDate."',CreationDate)) AS DiffDate,sellingPrice from EbayTransactions Where sku!='' group by ItemId  ORDER BY `DiffDate`";

//echo $query;
$result= mysql_query($query);
$rowsnum = mysql_num_rows($result);
$debug=0;
for($j=0;$j<$rowsnum;$j++)
{

    $row=mysql_fetch_row($result);
    $numMonths=intval($row[4]/30);
    $itemId=$row[0];
    //!in_array($row[0],$arrayInactiveProducts)||
    //if the item is not in non-existing array that is they still exists
    if(!array_key_exists($itemId, $arrayNonExisting))
    {
        //if the item is not inactive that is its a valid one
        if(!array_key_exists($itemId, $arrayInactiveProducts))
        {
            //$obj=new Item($itemId,$row[1],$row[2],$row[3],$row[4],$numMonths);
            $obj = new StdClass();
            $obj->itemId=$itemId;
            $obj->title=$row[1];
            $obj->sku=$row[2];
            $obj->sellingPrice=$row[5];
            // $obj->thumbnail=$thumbnail;
            // $obj->itemUrl=$itemUrl;
            //print_r($obj);
            array_push($arrData,$obj);
        }
        else
        {
            //these are the products which exist in the ebay but have been sold out
            if($debug)
                echo "inactive".$itemId."<br/>";
        }
    }
    else
    {
        //these are the products which are now non existing in the ebay so need to be removed
        if($debug)
            echo "NF".$itemId."<br/>";
    }
}


//now adding the items which are unsold as per our database

$query2="SELECT EbayItemId,Title,SKU,sellingPrice FROM EbayProductsForTx WHERE sku not in (select distinct(SKU) from EbayTransactions) and continueDiscontinue=0 ";
// echo $query2;
$result2=mysql_query($query2);
$rowsnum2=mysql_num_rows($result2);
for($j=0;$j<$rowsnum2;$j++)
{
    $row=mysql_fetch_row($result2);
    //print_r($row);
    //$obj=new Item($row[0],$row[1],$row[2],'','',13);
    $obj = new StdClass();
    $obj->itemId=$row[0];
    $obj->title=$row[1];
    $obj->sku=$row[2];
    $obj->sellingPrice=$row[3];
    //print_r($obj);
    if($debug)
        echo "added new ".$obj->itemId."<br/>";
    array_push($arrData,$obj);

}



$length=sizeof($arrData);
$lines='';
$arrFinalData=array();
for($i=0;$i<500;$i++)
{

    $tempObj=$arrData[$i];
    $itemId=$tempObj->itemId;

    //get the thumbnal and the productURL
    $queryPhoto="Select thumbnail,itemUrl,thumbnailWidth,thumbnailHeight from EbayProductsForTx where EbayItemId='".$itemId."'";
    $resultPhoto=mysql_query($queryPhoto);
    $rowsPhotoNum=mysql_num_rows($resultPhoto);
    if($rowsPhotoNum==0)
    {
        $thumbnail='';
        $itemUrl='';
        continue;
    }
    else
    {

        $rowPhoto=mysql_fetch_row($resultPhoto);
        $thumbnail=$rowPhoto[0];
        $itemUrl=$rowPhoto[1];
    }


    $thumbnailWidth=$rowPhoto[2];
    $thumbnailHeight=$rowPhoto[3];



    if($thumbnailHeight>95 || $thumbnailHeight<50)
        continue;
    if($thumbnailWidth>141 || $thumbnailWidth<50)
        continue;


    $thumbnailHeight=70;
    $thumbnailWidth=70;

    $obj = new StdClass();
    $obj->itemId=$itemId;
    $obj->sku=$tempObj->sku;
    $obj->title=$tempObj->title;
    $obj->sellingPrice=$tempObj->sellingPrice;
    $obj->thumbnail=$thumbnail;
    $obj->itemUrl=$itemUrl;



    // print_r($obj);
    array_push($arrFinalData,$obj);


}
//to randomize
shuffle($arrFinalData);

$time_end = microtime(true);


$execution_time = ($time_end - $time_start);

if($debug)
    echo '<b>Total Execution Time:</b> '.$execution_time.' Secs';

if($debug)
    echo sizeof($arrFinalData);

echo json_encode($arrFinalData);

//echo json_encode($arrFinalData);


//get all the items that are of 1 month
/*for($k=1;$k<14;$k++)
{
    $count=1;
    if($k==13)
    {
        $lines.="<h2> Never sold items </h2>";

    }
    else
    {
        $lines.="<h2> Unsold items since ".$k." month</h2>";

    }
    $lines.="<table border='1'><tr><th>Sno</th><th>EbayItemId</th><th>Title</th><th>SKU</th><th>Selling Date</th><th>DaysUnsold</th><th>Selling History</th></tr>";

    for($j=0;$j<$length;$j++)
    {
        $obj=$arrData[$j];
        if($obj->monthsUnsold==$k)
        {

        //testSKU.php?SKU='.$obj->sku.'
            $link='echo <a href=""></a>';
            $lines.="<tr><td>".$count++."</td><td>".$obj->itemId."</td><td>".$obj->title."</td><td>".$obj->sku."
            </td><td>".$obj->lastSellingDate."</td><td>".$obj->daysUnSold."</td>";
            $reviewLink="testSKU.php?ItemId=".$obj->itemId;
            $lines.="<td><a href=".$reviewLink.">history</a></td></tr>";
        }

    }
    $lines.="<tr><td></td><td></td><td></td><td><td><b>Total Unsold</b></td><td>".intval($count-1)."</td><td></td></tr>";
    $lines.="</table>";
}*/



    //$lines.="</table>";

//$lines.="</div><br/>";

//print_r($arrData);



//return;






?>
