// Toggle notification dropdown
document.addEventListener('DOMContentLoaded', function() {
    const notificationButton = document.getElementById('notification-button');
    const notificationDropdown = document.getElementById('notification-dropdown');

    if (notificationButton && notificationDropdown) {
        // Toggle dropdown on button click
        notificationButton.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });
    }

    // Mark notification as read when clicked
    document.querySelectorAll('.mark-as-read').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationItem = this.closest('li');
                    notificationItem.classList.remove('bg-gray-50');
                    notificationItem.classList.add('bg-white');
                    this.remove();
                    
                    // Update notification count
                    const notificationCount = document.querySelectorAll('.mark-as-read').length - 1;
                    const notificationBadge = document.querySelector('#notification-button .bg-red-500');
                    
                    if (notificationCount <= 0 && notificationBadge) {
                        notificationBadge.remove();
                    }
                }
            });
        });
    });
});
