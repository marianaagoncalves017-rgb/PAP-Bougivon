<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o utilizador já estiver logado, redireciona diretamente para o catálogo
if (isset($_SESSION['user_id'])) {
    header("Location: user.php");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['pass']; // Ajustado para corresponder ao name="pass" do teu formulário

    if (empty($email) || empty($password)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            // Consulta os dados com base na estrutura real da tua tabela (nome_completo, palavra_passe, perfil_role)
            $stmt = $pdo->prepare("SELECT id, nome_completo, palavra_passe, perfil_role FROM velas_clientes WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['palavra_passe'])) {
                // Instancia as variáveis de sessão necessárias para o projeto
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome_completo'];
                $_SESSION['user_role'] = $user['perfil_role'];

                // Redireciona consoante o nível de acesso
                if ($user['perfil_role'] === 'admin') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: user.php");
                }
                exit();
            } else {
                $erro = "E-mail ou palavra-passe incorretos.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no sistema: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sessão | Bougivon</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fdfaf6;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(141, 110, 99, 0.1);
            border: 1px solid #f3ebe6;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            text-align: center;
        }

        /* Estilo do link de Voltar à Loja */
        .btn-back-home {
            display: block;
            text-align: left;
            margin-bottom: 25px;
            color: #8d6e63;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9em;
            transition: 0.3s;
        }
        .btn-back-home:hover {
            color: #5d4037;
            text-decoration: underline;
        }

        .brand-title {
            font-family: 'Georgia', serif;
            font-size: 2.2em;
            font-weight: bold;
            color: #5d4037;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            font-size: 0.85em;
            color: #888;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="email"], input[type="password"] {
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95em;
            font-family: inherit;
            box-sizing: border-box;
            background-color: #fafafa;
            width: 100%;
        }

        input:focus {
            border-color: #8d6e63;
            outline: none;
            background-color: #fff;
        }

        button {
            background-color: #8d6e63;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1em;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        button:hover {
            background-color: #6d4c41;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9em;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            border: 1px solid #ffcdd2;
        }

        .register-link {
            display: inline-block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
            text-decoration: none;
            width: 100%;
        }

        .register-link span {
            color: #8d6e63;
            font-weight: bold;
        }

        .register-link:hover span {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <a href="index.php" class="btn-back-home">← Voltar à Loja</a>

        <div class="brand-title">BOUGIVON</div>
        <div class="brand-subtitle">ÁREA DE LOGIN</div>

        <?php if (!empty($erro)): ?>
            <div class="error-message"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="pass" placeholder="Palavra-passe" required>
            <button type="submit">ENTRAR</button>
        </form>

        <a href="create_account.php" class="register-link">
            Não tem conta? <span>Criar Conta</span>
        </a>

        <a href="change_password.php" class="register-link" style="margin-top: 12px;">
            Esqueceu-se da password? <span>Altere sua password.</span>
        </a>
    </div>

</body>
</html>