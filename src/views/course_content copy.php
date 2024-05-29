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
            <div id="accordion">
                <?php foreach ($sections as $index => $section): ?>
                    <div class="card card-primary">
                        <div class="card-header" id="heading<?php echo $index; ?>">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($section['title']); ?>
                                </button>
                            </h5>
                        </div>
                        <div id="collapse<?php echo $index; ?>" class="collapse" aria-labelledby="heading<?php echo $index; ?>" data-parent="#accordion" data-section-id="section<?php echo $index; ?>">
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
                                                <video width="320" height="240" controls class="mt-2" data-lesson-id="<?php echo $lesson['id']; ?>" data-section-id="section<?php echo $index; ?>">
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
                    </div>
                <?php endforeach; ?>
            </div>
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
    const userId = <?php echo $_SESSION['user_id']; ?>;
    const sectionProgress = new Map(); // To track progress by section
    const fetchPromises = [];

    videos.forEach(video => {
        const lessonId = video.getAttribute('data-lesson-id');
        const sectionId = video.getAttribute('data-section-id');

        if (!sectionProgress.has(sectionId)) {
            sectionProgress.set(sectionId, true); // Assume section is completed
        }

        // Fetch progress from the server
        const fetchPromise = fetch(`/courses/public/index.php?view=get_progress&lesson_id=${lessonId}&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                console.log(`Progress for lesson ${lessonId}:`, data); // Debugging line
                if (data && data.progress) {
                    video.currentTime = data.progress;
                }
                if (data && !data.completed) {
                    sectionProgress.set(sectionId, false); // Mark section as not completed
                }
            });

        fetchPromises.push(fetchPromise);

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

    // Ensure sections are opened/closed after all fetch requests are completed
    Promise.all(fetchPromises).then(() => {
        sectionProgress.forEach((isCompleted, sectionId) => {
            const sectionElement = document.querySelector(`[data-section-id="${sectionId}"]`);
            console.log('Final Section State - Section ID:', sectionId, 'isCompleted:', isCompleted); // Debugging line
            if (sectionElement) {
                if (!isCompleted) {
                    sectionElement.classList.add('show');
                } else {
                    sectionElement.classList.remove('show');
                }
            }
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
