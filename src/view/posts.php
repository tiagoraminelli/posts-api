<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: /posts_api/public/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed de Posts</title>
    <link rel="stylesheet" href="/posts_api/public/css/posts.css">
</head>
<body>
    <div class="sidebar">
    <h2>Mi App</h2>
    <nav>
        <a href="#">📄 Feed</a>
        <a href="#">➕ Nuevo post</a>
        <!-- más enlaces futuros -->
    </nav>

    <div class="user-info">
        <strong>Usuario:</strong><br>
        <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?><br>
        <small><?= htmlspecialchars($_SESSION['usuario']['rol']) ?></small>
    </div>
</div>
    <div class="container">
        <div class="header">
            <h1>Feed de Posts</h1>
            <button id="refresh-btn">↻ Actualizar</button>
        </div>
        
        <div id="posts-container">
            <div class="loading">Cargando posts...</div>
        </div>
    </div>

    <script>
        // Datos de ejemplo (reemplazar con llamada API real)
       
        // Función para formatear la fecha
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('es-ES', options);
        }

        // Función para renderizar los posts
        function renderPosts(posts) {
            const container = document.getElementById('posts-container');
            
            if (!posts || posts.length === 0) {
                container.innerHTML = '<div class="post-card">No hay posts disponibles</div>';
                return;
            }
            
            let html = '';
            
            // Filtrar solo posts publicados (opcional)
            const publishedPosts = posts.filter(post => post.status === 'published');
            
            publishedPosts.forEach(post => {
                const firstLetter = post.title.charAt(0).toUpperCase();
                const formattedDate = formatDate(post.created_at);
                const statusClass = post.status === 'published' ? 'status-published' : 'status-draft';
                
                html += `
                    <div class="post-card">
                        <div class="post-header">
                            <div class="post-avatar">${firstLetter}</div>
                            <div>
                                <span class="post-user">Autor Anónimo</span>
                                <span class="post-date">· ${formattedDate}</span>
                            </div>
                        </div>
                        <div class="post-title">${post.title}</div>
                        <div class="post-content">${post.content}</div>
                        <div class="post-footer">
                            <span class="post-status ${statusClass}">${post.status === 'published' ? 'Publicado' : 'Borrador'}</span>
                            <span>${post.id}</span>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Función para cargar los posts desde la API
        async function loadPosts() {
            try {
                const token = localStorage.getItem('auth_token');
                const response = await fetch('/posts_api/public/index.php', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Error al cargar posts');
                }
                
                const data = await response.json();
                renderPosts(data.data);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('posts-container').innerHTML = `
                    <div class="post-card" style="color: #E0245E;">
                        Error al cargar los posts. Intenta recargar la página.
                    </div>
                `;
            }
        }

        // Event listeners
        document.getElementById('refresh-btn').addEventListener('click', loadPosts);

        // Inicialización
        document.addEventListener('DOMContentLoaded', () => {
            // Usar datos de ejemplo o cargar desde API
            // renderPosts(postsData.data);
            
            // Cargar desde API (recomendado)
            loadPosts();
        });
    </script>
</body>
</html>