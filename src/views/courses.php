<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: /courses/public/index.php");
    exit();
}

include 'header.php';

$courseController = new CourseController();
$courses = $courseController->index();
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Courses</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Courses</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-4">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($course['description']); ?></p>
                                <a href="index.php?view=course_content&course_id=<?php echo $course['id']; ?>" class="btn btn-primary">View Course</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>
<?php include 'footer.php'; ?>
