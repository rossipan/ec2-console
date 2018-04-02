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

$instanceIds = array('i-xxxxxxxxxxxxxxxx', //kube master
                    'i-yyyyyyyyyyyyyyyyyy'
);

$result = $aws->DescribeInstances();
$reservations = $result['Reservations'];



echo '<div class="container">' . PHP_EOL;
echo '<table cellpadding="8" border="2">' . PHP_EOL;
echo '<tr><td>Instance Name</td><td>Feature</td><td>Instance ID</td><td>Instance Type</td><td>State</td><td>Action</td></tr>' . PHP_EOL;

foreach ($reservations as $reservation) {
    $instances = $reservation['Instances'];
    foreach ($instances as $instance) {
        $instanceName = '';

        foreach ($instance['Tags'] as $tag) {
            if ($tag['Key'] == 'Name') {
                $instanceName = $tag['Value'];
            }
            if ($tag['Key'] == 'feature') {
                $feature = $tag['Value'];
            }
        }
        if (in_array($instance['InstanceId'], $instanceIds)) {
            echo '<tr><td>' . $instanceName . '</td>' . PHP_EOL;
            echo '<td>' . $feature . '</td>' . PHP_EOL;
            echo '<td>' . $instance['InstanceId'] . '</td>' . PHP_EOL;
            echo '<td>' . $instance['InstanceType'] . '</td>' . PHP_EOL;
            echo '<td>' . $instance['State']['Name'] . '</td>' . PHP_EOL;
            if ($instance['State']['Name'] == 'running') {
                echo '<td style="padding-left:5px;padding-right:5px;"><form action="action.php" method="POST"><button type="submit" name="shutdown" value="' . $instance['InstanceId'] . '" class="btn btn-warning">shutdown</button></form></td>' . PHP_EOL;
            } elseif ($instance['State']['Name'] == 'stopped') {
                echo '<td style="padding-left:5px;padding-right:5px;"><form action="action.php" method="POST"><button type="submit" name="start" value="' . $instance['InstanceId'] . '" class="btn btn-success">start</button></form></td>' . PHP_EOL;
            } else {
                echo '<td></td>' . PHP_EOL;
            }
            echo '</tr>' . PHP_EOL;
        }
    }
}




echo '</table>' . PHP_EOL;
echo '</div>' . PHP_EOL;
?>
</center>
</body>
</html>
