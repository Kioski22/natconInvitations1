<?php
// supervisor_form.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>74th NatCon Company Invitation Request</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: '#0B1F4D',
            royal: '#102B6A',
            gold: '#D4AF37',
            goldLight: '#F7D774',
            goldDeep: '#C89B2B',
            textDark: '#1F2937'
          },
          boxShadow: {
            soft: '0 24px 60px rgba(8, 18, 40, 0.35)',
            glow: '0 10px 30px rgba(212, 175, 55, 0.35)'
          },
          animation: {
            float: 'float 10s ease-in-out infinite',
            fadeUp: 'fadeUp 650ms ease-out forwards',
            pulseGlow: 'pulseGlow 2s ease-in-out infinite'
          },
          keyframes: {
            float: {
              '0%, 100%': { transform: 'translateY(0px)' },
              '50%': { transform: 'translateY(-12px)' }
            },
            fadeUp: {
              '0%': { opacity: 0, transform: 'translateY(20px)' },
              '100%': { opacity: 1, transform: 'translateY(0px)' }
            },
            pulseGlow: {
              '0%, 100%': { boxShadow: '0 0 0 rgba(212, 175, 55, 0.0)' },
              '50%': { boxShadow: '0 0 24px rgba(212, 175, 55, 0.35)' }
            }
          }
        }
      }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    html, body { height: 100%; }
    body { font-family: 'Manrope', sans-serif; }
    .glass {
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      background: rgba(255, 255, 255, 0.92);
    }
    .particle {
      position: absolute;
      border-radius: 9999px;
      opacity: 0.22;
      animation: float 12s ease-in-out infinite;
    }
    .particle:nth-child(odd) { animation-duration: 16s; }
    .particle:nth-child(3n) { animation-duration: 20s; }
    .particle:nth-child(4n) { animation-duration: 14s; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-navy via-royal to-navy text-textDark">
  <div id="root" class="relative min-h-screen overflow-hidden"></div>

  <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
  <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
  <script>
    const { useState } = React;

    const IconUser = (props) => (
      React.createElement('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round', ...props },
        React.createElement('path', { d: 'M20 21a8 8 0 0 0-16 0' }),
        React.createElement('circle', { cx: 12, cy: 7, r: 4 })
      )
    );

    const IconBriefcase = (props) => (
      React.createElement('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round', ...props },
        React.createElement('rect', { x: 3, y: 7, width: 18, height: 13, rx: 2 }),
        React.createElement('path', { d: 'M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2' }),
        React.createElement('path', { d: 'M3 13h18' })
      )
    );

    const IconBuilding = (props) => (
      React.createElement('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round', ...props },
        React.createElement('rect', { x: 3, y: 4, width: 18, height: 16, rx: 2 }),
        React.createElement('path', { d: 'M7 20v-4h10v4' }),
        React.createElement('path', { d: 'M7 8h.01' }),
        React.createElement('path', { d: 'M12 8h.01' }),
        React.createElement('path', { d: 'M17 8h.01' }),
        React.createElement('path', { d: 'M7 12h.01' }),
        React.createElement('path', { d: 'M12 12h.01' }),
        React.createElement('path', { d: 'M17 12h.01' })
      )
    );

    const IconMapPin = (props) => (
      React.createElement('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round', ...props },
        React.createElement('path', { d: 'M12 22s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12z' }),
        React.createElement('circle', { cx: 12, cy: 10, r: 2.5 })
      )
    );

    const IconMail = (props) => (
      React.createElement('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round', ...props },
        React.createElement('rect', { x: 3, y: 5, width: 18, height: 14, rx: 2 }),
        React.createElement('path', { d: 'M3 7l9 6 9-6' })
      )
    );

    const InputField = ({ label, name, type = 'text', placeholder, helper, icon: Icon, value, onChange, error, required }) => (
      React.createElement('div', { className: 'space-y-2' },
        React.createElement('label', { className: 'text-[13px] font-semibold text-textDark flex items-center gap-2' },
          label,
          helper && React.createElement('span', { className: 'text-[11px] text-slate-500 font-normal' }, `(${helper})`),
          required && React.createElement('span', { className: 'text-gold text-xs' }, '*')
        ),
        React.createElement('div', { className: 'relative' },
          React.createElement('span', { className: 'absolute left-3 top-1/2 -translate-y-1/2 text-slate-400' },
            React.createElement(Icon, { size: 18 })
          ),
          React.createElement('input', {
            type,
            name,
            value,
            onChange,
            placeholder,
            required,
            className: [
              'w-full rounded-2xl border border-slate-200 bg-white/90 px-10 py-2.5 text-[13px] text-textDark',
              'placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-gold/60 focus:border-gold',
              'transition-all duration-200',
              error ? 'border-red-400 focus:ring-red-400/60' : ''
            ].join(' ')
          })
        ),
        error && React.createElement('p', { className: 'text-xs text-red-500' }, error)
      )
    );

    const TextAreaField = ({ label, name, placeholder, icon: Icon, value, onChange, error, required }) => (
      React.createElement('div', { className: 'space-y-2' },
        React.createElement('label', { className: 'text-[13px] font-semibold text-textDark flex items-center gap-2' },
          label,
          required && React.createElement('span', { className: 'text-gold text-xs' }, '*')
        ),
        React.createElement('div', { className: 'relative' },
          React.createElement('span', { className: 'absolute left-3 top-4 text-slate-400' },
            React.createElement(Icon, { size: 18 })
          ),
          React.createElement('textarea', {
            name,
            value,
            onChange,
            placeholder,
            required,
            rows: 3,
            className: [
              'w-full rounded-2xl border border-slate-200 bg-white/90 px-10 py-2.5 text-[13px] text-textDark',
              'placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-gold/60 focus:border-gold',
              'transition-all duration-200 resize-none',
              error ? 'border-red-400 focus:ring-red-400/60' : ''
            ].join(' ')
          })
        ),
        error && React.createElement('p', { className: 'text-xs text-red-500' }, error)
      )
    );

    const Toast = ({ type, message }) => {
      if (!message) return null;
      const color = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
      return React.createElement('div', {
        className: `${color} text-white text-sm px-4 py-3 rounded-xl shadow-soft mb-4 animate-fadeUp`
      }, message);
    };

    const SupervisorForm = () => {
      const [form, setForm] = useState({
        supervisor_name: '',
        designation: '',
        company: '',
        company_address: '',
        email: ''
      });
      const [errors, setErrors] = useState({});
      const [loading, setLoading] = useState(false);
      const [toast, setToast] = useState({ type: '', message: '' });

      const handleChange = (event) => {
        const { name, value } = event.target;
        setForm(prev => ({ ...prev, [name]: value }));
      };

      const validate = () => {
        const nextErrors = {};
        if (!form.supervisor_name.trim()) nextErrors.supervisor_name = 'Supervisor name is required.';
        if (!form.designation.trim()) nextErrors.designation = 'Designation is required.';
        if (!form.company.trim()) nextErrors.company = 'Company name is required.';
        if (!form.company_address.trim()) nextErrors.company_address = 'Company address is required.';
        if (!form.email.trim()) {
          nextErrors.email = 'Email address is required.';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
          nextErrors.email = 'Enter a valid email address.';
        }
        return nextErrors;
      };

      const handleSubmit = async (event) => {
        event.preventDefault();
        const validationErrors = validate();
        setErrors(validationErrors);
        if (Object.keys(validationErrors).length > 0) return;

        setLoading(true);
        setToast({ type: '', message: '' });

        try {
          const body = new URLSearchParams(form).toString();
          const response = await fetch('process_supervisor_request.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
          });
          const text = await response.text();
          if (!response.ok || text.toLowerCase().includes('error')) {
            throw new Error(text || 'Unable to send invitation.');
          }
          setToast({ type: 'success', message: 'Invitation request submitted successfully.' });
          setForm({ supervisor_name: '', designation: '', company: '', company_address: '', email: '' });
        } catch (err) {
          setToast({ type: 'error', message: err.message || 'Something went wrong. Please try again.' });
        } finally {
          setLoading(false);
          setTimeout(() => setToast({ type: '', message: '' }), 4000);
        }
      };

      return React.createElement('div', { className: 'relative min-h-screen flex items-center justify-center px-4 py-12' },
        React.createElement('div', { className: 'absolute inset-0 overflow-hidden' },
          Array.from({ length: 14 }).map((_, idx) => (
            React.createElement('span', {
              key: idx,
              className: 'particle bg-white/30',
              style: {
                width: `${12 + (idx % 5) * 6}px`,
                height: `${12 + (idx % 5) * 6}px`,
                top: `${(idx * 7) % 90}%`,
                left: `${(idx * 11) % 90}%`,
                animationDelay: `${idx * 0.4}s`
              }
            })
          ))
        ),
        React.createElement('div', { className: 'relative z-10 w-full max-w-[440px]' },
          React.createElement('div', { className: 'flex justify-center' },
            React.createElement('div', { className: 'relative -mb-12' },
              React.createElement('div', { className: 'h-24 w-24 md:h-28 md:w-28 rounded-full bg-white shadow-soft flex items-center justify-center ring-4 ring-gold/40' },
                React.createElement('img', {
                  src: '74th NatCon Logo.svg',
                  alt: 'NatCon Logo',
                  className: 'h-16 w-16 md:h-20 md:w-20 object-contain'
                })
              )
            )
          ),
          React.createElement('div', { className: 'glass rounded-3xl shadow-soft px-6 pb-7 pt-12 md:px-8 animate-fadeUp' },
            React.createElement('div', { className: 'text-center space-y-2 mb-6' },
              React.createElement('p', { className: 'text-[11px] uppercase tracking-[0.35em] text-slate-400' }, 'Request Form'),
              React.createElement('h1', { className: 'text-[22px] md:text-[24px] font-extrabold text-textDark leading-tight' },
                '74th NatCon Company Invitation Request'
              ),
              React.createElement('p', { className: 'text-xs text-slate-500' },
                'Provide supervisor details to send a premium invitation.'
              )
            ),
            React.createElement(Toast, { type: toast.type, message: toast.message }),
            React.createElement('form', { onSubmit: handleSubmit, className: 'space-y-4' },
              React.createElement(InputField, {
                label: 'Name of the Supervisor',
                name: 'supervisor_name',
                placeholder: 'Enter full name',
                helper: 'Include salutations Ex. Engr. Juan',
                icon: IconUser,
                value: form.supervisor_name,
                onChange: handleChange,
                error: errors.supervisor_name,
                required: true
              }),
              React.createElement(InputField, {
                label: 'Designation / Position',
                name: 'designation',
                placeholder: 'e.g. Plant Manager, Head Engineer',
                icon: IconBriefcase,
                value: form.designation,
                onChange: handleChange,
                error: errors.designation,
                required: true
              }),
              React.createElement(InputField, {
                label: 'Company',
                name: 'company',
                placeholder: 'Enter company name',
                icon: IconBuilding,
                value: form.company,
                onChange: handleChange,
                error: errors.company,
                required: true
              }),
              React.createElement(TextAreaField, {
                label: 'Company Address',
                name: 'company_address',
                placeholder: 'Enter company address',
                icon: IconMapPin,
                value: form.company_address,
                onChange: handleChange,
                error: errors.company_address,
                required: true
              }),
              React.createElement(InputField, {
                label: 'Email Address',
                name: 'email',
                type: 'email',
                placeholder: 'example@email.com',
                icon: IconMail,
                value: form.email,
                onChange: handleChange,
                error: errors.email,
                required: true
              }),
              React.createElement('button', {
                type: 'submit',
                disabled: loading,
                className: [
                  'w-full rounded-2xl py-3 text-[13px] font-semibold uppercase tracking-wide text-navy',
                  'bg-gradient-to-r from-goldLight via-gold to-goldDeep shadow-glow transition-all duration-200',
                  'hover:shadow-[0_0_26px_rgba(212,175,55,0.5)] focus:outline-none focus:ring-2 focus:ring-gold/70',
                  'disabled:opacity-70 disabled:cursor-not-allowed',
                  'animate-pulseGlow'
                ].join(' ')
              }, loading ? 'Sending...' : 'Send Invitation')
            )
          )
        )
      );
    };

    const root = ReactDOM.createRoot(document.getElementById('root'));
    root.render(React.createElement(SupervisorForm));
  </script>
</body>
</html>