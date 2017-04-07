<?php

require_once "login.php";

class utilityFunctions
{

    private static $arrData=Array();
    private static $generateRandom=true;
    private static $numberOfProducts=10;
    private static $discountPercent=10;


    /**
     * Function to send the mail
     * @param $to
     * @param $from
     * @param $subject
     * @param $message
     * @return bool
     */
    public static function sendMail($to,$from,$subject,$message)
        {
             // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Additional headers
            $headers .= 'From:'.$from . "\r\n";  //should not be gmail otherwise the phishing message come
             // Mail it
            return mail($to, $subject, $message, $headers);
        }

	//this function is used to get the randowm products. Called from the configure.php file
    public static function fetchRandomProducts($count,$discount)
    {
        $arrProductIds=self::randomNumberGeneration($count,1,$discount);
        self::$discountPercent=$discount;
        self::populateProductArray($arrProductIds);
    }

    /**
     * This function is called from muliple places to know the current set of products.
     * This is not going to generate new set of products
     */
    public static function fetchProducts()
    {
        $fp=fopen("randomGeneration.txt","r");
        $str=fgets($fp);
        list($tempProductArr,$numProducts,$discount,$dateTime)=explode("^",$str);
        fclose($fp);
        $arrProductIds=explode(",",$tempProductArr);
        self::$discountPercent=floatval($discount);
        self::populateProductArray($arrProductIds);
    }
	
	/**
     * this function will be used to return the random generation based on the earlier set value of the count and dicount
     * This is called from mail.php
     */
	public static function fetchRandomProductsBasedOnSetConfigurations()
	{
	
		$fp=fopen("randomGeneration.txt","r");
		$str=fgets($fp);
        list($tempProductArr,$count,$discount,$dateTime)=explode("^",$str);
        fclose($fp);

        self::fetchRandomProducts($count,$discount);
		/*
		//get the randomw products..made the change of product_id from * to product_id
		$sql="SELECT *  FROM  `jos_vm_product` ORDER BY RAND( ) LIMIT ".$count;
		$result=mysql_query($sql);
		$rowsnum=mysql_num_rows($result);
		$arrRand=array();
		for($i=0;$i<$rowsnum;$i++)
		{
			$row=mysql_fetch_object($result);
			//echo $row->product_id."\t";
			array_push($arrRand,intval($row->product_id));
		}
		//read from the file so that we get the discount
		
		//update the file
		$fp=fopen("randomGeneration.txt","w");
		$str=implode(",",$arrRand)."^".$count."^".$discount."^".self::getTimeStamp();

		fprintf($fp,$str);
		fclose($fp);
		
        self::$discountPercent=floatval($discount);
        self::populateProductArray($arrRand);
		*/
			
	}

    /**
     * Helper function to fetch all the values from the product table based on the product Id
     * @param $arrProductIds
     */
    public static function populateProductArray($arrProductIds)
    {

        self::$arrData=array();
        $len=sizeof($arrProductIds);
        for($i=0;$i<$len;$i++)
        {
        $sql="Select * from jos_vm_product where product_id='".$arrProductIds[$i]."'";
        $result=mysql_query($sql);
        $row=mysql_fetch_object($result);
        $productId=$row->product_id;
        $productSku=$row->product_sku;
        $productName=$row->product_name;
        $productQty=$row->product_s_desc;
        $productImage=$row->product_full_image;


        $sqlPrice="Select * from jos_vm_product_price where product_id='".$arrProductIds[$i]."'";
        $resultPrice=mysql_query($sqlPrice);
        $rowPrice=mysql_fetch_object($resultPrice);
        $productPrice=floatval($rowPrice->product_price);
        $discountedPrice=number_format($productPrice*(1-.01*self::$discountPercent), 2, '.', '');
	$offerImage="http://mygann.com/esale/offer_10.png";
	if(intval(self::$discountPercent)=='5')
		$offerImage="http://mygann.com/esale/offer_5.png";
	else if(intval(self::$discountPercent)=='10')
		$offerImage="http://mygann.com/esale/offer_10.png";
	else if(intval(self::$discountPercent)=='20')
		$offerImage="http://mygann.com/esale/offer_20.png";
		

        array_push(self::$arrData,array("id"=>$productId,"sku"=>$productSku,"name"=>$productName,"qty"=>$productQty,
        "image"=>$productImage,"price"=>$productPrice,"discountedPrice"=>$discountedPrice,"offerImage"=>$offerImage));

        }

        //return $this->arrData;
   }


    /**
     * This is to render the catalog properlye
     * @return string
     */
    public static function displayCatalog()
    {
        $header = '<html>
                    <head>
                     <title>MyGann - eSale Special</title>
                    <style type="text/css">
                        body{ font-family:Verdana, Arial, Helvetica, sans-serif;}
                    </style>
                    </head>

                    <body>
                    <img src="http://mygann.com/esale/esale_logo.jpg" alt="logo" />
                    <br /><br />
                    <table border="0" cellspacing="5" cellpadding="5">';


        $message='';

        $len=sizeof(self::$arrData);
        //add this to show product id also <span>'.self::$arrData[$i]["id"].'</span>
        for($i=0;$i<$len;$i++)
        {

                $message .=	'<tr>';
                $message .=	'<td><img src='.self::$arrData[$i]["image"].' height="250" width="250" /></td>';

                $message .= '<td width="5px">&nbsp;</td>

                            <td>
                                <span style="font-size:20px; color:#666699; font-weight:bold">'.self::$arrData[$i]["sku"].'</span>
                                <span style="font-size:20px; color:#666699; font-weight:bold">'.self::$arrData[$i]["name"].'</span>
                                <br /><br />
                                <span style="font-size:14px;"><span style="color:#bf0000; text-decoration: line-through">$'. self::$arrData[$i]["price"]."</span>".
                    '<img src='.self::$arrData[$i]["offerImage"].' /></span><br /><br />
                     <span style="font-size:18px; font-weight:bold;">$'.self::$arrData[$i]["discountedPrice"]."  ".self::$arrData[$i]["qty"] .'</span><br /><br />

                                <span style="font-size:18px; color:#990000">Free Shipping</span>
                                <br /><br />
                                
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                            <input type="hidden" name="cmd" value="_xclick"/>
                            <input type="hidden" name="business" value="mvone@verizon.net"/>
                            <input type="hidden" name="item_name" value="'.self::$arrData[$i]["name"].'"/>
                            <input type="hidden" name="item_number" value="'.self::$arrData[$i]["sku"].'"/>
                            <input type="hidden" name="amount" value="'.self::$arrData[$i]["discountedPrice"].'"/>
                            <input type="hidden" name="item_qty" value="'.self::$arrData[$i]["qty"].'"/>
                            <input type="hidden" name="no_shipping" value="2"/>
                            <input type="hidden" name="no_note" value="1"/>
                            <input type="hidden" name="currency_code" value="USD"/>
                            <input type="hidden" name="bn" value="PP-BuyNowBF"/>
                            <!--<input type="hidden" name="rm" value="2"/>-->
                            <input type="hidden" name="return" value="http://mygann.com/esale/complete.php">
                            <!-- the address of the canel page in case the transaction did not hapen -->
                            <input type="hidden" name="cancel_return" value="http://mygann.com/esale/cancel.php">
                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit"/>
                            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
                            </form>
                            <td>
                            </tr>';



        }
	$message.="</table>";
		$message.="<hr/>";
		$message.='<div style="font-size:.8em;text-align:center">
					If you have any question or concern, please contact us at <a href="mailto:esale@mygann.com">esale@mygann.com</a>.
					<br/>
					Please contact us if you want to unsubscribe from any future weekly esale.
					</div>';
        $displayMessage= $header.$message;
        $displayMessage= $header.$message;
        return $displayMessage;

    }


    public static function getTimeStamp()
    {
        $accessDate = date("Y-m-d");
        $timezone = 'Asia/Calcutta';
        date_default_timezone_set($timezone);
        $tz = date_default_timezone_get();
        $accessTime = date("H:i:s");
        $timeStamp = $accessDate . " " . $accessTime;
        return $timeStamp;
    }


   public static function getSavedDiscountAndProducts()
    {
        $fp=fopen("randomGeneration.txt","r");
        $str=fgets($fp);
        list($tempProductArr,$numProducts,$discount,$dateTime)=explode("^",$str);
        fclose($fp);

        self::$discountPercent=floatval($discount);
        self::$numberOfProducts=intval($numProducts);
        return $discount.":".$numProducts;
    }

    /**
     * random number is generated only when you pass 1
     * @param $count
     * @param int $generate
     * @return array
     */
    public static function randomNumberGenerationOldNotUsed($count,$generate,$discount)
    {

        if($generate==1)
        {
            $fp=fopen("randomGeneration.txt","w");
            $sql="SELECT product_id  FROM  `jos_vm_product` ORDER BY RAND( ) LIMIT ".$count;
            $result=mysql_query($sql);
            $rowsnum=mysql_num_rows($result);
            $arrRand=array();
            for($i=0;$i<$rowsnum;$i++)
            {
                $row=mysql_fetch_object($result);
                //echo $row->product_id."\t";
                array_push($arrRand,intval($row->product_id));
            }

//            $str="<data>";
//            $str .="<ProductIds>".implode(",",$arrRand)."</ProductIds>";
//            $str .="<ProductCount>".$count."</ProductCount>";
//            $str .="<Discount>".$discount."</Discount>";
//            $str .="<DateGenerated>".$this->getTimeStamp()."</DateGenerated>";
//            $str .="</data>";

            $str=implode(",",$arrRand)."^".$count."^".$discount."^".self::getTimeStamp();

            fprintf($fp,$str);
            fclose($fp);
        }
        else
        {
            $fp=fopen("randomGeneration.txt","r");
            $str=fgets($fp);
            $arrRand=explode(",",$str);
            fclose($fp);
            //read from the file and show

        }
        return $arrRand;
        //print_r($arrRand);

        //fprintf($fp,)
    }


/**
     * random number is generated only when you pass 1
     * @param $count
     * @param int $generate
     * @return array
     */
    public static function randomNumberGeneration($count,$generate,$discount)
    {

        if($generate==1)
        {
        	$tempcount=20;
            $fp=fopen("randomGeneration.txt","w");
            $sql="SELECT product_id  FROM  `jos_vm_product` ORDER BY RAND( ) LIMIT ".$tempcount;
            $result=mysql_query($sql);
            $rowsnum=mysql_num_rows($result);
            $arrRand=array();
            $chkStr='';
            for($i=0;$i<$rowsnum;$i++)
            {
                $row=mysql_fetch_object($result);
                //echo $row->product_id."\t";
                if($i==($rowsnum-1))
            		$chkStr .=intval($row->product_id);
            	else
            		$chkStr .=intval($row->product_id).",";
            }
            
         //   echo $chkStr;
            
            //check if the product_ids are valid
            
            $sql_check="SELECT product_id,product_sku FROM jos_vm_product WHERE product_id in (".$chkStr.")";
            //echo $sql_check;
            
            //essentially the query is 
            //SELECT product_id,product_sku FROM jos_vm_product WHERE product_id in (1740,2020,2500,1975,772,2897,2049,1728,2898,2206,157,2510,152,2587,2519,3068,3220,242,2487,2652)
            
            $result_check=mysql_query($sql_check);
            $rowsnum_check=mysql_num_rows($result_check);
			//get the first ten  products and place it in the array.
			$counter=0;
			for($j=0;$j<$count;$j++)
			{
				$row_check=mysql_fetch_object($result_check);
				array_push($arrRand,intval($row_check->product_id));
        		
				//array_push($arrRand,array("id"=>intval($row_check->product_id),"sku"=>$row_check->product_sku));
        				
			}
			
          //  print_r($arrRand);
            

//            $str="<data>";
//            $str .="<ProductIds>".implode(",",$arrRand)."</ProductIds>";
//            $str .="<ProductCount>".$count."</ProductCount>";
//            $str .="<Discount>".$discount."</Discount>";
//            $str .="<DateGenerated>".$this->getTimeStamp()."</DateGenerated>";
//            $str .="</data>";

            $str=implode(",",$arrRand)."^".$count."^".$discount."^".self::getTimeStamp();

            fprintf($fp,$str);
            fclose($fp);
			
        }
        else
        {
            $fp=fopen("randomGeneration.txt","r");
            $str=fgets($fp);
            $arrRand=explode(",",$str);
            fclose($fp);
            //read from the file and show

        }
        return $arrRand;
        //print_r($arrRand);

        //fprintf($fp,)
    }

}



?>