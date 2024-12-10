<?php
session_start();

if (!isset($_SESSION['name']) || !isset($_SESSION['nim'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quizResults = [
        'name' => htmlspecialchars($_SESSION['name']),
        'nim' => htmlspecialchars($_SESSION['nim']),
        'totalScore' => filter_input(INPUT_POST, 'totalScore', FILTER_VALIDATE_INT),
        'correctAnswers' => filter_input(INPUT_POST, 'correctAnswers', FILTER_VALIDATE_INT),
        'answeredQuestions' => filter_input(INPUT_POST, 'answeredQuestions', FILTER_VALIDATE_INT),
        'questions' => json_decode($_POST['questions'], true),
        'answers' => json_decode($_POST['answers'], true)
    ];

    if (
        $quizResults['totalScore'] === false || $quizResults['correctAnswers'] === false ||
        $quizResults['answeredQuestions'] === false
    ) {
        die('Invalid quiz data submitted');
    }

    $_SESSION['quizResults'] = json_encode($quizResults);
    header('Location: results.php');
    exit();
}

$questionsFile = 'quiz.js';
if (!file_exists($questionsFile)) {
    die('Questions file not found');
}
$questions = json_decode(file_get_contents($questionsFile), true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes countdown {
            from {
                stroke-dashoffset: 0;
            }

            to {
                stroke-dashoffset: 251.2;
            }
        }

        .timer-circle {
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        .timer-circle circle {
            stroke-dasharray: 251.2;
            stroke-linecap: round;
            transition: stroke-dashoffset 1s linear;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-600">Name: </span>
                        <span id="playerName" class="font-semibold"><?= $_SESSION['name'] ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">NIM: </span>
                        <span id="playerNIM" class="font-semibold"><?= $_SESSION['nim'] ?></span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div class="relative w-16 h-16">
                    <svg class="timer-circle w-16 h-16">
                        <circle cx="32" cy="32" r="28" fill="none" stroke="#e5e7eb" stroke-width="4" />
                        <circle id="timerCircle" cx="32" cy="32" r="28" fill="none" stroke="#3b82f6"
                            stroke-width="4" style="animation: countdown 30s linear infinite" />
                    </svg>
                    <span id="timer" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
                                         text-xl font-bold text-blue-600">30</span>
                </div>

                <div class="flex-1 ml-6">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-600">Progress</span>
                        <span class="text-sm text-gray-600">
                            Question <span id="currentQuestion">1</span> of <span id="totalQuestions">10</span>
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                            id="progressBar" style="width: 10%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4" id="questionText">Loading question...</h2>

                <div id="multipleChoiceContainer" class="space-y-3 mb-4">
                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                        <input type="radio" name="answer" id="optionA" value="A" class="mr-3">
                        <label for="optionA" id="labelA" class="text-gray-700 cursor-pointer w-full">Option A</label>
                    </div>
                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                        <input type="radio" name="answer" id="optionB" value="B" class="mr-3">
                        <label for="optionB" id="labelB" class="text-gray-700 cursor-pointer w-full">Option B</label>
                    </div>
                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                        <input type="radio" name="answer" id="optionC" value="C" class="mr-3">
                        <label for="optionC" id="labelC" class="text-gray-700 cursor-pointer w-full">Option C</label>
                    </div>
                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition cursor-pointer">
                        <input type="radio" name="answer" id="optionD" value="D" class="mr-3">
                        <label for="optionD" id="labelD" class="text-gray-700 cursor-pointer w-full">Option D</label>
                    </div>
                </div>

                <div id="textInputContainer" class="hidden">
                    <input type="text" id="textAnswer" placeholder="Type your answer here"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 
                                  focus:outline-none hover:border-blue-300 transition">
                </div>
            </div>

            <div class="flex justify-between items-center">
                <button id="prevButton" onclick="previousQuestion()"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 
                               transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <div class="text-center">
                    <span class="text-gray-600">
                        Answered: <span id="answeredCount" class="font-bold text-blue-600">0</span>
                        /<span id="totalCount">10</span>
                    </span>
                </div>
                <button id="nextButton" onclick="nextQuestion()"
                    class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 
                               transition duration-300">
                    Next
                </button>
            </div>
        </div>
    </div>
    <script>
        const userSession = {
            name: <?= json_encode($_SESSION['name']) ?>,
            nim: <?= json_encode($_SESSION['nim']) ?>
        };
        const serverQuestions = <?= json_encode($questions) ?>;
    </script>
    <script src="quiz.js"></script>
</body>

</html>