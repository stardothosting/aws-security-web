<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL); 

// Load sdk
require 'vendor/autoload.php';
use Aws\Ec2\Ec2Client;
use Aws\Ec2\Exception\Ec2Exception as ec2;

// Cross Site Script  & Code Injection Sanitization
function xss_cleaner($input_str) {
    $return_str = str_replace( array('<',';','|','&','>',"'",'"',')','('), array('&lt;','&#58;','&#124;','&#38;','&gt;','&apos;','&#x22;','&#x29;','&#x28;'), $input_str );
    $return_str = str_ireplace( '%3Cscript', '', $return_str );
    return $return_str;
}

// Get Users IP
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
    $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $client_ip = $_SERVER['REMOTE_ADDR'];
}

// Process form submission
if (!empty($_POST['ip_address'])) {
	$clean_ip = xss_cleaner($_POST['ip_address']) . '/32';
	$ec2Client = Ec2Client::factory(array(
		'profile' => 'aws-project1',
		'region' => 'us-east-1' // (e.g., us-east-1)
	));
	// Set ingress rules for the security group
	try {	
		$ec2Client->authorizeSecurityGroupIngress(array(
			'GroupName'     => 'WWW',
			'IpPermissions' => array(
				array(
					'IpProtocol' => 'tcp',
					'FromPort'   => 21,
					'ToPort'     => 21,
					'IpRanges'   => array(
						array('CidrIp' => $clean_ip)
						),
					)
	    			)
			)); 
	} catch (ec2 $e) {
		$result = $e->getMessage();
	}
} 
?>
<html>
<head>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<title>AWS Security IP Interface</title>
<style>
#message {
	text-align:center;
	padding-top:25px;
	color: #800000;
	font-size:12px;
}
.success {
	color: #10C407 !important;
}
</style>
</head>
<body>
<center>
<div class="container">
<h1>AWS IP Security</h1>
<h3>Add your IP address for FTP access</h3>
<form action="index.php" method="post">
<table width="50%">
<tr>
<td width="25%"><label>IP Address :</label></td>
<td width="75%"><input type="text" name="ip_address" value="<?php echo $client_ip; ?>" class="form-control"></td>
<td width="25%" style="padding-left:25px;"><button type="submit" class="btn btn-success">Submit</button></td>
</tr>
</table>
</form>
<span id="message"></span>
</div>
</center>
<?php if (!empty($result)) { ?>
<script type="text/javascript">
<?php
        echo ' jQuery( document ).ready(function() {
                        $("#message").html("<span id=\"message\">'.$result.'</span>");
                        $("#message").fadeIn();
                });
        ';
?>
</script>
<?php } else if (empty($result) && !empty($_POST['ip_address'])) { ?>
<script type="text/javascript">
<?php
        echo 'jQuery( document ).ready(function() {
                        $("#message").html("<span id=\"message\" class=\"success\">Your IP ('.$clean_ip.') has been added successfully. </span>");
                        $("#message").fadeIn();
                });
        ';
?></script>
<?php } ?>
</body>
</html>
