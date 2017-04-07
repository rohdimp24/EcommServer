<?php
$message= '';
$mailingList='';

if(isset($_POST['from'])&&isset($_POST['message']))
{


    sendMail("rohitagarwal24@gmail.com",$_POST['from'],"contact mygann",$_POST['message']);
    echo json_encode($_POST);
}
else
{

    echo "post"+$_POST['from'];
}


function sendMail($to,$from,$subject,$message)
{
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Additional headers
    $headers .= 'From:'.$from . "\r\n";  //should not be gmail otherwise the phishing message come
    // Mail it
    return mail($to, $subject, $message, $headers);
}


?>