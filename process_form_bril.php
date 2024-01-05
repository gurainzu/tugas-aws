<?php
   require 'vendor/autoload.php';

   use Aws\S3\S3Client;
   use Aws\Exception\AwsException;

   // Start Database Credentials
   $db_host = 'database-1.crgcsk0a6cd1.us-east-1.rds.amazonaws.com'; // Change to your database host
   $db_user = 'root'; // Change to your database user
   $db_pass = 'clearance01'; // Change to your database password
   $db_name = 'mydb'; // Change to your database
   // End Database Credentials

   // Start AWS Credentials
   $region = 'us-east-1'; // Change to your region
   $key = 'ASIAUW737UGOS6QV7I4W'; // Change to your access key id
   $secret = 'ttprYSTiJuphRja7zboUI3EdZ63hgAIBM0YupCJf'; // Change to your secret key
   $token = 'FwoGZXIvYXdzEAkaDAZFFxo31KSEd4bS+SLEASVut5h3KGEfnB5LVa4Py2QUJjU5a8vwwa+rX62/mKTaFO3RnL8MAAl+SElTyQ6GyqHanSRG9nOtIMF1CyYB5XTroqCYbCsrOe2qe/xC2eFwYFE1F7idJZ>   $bucket_name = 'brilian-bucket'; // Change to your S3 bucket name
   // End AWS Credentials

   $success = false;
   $err = '';

   if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $nama = $_POST['nama'];
      $email = $_POST['email'];
      $file = "";

      if(isset($_FILES['photo'])) {

         $file_name = $_FILES['photo']['name'];
         $temp_file_location = $_FILES['photo']['tmp_name'];

         $config = [
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
               'key'    => $key,
               'secret' => $secret,
               'token' => $token
            ]
         ];

         $s3client = new Aws\S3\S3Client($config);

         try {
            $res = $s3client->putObject([
               'Bucket' => $bucket_name,
               'Key' => $file_name,
               'SourceFile' => $temp_file_location,
               'ACL' => 'public-read',
            ]);

            $success = true;
         } catch (Exception $exception) {
            $err = $exception->getMessage();
         }
      }

      if ($success) {
         $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

         if ($conn->connect_error) {
            $success = false;
            $err = $conn->connect_error;
         }

         $randomId = rand();
         $sql = "INSERT INTO user (id, name, email, photo) VALUES ('$randomId','$nama', '$email', '$file')";
         $result = $conn->query($sql);

         if (!$result) {
            $success = false;
            $err = $conn->error;
         }
      }

      if ($success) {
         echo "Success";
      } else {
         echo "Error: $err";
      }

      $conn->close();
   }
?>