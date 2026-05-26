<?php
// supervisor_form.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>74th NatCon Company Invitation Request</title>
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
      padding: 2rem 2.5rem;
      max-width: 480px;
      width: 100%;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    }

    .logo-wrapper {
      display: flex;
      justify-content: center;
      margin-bottom: 1rem;
    }

    .logo {
      width: 90px;
      height: 90px;
      border-radius: 999px;
      object-fit: cover;
      display: block;
    }

    h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      font-weight: 700;
      text-align: center;
      margin: 0 0 1.5rem 0;
      color: var(--text-dark);
      line-height: 1.25;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    label {
      display: block;
      font-weight: 700;
      font-size: 0.85rem;
      margin-bottom: 0.3rem;
    }

    .hint {
      font-style: italic;
      font-weight: 400;
      color: var(--text-muted);
      margin-left: 0.35rem;
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

    input, textarea {
      width: 100%;
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 0.6rem 0.75rem 0.6rem 2.2rem;
      font-size: 0.9rem;
      outline: none;
      font-family: 'Inter', sans-serif;
      background: #ffffff;
    }

    input::placeholder, textarea::placeholder {
      color: #b3b3b3;
      font-style: italic;
    }

    input:focus, textarea:focus {
      border-color: var(--gold-1);
      box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.2);
    }

    textarea {
      resize: vertical;
      min-height: 70px;
    }

    .error-message {
      color: var(--error);
      font-size: 0.78rem;
      margin-top: 0.35rem;
    }

    .success-message {
      background: #e7f7ea;
      color: #1d6f2c;
      border: 1px solid #b7e1c3;
      border-radius: 10px;
      padding: 0.7rem 0.85rem;
      font-size: 0.9rem;
      margin-bottom: 1rem;
      text-align: center;
      display: none;
    }

    .btn-submit {
      width: 100%;
      padding: 0.85rem;
      background: linear-gradient(135deg, var(--gold-1), var(--gold-2), var(--gold-3));
      color: #1a1200;
      font-weight: 700;
      font-size: 1rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      letter-spacing: 0.5px;
      margin-top: 0.5rem;
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
    #sample{
      color: var(--text-muted);
      font-size: 0.78rem;
      font-style: italic;
    }

    @media (max-width: 520px) {
      .card { padding: 1.75rem 1.5rem; }
      h1 { font-size: 1.4rem; }
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo-wrapper">
      <img src="74th NatCon Logo.svg" alt="74th NatCon Logo" class="logo">
    </div>

    <h1>74th NatCon Company<br>Invitation Request</h1>

    <div id="successMessage" class="success-message">✅ Invitation sent successfully!</div>

    <form id="invitationForm" method="POST" action="process_supervisor_request.php" novalidate>
      <div class="form-group">
        <label>Name of the Supervisor
          <span class="hint">(include salutations Ex. Engr. Juan)</span>
        </label>
        <div class="input-wrapper">
          <i class="fa-solid fa-user"></i>
          <input type="text" name="supervisor_name" placeholder="Enter full name" required>
        </div>
        <div class="error-message" data-error-for="supervisor_name"></div>
      </div>

      <div class="form-group">
        <label>Designation / Position</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-id-badge"></i>
          <input type="text" name="designation" placeholder="e.g. Plant Manager, Head Engineer" required>
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
        <label>Company Address</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-location-dot"></i>
          <textarea name="company_address" placeholder="Enter company address" required></textarea>
        </div>
        <div class="error-message" data-error-for="company_address"></div>
      </div>

      <div class="form-group">
        <label>Thru: 
          <span class="hint">(Optional)</span>
        </label>
        <div class="input-wrapper">
          <i class="fa-solid fa-users"></i>
          <input type="text" name="thru">
        </div>
        <p id="sample">Single: Engr. Juan Dela Cruz, Executive Director <br> Multiple: Engr. Juan Dela Cruz, Executive Director; Engr. Maria Santos, Project Manager</p>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" name="email" placeholder="example@email.com" required>
        </div>
        <div class="error-message" data-error-for="email"></div>
      </div>

      <button type="submit" class="btn-submit" id="submitBtn">Send Invitation</button>
    </form>
  </div>

  <script>
    const form = document.getElementById('invitationForm');
    const submitBtn = document.getElementById('submitBtn');
    const successMessage = document.getElementById('successMessage');

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

      if (!String(data.get('supervisor_name') || '').trim()) {
        errors.supervisor_name = 'Supervisor name is required.';
      }
      if (!String(data.get('designation') || '').trim()) {
        errors.designation = 'Designation is required.';
      }
      if (!String(data.get('company') || '').trim()) {
        errors.company = 'Company name is required.';
      }
      if (!String(data.get('company_address') || '').trim()) {
        errors.company_address = 'Company address is required.';
      }

      const email = String(data.get('email') || '').trim();
      if (!email) {
        errors.email = 'Email address is required.';
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.email = 'Enter a valid email address.';
      }

      return errors;
    };

    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      successMessage.style.display = 'none';
      clearErrors();

      const errors = validateForm();
      Object.keys(errors).forEach(name => setError(name, errors[name]));

      if (Object.keys(errors).length > 0) {
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';

      try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData
        });

        const responseText = await response.text();
        if (!response.ok || /error/i.test(responseText)) {
          throw new Error('Unable to send invitation.');
        }

        successMessage.style.display = 'block';
        form.reset();
      } catch (error) {
        setError('email', 'Submission failed. Please try again.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Invitation';
      }
    });
  </script>
</body>
</html>