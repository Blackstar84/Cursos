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
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createSectionModal">
                Add Section
            </button>
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
                        <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#createLessonModal" data-section-id="<?php echo $section['id']; ?>">
                            Add Lesson
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<!-- Modal for Creating Section -->
<div class="modal fade" id="createSectionModal" tabindex="-1" role="dialog" aria-labelledby="createSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSectionModalLabel">Add Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?view=course_content&course_id=<?php echo $course_id; ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="section_title">Title</label>
                        <input type="text" name="section_title" class="form-control" required>
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
            <form id="createLessonForm" action="index.php?view=course_content&course_id=<?php echo $course_id; ?>&section_id=" method="post" enctype="multipart/form-data">
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
                if (data && data.progress) {
                    video.currentTime = data.progress;
                }
            });

        video.addEventListener('pause', () => {
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
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    console.error('Error saving progress:', data);
                }
            })
            .catch(error => {
                console.error('Error saving progress:', error);
            });
        });
    });

    // Handle passing section id to the lesson creation form
    $('#createLessonModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var sectionId = button.data('section-id');
        var form = document.getElementById('createLessonForm');
        form.action = 'index.php?view=course_content&course_id=<?php echo $course_id; ?>&section_id=' + sectionId;
    });
});
</script>
