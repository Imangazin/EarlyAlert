// When the document is ready
$(document).ready(function() {
    // Form submit event
    $("#earlyAlertForm").submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Serialize form data
        var formData = $(this).serialize();

        // Make AJAX request
        $.ajax({
            url: "earlyAlertApi.php", // Replace with your server endpoint
            type: "POST",
            data: formData,
            success: function(response) {
                // Handle successful response
                $("#responseContainer").className = 'alert alert-success';
                $("#responseContainer").focus();
                $("#responseContainer").html(response);
                
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error("Error:", status, error);
                $("#responseContainer").focus();
                $("#responseContainer").html(response);
            }
        });
    });
});