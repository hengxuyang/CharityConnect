// CharityConnect Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Handle AJAX form submissions
    setupAjaxForms();

    // Handle dynamic content loading
    setupDynamicContent();

    // Setup inventory management
    setupInventoryManagement();

    // Setup donation request management
    setupRequestManagement();

    // Setup donation management
    setupDonationManagement();
});

// AJAX Form Submissions
function setupAjaxForms() {
    const ajaxForms = document.querySelectorAll('form[data-ajax="true"]');
    
    ajaxForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            const method = this.getAttribute('method') || 'POST';
            const successMessage = this.getAttribute('data-success-message') || 'Operation completed successfully';
            const redirectUrl = this.getAttribute('data-redirect');
            
            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(successMessage, 'success');
                    
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    }
                } else {
                    showNotification(data.message || 'An error occurred', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while processing your request', 'danger');
            });
        });
    });
}

// Dynamic Content Loading
function setupDynamicContent() {
    const dynamicContainers = document.querySelectorAll('[data-dynamic-content]');
    
    dynamicContainers.forEach(container => {
        const url = container.getAttribute('data-url');
        const refreshInterval = parseInt(container.getAttribute('data-refresh-interval')) || 0;
        
        if (url) {
            loadContent(container, url);
            
            if (refreshInterval > 0) {
                setInterval(() => {
                    loadContent(container, url);
                }, refreshInterval * 1000);
            }
        }
    });
}

function loadContent(container, url) {
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        container.innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading content:', error);
    });
}

// Inventory Management
function setupInventoryManagement() {
    const inventoryForms = document.querySelectorAll('.inventory-update-form');
    
    inventoryForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Inventory updated successfully', 'success');
                    
                    // Refresh the inventory table if it exists
                    const inventoryTable = document.querySelector('#inventory-table');
                    if (inventoryTable) {
                        const tableContainer = inventoryTable.closest('[data-dynamic-content]');
                        if (tableContainer) {
                            loadContent(tableContainer, tableContainer.getAttribute('data-url'));
                        }
                    }
                } else {
                    showNotification(data.message || 'An error occurred', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating inventory', 'danger');
            });
        });
    });
}

// Donation Request Management
function setupRequestManagement() {
    // Handle request status updates
    const requestStatusForms = document.querySelectorAll('.request-status-form');
    
    requestStatusForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            const requestId = this.getAttribute('data-request-id');
            
            fetch(url, {
                method: 'PUT',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Request status updated successfully', 'success');
                    
                    // Update the request status badge
                    const statusBadge = document.querySelector(`#request-status-${requestId}`);
                    if (statusBadge) {
                        statusBadge.textContent = formData.get('status');
                        
                        // Update badge classes
                        statusBadge.classList.remove('bg-warning', 'bg-success', 'bg-danger');
                        
                        switch (formData.get('status')) {
                            case 'Pending':
                                statusBadge.classList.add('bg-warning');
                                break;
                            case 'Fulfilled':
                                statusBadge.classList.add('bg-success');
                                break;
                            case 'Cancelled':
                                statusBadge.classList.add('bg-danger');
                                break;
                        }
                    }
                } else {
                    showNotification(data.message || 'An error occurred', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating request status', 'danger');
            });
        });
    });
}

// Donation Management
function setupDonationManagement() {
    // Handle donation status updates
    const donationStatusForms = document.querySelectorAll('.donation-status-form');
    
    donationStatusForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            const donationId = this.getAttribute('data-donation-id');
            
            fetch(url, {
                method: 'PUT',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Donation status updated successfully', 'success');
                    
                    // Update the donation status badge
                    const statusBadge = document.querySelector(`#donation-status-${donationId}`);
                    if (statusBadge) {
                        statusBadge.textContent = formData.get('status');
                        
                        // Update badge classes
                        statusBadge.classList.remove('bg-warning', 'bg-success', 'bg-danger');
                        
                        switch (formData.get('status')) {
                            case 'Pending':
                                statusBadge.classList.add('bg-warning');
                                break;
                            case 'Delivered':
                                statusBadge.classList.add('bg-success');
                                break;
                            case 'Cancelled':
                                statusBadge.classList.add('bg-danger');
                                break;
                        }
                    }
                } else {
                    showNotification(data.message || 'An error occurred', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating donation status', 'danger');
            });
        });
    });
    
    // Handle thank you notes
    const thankYouForms = document.querySelectorAll('.thank-you-form');
    
    thankYouForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Thank you note sent successfully', 'success');
                    
                    // Close the modal if it exists
                    const modal = this.closest('.modal');
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        bsModal.hide();
                    }
                    
                    // Reset the form
                    this.reset();
                } else {
                    showNotification(data.message || 'An error occurred', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while sending thank you note', 'danger');
            });
        });
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const notificationContainer = document.getElementById('notification-container');
    
    if (!notificationContainer) {
        // Create notification container if it doesn't exist
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    
    const notification = document.createElement('div');
    notification.className = `toast align-items-center text-white bg-${type} border-0`;
    notification.setAttribute('role', 'alert');
    notification.setAttribute('aria-live', 'assertive');
    notification.setAttribute('aria-atomic', 'true');
    
    notification.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    document.getElementById('notification-container').appendChild(notification);
    
    const toast = new bootstrap.Toast(notification, {
        autohide: true,
        delay: 5000
    });
    
    toast.show();
}
