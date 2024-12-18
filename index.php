<?php
session_start(); 

if (isset($_SESSION['name']) && isset($_SESSION['nim'])) {
    header("Location: quiz.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Quiz App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex justify-center h-[100vh]">
        <div class="w-full max-w-md p-8 space-y-6 bg-white shadow-2xl rounded-2xl h-fit my-auto">
            <h2 class="text-3xl font-extrabold text-center text-blue-900">Welcome to the Quiz App</h2>
            <p class="text-center text-lg text-gray-600">Please log in to start the quiz.</p>

            <div class="text-center mt-4">
                <a href="login.php" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300">Go to Login</a>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
