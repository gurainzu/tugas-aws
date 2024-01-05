<?php
   require 'vendor/autoload.php';

   use Aws\S3\S3Client;
   use Aws\Exception\AwsException;

   // Start Database Credentials
   $db_host = 'database1.ctcwcaiw4z14.us-east-1.rds.amazonaws.com'; // Change to your database host
   $db_user = 'admin'; // Change to your database user
   $db_pass = 'admin1139'; // Change to your database password
   $db_name = 'mydb'; // Change to your database
   // End Database Credentials

   // Start AWS Credentials
   $region = 'us-east-1'; // Change to your region
   $key = 'ASIAWARADTMXBYPLTNUH'; // Change to your access key id
   $secret = 'lczmbOvhBnkLLUNIJMUKwvIlUlzBNfP94p1TgMY9'; // Change to your secret key
   $token = 'FwoGZXIvYXdzEAcaDGksjwSQoDMM43yXByLBARlrl4/wUH7gtVr4Zbi+DRFbqcz8z/iV6tFhPWYvYEQWRNn4zIU/M6RGi2y/7RXCgPtcGEQcNHSemIO0beQurONsHL1kjvdp7ySOGjs9z9FjUP4SFUND/CFcnI689okv9nqv1c3RxLU/48fSnRu72l+xr+UgJ0pTUHGVP584os+jGA3C0SjxjeOXKlwbVZWtdc3k0idVJBvgPgW2paZpiWmmzIQgQt4g0slZnCpj6TkT5QNHzVHfinHRq06ySkJiAoMol5bgrAYyLdEgYLhaSplOJ/qR3sltAMOTW59cDPrrk0/f4fLtKJM4KUx+nDb3oNn2a8NZJw=='; // Change to your token
   $bucket_name = 'embereval1'; // Change to your S3 bucket name
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