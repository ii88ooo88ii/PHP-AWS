<?php 
 
// sample settings
$siteName = 'Sample Site'; 
$siteEmail = 'sample.site@samplesite.com'; 
 
$siteURL = ($_SERVER["HTTPS"] == "on")?'https://':'http://'; 
$siteURL = $siteURL.$_SERVER["SERVER_NAME"].dirname($_SERVER['REQUEST_URI']).'/'; 
 
// db config 
define('DB_HOST', 'localhost');  
define('DB_USERNAME', 'root');  
define('DB_PASSWORD', 'root');  
define('DB_NAME', 'sample_db'); 