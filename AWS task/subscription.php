<?php 
// config file 
require_once 'config.php'; 
 
// subscriber class 
require_once 'Subscriber.class.php'; 
$subscriber = new Subscriber(); 

// AWS SNS Service
require 'vendor/autoload.php';

use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;
 
if(isset($_POST['subscribe'])){ 
    $errorMsg = ''; 
   
    $response = array( 
        'status' => 'err', 
        'msg' => 'Something went wrong, please try again.' 
    ); 
     
    // Input fields validation 
    if(empty($_POST['name'])){ 
        $pre = !empty($msg)?'<br/>':''; 
        $errorMsg .= $pre.'Please enter your full name.'; 
    } 
    if(empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){ 
        $pre = !empty($msg)?'<br/>':''; 
        $errorMsg .= $pre.'Please enter a valid email.'; 
    } 
     
    // If validation successful 
    if(empty($errorMsg)){ 
        $name = $_POST['name']; 
        $email = $_POST['email']; 
        $verify_code = md5(uniqid(mt_rand())); 
         
        // Check if email already exists 
        $con = array( 
            'where' => array( 
                'email' => $email 
            ), 
            'return_type' => 'count' 
        ); 
        $prevRow = $subscriber->getRows($con); 
         
        if($prevRow > 0){ 
            $response['msg'] = 'The email entered is already in our subscribers list.'; 
        }else{ 
            // Insert data
            $data = array( 
                'name' => $name, 
                'email' => $email, 
                'verify_code' => $verify_code 
            ); 
            $insert = $subscriber->insert($data); 
             
            if($insert){ 

                // Send verification
                $SnSclient = new SnsClient([
                    'profile' => 'default',
                    'region' => 'us-east-1',
                    'version' => '2010-03-31'
                ]);
                
                $protocol = 'email';
                $endpoint = 'sample@example.com';
                $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
                
                try {
                    $result = $SnSclient->subscribe([
                        'Protocol' => $protocol,
                        'Endpoint' => $endpoint,
                        'ReturnSubscriptionArn' => true,
                        'TopicArn' => $topic,
                    ]);
                    
                    var_dump($result);

                    $response = array( 
                        'status' => 'ok', 
                        'msg' => 'A verification link has been sent to your email address, please check your email and verify.' 
                    ); 
                } catch (AwsException $e) {
                    // output error message if fails
                    error_log($e->getMessage());
                } 
            } 
        } 
    }else{ 
        $response['msg'] = $errorMsg; 
    } 
     
    // Return response 
    echo json_encode($response); 
} 
?>