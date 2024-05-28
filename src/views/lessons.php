<?php


if (!isset($_SESSION['user_id'])) {
    header("Location: /courses/public/index.php");
    exit();
}

include 'header.php';

$course_id = $_GET['course_id'];
$courseController = new CourseController();
$course = $courseController->show($course_id);
$lessons = $courseController->getLessons($course_id);
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Lessons for <?php echo htmlspecialchars($course['title']); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?view=courses">Courses</a></li>
                        <li class="breadcrumb-item active">Lessons</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lesson List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createLessonModal">
                                    Add Lesson
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($lessons)): ?>
                                <div class="alert alert-info">No lessons available.</div>
                            <?php else: ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Video</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lessons as $lesson): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                                                <td>
                                                    <video width="320" height="240" controls data-lesson-id="<?php echo $lesson['id']; ?>">
                                                        <source src="<?php echo htmlspecialchars($lesson['video_path']); ?>" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </td>
                                                <td>
                                                    <a href="index.php?view=edit_lesson&id=<?php echo $lesson['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="index.php?view=delete_lesson&id=<?php echo $lesson['id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this lesson?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for Creating Lesson -->
    <div class="modal fade" id="createLessonModal" tabindex="-1" role="dialog" aria-labelledby="createLessonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLessonModalLabel">Add Lesson</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="index.php?view=create_lesson&course_id=<?php echo $course_id; ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="video">Video</label>
                            <input type="file" name="video" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const videos = document.querySelectorAll('video');
    videos.forEach(video => {
        const lessonId = video.getAttribute('data-lesson-id');
        const userId = <?php echo $_SESSION['user_id']; ?>;
        
        // Fetch progress from the server
        fetch(`/courses/public/index.php?view=get_progress&lesson_id=${lessonId}&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.progress) {
                    video.currentTime = data.progress;
                }
            });

        video.addEventListener('timeupdate', () => {
            const progress = video.currentTime;
            const completed = video.ended;

            fetch(`/courses/public/index.php?view=save_progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    lesson_id: lessonId,
                    progress: progress,
                    completed: completed
                })
            });
        });
    });
});
</script>
