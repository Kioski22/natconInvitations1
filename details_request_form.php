<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PSME Invitation Request Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy-1: #0a0e2a;
            --navy-2: #0f1c46;
            --card: #fdfcf8;
            --gold-1: #c9a84c;
            --gold-2: #f0d080;
            --gold-3: #b8860b;
            --text-dark: #111111;
            --text-muted: #666666;
            --border: #dddddd;
            --error: #c73333;
            --success: #1d6f2c;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top, #122a66 0%, var(--navy-2) 35%, var(--navy-1) 100%);
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 1.4rem 2rem;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 0.55rem;
        }

        .logo {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            object-fit: cover;
            display: block;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin: 0 0 0.25rem 0;
            color: var(--text-dark);
            line-height: 1.25;
        }

        .subtitle {
            text-align: center;
            color: var(--text-muted);
            margin: 0 0 0.85rem 0;
            font-size: 0.85rem;
        }

        .form-group {
            margin-bottom: 0.7rem;
        }

        label {
            display: block;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 0.6rem;
            color: #888888;
            font-size: 0.9rem;
        }

        input, textarea, select {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 0.7rem 0.5rem 2.1rem;
            font-size: 0.9rem;
            outline: none;
            font-family: 'Inter', sans-serif;
            background: #ffffff;
        }

        input[readonly] {
            background: #f4f4f4;
            color: #555555;
        }

        input::placeholder, textarea::placeholder {
            color: #b3b3b3;
            font-style: italic;
        }

        input:focus, textarea:focus, select:focus {
            border-color: var(--gold-1);
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.2);
        }

        textarea {
            resize: vertical;
            min-height: 52px;
        }

        .inline-grid {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 0.75rem;
        }

        .toggle-hr {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid var(--gold-1);
            color: var(--gold-3);
            background: transparent;
            padding: 0.32rem 0.7rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 0.4rem;
        }

        .error-message {
            color: var(--error);
            font-size: 0.78rem;
            margin-top: 0.35rem;
        }

        .alert {
            border-radius: 10px;
            padding: 0.55rem 0.8rem;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
            text-align: center;
            display: none;
        }

        .alert.success {
            background: #e7f7ea;
            color: var(--success);
            border: 1px solid #b7e1c3;
        }

        .alert.error {
            background: #fde8e8;
            color: var(--error);
            border: 1px solid #f1b5b5;
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--gold-1), var(--gold-2), var(--gold-3));
            color: #1a1200;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            letter-spacing: 0.5px;
            margin-top: 0.2rem;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            filter: brightness(1.1);
            transform: scale(1.02);
        }

        .btn-submit:disabled {
            cursor: not-allowed;
            opacity: 0.75;
            transform: none;
        }

        @media (max-width: 620px) {
            .card { padding: 1.2rem 1.1rem; }
            .inline-grid { grid-template-columns: 1fr; }
            h1 { font-size: 1.35rem; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-wrapper">
            <img src="74th NatCon Logo.svg" alt="PSME Logo" class="logo">
        </div>

        <h1>Invitation Request Form</h1>
        <p class="subtitle">74th PSME National Convention</p>

        <div id="successMessage" class="alert success">✅ Invitation request submitted successfully.</div>
        <div id="errorMessage" class="alert error">There was an error submitting your request. Please try again.</div>

        <form id="invitationForm" action="process_request.php" method="POST" novalidate>
            <div class="form-group">
                <label>Event</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="text" name="event" value="74th PSME National Convention" readonly>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="example@email.com" required>
                </div>
                <div class="error-message" data-error-for="email"></div>
            </div>

            <div class="form-group">
                <button type="button" class="toggle-hr" id="toggleHrEmail">
                    <span id="toggleIcon">+</span> Add HR Email Address
                </button>
                <div id="hrEmailSection" style="display: none;">
                    <label>HR Email Address</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope-open"></i>
                        <input type="email" name="hr_email" placeholder="hr@example.com">
                    </div>
                    <div class="error-message" data-error-for="hr_email"></div>
                </div>
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <div class="inline-grid">
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <select name="salutation" required>
                            <option value="" disabled selected>Salutation</option>
                            <option>Mr.</option>
                            <option>Ms.</option>
                            <option>Engr.</option>
                            <option>Dr.</option>
                            <option>Atty.</option>
                            <option>Hon.</option>
                            <option>Prof.</option>
                            <option>Rev.</option>
                            <option>Sr.</option>
                            <option>Fr.</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="full_name" placeholder="Full Name" required>
                    </div>
                </div>
                <div class="error-message" data-error-for="salutation"></div>
                <div class="error-message" data-error-for="full_name"></div>
            </div>

            <div class="form-group">
                <label>Designation</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-id-badge"></i>
                    <input type="text" name="designation" placeholder="Enter designation" required>
                </div>
                <div class="error-message" data-error-for="designation"></div>
            </div>

            <div class="form-group">
                <label>Company</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-building"></i>
                    <input type="text" name="company" placeholder="Enter company name" required>
                </div>
                <div class="error-message" data-error-for="company"></div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-location-dot"></i>
                    <textarea name="address" placeholder="Enter company address" required></textarea>
                </div>
                <div class="error-message" data-error-for="address"></div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">Submit Request</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('invitationForm');
        const submitBtn = document.getElementById('submitBtn');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const toggleButton = document.getElementById('toggleHrEmail');
        const hrEmailSection = document.getElementById('hrEmailSection');
        const toggleIcon = document.getElementById('toggleIcon');

        const setError = (name, message) => {
            const errorEl = document.querySelector(`[data-error-for="${name}"]`);
            if (errorEl) errorEl.textContent = message || '';
        };

        const clearErrors = () => {
            document.querySelectorAll('.error-message').forEach(el => { el.textContent = ''; });
        };

        const validateForm = () => {
            const data = new FormData(form);
            const errors = {};

            const email = String(data.get('email') || '').trim();
            if (!email) {
                errors.email = 'Email address is required.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.email = 'Enter a valid email address.';
            }

            const hrEmail = String(data.get('hr_email') || '').trim();
            if (hrEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(hrEmail)) {
                errors.hr_email = 'Enter a valid HR email address.';
            }

            if (!String(data.get('salutation') || '').trim()) {
                errors.salutation = 'Select a salutation.';
            }
            if (!String(data.get('full_name') || '').trim()) {
                errors.full_name = 'Full name is required.';
            }
            if (!String(data.get('designation') || '').trim()) {
                errors.designation = 'Designation is required.';
            }
            if (!String(data.get('company') || '').trim()) {
                errors.company = 'Company name is required.';
            }
            if (!String(data.get('address') || '').trim()) {
                errors.address = 'Address is required.';
            }

            return errors;
        };

        toggleButton.addEventListener('click', () => {
            const isHidden = hrEmailSection.style.display === 'none' || hrEmailSection.style.display === '';
            hrEmailSection.style.display = isHidden ? 'block' : 'none';
            toggleIcon.textContent = isHidden ? '-' : '+';
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';
            clearErrors();

            const errors = validateForm();
            Object.keys(errors).forEach(name => setError(name, errors[name]));

            if (Object.keys(errors).length > 0) {
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                });
                const responseText = await response.text();

                if (!response.ok || !/success/i.test(responseText)) {
                    throw new Error('Submission failed.');
                }

                successMessage.style.display = 'block';
                form.reset();
                hrEmailSection.style.display = 'none';
                toggleIcon.textContent = '+';
            } catch (error) {
                errorMessage.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Request';
            }
        });
    </script>
</body>
</html>