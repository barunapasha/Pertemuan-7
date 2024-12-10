<?php
session_start(); 

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'] ?? '';
  $nim = $_POST['nim'] ?? '';

  if (!empty($name) && !empty($nim)) {
    $_SESSION['name'] = $name;
    $_SESSION['nim'] = $nim;

    header("Location: quiz.php");
    exit();
  } else {
    $error = 'Nama dan NIM harus diisi.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
  <div class="flex justify-center h-[100vh]">
    <div class="w-full max-w-md p-8 space-y-6 bg-white shadow-2xl rounded-2xl h-fit my-auto">
      <h2 class="text-2xl font-extrabold text-center text-blue-900">Login</h2>

      <?php if ($error): ?>
        <div class="bg-red-200 text-red-600 p-2 rounded-lg mb-4"><?= $error ?></div>
      <?php endif; ?>

      <form action="login.php" method="post">
        <div class="mb-3 flex flex-col">
          <label class="text-gray-700" for="name">Name</label>
          <input type="text" id="name" name="name" class="form-control border border-black p-2 rounded-lg" required />
        </div>
        <div class="mb-3 flex flex-col">
          <label class="text-gray-700" for="nim">Student ID (NIM)</label>
          <input type="text" id="nim" name="nim" class="form-control border border-black p-2 rounded-lg" required />
        </div>
        <button class="w-full mt-4 py-2 px-8 font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-800">Login</button>
      </form>
    </div>
  </div>
</body>
</html>