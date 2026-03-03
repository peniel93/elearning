<?php
require_once __DIR__ . '/../config.php';

class Quiz {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($lesson_id, $question, $options, $correct_answer) {
        $options_json = json_encode($options);
        $stmt = $this->pdo->prepare("INSERT INTO quizzes (lesson_id, question, options, correct_answer) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$lesson_id, $question, $options_json, $correct_answer]);
    }

    public function getByLessonId($lesson_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE lesson_id = ?");
        $stmt->execute([$lesson_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function submit($user_id, $quiz_id, $score) {
        $stmt = $this->pdo->prepare("INSERT INTO quiz_submissions (user_id, quiz_id, score) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $quiz_id, $score]);
    }
}
?>