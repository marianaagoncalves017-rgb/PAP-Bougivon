<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_utilizador = $_SESSION['user_id'];
$erro = "";
$sucesso = false;

// 1. Obter número de telefone guardado no perfil
$stmt_user = $pdo->prepare("SELECT telefone FROM velas_clientes WHERE id = ? LIMIT 1");
$stmt_user->execute([$id_utilizador]);
$dados_user = $stmt_user->fetch();
$telefone_cliente = ($dados_user && !empty($dados_user['telefone'])) ? $dados_user['telefone'] : '';

// 2. Carregar itens a partir da sessão ($_SESSION['carrinho'])
$carrinho_sessao = $_SESSION['carrinho'] ?? [];

if (empty($carrinho_sessao)) {
    header("Location: cart.php");
    exit();
}

$itens_carrinho = [];
$total_encomenda = 0;

// Processar e calcular o total com base na sessão
foreach ($carrinho_sessao as $item_key => $item) {
    if (is_array($item)) {
        $id_real = (int)$item['id'];
        $qtd = (int)$item['qtd'];
        $opcao = $item['opcao'] ?? 'simples';
    } else {
        $partes = explode('_', $item_key);
        $id_real = (int)$partes[0];
        $qtd = (int)$item;
        $opcao = 'simples';
    }

    $stmt = $pdo->prepare("SELECT * FROM velas_artigos WHERE id = ?");
    $stmt->execute([$id_real]);
    $produto = $stmt->fetch();

    if (!$produto) continue;

    $preco_base = (float)$produto['preco'];
    $preco_unitario = $preco_base;

    // Cálculo das opções personalizadas
    if ($id_real === 14) {
        $preco_unitario = 0;
        if (str_contains($opcao, 'tamanho:grande')) $preco_unitario += 5.00;
        if (str_contains($opcao, 'tamanho:pequeno') || str_contains($opcao, 'tamanho:grande_pequeno')) $preco_unitario += 2.50;
    }
    if (str_contains($opcao, 'decorada')) $preco_unitario += 1.00;
    if (str_contains($opcao, '_tronco:sim')) $preco_unitario += 2.00;
    if (str_contains($opcao, '_azevinho:sim')) $preco_unitario += 0.80;

    $subtotal = $preco_unitario * $qtd;
    $total_encomenda += $subtotal;

    $itens_carrinho[] = [
        'id_produto' => $id_real,
        'nome_produto' => $produto['nome_produto'],
        'quantidade' => $qtd,
        'preco_unitario' => $preco_unitario,
        'opcao' => $opcao
    ];
}

// Variables para armazenar a simulação após submissão
$dados_pagamento = [];

// 3. Processar a submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $morada_entrega = isset($_POST['morada_entrega']) ? trim($_POST['morada_entrega']) : '';
    $telefone_digitado = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $metodo_pagamento = $_POST['metodo_pagamento'] ?? 'MBWAY';

    if (empty($morada_entrega) || empty($telefone_digitado)) {
        $erro = "Por favor, preencha todos os dados de envio.";
    } else {
        try {
            $pdo->beginTransaction();

            date_default_timezone_set('Europe/Lisbon');
            $data_encomenda = date('Y-m-d H:i:s');
            $tracking_id = "BV-" . rand(10000, 99999);

            // Gravar o pedido principal
            $stmt = $pdo->prepare("
                INSERT INTO velas_pedidos (id_utilizador, tracking_id, valor_total, data_encomenda, status_pagamento, morada_entrega) 
                VALUES (?, ?, ?, ?, 'Pendente', ?)
            ");
            $stmt->execute([$id_utilizador, $tracking_id, $total_encomenda, $data_encomenda, $morada_entrega]);

            // Atualizar o stock de cada artigo
            foreach ($itens_carrinho as $item) {
                $stmt_stock = $pdo->prepare("
                    UPDATE velas_artigos 
                    SET stock_atual = stock_atual - ? 
                    WHERE id = ?
                ");
                $stmt_stock->execute([$item['quantidade'], $item['id_produto']]);
            }

            // Atualizar contacto telefónico se alterado
            if ($telefone_digitado !== $telefone_cliente) {
                $stmt_update_tel = $pdo->prepare("UPDATE velas_clientes SET telefone = ? WHERE id = ?");
                $stmt_update_tel->execute([$telefone_digitado, $id_utilizador]);
            }

            // Preparar os dados simulados para a apresentação ao utilizador
            $_SESSION['ultimo_pedido'] = [
                'tracking_id' => $tracking_id,
                'total' => $total_encomenda,
                'metodo' => $metodo_pagamento,
                'telefone' => $telefone_digitado,
                'entidade' => '12345',
                'referencia' => rand(100, 999) . ' ' . rand(100, 999) . ' ' . rand(100, 999)
            ];

            // Limpar o carrinho da sessão
            unset($_SESSION['carrinho']);

            $pdo->commit();
            $sucesso = true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $erro = "Erro ao processar a encomenda: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Encomenda | Bougivon</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fdfaf6; margin: 0; color: #333; }
        .navbar { background: #fff; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; color: #8d6e63; font-family: 'Georgia', serif; }
        .nav-links a { text-decoration: none; color: #8d6e63; font-weight: bold; margin-left: 20px; }
        
        .container { max-width: 600px; margin: 40px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
        h3 { font-family: 'Georgia', serif; color: #5d4037; margin-top: 0; }
        
        .resumo-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 0.95em; }
        .total-box { display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2em; color: #8d6e63; padding: 15px 0; margin-bottom: 20px; }
        
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #5d4037; font-size: 0.9em; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #eee; border-radius: 8px; background-color: #f9f9f9; box-sizing: border-box; font-family: inherit; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #8d6e63; background-color: #fff; }
        
        .btn-submit { width: 100%; padding: 14px; background-color: #8d6e63; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; text-align: center; display: block; text-decoration: none; }
        .btn-submit:hover { background-color: #6d4c41; }
        .btn-back { display: inline-block; margin-top: 15px; color: #8d6e63; text-decoration: none; font-size: 0.9em; }
        
        .error-message { color: #d32f2f; background: #ffebee; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9em; }
        .success-box { text-align: center; padding: 20px; }
        .success-icon { font-size: 3em; color: #2e7d32; margin-bottom: 10px; }

        /* Caixas de Simulação de Pagamento */
        .box-pagamento { background: #fdf8f5; border: 1px dashed #8d6e63; border-radius: 10px; padding: 20px; margin: 20px 0; text-align: left; }
        .box-pagamento h4 { margin-top: 0; color: #5d4037; text-align: center; }
        .info-linha { display: flex; justify-content: space-between; font-size: 1em; padding: 6px 0; border-bottom: 1px solid #f0e0d6; }
        .info-linha span:first-child { font-weight: bold; color: #8d6e63; }
        .timer { font-size: 1.2em; font-weight: bold; color: #d32f2f; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Bougivon</h2>
        <div class="nav-links">
            <a href="user.php">Voltar à Loja</a>
        </div>
    </div>

    <div class="container">
        <?php if ($sucesso && isset($_SESSION['ultimo_pedido'])): 
            $p = $_SESSION['ultimo_pedido'];
        ?>
            <div class="success-box">
                <div class="success-icon">🌿</div>
                <h3>Encomenda Registada com Sucesso!</h3>
                <p>Número do Pedido: <strong><?= htmlspecialchars($p['tracking_id']) ?></strong></p>

                <!-- SIMULAÇÃO MB WAY -->
                <?php if ($p['metodo'] === 'MBWAY'): ?>
                    <div class="box-pagamento">
                        <h4>📲 Pagamento via MB WAY</h4>
                        <p style="text-align: center;">Enviámos um pedido de pagamento de <strong><?= number_format($p['total'], 2, ',', '.') ?>€</strong> para o número <strong><?= htmlspecialchars($p['telefone']) ?></strong>.</p>
                        <p style="text-align: center; font-size: 0.9em; color: #666;">Abre a tua aplicação MB WAY e aceita o pagamento para concluir a compra.</p>
                        <div class="timer">A aguardar aprovação... <span id="countdown">04:59</span></div>
                    </div>
                    <script>
                        var seconds = 299;
                        setInterval(function() {
                            var mins = Math.floor(seconds / 60);
                            var secs = seconds % 60;
                            document.getElementById('countdown').innerHTML = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
                            if (seconds > 0) seconds--;
                        }, 1000);
                    </script>

                <!-- SIMULAÇÃO MULTIBANCO -->
                <?php elseif ($p['metodo'] === 'Entidade_Referencia'): ?>
                    <div class="box-pagamento">
                        <h4>🏛️ Dados para Pagamento Multibanco</h4>
                        <div class="info-linha"><span>Entidade:</span> <span><?= $p['entidade'] ?></span></div>
                        <div class="info-linha"><span>Referência:</span> <span><?= $p['referencia'] ?></span></div>
                        <div class="info-linha"><span>Montante:</span> <span><?= number_format($p['total'], 2, ',', '.') ?>€</span></div>
                        <p style="text-align: center; font-size: 0.85em; color: #666; margin-top: 15px;">Pode pagar no Multibanco ou no seu Homebanking na opção "Pagamento de Serviços".</p>
                    </div>

                <!-- SIMULAÇÃO TRANSFERÊNCIA -->
                <?php else: ?>
                    <div class="box-pagamento">
                        <h4>🏦 Transferência Bancária</h4>
                        <div class="info-linha"><span>IBAN:</span> <span>PT50 0000 1234 5678 9012 3456 7</span></div>
                        <div class="info-linha"><span>Titular:</span> <span>Bougivon Velas Lda</span></div>
                        <div class="info-linha"><span>Montante:</span> <span><?= number_format($p['total'], 2, ',', '.') ?>€</span></div>
                        <p style="text-align: center; font-size: 0.85em; color: #666; margin-top: 15px;">Por favor, inclua o código <strong><?= htmlspecialchars($p['tracking_id']) ?></strong> no descritivo da transferência.</p>
                    </div>
                <?php endif; ?>

                <a href="user.php" class="btn-submit">Voltar ao Início</a>
            </div>
        <?php else: ?>
            <h3>Resumo do Pedido</h3>
            
            <?php foreach ($itens_carrinho as $item): ?>
                <div class="resumo-item">
                    <span><?= htmlspecialchars($item['nome_produto']) ?> (x<?= $item['quantidade'] ?>)</span>
                    <span><?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?>€</span>
                </div>
            <?php endforeach; ?>

            <div class="total-box">
                <span>Total a Pagar:</span>
                <span><?= number_format($total_encomenda, 2, ',', '.') ?>€</span>
            </div>

            <h3>Dados de Envio e Pagamento</h3>

            <?php if ($erro): ?>
                <div class="error-message"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label for="morada_entrega">Morada de Entrega</label>
                <textarea id="morada_entrega" name="morada_entrega" rows="3" placeholder="Ex: Rua Direita, Vagos" required></textarea>

                <label for="telefone">Contacto Telefónico</label>
                <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($telefone_cliente) ?>" placeholder="Ex: 912345678" required>

                <label for="metodo_pagamento">Método de Pagamento</label>
                <select id="metodo_pagamento" name="metodo_pagamento" onchange="atualizarInfoPagamento()">
                    <option value="MBWAY">MB WAY</option>
                    <option value="Entidade_Referencia">Entidade e Referência Multibanco</option>
                    <option value="Transferencia">Transferência Bancária</option>
                </select>

                <!-- Avisos dinâmicos antes de confirmar -->
                <div id="info_mbway" style="font-size:0.85em; color:#666; margin-bottom:15px;">
                    💡 Irá receber uma notificação de pagamento no telemóvel indicado.
                </div>
                <div id="info_mb" style="font-size:0.85em; color:#666; margin-bottom:15px; display:none;">
                    💡 A entidade e referência serão geradas imediatamente após confirmar.
                </div>
                <div id="info_transf" style="font-size:0.85em; color:#666; margin-bottom:15px; display:none;">
                    💡 Os dados do IBAN serão apresentados no ecrã seguinte.
                </div>

                <button type="submit" class="btn-submit">CONFIRMAR E ENCOMENDAR</button>
            </form>

            <center><a href="cart.php" class="btn-back">Voltar ao Carrinho</a></center>
        <?php endif; ?>
    </div>

    <script>
    function atualizarInfoPagamento() {
        var m = document.getElementById('metodo_pagamento').value;
        document.getElementById('info_mbway').style.display = (m === 'MBWAY') ? 'block' : 'none';
        document.getElementById('info_mb').style.display = (m === 'Entidade_Referencia') ? 'block' : 'none';
        document.getElementById('info_transf').style.display = (m === 'Transferencia') ? 'block' : 'none';
    }
    </script>
</body>
</html>