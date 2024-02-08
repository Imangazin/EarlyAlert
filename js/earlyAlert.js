document.addEventListener('DOMContentLoaded', () => {
    // Form submit event
    const form = document.getElementById('AlertForm');
    const responseContainer = document.getElementById('responseContainer');
    form.addEventListener('submit', (event) => {    // Prevent the default form
      event.preventDefault();
  
      // Serialize form data
      const formData = new FormData(form);
  
      // Make AJAX request with fetch
      fetch('earlyAlertApi.php', {
        method: 'POST',
        body: formData,
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Error in API response');
        }
        return response.text();
      })
      .then(response => {
        // Handle successful response
        document.getElementById('advisor').disabled = true;
        document.getElementById('early_alert_btn').disabled = true;
        responseContainer.className = 'alert alert-success';
        responseContainer.focus();
        responseContainer.textContent = response;
      })
      .catch(error => {
        // Handle errors
        console.error('Error:', error);
        responseContainer.focus();
        responseContainer.textContent = error.message;
      });
    });
  });