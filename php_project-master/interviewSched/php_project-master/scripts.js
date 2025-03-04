// scripts.js
document.addEventListener('DOMContentLoaded', function () {
    // Add event listeners for delete buttons
    document.querySelectorAll('.delete').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent form submission
            const interviewId = this.closest('form').querySelector('input[name="id"]').value;
            if (confirm('Are you sure you want to delete this interview?')) {
                fetch(`delete_interview.php?id=${interviewId}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Interview deleted successfully!');
                        window.location.reload(); // Refresh the page
                    } else {
                        alert('Error deleting interview.');
                    }
                });
            }
        });
    });

    // Add event listeners for cancel buttons
    document.querySelectorAll('.cancel').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent form submission
            const interviewId = this.closest('form').querySelector('input[name="id"]').value;
            if (confirm('Are you sure you want to cancel this interview?')) {
                fetch(`cancel_interview.php?id=${interviewId}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Interview canceled successfully!');
                        window.location.reload(); // Refresh the page
                    } else {
                        alert('Error canceling interview.');
                    }
                });
            }
        });
    });
});