<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: /courses/public/index.php");
    exit();
}

include 'header.php';

$course_id = $_GET['course_id'];
$courseController = new CourseController();
$sections = $courseController->getSections($course_id);
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Course Content</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?view=courses">Courses</a></li>
                        <li class="breadcrumb-item active">Course Content</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php foreach ($sections as $section): ?>
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo htmlspecialchars($section['title']); ?></h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $lessons = $courseController->getLessonsBySection($section['id']);
                        if (empty($lessons)):
                        ?>
                            <p>No lessons available for this section.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($lessons as $lesson): ?>
                                    <li class="list-group-item">
                                        <h4><?php echo htmlspecialchars($lesson['title']); ?></h4>
                                        <video width="320" height="240" controls class="mt-2" data-lesson-id="<?php echo $lesson['id']; ?>">
                                            <source src="<?php echo htmlspecialchars($lesson['video_path']); ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
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
