<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteção: Garante que o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$mensagem_dados = "";
$mensagem_senha = "";

// 1. Carregar as informações do utilizador com base na estrutura real do SQL
try {
    $stmt = $pdo->prepare("SELECT nome_completo, email, telefone, morada, codigo_postal, localidade, created_at FROM velas_clientes WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao carregar o perfil: " . $e->getMessage());
}

// 2. Processar a Atualização dos Dados de Envio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_update_profile'])) {
    $nome_completo = trim($_POST['nome_completo']);
    $telefone = trim($_POST['telefone']);
    $morada = trim($_POST['morada']);
    $codigo_postal = trim($_POST['codigo_postal']);
    $localidade = trim($_POST['localidade']);

    if (empty($nome_completo)) {
        $mensagem_dados = "<div class='alert error'>❌ O campo Nome não pode ficar vazio.</div>";
    } else {
        try {
            $stmt_update = $pdo->prepare("UPDATE velas_clientes SET nome_completo = ?, telefone = ?, morada = ?, codigo_postal = ?, localidade = ? WHERE id = ?");
            $stmt_update->execute([$nome_completo, $telefone, $morada, $codigo_postal, $localidade, $user_id]);
            
            // Sincronizar o nome atualizado com a sessão do catálogo
            $_SESSION['user_name'] = $nome_completo;
            
            // Atualizar variáveis locais para exibição imediata no formulário
            $user['nome_completo'] = $nome_completo;
            $user['telefone'] = $telefone;
            $user['morada'] = $morada;
            $user['codigo_postal'] = $codigo_postal;
            $user['localidade'] = $localidade;

            $mensagem_dados = "<div class='alert success'>✨ Dados de envio guardados com sucesso!</div>";
        } catch (PDOException $e) {
            $mensagem_dados = "<div class='alert error'>❌ Erro ao atualizar dados: " . $e->getMessage() . "</div>";
        }
    }
}

// 3. Processar a Alteração da Palavra-passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_change_password'])) {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    if (empty($senha_atual) || empty($nova_senha) || empty($confirma_senha)) {
        $mensagem_senha = "<div class='alert error'>❌ Por favor, preencha todos os campos de segurança.</div>";
    } elseif ($nova_senha !== $confirma_senha) {
        $mensagem_senha = "<div class='alert error'>❌ A nova palavra-passe e a confirmação não coincidem.</div>";
    } else {
        try {
            // Seleciona usando a coluna correta 'palavra_passe'
            $stmt_pass = $pdo->prepare("SELECT palavra_passe FROM velas_clientes WHERE id = ? LIMIT 1");
            $stmt_pass->execute([$user_id]);
            $hash_atual = $stmt_pass->fetchColumn();

            if (password_verify($senha_atual, $hash_atual)) {
                $novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                
                $stmt_up_pass = $pdo->prepare("UPDATE velas_clientes SET palavra_passe = ? WHERE id = ?");
                $stmt_up_pass->execute([$novo_hash, $user_id]);

                $mensagem_senha = "<div class='alert success'>🔒 Palavra-passe alterada com sucesso!</div>";
            } else {
                $mensagem_senha = "<div class='alert error'>❌ A palavra-passe atual está incorreta.</div>";
            }
        } catch (PDOException $e) {
            $mensagem_senha = "<div class='alert error'>❌ Erro ao atualizar a palavra-passe.</div>";
        }
    }
}

// Formatar data de registo amigável
$data_registo = isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O Meu Perfil | Bougivon</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fdfaf6; margin: 0; color: #333; }
        
        .navbar { background: #fff; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .navbar h2 { margin: 0; color: #8d6e63; font-family: 'Georgia', serif; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #5d4037; font-weight: bold; font-size: 0.95em; transition: 0.3s; }
        .nav-links a:hover { color: #8d6e63; }
        .btn-cart { background: #8d6e63; color: white !important; padding: 8px 18px; border-radius: 20px; }
        .btn-cart:hover { background: #5d4037; }

        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; box-sizing: border-box; }
        
        .profile-header { background: white; padding: 30px; border-radius: 12px; border: 1px solid #f0e6df; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .profile-title h1 { margin: 0; font-family: 'Georgia', serif; color: #5d4037; font-size: 1.8em; }
        .profile-title p { margin: 5px 0 0 0; color: #888; font-size: 0.95em; }
        .badge-member { background: #e0d4cc; color: #5d4037; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 0.85em; }

        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        @media (max-width: 768px) { .profile-grid { grid-template-columns: 1fr; } }

        .profile-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #f0e6df; box-sizing: border-box; }
        .profile-card h3 { margin-top: 0; margin-bottom: 20px; font-family: 'Georgia', serif; color: #8d6e63; border-bottom: 2px solid #fdfaf6; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 6px; color: #5d4037; font-size: 0.9em; }
        input[type="text"], input[type="email"], input[type="password"] { padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 0.95em; box-sizing: border-box; background: #fff; }
        input:focus { border-color: #8d6e63; outline: none; }
        input:disabled { background-color: #f5f5f5; color: #999; cursor: not-allowed; }

        .row-postal { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

        .btn-submit { background: #8d6e63; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-weight: bold; font-size: 0.95em; cursor: pointer; transition: 0.3s; width: 100%; margin-top: 10px; }
        .btn-submit:hover { background: #6d4c41; }
        
        .alert { padding: 12px; border-radius: 8px; font-size: 0.9em; text-align: center; font-weight: bold; margin-bottom: 15px; }
        .success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .error { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

        .btn-back-container { text-align: center; margin-top: 30px; }
        .btn-back { display: inline-block; color: #8d6e63; text-decoration: none; font-weight: bold; font-size: 0.95em; }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Bougivon Loja</h2>
        <div class="nav-links">
            <a href="user.php">Catálogo</a>
            <a href="add_personalized_candle.php" style="color: #c62828;">✨ Personalizar Vela</a>
            <a href="cart.php" class="btn-cart">🛒 O meu Carrinho</a>
            <a href="logout.php" style="color: #dc3545;">Sair</a>
        </div>
    </div>

    <div class="container">
        
        <div class="profile-header">
            <div class="profile-title">
                <h1>Área Pessoal de <?= htmlspecialchars($user['nome_completo']) ?></h1>
                <p>Mantém os teus dados de envio atualizados para facilitar as tuas encomendas.</p>
            </div>
            <div class="badge-member">
                🌱 Cliente desde <?= $data_registo ?>
            </div>
        </div>

        <div class="profile-grid">
            
            <div class="profile-card">
                <h3>📦 Endereço de Faturação e Envio</h3>
                <?= $mensagem_dados ?>

                <form action="profile.php" method="POST">
                    <input type="hidden" name="action_update_profile" value="1">
                    
                    <div class="form-group">
                        <label for="email">E-mail de Registo:</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="nome_completo">Nome Completo:</label>
                        <input type="text" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($user['nome_completo']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telemóvel / Contacto Telefónico:</label>
                        <input type="text" id="telefone" name="telefone" placeholder="Ex: 912345678" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="morada">Morada de Entrega:</label>
                        <input type="text" id="morada" name="morada" placeholder="Rua, número, andar..." value="<?= htmlspecialchars($user['morada'] ?? '') ?>">
                    </div>

                    <div class="row-postal">
                        <div class="form-group">
                            <label for="codigo_postal">Código Postal:</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" placeholder="0000-000" value="<?= htmlspecialchars($user['codigo_postal'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="localidade">Localidade:</label>
                            <input type="text" id="localidade" name="localidade" placeholder="Cidade / Vila" value="<?= htmlspecialchars($user['localidade'] ?? '') ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Guardar Dados de Envio</button>
                </form>
            </div>

            <div class="profile-card">
                <h3>🔒 Alterar Palavra-Passe</h3>
                <?= $mensagem_senha ?>

                <form action="profile.php" method="POST">
                    <input type="hidden" name="action_change_password" value="1">
                    
                    <div class="form-group">
                        <label for="senha_atual">Palavra-Passe Atual:</label>
                        <input type="password" id="senha_atual" name="senha_atual" placeholder="Senha atual da conta" required>
                    </div>

                    <div class="form-group">
                        <label for="nova_senha">Nova Palavra-Passe:</label>
                        <input type="password" id="nova_senha" name="nova_senha" placeholder="Mínimo 6 caracteres" required>
                    </div>

                    <div class="form-group">
                        <label for="confirma_senha">Confirmar Nova Palavra-Passe:</label>
                        <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Repita a nova senha" required>
                    </div>

                    <button type="submit" class="btn-submit">Atualizar Palavra-Passe</button>
                </form>
            </div>

        </div>

        <div class="btn-back-container">
            <a href="user.php" class="btn-back">← Voltar para o Catálogo de Velas</a>
        </div>

    </div>

</body>
</html>