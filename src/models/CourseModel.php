<?php
require_once __DIR__ . '/../../config/database.php';

class CourseModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getCourses() {
        $query = "SELECT * FROM courses ORDER BY id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourseById($id) {
        $query = "SELECT * FROM courses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCourse($title, $description) {
        $query = "INSERT INTO courses (title, description) VALUES (:title, :description)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function updateCourse($id, $title, $description) {
        $query = "UPDATE courses SET title = :title, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function deleteCourse($id) {
        $query = "DELETE FROM courses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getCourse($course_id) {
        $query = "SELECT * FROM courses WHERE id = :course_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSections($course_id) {
        $query = "SELECT * FROM sections WHERE course_id = :course_id ORDER BY id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLessonsBySection($section_id) {
        $query = "SELECT * FROM lessons WHERE section_id = :section_id ORDER BY id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':section_id', $section_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createSection($course_id, $title) {
        $query = "INSERT INTO sections (course_id, title) VALUES (:course_id, :title)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':title', $title);
        return $stmt->execute();
    }

    public function createLesson($course_id, $section_id, $title, $video_path) {
        $query = "INSERT INTO lessons (course_id, section_id, title, video_path) VALUES (:course_id, :section_id, :title, :video_path)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':section_id', $section_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':video_path', $video_path);
        return $stmt->execute();
    }

    public function deleteLesson($id) {
        $query = "DELETE FROM lessons WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function saveVideoProgress($user_id, $lesson_id, $progress, $completed) {
        $query = "INSERT INTO video_progress (user_id, lesson_id, progress, completed) 
                  VALUES (:user_id, :lesson_id, :progress, :completed)
                  ON CONFLICT (user_id, lesson_id) DO UPDATE 
                  SET progress = EXCLUDED.progress, completed = EXCLUDED.completed";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $stmt->bindValue(':progress', $progress, PDO::PARAM_STR);  // Asumiendo que el progreso es un número decimal, puedes ajustarlo a PARAM_INT si es necesario
        $stmt->bindValue(':completed', $completed, PDO::PARAM_BOOL);  // Asegúrate de que el valor sea booleano
        return $stmt->execute();
    }
    
    public function getVideoProgress($user_id, $lesson_id) {
        $query = "SELECT progress, completed FROM video_progress WHERE user_id = :user_id AND lesson_id = :lesson_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':lesson_id', $lesson_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
     

}
