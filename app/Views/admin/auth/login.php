<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Admin Login'; ?> - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: linear-gradient(135deg, #343a40, #212529);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #6c757d;
            background-color: #fff;
        }
        .btn-admin {
            background-color: #212529;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-admin:hover {
            background-color: #343a40;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h3 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2"></i>Admin Panel</h3>
        <p class="text-white-50 small mb-0 mt-1">Casual Steps Management</p>
    </div>
    <div class="login-body">
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger small rounded-3 border-0 bg-danger bg-opacity-10 text-danger px-3 py-2" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($_SESSION['login_error']); ?>
            </div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['form_message'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']); ?> small rounded-3 border-0 px-3 py-2" role="alert">
                <?= htmlspecialchars($_SESSION['form_message']); ?>
            </div>
            <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>index.php?url=AdminAuth/processLogin" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">USERNAME</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" name="username" required placeholder="Masukkan username">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" name="password" required placeholder="Masukkan password">
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-admin">Masuk Sistem</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
