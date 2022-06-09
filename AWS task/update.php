<?php 
// config file 
require_once 'config.php'; 
 
// Subscriber class 
require_once 'Subscriber.class.php'; 
$subscriber = new Subscriber(); 

require 'vendor/autoload.php';

use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;
 
if(!empty($_GET['email_verify'])){ 
    // AWS SNS Service
    $SnSclient = new SnsClient([
        'profile' => 'default',
        'region' => 'us-east-1',
        'version' => '2010-03-31'
    ]);
    
    $subscription_token = 'arn:aws:sns:us-east-1:111122223333:MyTopic:123456-abcd-12ab-1234-12ba3dc1234a';
    $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
    
    try {
        $result = $SnSclient->subscribe([
            'Token' => $subscription_token,
            'TopicArn' => $topic,
        ]);
        var_dump($result);
    } catch (AwsException $e) {
        // output error message if fails
        error_log($e->getMessage());
    } 

    // validate and update the database
    $verify_code = $_GET['email_verify']; 
    $con = array( 
        'where' => array( 
            'verify_code' => $verify_code 
        ), 
        'return_type' => 'count' 
    ); 
    $rowNum = $subscriber->getRows($con); 
    if($rowNum > 0){ 
        // set the is_verified to 1
        $data = array( 
            'is_verified' => 1 
        ); 
        $con = array( 
            'verify_code' => $verify_code 
        ); 
        $update = $subscriber->update($data, $con); 
        if($update){ 
            $statusMsg = '<p class="success">Your email address has been verified successfully.</p>'; 
        }else{ 
            $statusMsg = '<p class="error">Some problem occurred on verifying your email, please try again.</p>'; 
        } 
    }else{ 
        $statusMsg = '<p class="error">You have clicked on the wrong link, please check your email and try again.</p>'; 
    } 
?>  

<!DOCTYPE html>
<html lang="en">
<head>
<title>Email Verification</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">

<!-- Stylesheet file -->
<link rel="stylesheet" type="text/css" href="css/custom.css" />

</head>
<body class="subs">
<div class="container">
    <div class="subscribe box-sizing">
        <div class="sloc-wrap box-sizing">
            <div class="sloc-content">
                <div class="sloc-text">
                    <div class="sloc-header"><?php echo $statusMsg; ?></div>
                </div>
                <a href="<?php echo $siteURL; ?>" class="cwlink">Go to Site</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php 
} 
?>