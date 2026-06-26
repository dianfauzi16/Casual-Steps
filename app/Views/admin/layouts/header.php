<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?> - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Nanti style admin spesifik diletakkan di public/admin/css/style.css -->
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
        .sidebar { position: sticky; top: 0; height: 100vh; overflow-y: auto; background: #343a40; color: #fff; }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.2); border-radius: 4px; }
        .sidebar-link { color: rgba(255,255,255,.75); text-decoration: none; padding: .75rem 1rem; display: block; border-radius: .25rem; margin-bottom: .25rem; transition: all .2s;}
        .sidebar-link:hover, .sidebar-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .main-content { flex: 1; min-width: 0; }
        .top-header { position: sticky; top: 0; z-index: 1020; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(5px); box-shadow: 0 2px 10px rgba(0,0,0,.05); padding: 1rem 1.5rem; }
    </style>
</head>
<body>
<div class="d-flex">
