<?php
/**
 * Created by PhpStorm.
 * User: rohitagarwal
 * Date: 8/27/15
 * Time: 12:39 PM
 */


/**
 * This script will read the xls file to get the required fields that we want
 * We can directly enter the case ids in the database...Note I will for now not enter the old cases
 */
require_once 'Excel/reader.php';
$analysisData = new Spreadsheet_Excel_Reader();

//$db_hostname='localhost';
//$db_database='newUnicornPlatformProject';
//$db_username='root';
//$db_password='root123';
//
//// establish the connection with the database server
//$db_server = mysql_connect($db_hostname, $db_username, $db_password);
//if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
//// select the correct database
//mysql_select_db($db_database)
//or die("Unable to select database: " . mysql_error());

require_once 'newRevampLogin.php';

// Set output Encoding.
$analysisData->setOutputEncoding('CP1251');
$inputFileName = 'Files/nextOldDb.xls';
$analysisData->read($inputFileName);
error_reporting(E_ALL ^ E_NOTICE);
$numRows=$analysisData->sheets[0]['numRows'];
$numCols=$analysisData->sheets[0]['numCols'];


$arr=array();

for($i=2;$i<=$numRows;$i++)
{
    // print_r($analysisData->sheets[0]['cells'][$i]);
    $databaseName=$analysisData->sheets[0]['cells'][$i][1];
//    if($databaseName=="OLD")
//        continue;
    $createdDate=$analysisData->sheets[0]['cells'][$i][2];
    $casePriorityArr=explode(",",$analysisData->sheets[0]['cells'][$i][3]);
    $casePriority=$casePriorityArr[1];
    $productName=mysql_real_escape_string($analysisData->sheets[0]['cells'][$i][4]);
    $shortDescription=mysql_real_escape_string($analysisData->sheets[0]['cells'][$i][6]);
    $caseId=$analysisData->sheets[0]['cells'][$i][9];
    $customerName=mysql_real_escape_string($analysisData->sheets[0]['cells'][$i][27]);
    $region=mysql_real_escape_string($analysisData->sheets[0]['cells'][$i][33]);

    $creationDate=date('Y-m-d', strtotime(str_replace('/', '-', $createdDate)));


    //get the priority value

    $queryPriorityLevel="Select `PriortyLevelId` from `priortylevel` WHERE `DESCRIPTION` LIKE '".trim($casePriority)."'";
    echo $queryPriorityLevel;
    $resultPriorityLevel=mysql_query($queryPriorityLevel);
    $numofresult=mysql_num_rows($resultPriorityLevel);
    if($numofresult==0)
    {
        echo "Priorty could not be found";
        continue;
    }
    else
    {
        $row=mysql_fetch_row($resultPriorityLevel);

        $priorityId=$row[0];
    }


    echo $databaseName.",".$createdDate.",".$casePriority.",".$priorityId.",".$productName.",".$shortDescription.",".$caseId.",".$customerName.",".$region."<br/>";

    $query="INSERT INTO `cases`(`CASE_ID`, `DATE_CREATED`,`DBID`, `PRIORITY`, `PRODUCT_NAME`, `SHORT_DESCRIPTION`, `CUSTOMER_NAME`, `CUSTOMER_REGION`) VALUES
    ( '".$caseId."','".$creationDate."','".$databaseName."','".$priorityId."','".$productName."','".$shortDescription."','".$customerName."','".$region."')";
    echo $query."<br/>";
    $result= mysql_query($query);
    if($result)
    {
        echo "insert successful<br/>";
    }
    else
    {
        echo "insert failed".mysql_error()."<br/>";
    }



}


//print_r($arr);


/*for($i=0;$i<sizeof($arr);$i++)
{
    print_r($arr[$i]);
    echo "<br/>";
} */

//insert in the db



  //  echo date('d/m/Y', strtotime('12/13/2012'));

//exit(0);

/*for($i=0;$i<sizeof($arr);$i++)
{
    $obj=$arr[$i];
    //check if the data already exisats
    $checkQuery="Select * from cases where CASE_ID='".$obj->caseId."'";
    $resultQuery=mysql_query($checkQuery);
    $rowsnum = mysql_num_rows($resultQuery);
    if($rowsnum>0)
    {
        echo $obj->caseId." Already exists<br/>";
        continue;
    }
    else
    {
    $query="INSERT INTO `cases` ( `CASE_ID`,`DATE_CREATED`,`CASE_TYPE_ID`,
    `RESOLUTION`,`PRIME_RESPONSIBLE`,`SWEDEN_SUPPORT`,`STATUS_TYPE_ID` ) VALUES
    ( '".$obj->caseId."','".$obj->creationDate."','".$obj->caseType."','".$obj->resolution."','".$obj->primeResponsible."',
    '".$obj->swedishSupport."','".$obj->status."')";
     echo $query."<br/>";
     $result= mysql_query($query);
        if($result)
        {
            echo "insert successful<br/>";
        }
        else
        {
            echo "insert failed<br/>";
        }
    }

}
*/


?>