<?php
require_once ('login.php');


    //echo "hi i am in ".$month;
    $lines="<div style='margin-left:10px'>";

    $endDate="2015-12-31";
    $startDate="2015-01-01";
    // $lines='';
    /*$query="SELECT SKU,Title,SUM(QTY) AS output,SellingPrice,SUM(SellingPrice*Qty),ItemId
    FROM EbayTransactions Where SKU!='' AND SellingPrice > 0.00 AND
     CreationDate <='".$endDate."'and CreationDate >='".$startDate."' GROUP BY Title ORDER BY `output` DESC";
    */
    //$query="SELECT ItemId,Title,SUM(QTY) AS output FROM EbayTransactions Where CreationDate <='".$endDate."'and CreationDate >='".$startDate."' GROUP BY ItemId ORDER BY `output` DESC";

    $time_start = microtime(true);

    $query="select distinct (A.EbayItemId),A.SKU,A.Title,A.SellingPrice,A.thumbnail,A.itemUrl,A.continueDiscontinue,B.ItemId,A.thumbnailWidth,A.thumbnailHeight, A.Size 
    from EbayProductsForTx as A,
    EbayTransactions as B where A.EbayItemId=B.ItemId and A.continuediscontinue=0";
    //echo $query;
    $result= mysql_query($query);
    $rowsnum = mysql_num_rows($result);
    if($rowsnum==0)
    {
        //return;
    }
    else
    {
        $arrData=array();
        $count=1;
        $total=0.0;
        for($j=0;$j<$rowsnum;$j++)
        {
            $row=mysql_fetch_row($result);
            //print_r($row);
            // $lines.="<tr><td>".$count++."</td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>";
            //$lines.="<tr><td>".$count++."</td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[5]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td></tr>";

            $itemId=$row[0];
            $sku=$row[1];
            $title=$row[2];
            $sellingPrice=$row[3];
            $thumbnail=$row[4];
            $itemUrl=$row[5];
            $coninueDiscontinue=$row[6];
            $thumbnailWidth=$row[8];
            $thumbnailHeight=$row[9];
			$size=$row[10];

            /*$itemId=$row[5];
            $sku=$row[0];
            $sellingPrice=$row[3];
            $title=$row[1];

            $queryPhoto="Select thumbnail,itemUrl from EbayProductsForTx where EbayItemId='".$itemId."'";
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
            }*/






            if($thumbnailHeight>95 || $thumbnailHeight<50)
                continue;
            if($thumbnailWidth>141 || $thumbnailWidth<50)
                continue;

            $obj = new StdClass();
            $obj->itemId=$itemId;
            $obj->sku=$sku;
            $obj->title=$title;
            $obj->sellingPrice=$sellingPrice;
            $obj->thumbnail=$thumbnail;
            $obj->itemUrl=$itemUrl;
            $obj->continueDiscontinue=$coninueDiscontinue;
            $obj->thumbnailWidth=$thumbnailWidth;
            $obj->thumbnailHeight=$thumbnailHeight;
			$obj->size=$size;

            //gte the size of the image
//            $size=getimagesize($thumbnail);
//            $obj->width=$size[0];
//            $obj->height=$size[1];

            //print_r($size);

           // print_r($obj);
            array_push($arrData,$obj);


        }

    }

    //randomize
    shuffle($arrData);

    $time_end = microtime(true);
    $execution_time = ($time_end - $time_start);

    echo json_encode($arrData);
    //return;




?>
