<?php
session_start();

if (!isset($_SESSION['name']) || !isset($_SESSION['nim'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['quizResults'])) {
    header("Location: quiz.php");
    exit();
}

try {
    $results = json_decode($_SESSION['quizResults'], true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    error_log("Error decoding quiz results: " . $e->getMessage());
    header("Location: quiz.php");
    exit();
}

$requiredFields = ['totalScore', 'correctAnswers', 'answeredQuestions', 'questions', 'answers'];
foreach ($requiredFields as $field) {
    if (!isset($results[$field])) {
        error_log("Missing required field in quiz results: $field");
        header("Location: quiz.php");
        exit();
    }
}

$totalPossibleScore = array_reduce($results['questions'], function($sum, $question) {
    return $sum + ($question['points'] ?? 0);
}, 0);

$percentage = ($results['totalScore'] / $totalPossibleScore) * 100;
$grade = 'A';
if ($percentage < 90) $grade = 'B';
if ($percentage < 80) $grade = 'C';
if ($percentage < 70) $grade = 'D';
if ($percentage < 60) $grade = 'F';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-extrabold text-center text-blue-900 mb-4">Quiz Completed!</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">Name:</p>
                        <p class="font-bold"><?= htmlspecialchars($_SESSION['name']) ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">NIM:</p>
                        <p class="font-bold"><?= htmlspecialchars($_SESSION['nim']) ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-gray-600">Total Score</p>
                        <p class="text-xl font-bold text-blue-600"><?= $results['totalScore'] ?>/<?= $totalPossibleScore ?></p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-gray-600">Correct Answers</p>
                        <p class="text-xl font-bold text-green-600"><?= $results['correctAnswers'] ?>/<?= count($results['questions']) ?></p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <p class="text-gray-600">Final Grade</p>
                        <p class="text-xl font-bold <?= 
                            $grade === 'A' ? 'text-green-600' :
                            ($grade === 'B' ? 'text-blue-600' :
                            ($grade === 'C' ? 'text-yellow-600' :
                            ($grade === 'D' ? 'text-orange-600' : 'text-red-600')))
                        ?>"><?= $grade ?></p>
                    </div>
                </div>
            </div>

            <div id="questionBreakdown" class="space-y-4">
                <?php foreach ($results['questions'] as $index => $question): 
                    $userAnswer = $results['answers'][$index] ?? null;
                    $isCorrect = false;
                    
                    if ($question['type'] === 'multiple') {
                        $isCorrect = $userAnswer === $question['correctAnswer'];
                    } else {
                        $isCorrect = strtolower($userAnswer ?? '') === strtolower($question['correctAnswer']);
                    }
                ?>
                    <div class="p-4 border rounded-lg <?= $isCorrect ? 'bg-green-50' : 'bg-red-50' ?>">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-medium">Question <?= $index + 1 ?></p>
                                <p class="text-gray-600"><?= htmlspecialchars($question['question']) ?></p>
                                <p class="mt-2">
                                    Your answer: 
                                    <span class="font-medium">
                                        <?php if ($question['type'] === 'multiple' && $userAnswer): ?>
                                            <?= htmlspecialchars($userAnswer . ': ' . ($question['options'][$userAnswer] ?? '')) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($userAnswer ?? 'Not answered') ?>
                                        <?php endif; ?>
                                    </span>
                                </p>
                                <p>
                                    Correct answer: 
                                    <span class="font-medium">
                                        <?php if ($question['type'] === 'multiple'): ?>
                                            <?= htmlspecialchars($question['correctAnswer'] . ': ' . 
                                                ($question['options'][$question['correctAnswer']] ?? '')) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($question['correctAnswer']) ?>
                                        <?php endif; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="ml-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    <?= $isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $isCorrect ? '+' . $question['points'] : '0' ?> points
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-center mt-6 space-x-4">
                <a href="quiz.php" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 
                                       transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Retry Quiz
                </a>
                <a href="logout.php" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 
                                         transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Logout
                </a>
            </div>
        </div>
    </div>

    <script>
        const quizResults = <?= json_encode($results, JSON_HEX_TAG | JSON_HEX_QUOT) ?>;
    </script>
    <script src="results.js" defer></script>
</body>
</html>