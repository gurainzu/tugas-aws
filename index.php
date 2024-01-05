<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Formulir</title>
   </head>
   <body>
      <h1>Form Registration</h1>
      <form action="process_form.php" method="post" enctype="multipart/form-data">
         <table>
            <tr>
               <td><label for="nama">Nama</label></td>
               <td>:</td>
               <td><input type="text" name="nama" required></td>
            </tr>
            <tr>
               <td><label for="email">Email</label></td>
               <td>:</td>
               <td><input type="email" name="email" required></td>
            </tr>
            <tr>
               <td><label for="photo">Foto</label></td>
               <td>:</td>
               <td><input type="file" name="photo" accept="image/*" required></td>
            </tr>
         </table>
         <br>
         <input type="submit" value="Simpan">
      </form>
   </body>
</html>