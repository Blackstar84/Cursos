// assets/js/main.js
document.addEventListener("DOMContentLoaded", () => {
    const courseForm = document.getElementById('courseForm');
    const lessonForm = document.getElementById('lessonForm');

    if (courseForm) {
        courseForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(courseForm);
            const response = await fetch('path_to_course_handler', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            // Handle result
        });
    }

    if (lessonForm) {
        lessonForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(lessonForm);
            const response = await fetch('path_to_lesson_handler', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            // Handle result
        });
    }
});
