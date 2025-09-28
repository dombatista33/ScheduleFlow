// JavaScript functionality for Terapia e Bem Estar appointment system

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeFormValidation();
    initializePhoneFormatting();
    initializeNavigation();
    initializeCalendar();
});

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'Este campo é obrigatório');
            isValid = false;
        } else {
            clearFieldError(field);
        }
        
        // Email validation
        if (field.type === 'email' && field.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                showFieldError(field, 'Por favor, insira um e-mail válido');
                isValid = false;
            }
        }
        
        // Phone validation (Brazilian format)
        if (field.type === 'tel' && field.value.trim()) {
            const phoneRegex = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
            if (!phoneRegex.test(field.value)) {
                showFieldError(field, 'Por favor, insira um telefone válido no formato (11) 99999-9999');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = 'var(--warning-color)';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '0.3rem';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
    field.style.borderColor = 'var(--warning-color)';
}

function clearFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    field.style.borderColor = '';
}

// Phone number formatting
function initializePhoneFormatting() {
    const phoneFields = document.querySelectorAll('input[type="tel"], input[name="whatsapp"]');
    
    phoneFields.forEach(field => {
        field.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                if (value.length === 11) {
                    value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                } else if (value.length === 10) {
                    value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                } else if (value.length > 2) {
                    value = value.replace(/(\d{2})(\d+)/, '($1) $2');
                }
            }
            
            e.target.value = value;
        });
        
        field.addEventListener('blur', function(e) {
            // Add validation feedback on blur
            if (e.target.value && e.target.required) {
                const phoneRegex = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
                if (!phoneRegex.test(e.target.value)) {
                    showFieldError(e.target, 'Formato inválido. Use: (11) 99999-9999');
                } else {
                    clearFieldError(e.target);
                }
            }
        });
    });
}

// Navigation enhancements
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state for navigation
            if (!link.target) {
                const spinner = document.createElement('span');
                spinner.innerHTML = '...';
                spinner.style.marginLeft = '5px';
                link.appendChild(spinner);
            }
        });
    });
}

// Calendar functionality
function initializeCalendar() {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;
    
    // Generate calendar if not already done by PHP
    if (!calendarContainer.innerHTML.trim()) {
        generateCalendar();
    }
    
    // Add calendar navigation
    setupCalendarNavigation();
}

function generateCalendar() {
    const container = document.getElementById('calendar-container');
    if (!container) return;
    
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    
    container.innerHTML = createCalendarHTML(currentYear, currentMonth);
}

function createCalendarHTML(year, month) {
    const monthNames = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
    
    let html = `
        <div class="calendar">
            <div class="calendar-header">
                <button class="calendar-nav" onclick="changeMonth(-1)">&lt;</button>
                <h3 id="month-year">${monthNames[month]} ${year}</h3>
                <button class="calendar-nav" onclick="changeMonth(1)">&gt;</button>
            </div>
            <div class="calendar-grid">
                <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Dom</div>
                <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Seg</div>
                <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Ter</div>
                <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Qua</div>
                <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Qui</div>
                <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Sex</div>
                <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Sáb</div>
    `;
    
    // Generate calendar days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const isCurrentMonth = date.getMonth() === month;
        const isPast = date < today;
        const isWeekend = date.getDay() === 0 || date.getDay() === 6;
        const dateStr = date.toISOString().split('T')[0];
        
        let classes = 'calendar-day';
        if (!isCurrentMonth) classes += ' text-light';
        if (isPast || isWeekend) classes += ' unavailable';
        
        const clickable = isCurrentMonth && !isPast && !isWeekend;
        
        html += `
            <div class="${classes}" ${clickable ? `onclick="selectDate('${dateStr}')"` : ''}>
                ${date.getDate()}
            </div>
        `;
    }
    
    html += '</div></div>';
    return html;
}

function setupCalendarNavigation() {
    // Calendar navigation is handled by inline onclick events
    // This can be enhanced further if needed
}

// Time slot selection
function selectTime(time) {
    const serviceId = getUrlParameter('service_id');
    const date = getUrlParameter('date');
    
    if (date) {
        if (serviceId) {
            window.location.href = `index.php?page=calendar&service_id=${serviceId}&date=${date}&time=${time}`;
        } else {
            // Allow time selection even without service_id
            window.location.href = `index.php?page=calendar&date=${date}&time=${time}`;
        }
    }
}

function selectDate(date) {
    const serviceId = getUrlParameter('service_id');
    if (serviceId) {
        window.location.href = `index.php?page=calendar&service_id=${serviceId}&date=${date}`;
    } else {
        // Allow date selection even without service_id
        window.location.href = `index.php?page=calendar&date=${date}`;
    }
}

// Utility functions
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Admin panel enhancements
function initializeAdminPanel() {
    // Auto-refresh appointment status updates
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.style.background = 'var(--primary-color)';
            this.style.color = 'white';
            setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    });
    
    // Confirm before status changes
    const forms = document.querySelectorAll('form[method="POST"]');
    forms.forEach(form => {
        if (form.querySelector('input[name="update_status"]')) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Confirma a alteração do status do agendamento?')) {
                    e.preventDefault();
                }
            });
        }
    });
}

// Initialize admin functionality if on admin page
if (window.location.href.includes('page=admin')) {
    document.addEventListener('DOMContentLoaded', initializeAdminPanel);
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Loading states for buttons
function addLoadingState(button, text = 'Carregando...') {
    button.disabled = true;
    button.originalText = button.textContent;
    button.textContent = text;
    button.style.opacity = '0.7';
}

function removeLoadingState(button) {
    button.disabled = false;
    button.textContent = button.originalText || button.textContent;
    button.style.opacity = '1';
}

// Form submission loading states
document.addEventListener('DOMContentLoaded', function() {
    const submitButtons = document.querySelectorAll('button[type="submit"], input[type="submit"]');
    
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form && validateForm(form)) {
                addLoadingState(this);
            }
        });
    });
});

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    `;
    
    switch(type) {
        case 'success':
            notification.style.background = 'var(--success-color)';
            break;
        case 'error':
            notification.style.background = 'var(--warning-color)';
            break;
        case 'info':
        default:
            notification.style.background = 'var(--primary-color)';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Export functions for global use
window.selectDate = selectDate;
window.selectTime = selectTime;
window.showNotification = showNotification;