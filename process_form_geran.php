<?php
   require 'vendor/autoload.php';

   use Aws\S3\S3Client;
   use Aws\Exception\AwsException;

   // Start Database Credentials
   $db_host = 'mysql.cdgiygu4icar.us-east-1.rds.amazonaws.com'; // Change to your database host
   $db_user = 'admin'; // Change to your database user
   $db_pass = 'perseke07'; // Change to your database password
   $db_name = 'mydb'; // Change to your database
   // End Database Credentials

   // Start AWS Credentials
   $region = 'us-east-1'; // Change to your region
   $key = 'ASIA3CLQHM24ND72CWGK'; // Change to your access key id
   $secret = 'LHQoTZ+5mzFxTDTrRjtd2LdmOMtZA3j16EL3VtxU'; // Change to your secret key
   $token = 'FwoGZXIvYXdzEAkaDCc1vRwtZbq04m7y0CLDASUJZG2XirSNUPGCpJ+uSX+5dPnOQfJ5H7EEN/F1jhPtqd+wl2JSjwOWKUrjo989hzOrXSRgLzh4sNAZzIHwHZhYfATjWQTGR5fsPddTwtizIJY4GFIKIAFTpZw+nkicVlGTj3IhzgYYBkZJtoiHN1f9Gi2/lltWjFKqiMM99AbH1kuBSy+wPlOhfBvC9d/KKzVwf/cu+l13vLXH1qAF9oYYSjY/LrzMixPH9i/QVOVbjjOwIU0N7qJ52R5NT3WI8TG5lCjwt+CsBjItidS0UuecNzNmJL1h+R0lYhusiW4Zezz5K4ePqc17cWXIHQR4k9dRZEtjNi7U'; // Change to your token
   $bucket_name = 'geranbucket'; // Change to your S3 bucket name
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
