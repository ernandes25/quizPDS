<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header id="header-php">
        <h1 id="title-php">Cadastro concluído</h1>
    </header>
    <main class="main">
        <?php
        $nome = $_GET["nome"] ?? "sem nome";
        
        echo "<br>";


        $email = $_GET["email"] ?? "desconhecido";
        echo nl2br("<br>");
        echo "<br>";


        $telefone = $_GET["telefone"] ?? "desconhecido";

        
        echo nl2br("<br>");
        echo "<br>";



        echo "<p> É um prazer te conhecer, <br> <br>
        <strong> $nome</strong> 

        <br>
        
        

        <strong>  $email</strong> 
        <br>
        
        <strong> $telefone!</strong> 
        <br>
        
        Bem vindo a teste PDS!";
        echo nl2br("<br>");
        echo "<br>";


        ?>
        <p> <a class="start-test" href="quiz.html"> Vamos começar o teste! </a></p>
    </main>
    <script src="scripts.js"></script>
</body>

</html>