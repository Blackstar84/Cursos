<?php
require_once __DIR__ . '/../models/CourseModel.php';

class CourseController {
    private $courseModel;

    public function __construct() {
        $this->courseModel = new CourseModel();
    }

    public function index() {
        return $this->courseModel->getCourses();
    }


    public function getSections($course_id) {
        return $this->courseModel->getSections($course_id);
    }

    public function getLessonsBySection($section_id) {
        return $this->courseModel->getLessonsBySection($section_id);
    }

    public function show($course_id) {
        return $this->courseModel->getCourse($course_id);
    }

    public function createSection($course_id, $title) {
        return $this->courseModel->createSection($course_id, $title);
    }

    public function createLesson($course_id, $section_id, $title, $video_path) {
        return $this->courseModel->createLesson($course_id, $section_id, $title, $video_path);
    }
    public function deleteLesson($id) {
        return $this->courseModel->deleteLesson($id);
    }

    public function saveVideoProgress($user_id, $lesson_id, $progress, $completed) {
        return $this->courseModel->saveVideoProgress($user_id, $lesson_id, $progress, $completed);
    }
    
    public function getVideoProgress($user_id, $lesson_id) {
        return $this->courseModel->getVideoProgress($user_id, $lesson_id);
    }

    public function create($title, $description) {
        return $this->courseModel->createCourse($title, $description);
    }

    public function update($id, $title, $description) {
        return $this->courseModel->updateCourse($id, $title, $description);
    }

    public function delete($id) {
        return $this->courseModel->deleteCourse($id);
    }

}