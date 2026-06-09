// MailBox Configuration JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const providerButtons = document.querySelectorAll('.provider-btn');
    const configForm = document.getElementById('mailbox-config-form');
    const formFields = {
        email: document.getElementById('mail_username'),
        password: document.getElementById('mail_password'),
        imapHost: document.getElementById('mail_host'),
        imapPort: document.getElementById('incoming_port'),
        smtpPort: document.getElementById('outgoing_port'),
        encryption: document.getElementById('mail_encryption'),
        fromName: document.getElementById('mail_from_name')
    };

    const providerConfigs = {
        gmail: { imap_host: 'imap.gmail.com', imap_port: 993, smtp_port: 587, encryption: 'ssl' },
        outlook: { imap_host: 'outlook.office365.com', imap_port: 993, smtp_port: 587, encryption: 'tls' },
        yahoo: { imap_host: 'imap.mail.yahoo.com', imap_port: 993, smtp_port: 587, encryption: 'ssl' },
        icloud: { imap_host: 'imap.mail.me.com', imap_port: 993, smtp_port: 587, encryption: 'ssl' }
    };

    // Provider button click handlers
    providerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const provider = this.dataset.provider;
            
            // Remove active class from all buttons
            providerButtons.forEach(btn => btn.classList.remove('active', 'btn-primary'));
            providerButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
            
            // Add active class to clicked button
            this.classList.remove('btn-outline-primary');
            this.classList.add('active', 'btn-primary');
            
            // Set hidden provider field
            const providerInput = document.getElementById('selected_provider');
            if (providerInput) {
                providerInput.value = provider;
            }
            
            if (provider === 'custom') {
                enableAllFields();
                clearAllFields();
            } else {
                disableConfigFields();
                fillProviderConfig(provider);
            }
        });
    });

    function enableAllFields() {
        // Enable ALL fields for custom setup - ALL WRITABLE
        Object.values(formFields).forEach(field => {
            if (field) {
                field.disabled = false;
                field.readOnly = false;
                field.classList.remove('bg-light', 'text-muted');
                field.style.cursor = 'text';
            }
        });
    }

    function disableConfigFields() {
        // Disable server configuration fields - NOT WRITABLE
        ['imapHost', 'imapPort', 'smtpPort', 'encryption'].forEach(fieldKey => {
            const field = formFields[fieldKey];
            if (field) {
                field.disabled = true;
                field.readOnly = true;
                field.classList.add('bg-light', 'text-muted');
                field.style.cursor = 'not-allowed';
            }
        });
        
        // Keep user fields enabled - WRITABLE
        ['email', 'password', 'fromName'].forEach(fieldKey => {
            const field = formFields[fieldKey];
            if (field) {
                field.disabled = false;
                field.readOnly = false;
                field.classList.remove('bg-light', 'text-muted');
                field.style.cursor = 'text';
            }
        });
    }

    function fillProviderConfig(provider) {
        const config = providerConfigs[provider];
        if (config) {
            if (formFields.imapHost) formFields.imapHost.value = config.imap_host;
            if (formFields.imapPort) formFields.imapPort.value = config.imap_port;
            if (formFields.smtpPort) formFields.smtpPort.value = config.smtp_port;
            if (formFields.encryption) formFields.encryption.value = config.encryption;
        }
    }

    function clearAllFields() {
        Object.values(formFields).forEach(field => {
            if (field && field.id !== 'mail_username' && field.id !== 'mail_password') {
                field.value = '';
            }
        });
    }

    // Test connection functionality
    const testButton = document.getElementById('test-connection');
    if (testButton) {
        testButton.addEventListener('click', function() {
            const formData = new FormData(configForm);
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
            
            fetch('/mailbox/test-connection', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                showAlert('error', 'Connection test failed');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-plug"></i> Test Connection';
            });
        });
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});