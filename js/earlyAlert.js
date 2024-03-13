// When the document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Form submit event
    document.getElementById('earlyAlertForm').addEventListener('submit', function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Serialize form data
        const formData = new FormData(this);

        // Make fetch request
        fetch('earlyAlertApi.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            // Handle successful response
            if (response.ok) {
                return response.text();
            } else {
                throw new Error('Error in response');
            }
        })
        .then(response => {
            // Display success message
            const responseContainer = document.getElementById('responseContainer');
            responseContainer.className = 'alert alert-success';
            responseContainer.focus();
            responseContainer.innerHTML = response;
        })
        .catch(error => {
            // Handle errors
            console.error('Error:', error);
            const responseContainer = document.getElementById('responseContainer');
            responseContainer.focus();
            responseContainer.innerHTML = 'An error occurred. Please try again.';
        });
    });
});

function sendHeightToParent() {
    var height = document.body.scrollHeight;
    window.parent.postMessage({
        type: 'setHeight',
        height: height
    }, 'https://brocktest.brightspace.com');
}

window.onload = sendHeightToParent;