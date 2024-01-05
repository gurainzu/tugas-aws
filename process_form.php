<?php
   require 'vendor/autoload.php';

   use Aws\S3\S3Client;
   use Aws\Exception\AwsException;

   $db_host = 'mydb.c1ym4s4w8bbm.us-east-1.rds.amazonaws.com';
   $db_user = 'root';
   $db_pass = 'admin123';
   $db_name = 'mydb';

   $success = false;
   $err = '';
   
   if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $nama = $_POST['nama'];
      $email = $_POST['email'];
      $file = "";

      if(isset($_FILES['photo'])) {

         $file_name = $_FILES['photo']['name'];   
         $temp_file_location = $_FILES['photo']['tmp_name'];
         $bucket_name = 'mybucket-9345';

         $config = [
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
               'key'    => "ASIAWB7B4M7TKJPZOBXC",
               'secret' => "TRxGQV/wLWbKJR4xe0b/lHAaFgTlOAElmN3N3gVC",
               'token' => "FwoGZXIvYXdzEP3//////////wEaDP+BSsPcgQ2y19amACK9AYyDVK95t1DlWPqYsZ3Nja/YToJRx2PcCTnkmNvMBxMy62naAXBtVLC0xnZsdXx5ifGmXwyyI5u41umhPaHaZBMbuiLTtri4inStamQ2dB3rsqzP4kWzCH1xOSDbztYUPIZEX2bgtYbxPgam05WyIx3g1m727l6gQ5kJDN6yg5l9FTBRcTXcxBmrSRSCU/haebho+2+0OD1ScJbgZMeQF527oeNnF+KcRuIsZ5ksaKBbGffUTWoxE3g9IzN2VSjH5t2sBjIttKNQmpiT38QE06cDBxvu5vBz9TFvqvUA0mjY/i52TajiC64+R0R3Ag/jaInL"
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
            $file = $res['ObjectURL'];

            // echo "Uploaded $file_name to $bucket_name.\n";
            // var_dump($res['ObjectURL']);
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

         $sql = "INSERT INTO user (nama, email, photo) VALUES ('$nama', '$email', '$file')";
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