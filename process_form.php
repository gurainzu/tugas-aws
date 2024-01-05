<?php
   require 'vendor/autoload.php';

   use Aws\S3\S3Client;
   use Aws\Exception\AwsException;

   // Start Database Credentials
   $db_host = 'YOUR_DB_HOST'; // Change to your database host
   $db_user = 'YOUR_DB_USER'; // Change to your database user
   $db_pass = 'YOUR_DB_PASSWORD'; // Change to your database password
   $db_name = 'YOUR_DB_NAME'; // Change to your database
   // End Database Credentials

   // Start AWS Credentials
   $region = 'us-east-1'; // Change to your region
   $key = 'YOUR_AWS_KEY'; // Change to your access key id
   $secret = 'YOUR_AWS_SECRET'; // Change to your secret key
   $token = 'YOUR_AWS_TOKEN'; // Change to your token
   $bucket_name = 'YOUR_AWS_BUCKET'; // Change to your S3 bucket name
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