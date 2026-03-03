<?php
session_start();
require_once 'config.php';
require_once 'models/Course.php';
require_once 'models/Lesson.php';
require_once 'models/Quiz.php';
require_once 'models/Enrollment.php';
require_once 'models/Notification.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$course_id = $_GET['id'] ?? 0;
$courseModel = new Course($pdo);
$lessonModel = new Lesson($pdo);
$quizModel = new Quiz($pdo);
$enrollmentModel = new Enrollment($pdo);
//$notificationModel = new Notification($pdo);

$course = $courseModel->getById($course_id);
if (!$course) {
    die("Course not found");
}

$lessons = $lessonModel->getByCourseId($course_id);

// Handle enrollment
if (isset($_POST['enroll'])) {
    if ($enrollmentModel->enroll($_SESSION['user_id'], $course_id)) {
        $notificationModel->create($_SESSION['user_id'], "Enrolled in " . $course['title']);
    }
}

// Handle lesson creation (if instructor)
if (isset($_POST['create_lesson']) && $_SESSION['role'] === 'instructor' && $course['instructor_id'] == $_SESSION['user_id']) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $media_path = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $target_dir = "uploads/";
        $media_path = $target_dir . basename($_FILES["media"]["name"]);
        move_uploaded_file($_FILES["media"]["tmp_name"], $media_path);
    }
    $lessonModel->create($course_id, $title, $content, $media_path);
    header("Location: course.php?id=$course_id");
    exit;
}

// Handle quiz submission (simplified)
if (isset($_POST['submit_quiz'])) {
    $quiz_id = $_POST['quiz_id'];
    $answer = $_POST['answer'];
    $quiz = $quizModel->getByLessonId($_POST['lesson_id'])[0];  // Assume one quiz per lesson for simplicity
    $score = ($answer === $quiz['correct_answer']) ? 100 : 0;
    $quizModel->submit($_SESSION['user_id'], $quiz_id, $score);
    // Update progress (simplified)
    $enrollmentModel->updateProgress($_SESSION['user_id'], $course_id, 50);  // Example
}

$is_enrolled = $enrollmentModel->getProgress($_SESSION['user_id'], $course_id) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p><?php echo htmlspecialchars($course['description']); ?></p>
        
        <?php if (!$is_enrolled && $_SESSION['role'] === 'student'): ?>
            <form method="POST">
                <button type="submit" name="enroll" class="btn btn-success">Enroll</button>
            </form>
        <?php endif; ?>
        
        <?php if ($is_enrolled || $_SESSION['role'] !== 'student'): ?>
            <h2>Lessons</h2>
            <ul class="list-group">
                <?php foreach ($lessons as $lesson): ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($lesson['title']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($lesson['content'])); ?></p>
                        <?php if ($lesson['media_path']): ?>
                            <a href="<?php echo $lesson['media_path']; ?>" target="_blank">View Media</a>
                        <?php endif; ?>
                        
                        <!-- Quiz (assume one per lesson) -->
                        <?php $quizzes = $quizModel->getByLessonId($lesson['id']); ?>
                        <?php if (!empty($quizzes)): $quiz = $quizzes[0]; ?>
                            <h6>Quiz: <?php echo htmlspecialchars($quiz['question']); ?></h6>
                            <form method="POST">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
                                <?php $options = json_decode($quiz['options']); foreach ($options as $opt): ?>
                                    <div class="form-check">
                                        <input type="radio" name="answer" value="<?php echo substr($opt, 0, 1); ?>" class="form-check-input">
                                        <label><?php echo htmlspecialchars($opt); ?></label>
                                    </div>
                                <?php endforeach; ?>
                                <button type="submit" name="submit_quiz" class="btn btn-primary mt-2">Submit Quiz</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <?php if ($_SESSION['role'] === 'instructor' && $course['instructor_id'] == $_SESSION['user_id']): ?>
            <h2>Add Lesson</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label>Media Upload</label>
                    <input type="file" name="media" class="form-control-file">
                </div>
                <button type="submit" name="create_lesson" class="btn btn-primary">Add Lesson</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>