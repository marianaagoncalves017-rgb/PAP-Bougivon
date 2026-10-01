<?php 
include 'config.php';

$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    // Encriptação da pass para ser compatível com password_verify do login
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT); 
    $telefone = $_POST['telefone'];

    try {
        // Verifica se o email já existe
        $check = $pdo->prepare("SELECT id FROM velas_clientes WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            $mensagem = "Este e-mail já está registado.";
            $tipo_alerta = "error";
        } else {
            // Insere o novo cliente (por padrão o perfil_role é 'cliente')
            $sql = "INSERT INTO velas_clientes (nome_completo, email, palavra_passe, perfil_role, telefone) VALUES (?, ?, ?, 'cliente', ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $email, $pass, $telefone]);
            
            $mensagem = "Conta criada com sucesso! Já pode fazer login.";
            $tipo_alerta = "success";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao criar conta: " . $e->getMessage();
        $tipo_alerta = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta | Bougivon</title>
    <style>
        body { 
            background-color: #fdfaf6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            font-family: 'Segoe UI', sans-serif;
        }

        .register-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin: 20px;
        }

        .brand-title {
            color: #8d6e63;
            font-family: 'Georgia', serif;
            font-size: 2em;
            letter-spacing: 3px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .instruction {
            color: #bcaaa4;
            font-size: 0.9em;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #eee;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus { border-color: #8d6e63; background: #fff; outline: none; }

        button {
            width: 100%;
            padding: 14px;
            background-color: #8d6e63;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover { background-color: #6d4c41; }

        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85em;
            margin-bottom: 20px;
        }
        .error { color: #d32f2f; background: #ffebee; }
        .success { color: #2e7d32; background: #e8f5e9; }

        .login-link {
            display: block;
            margin-top: 25px;
            color: #666;
            text-decoration: none;
            font-size: 0.85em;
        }
        .login-link span { color: #8d6e63; font-weight: bold; }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="brand-title">BOUGIVON</div>
        <p class="instruction">Crie a sua conta para encomendar as suas velas favoritas 🌿</p>

        <?php if ($mensagem): ?>
            <div class="alert <?= $tipo_alerta ?>"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="tel" name="telefone" placeholder="Telefone (opcional)">
            <input type="password" name="pass" placeholder="Palavra-passe" required>
            <button type="submit">CRIAR CONTA</button>
        </form>

        <a href="login.php" class="login-link">
            Já tem conta? <span>Fazer Login</span>
        </a>
    </div>

</body>
</html>