<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Administrador</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <img id="logo" src="logo.jpeg">
            </ul>
            <ul>
                <a class="item" href="index.html">
                    <li>Início</li>
                </a>
            </ul>
            <ul>
                <a class="item" href="cadastro.html">
                    <li>Cadastro</li>
                </a>
            </ul>
            <ul>
                <a class="item" href="contato.html">
                    <li>Contato</li>
                </a>
            </ul>
        </nav>
    </header>
    <h1 class="title_cad">Login do Administrador</h1>
    <div class="form">
        <section>
            <form id="adminLoginForm" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required><br>
                <button type="submit">Login</button>
            </form>
        </section>
    </div>
    <footer>
        <img id="logo" src="./logo.jpeg">
        <span>Todos os direitos reservados &copy;</span>
        <span>Desenvolvido por: Bay Software - H & E Silva</span>
    </footer>
    <script>
        document.getElementById('adminLoginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('admin_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.href = 'admin_dashboard.html';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
            });
        });
    </script>
</body>
</html>