document.addEventListener('DOMContentLoaded', () => {
    initializeInteractiveFeatures();
});

function initializeInteractiveFeatures() {
    document.querySelectorAll('.question-detail-toggle').forEach(button => {
        button.addEventListener('click', toggleQuestionDetail);
    });

    initializeCharts();
}

function initializeCharts() {
    const correctAnswers = quizResults.correctAnswers;
    const incorrectAnswers = quizResults.questions.length - correctAnswers;
   
    updateScoreVisuals(correctAnswers, incorrectAnswers);
}

function updateScoreVisuals(correct, incorrect) {
    const total = correct + incorrect;
    const percentage = (correct / total) * 100;

    document.querySelectorAll('.score-percentage').forEach(element => {
        element.textContent = `${Math.round(percentage)}%`;
    });
}

function toggleQuestionDetail(event) {
    const detailSection = event.target.closest('.question-container')
                                   .querySelector('.question-details');
    if (detailSection) {
        detailSection.classList.toggle('hidden');
    }
}

window.addEventListener('error', function(e) {
    console.error('Results page error:', e.error);
});

window.history.pushState(null, '', window.location.href);
window.addEventListener('popstate', function() {
    window.history.pushState(null, '', window.location.href);
});