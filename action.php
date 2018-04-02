<html>
<head>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<title>AWS EC2 Console</title>
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
<?php } else if (empty($result) && !empty($_POST['start'])) { ?>
<script type="text/javascript">
<?php
        echo 'jQuery( document ).ready(function() {
                        $("#message").html("<span id=\"message\" class=\"success\">The instance (InstanceId) will be starting up. </span>");
                        $("#message").fadeIn();
                });
        ';
sleep(5);
header('Location: index.php');
//exit;
?></script>
<?php } else if (empty($result) && !empty($_POST['shutdown'])) { ?>
<script type="text/javascript">
<?php
        echo 'jQuery( document ).ready(function() {
                        $("#message").html("<span id=\"message\" class=\"success\">Instance will be shutting down. </span>");
                        $("#message").fadeIn();
                });
        ';
header('Location: index.php');
//exit;
?></script>
<?php } ?>

<?php
// Include the SDK using the Composer autoloader
date_default_timezone_set('America/Los_Angeles');
require '/usr/share/nginx/html/aws-autoloader/aws-autoloader.php';

//require "aws.phar";

use Aws\Ec2\Ec2Client;
use Aws\Common\Enum\Region;

$aws = Ec2Client::factory(array(
    'profile' => 'default',
    'region' => 'us-east-1',
    'version' => 'latest'
));

if(!empty($_POST['start'])) {
    $InstanceId=$_POST['start'];
    startFunc($InstanceId);
}
if(!empty($_POST['shutdown'])) {
    $InstanceId=$_POST['shutdown'];
    shutdownFunc($InstanceId);
}

function startFunc($InstanceId) {
    try {
        global $aws;
        $aws->startInstances([
            'InstanceIds' => array($InstanceId)
        ]);
    } catch (ec2 $e) {
        $result = $e->getMessage();
    }
}
function shutdownFunc($InstanceId) {
    try {
        global $aws;
        $aws->stopInstances([
            'InstanceIds' => array($InstanceId)
        ]);
    } catch (ec2 $e) {
        $result = $e->getMessage();
    }
}
echo '<span id="message"></span>' . PHP_EOL;

?>
</body>
</html>
