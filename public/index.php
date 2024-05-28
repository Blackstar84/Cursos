<?php

session_start();

require_once '../vendor/autoload.php';
require_once '../src/controllers/AuthController.php';
require_once '../src/controllers/CourseController.php';

$authController = new AuthController();
$courseController = new CourseController();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['register'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($authController->register($username, $email, $password)) {
                echo "Registration successful!";
            } else {
                echo "Registration failed!";
            }
        } elseif (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($authController->login($email, $password)) {
                $_SESSION['user_id'] = $authController->getUserID($email);
                $_SESSION['username'] = $authController->getUsername($email);
                header("Location: /courses/public/index.php?view=courses");
                exit();
            } else {
                echo "Login failed!";
            }
        } elseif (isset($_POST['title']) && isset($_FILES['video']) && isset($_GET['course_id']) && isset($_GET['section_id'])) {
            if (!isset($_SESSION['user_id'])) {
                header("Location: /courses/public/index.php");
                exit();
            }

            $title = $_POST['title'];
            $course_id = $_GET['course_id'];
            $section_id = $_GET['section_id'];

            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . basename($_FILES["video"]["name"]);
            if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
                $courseController->createLesson($course_id, $section_id, $title, $target_file);
                header("Location: /courses/public/index.php?view=course_content&course_id=$course_id");
                exit();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } elseif (isset($_POST['section_title']) && isset($_GET['course_id'])) {
            if (!isset($_SESSION['user_id'])) {
                header("Location: /courses/public/index.php");
                exit();
            }

            $section_title = $_POST['section_title'];
            $course_id = $_GET['course_id'];
            $courseController->createSection($course_id, $section_title);
            header("Location: /courses/public/index.php?view=course_content&course_id=$course_id");
            exit();
        } elseif (isset($_GET['view']) && $_GET['view'] === 'save_progress') {
            // Manejar la solicitud para guardar el progreso del video
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['user_id']) && isset($data['lesson_id']) && isset($data['progress']) && isset($data['completed'])) {
                // Convertir 'completed' a booleano
                $completed = filter_var($data['completed'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($completed === null) {
                    $completed = false;
                }
                $courseController->saveVideoProgress($data['user_id'], $data['lesson_id'], $data['progress'], $completed);
                echo json_encode(['status' => 'success']);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
                exit();
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['view'])) {
            if ($_GET['view'] === 'get_progress' && isset($_GET['lesson_id']) && isset($_GET['user_id'])) {
                $progress = $courseController->getVideoProgress($_GET['user_id'], $_GET['lesson_id']);
                echo json_encode($progress);
                exit();
            } elseif ($_GET['view'] === 'delete_course' && isset($_GET['id'])) {
                $id = $_GET['id'];
                $courseController->delete($id);
                header("Location: /courses/public/index.php?view=courses");
                exit();
            } elseif ($_GET['view'] === 'delete_lesson' && isset($_GET['id']) && isset($_GET['course_id'])) {
                $id = $_GET['id'];
                $course_id = $_GET['course_id'];
                $courseController->deleteLesson($id);
                header("Location: /courses/public/index.php?view=course_content&course_id=$course_id");
                exit();
            } elseif ($_GET['view'] === 'lessons' && isset($_GET['course_id'])) {
                include '../src/views/lessons.php';
                exit();
            } elseif ($_GET['view'] === 'course_content' && isset($_GET['course_id'])) {
                if (!isset($_SESSION['user_id'])) {
                    header("Location: /courses/public/index.php");
                    exit();
                }
                include '../src/views/course_content.php';
                exit();
            }
        }
    }

    $view = isset($_GET['view']) ? $_GET['view'] : 'login';
    if ($view === 'register') {
        include '../src/views/register.php';
    } elseif ($view === 'courses') {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /courses/public/index.php");
            exit();
        }
        include '../src/views/courses.php';
    } elseif ($view === 'course_content') {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /courses/public/index.php");
            exit();
        }
        include '../src/views/course_content.php';
    } else {
        include '../src/views/login.php';
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}
?>
