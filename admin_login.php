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

    <script src="script.js" defer></script>
    
</body>
</html>