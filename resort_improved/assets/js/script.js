

// --- Navigation Toggle 
function toggleNav() {
    var navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('show');
}

// --- Form Validation 

// Registration Form Validation
function validateRegistration() {
    var fullName = document.getElementById('full_name').value.trim();
    var email = document.getElementById('email').value.trim();
    var phone = document.getElementById('phone').value.trim();
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;

    // Validate full name (at least 3 characters)
    if (fullName.length < 3) {
        alert('Full name must be at least 3 characters long.');
        return false;
    }

    // Validate email format using indexOf 
    if (email.indexOf('@') === -1 || email.indexOf('.') === -1) {
        alert('Please enter a valid email address.');
        return false;
    }

    // Validate phone (at least 10 digits)
    if (phone.length < 10) {
        alert('Phone number must be at least 10 digits.');
        return false;
    }

    // Validate password length
    if (password.length < 6) {
        alert('Password must be at least 6 characters long.');
        return false;
    }

    // Confirm passwords match 
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return false;
    }

    return true;
}

// Booking Form Validation
function validateBooking() {
    var checkIn = document.getElementById('check_in').value;
    var checkOut = document.getElementById('check_out').value;

    if (!checkIn || !checkOut) {
        alert('Please select both check-in and check-out dates.');
        return false;
    }

    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var checkInDate = new Date(checkIn);
    var checkOutDate = new Date(checkOut);

    // Check-in must be today or later
    if (checkInDate < today) {
        alert('Check-in date cannot be in the past.');
        return false;
    }

    // Check-out must be after check-in
    if (checkOutDate <= checkInDate) {
        alert('Check-out date must be after check-in date.');
        return false;
    }

    return true;
}

// Contact Form Validation
function validateContact() {
    var name = document.getElementById('contact_name').value.trim();
    var email = document.getElementById('contact_email').value.trim();
    var message = document.getElementById('contact_message').value.trim();

    if (name.length < 2) {
        alert('Please enter your name.');
        return false;
    }
    if (email.indexOf('@') === -1) {
        alert('Please enter a valid email.');
        return false;
    }
    if (message.length < 10) {
        alert('Message must be at least 10 characters long.');
        return false;
    }
    return true;
}

// --- Dynamic Price Calculator -
function calculateTotal() {
    var checkIn = document.getElementById('check_in');
    var checkOut = document.getElementById('check_out');
    var priceDisplay = document.getElementById('price_display');
    var pricePerNight = document.getElementById('price_per_night');

    if (checkIn && checkOut && pricePerNight && priceDisplay) {
        if (checkIn.value && checkOut.value) {
            var date1 = new Date(checkIn.value);
            var date2 = new Date(checkOut.value);
            var nights = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24));

            if (nights > 0) {
                var price = parseFloat(pricePerNight.value);
                var total = nights * price;
                priceDisplay.innerHTML = nights + ' night(s) × KES ' + 
                    price.toLocaleString() + ' = <strong>KES ' + total.toLocaleString() + '</strong>';
            } else {
                priceDisplay.innerHTML = 'Please select valid dates.';
            }
        }
    }
}

// --- Auto-dismiss alerts after 5 seconds ---
document.addEventListener('DOMContentLoaded', function() {
    var alerts = document.querySelectorAll('.alert');
    for (var i = 0; i < alerts.length; i++) {
        (function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() { alert.style.display = 'none'; }, 300);
            }, 5000);
        })(alerts[i]);
    }

    // Set minimum date for date inputs to today
    var dateInputs = document.querySelectorAll('input[type="date"]');
    var today = new Date().toISOString().split('T')[0];
    for (var j = 0; j < dateInputs.length; j++) {
        if (dateInputs[j].name === 'check_in') {
            dateInputs[j].setAttribute('min', today);
        }
    }
});

// --- Confirm Delete Actions ---
function confirmDelete(itemName) {
    return confirm('Are you sure you want to delete this ' + itemName + '? This action cannot be undone.');
}

// --- Confirm Cancel Booking ---
function confirmCancel() {
    return confirm('Are you sure you want to cancel this booking?');
}
