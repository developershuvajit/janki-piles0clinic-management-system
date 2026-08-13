<?php
if (!defined('ROOT_PATH')) exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Janki Piles Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{background:#f8fafc;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}
        .login-wrap{max-width:1100px;width:100%;display:flex;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.08)}
        .login-left{flex:0 0 45%;padding:3rem 2.5rem;display:flex;flex-direction:column;justify-content:center}
        .login-right{flex:0 0 55%;background:linear-gradient(135deg,#1e3a5f,#0b1a2b);position:relative;min-height:500px;overflow:hidden}
        .login-right img{width:100%;height:100%;object-fit:cover;opacity:.85;transition:transform .5s;display:block}
        .login-right:hover img{transform:scale(1.03)}
        .login-right .overlay{position:absolute;bottom:0;left:0;right:0;padding:2rem;background:linear-gradient(transparent,rgba(11,26,43,.9));color:#fff}
        .login-right .overlay h3{font-size:1.4rem;font-weight:700;margin-bottom:.2rem}
        .login-right .overlay p{font-size:.85rem;opacity:.85;margin:0}
        .login-icon{width:50px;height:50px;background:#e6f0ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;color:#2563eb;font-size:1.5rem}
        .login-title{font-size:1.5rem;font-weight:700;color:#0b1a2b}
        .login-sub{font-size:.8rem;color:#64748b;margin-bottom:1.5rem}
        .login-sub .badge{background:#e6f5ed;color:#0f7b4a;padding:.15rem .7rem;border-radius:40px;font-size:.6rem;font-weight:600;margin-left:.4rem}
        .form-label{font-size:.75rem;font-weight:600;color:#1e293b;margin-bottom:.2rem;display:block}
        .form-control{width:100%;border-radius:10px;border:1px solid #e2e8f0;padding:.5rem .8rem;font-size:.85rem;transition:.2s;background:#fff}
        .form-control:focus{border-color:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,.08);outline:0}
        .input-group{display:flex;align-items:stretch}
        .input-group-text{background:#f8fafc;border:1px solid #e2e8f0;border-right:0;border-radius:10px 0 0 10px;color:#94a3b8;padding:.5rem .8rem;display:flex;align-items:center}
        .input-group .form-control{border-radius:0 10px 10px 0;border-left:0}
        .btn-login{background:#2563eb;color:#fff;border:0;border-radius:40px;padding:.6rem;font-weight:600;font-size:.85rem;width:100%;cursor:pointer;transition:.2s}
        .btn-login:hover{background:#1d4ed8;box-shadow:0 4px 12px rgba(37,99,235,.3)}
        .btn-login:disabled{opacity:.7;cursor:not-allowed}
        .form-check{display:flex;align-items:center;gap:.4rem}
        .form-check-input{width:15px;height:15px;cursor:pointer;accent-color:#2563eb}
        .form-check-label{font-size:.75rem;color:#64748b;cursor:pointer}
        .login-foot{text-align:center;margin-top:1.5rem;padding-top:1rem;border-top:1px solid #f1f5f9;font-size:.7rem;color:#94a3b8}
        .login-foot strong{color:#1e293b}
        #alert-container{border-radius:10px;padding:.4rem .8rem;font-size:.8rem;margin-bottom:1rem}
        .alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
        .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
        .d-none{display:none!important}
        .mb-2{margin-bottom:.7rem}
        .mb-3{margin-bottom:1rem}
        .d-flex{display:flex}
        .justify-between{justify-content:space-between}
        .align-center{align-items:center}
        .gap-2{gap:.5rem}
        .text-primary{color:#2563eb}
        .text-decoration-none{text-decoration:none}
        .small{font-size:.75rem}
        .fw-semibold{font-weight:600}
        .me-1{margin-right:.25rem}
        .me-2{margin-right:.5rem}
        .py-1{padding-top:.25rem;padding-bottom:.25rem}
        .spinner-border{display:inline-block;width:1rem;height:1rem;vertical-align:middle;border:.2em solid currentColor;border-right-color:transparent;border-radius:50%;animation:spinner-border .75s linear infinite}
        @keyframes spinner-border{to{transform:rotate(360deg)}}
        @media(max-width:768px){.login-wrap{flex-direction:column}.login-left{flex:1;padding:2rem 1.5rem}.login-right{flex:0 0 200px;min-height:200px}.login-right .overlay h3{font-size:1.1rem}}
    </style>
</head>
<body>
<div class="login-wrap">
   <div class="login-left">
      <div class="login-icon"><i class="bi bi-shield-lock-fill"></i></div>
      <div class="login-title">Welcome Back</div>
      <div class="login-sub">Sign in to your dashboard <span class="badge"><i class="bi bi-person-check-fill me-1"></i>Single Login</span></div>

      <div id="alert-container" class="d-none"></div>

      <form id="login-form" action="<?= site_url('/login') ?>" method="POST" novalidate>
         <?= csrf_field() ?>
         <div class="mb-2">
            <label class="form-label">Username or Email</label>
            <div class="input-group">
               <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
               <input type="text" class="form-control" name="username" id="username" value="<?= esc(old('username')) ?>" required autofocus placeholder="Enter username">
            </div>
         </div>

         <div class="mb-2">
            <label class="form-label">Password</label>
            <div class="input-group">
               <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
               <input type="password" class="form-control" name="password" id="password" required placeholder="Enter password">
            </div>
         </div>

         <div class="d-flex justify-between align-center mb-3">
            <label class="form-check">
               <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
               <span class="form-check-label">Remember Me</span>
            </label>
            <a href="<?= site_url('/forgot-password') ?>" class="text-primary text-decoration-none small fw-semibold">Forgot?</a>
         </div>

         <button type="submit" class="btn-login" id="loginBtn">
            <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
         </button>
      </form>

      <div class="login-foot">
         <i class="bi bi-info-circle text-primary me-1"></i>
         Demo: <strong>admin</strong> <span style="color:#cbd5e1">|</span> <strong>doctor</strong> <span style="color:#cbd5e1">|</span> <strong>receptionist</strong> <span style="color:#cbd5e1">|</span> <strong>branch_kolkata</strong>
         <br>Password: <strong>Admin@1234</strong>
      </div>
   </div>

   <div class="login-right">
      <img src="https://res.cloudinary.com/z37rtzse/image/upload/v1786515075/f96f17ef4e50315da875db320a65d3c8.jpg" alt="Healthcare" loading="lazy">
      <div class="overlay">
         <h3><i class="bi bi-heart-pulse-fill me-2"></i>Healthcare Management</h3>
         <p>Secure access to your dashboard</p>
      </div>
   </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
   const form = document.getElementById('login-form');
   const alert = document.getElementById('alert-container');
   const loginBtn = document.getElementById('loginBtn');
   const username = document.getElementById('username');
   const password = document.getElementById('password');

   // Clear error when user starts typing
   username.addEventListener('input', function() {
      alert.classList.add('d-none');
   });
   password.addEventListener('input', function() {
      alert.classList.add('d-none');
   });

   form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Show loading state
      const originalText = loginBtn.innerHTML;
      loginBtn.innerHTML = '<span class="spinner-border me-1"></span> Signing in...';
      loginBtn.disabled = true;
      
      // Hide previous alerts
      alert.classList.add('d-none');

      const data = new FormData(form);

      fetch(form.action, {
         method: 'POST',
         body: data,
         headers: { 
            'X-Requested-With': 'XMLHttpRequest'
         }
      })
      .then(response => {
         if (!response.ok) {
            return response.text().then(text => {
               try {
                  return JSON.parse(text);
               } catch {
                  throw new Error('Server error: ' + response.status);
               }
            });
         }
         return response.json();
      })
      .then(data => {
         alert.className = `alert alert-${data.success ? 'success' : 'danger'} py-1 small`;
         alert.textContent = data.message || (data.success ? 'Login successful. Redirecting...' : 'Invalid credentials.');
         alert.classList.remove('d-none');

         if (data.success) {
            loginBtn.innerHTML = '<span class="spinner-border me-1"></span> Redirecting...';
            setTimeout(() => {
               window.location.href = data.redirect || '<?= site_url("/dashboard") ?>';
            }, 800);
         } else {
            loginBtn.innerHTML = originalText;
            loginBtn.disabled = false;
         }
      })
      .catch(error => {
         console.error('Login error:', error);
         alert.className = 'alert alert-danger py-1 small';
         alert.textContent = error.message || 'Error occurred. Please refresh and try again.';
         alert.classList.remove('d-none');
         loginBtn.innerHTML = originalText;
         loginBtn.disabled = false;
      });
   });
});
</script>
</body>
</html>