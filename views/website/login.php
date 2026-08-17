<?php if (!defined('ROOT_PATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Janki Piles Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{background:linear-gradient(135deg,#f0f9ff,#e0f2fe);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}
        .container{max-width:1000px;width:100%;display:grid;grid-template-columns:1.2fr 1fr;background:#fff;border-radius:32px;overflow:hidden;box-shadow:0 25px 80px rgba(0,0,0,.08),0 10px 30px rgba(0,0,0,.04)}
        .left{padding:3.5rem 3rem;display:flex;flex-direction:column;justify-content:center}
        .right{background:linear-gradient(145deg,#1a3a5c,#0a1a2e);position:relative;min-height:480px;overflow:hidden}
        .right img{width:100%;height:100%;object-fit:cover;opacity:.6;transition:transform .7s ease;display:block}
        .right:hover img{transform:scale(1.05)}
        .right .overlay{position:absolute;bottom:0;left:0;right:0;padding:2.5rem;background:linear-gradient(transparent,rgba(10,26,46,.92));color:#fff}
        .right .overlay h2{font-size:1.5rem;font-weight:700;letter-spacing:-.5px}
        .right .overlay p{font-size:.85rem;opacity:.75;margin-top:.2rem}
        .brand{display:flex;align-items:center;gap:.8rem;margin-bottom:.5rem}
        .brand .icon{width:48px;height:48px;background:linear-gradient(135deg,#2563eb,#3b82f6);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;box-shadow:0 8px 20px rgba(37,99,235,.25)}
        .brand h1{font-size:1.6rem;font-weight:700;color:#0a1a2e;letter-spacing:-.5px}
        .subtitle{color:#64748b;font-size:.85rem;margin-bottom:1.8rem}
        .subtitle .tag{background:#dcfce7;color:#15803d;padding:.1rem .7rem;border-radius:20px;font-size:.6rem;font-weight:600;margin-left:.4rem;display:inline-block}
        .input-group{display:flex;align-items:stretch;background:#f8fafc;border-radius:14px;border:1.5px solid #e2e8f0;transition:.25s;margin-bottom:.8rem}
        .input-group:focus-within{border-color:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,.08);background:#fff}
        .input-group .icon-box{background:transparent;border:0;color:#94a3b8;padding:.6rem .8rem .6rem 1rem;display:flex;align-items:center;font-size:.9rem}
        .input-group input{flex:1;border:0;background:transparent;padding:.6rem .8rem .6rem 0;font-size:.85rem;outline:0;color:#0a1a2e;font-weight:500}
        .input-group input::placeholder{color:#aab4c8;font-weight:400}
        .row{display:flex;justify-content:space-between;align-items:center;margin:1rem 0 1.4rem}
        .row label{display:flex;align-items:center;gap:.4rem;font-size:.8rem;color:#475569;cursor:pointer}
        .row label input{width:16px;height:16px;accent-color:#2563eb;cursor:pointer}
        .row a{color:#2563eb;font-size:.8rem;font-weight:600;text-decoration:none}
        .row a:hover{text-decoration:underline}
        .btn{background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;border:0;border-radius:40px;padding:.7rem;font-weight:600;font-size:.9rem;width:100%;cursor:pointer;transition:.25s;display:flex;align-items:center;justify-content:center;gap:.5rem}
        .btn:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(37,99,235,.35)}
        .btn:disabled{opacity:.7;transform:none;cursor:not-allowed}
        .alert{border-radius:14px;padding:.5rem 1rem;font-size:.8rem;margin-bottom:1rem;display:none;align-items:center;gap:.5rem}
        .alert.show{display:flex}
        .alert-success{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
        .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
        .footer{text-align:center;margin-top:1.8rem;padding-top:1rem;border-top:1px solid #f1f5f9;font-size:.7rem;color:#94a3b8}
        .footer strong{color:#1e293b}
        .spinner{display:inline-block;width:1rem;height:1rem;border:.2em solid currentColor;border-right-color:transparent;border-radius:50%;animation:spin .7s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        @media(max-width:768px){.container{grid-template-columns:1fr}.left{padding:2.5rem 1.8rem}.right{min-height:200px}.right .overlay h2{font-size:1.1rem}}
    </style>
</head>
<body>
<div class="container">
    <div class="left">
        <div class="brand">
            <div class="icon"><i class="bi bi-shield-lock-fill"></i></div>
            <h1>Janki Piles</h1>
        </div>
        <div class="subtitle">Welcome back! Sign in to your dashboard <span class="tag"></span></div>

        <div id="alert" class="alert"></div>

        <form id="loginForm" action="<?= site_url('/login') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="input-group">
                <span class="icon-box"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="username" id="username" value="<?= esc(old('username')) ?>" placeholder="Username or Email" required autofocus>
            </div>
            <div class="input-group">
                <span class="icon-box"><i class="bi bi-key-fill"></i></span>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <div class="row">
                <label><input type="checkbox" name="remember_me"> Remember Me</label>
                <a href="<?= site_url('/forgot-password') ?>">Forgot?</a>
            </div>
            <button type="submit" class="btn" id="loginBtn"><i class="bi bi-box-arrow-in-right"></i> Sign In</button>
        </form>

        <div class="footer">
            <i class="bi bi-info-circle text-primary me-1"></i>
            Demo: <strong>admin</strong> · <strong>doctor</strong> · <strong>receptionist</strong> · <strong>branch_kolkata</strong>
            <br>Password: <strong>Admin@1234</strong>
        </div>
    </div>

    <div class="right">
        <img src="https://res.cloudinary.com/z37rtzse/image/upload/v1786515075/f96f17ef4e50315da875db320a65d3c8.jpg" alt="Healthcare" loading="lazy">
        <div class="overlay">
            <h2><i class="bi bi-heart-pulse-fill me-2"></i>Healthcare Management</h2>
            <p>Secure access to your dashboard</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded',()=>{
    const form=document.getElementById('loginForm'),alert=document.getElementById('alert'),btn=document.getElementById('loginBtn');
    const orig=btn.innerHTML;
    document.querySelectorAll('#username,#password').forEach(el=>el.addEventListener('input',()=>alert.classList.remove('show')));
    form.addEventListener('submit',e=>{
        e.preventDefault();
        btn.innerHTML='<span class="spinner"></span> Signing in...';
        btn.disabled=true;
        alert.classList.remove('show');
        fetch(form.action,{method:'POST',body:new FormData(form),headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>r.ok?r.json():r.text().then(t=>{try{return JSON.parse(t)}catch{throw new Error('Server error: '+r.status)}}))
        .then(d=>{
            alert.className='alert show '+(d.success?'alert-success':'alert-danger');
            alert.innerHTML=(d.success?'<i class="bi bi-check-circle-fill"></i> ':'<i class="bi bi-exclamation-circle-fill"></i> ')+(d.message||(d.success?'Login successful. Redirecting...':'Invalid credentials.'));
            if(d.success){btn.innerHTML='<span class="spinner"></span> Redirecting...';setTimeout(()=>window.location.href=d.redirect||'<?= site_url("/dashboard") ?>',700)}
            else{btn.innerHTML=orig;btn.disabled=false}
        })
        .catch(err=>{
            alert.className='alert show alert-danger';
            alert.innerHTML='<i class="bi bi-exclamation-circle-fill"></i> '+err.message;
            btn.innerHTML=orig;btn.disabled=false;
        });
    });
});
</script>
</body>
</html>