document.addEventListener('DOMContentLoaded', () => {
    const boxes = document.querySelectorAll('.inspiredmonks-clickable-box');
    const progressFill = document.getElementById('progress-fill');
    const progressLabel = document.getElementById('progress-label');

    const securityCount = document.getElementById('security-count');
    const privacyCount = document.getElementById('privacy-count');
    const crossOriginCount = document.getElementById('cross-origin-count');

    // Update the progress bar based on active headers
    const updateProgress = () => {
        const activeCount = document.querySelectorAll('.inspiredmonks-clickable-box.active').length;
        const totalCount = boxes.length;
        const percentage = (activeCount / totalCount) * 100;

        // Update the progress bar width and label text
        progressFill.style.width = `${percentage}%`;
        progressLabel.textContent = `${activeCount}/${totalCount} Headers Enabled`;
    };

    // Update the category counts based on active headers in each category
    const updateCategoryCounts = () => {
        const securityActive = document.querySelectorAll('.inspiredmonks-clickable-box.security.active').length;
        const privacyActive = document.querySelectorAll('.inspiredmonks-clickable-box.privacy.active').length;
        const crossOriginActive = document.querySelectorAll('.inspiredmonks-clickable-box.cross-origin.active').length;

        securityCount.textContent = securityActive;
        privacyCount.textContent = privacyActive;
        crossOriginCount.textContent = crossOriginActive;
    };

    // Add click event listener to each header box
    boxes.forEach(box => {
        box.addEventListener('click', () => {
            // Toggle the 'active' class on click (this will activate/deactivate the box)
            box.classList.toggle('active');
            const inputName = box.getAttribute('data-name');
            const input = document.getElementById(inputName);
            
            // Update the checkbox value to reflect the 'active' state
            if (box.classList.contains('active')) {
                input.value = '1'; // Activated
            } else {
                input.value = '0'; // Deactivated
            }

            // Update the progress bar and category counts after the toggle
            updateProgress();
            updateCategoryCounts();
        });
    });

    // Initial update when the page loads
    updateProgress();
    updateCategoryCounts();
});