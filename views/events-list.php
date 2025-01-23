<?php
require_once '../config/config.php';
require_once '../includes/utils.php';

$page_title = "Events List";
include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1>Events</h1>
        <div id="eventsList" class="row"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('<?php echo SITE_URL; ?>/api/events.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderEvents(data.data);
                } else {
                    console.error('Failed to fetch events:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>

<?php include '../templates/footer.php'; ?>
