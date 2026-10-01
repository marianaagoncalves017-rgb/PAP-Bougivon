<?php 
include 'config.php'; 

// Garante que a sessão está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Proteção de Segurança: Verifica se o utilizador tem sessão iniciada e se o perfil é 'admin'
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    session_destroy();
    header("Location: login.php");
    exit();
}

$id_admin_atual = $_SESSION['user_id'];

// Definição de variáveis para a saudação dinâmica
$nome_completo = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador';
$hora = (int)date('H');
if ($hora >= 5 && $hora < 13) {
    $saudacao = "Bom dia";
} elseif ($hora >= 13 && $hora < 20) {
    $saudacao = "Boa tarde";
} else {
    $saudacao = "Boa noite";
}

// Captura o filtro atual via GET para sabermos que tabela renderizar (Padrão: ativos)
$filtro_atual = isset($_GET['filtro_status']) ? strtolower(trim($_GET['filtro_status'])) : 'ativos';

// =========================================================================
// PROCESSA A ELIMINAÇÃO DE UM UTILIZADOR
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_utilizador_id'])) {
    $id_user_del = (int)$_POST['eliminar_utilizador_id'];

    if ($id_user_del === (int)$id_admin_atual) {
        $_SESSION['feedback_vela'] = "⚠️ Não podes eliminar a tua própria conta de Administrador enquanto estás ligado!";
    } else {
        try {
            $stmt_del = $pdo->prepare("DELETE FROM velas_clientes WHERE id = ?");
            $stmt_del->execute([$id_user_del]);
            $_SESSION['feedback_vela'] = "🗑️ Utilizador #$id_user_del eliminado com sucesso.";
        } catch (PDOException $e) {
            $_SESSION['feedback_vela'] = "❌ Erro ao eliminar utilizador: " . $e->getMessage();
        }
    }
    header("Location: dashboard.php?filtro_status=" . urlencode($filtro_atual));
    exit();
}

// =========================================================================
// PROCESSA A DECISÃO SOBRE UMA VELA PERSONALIZADA (ACEITAR / REJEITAR)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao_final'])) {
    $id_vela = (int)$_POST['id_vela'];
    $acao = $_POST['acao_final'];
    $nome_aviso = isset($_POST['nome_vela_aviso']) ? $_POST['nome_vela_aviso'] : 'Vela';

    if ($acao === 'confirmar_aceitar' && isset($_POST['preco_vela'])) {
        $preco_definido = (float)str_replace(',', '.', $_POST['preco_vela']);
        if ($preco_definido > 0) {
            $stmt = $pdo->prepare("UPDATE velas_artigos SET pending_price = 0, preco = ?, stock_atual = 1 WHERE id = ?");
            $stmt->execute([$preco_definido, $id_vela]);
            $_SESSION['feedback_vela'] = "✨ O pedido para \"$nome_aviso\" foi aceite com o preço de " . number_format($preco_definido, 2, ',', '.') . "€ e já está disponível no catálogo!";
        }
    } elseif ($acao === 'confirmar_rejeitar') {
        $stmt = $pdo->prepare("DELETE FROM velas_artigos WHERE id = ? AND pending_price = 1");
        $stmt->execute([$id_vela]);
        $_SESSION['feedback_vela'] = "❌ O pedido personalizado para \"$nome_aviso\" foi rejeitado e removido do sistema.";
    }
    header("Location: dashboard.php?filtro_status=" . urlencode($filtro_atual));
    exit();
}

// =========================================================================
// PROCESSA A ALTERAÇÃO DO ESTADO DE PAGAMENTO DA ENCOMENDA
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_encomenda']) && isset($_POST['novo_estado'])) {
    $id_encomenda = (int)$_POST['id_encomenda'];
    $novo_estado = trim($_POST['novo_estado']);
    
    $filtro_retorno = isset($_POST['filter_retorno']) ? strtolower(trim($_POST['filter_retorno'])) : 'ativos';

    try {
        $pdo->beginTransaction();

        $stmt_update = $pdo->prepare("UPDATE velas_pedidos SET status_pagamento = ? WHERE id = ?");
        $stmt_update->execute([$novo_estado, $id_encomenda]);

        if (strtolower($novo_estado) === 'concluido') {
            $stmt_user = $pdo->prepare("SELECT id_utilizador FROM velas_pedidos WHERE id = ?");
            $stmt_user->execute([$id_encomenda]);
            $id_utilizador = $stmt_user->fetchColumn();

            if ($id_utilizador) {
                $stmt_del_artigo = $pdo->prepare("DELETE FROM velas_artigos WHERE estilo = 'Personalizada' AND created_by = ?");
                $stmt_del_artigo->execute([$id_utilizador]);
            }
        }

        $pdo->commit();
        $_SESSION['feedback_vela'] = "💼 Estado da encomenda #$id_encomenda atualizado para '" . ucfirst($novo_estado) . "' com sucesso!";
        
        header("Location: dashboard.php?filtro_status=" . urlencode($filtro_retorno));
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "<script>alert('Erro ao atualizar a encomenda: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// =========================================================================
// CÁLCULO DOS INDICADORES E KPIs GERAIS
// =========================================================================
$total_vendas = (float)$pdo->query("SELECT SUM(valor_total) FROM velas_pedidos WHERE LOWER(status_pagamento) != 'pendente'")->fetchColumn();
$encomendas_pendentes = (int)$pdo->query("SELECT COUNT(*) FROM velas_pedidos WHERE LOWER(status_pagamento) = 'pendente'")->fetchColumn();
$artigos_esgotados = (int)$pdo->query("SELECT COUNT(*) FROM velas_artigos WHERE stock_atual <= 0 AND pending_price = 0")->fetchColumn();
$total_utilizadores = (int)$pdo->query("SELECT COUNT(*) FROM velas_clientes")->fetchColumn();

$stmt_pending = $pdo->query("SELECT * FROM velas_artigos WHERE pending_price = 1 ORDER BY id DESC");
$pending_customs = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração | Bougivon</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fdfaf6; margin: 0; color: #333; }
        .navbar { background: #fff; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; color: #8d6e63; font-family: 'Georgia', serif; }
        .logout-btn { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 0.9em; padding: 8px 15px; border: 1px solid #dc3545; border-radius: 20px; transition: 0.3s; }
        .logout-btn:hover { background: #dc3545; color: #fff; }

        .welcome-banner { background: #8d6e63; color: white; padding: 40px; text-align: left; }
        .welcome-banner h1 { margin: 0; font-family: 'Georgia', serif; font-size: 2em; }
        .welcome-banner p { margin-top: 10px; opacity: 0.9; }

        .container { padding: 40px; max-width: 1200px; margin: auto; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; margin-top: 20px; }
        .btn-add { background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: 0.3s; }
        .btn-add:hover { background: #218838; }

        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .kpi-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 5px solid #8d6e63; }
        .kpi-card h3 { margin: 0; font-size: 0.85em; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-card p { margin: 10px 0 0 0; font-size: 1.8em; font-weight: bold; color: #5d4037; }
        .kpi-card.alert { border-left-color: #dc3545; }
        .kpi-card.alert p { color: #dc3545; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #8d6e63; text-transform: uppercase; font-size: 0.8em; letter-spacing: 1px; }
        tr:hover { background: #fffcf9; }
        
        .badge { background: #e8f5e9; color: #2e7d32; padding: 5px 10px; border-radius: 4px; font-size: 0.85em; font-weight: bold; }
        .badge.stock-low { background: #fff3e0; color: #ef6c00; }
        .badge.stock-out { background: #ffebee; color: #c62828; }
        .badge.role-admin { background: #e3f2fd; color: #1565c0; }
        .badge.role-user { background: #f5f5f5; color: #616161; }

        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8em; font-weight: bold; text-transform: uppercase; }
        .status-pendente { background: #fff3e0; color: #f57c00; }
        .status-pago { background: #e8f5e9; color: #2e7d32; }
        .status-enviado { background: #e1f5fe; color: #0288d1; }
        .status-concluido { background: #f3e5f5; color: #7b1fa2; }

        .actions a, .actions button { text-decoration: none; font-weight: bold; margin-right: 10px; font-size: 0.9em; border: none; background: none; cursor: pointer; }
        .edit { color: #007bff; }
        .delete { color: #dc3545; }
        select.status-select { padding: 6px 10px; border-radius: 6px; border: 1px solid #ddd; font-family: inherit; font-size: 0.9em; background-color: #fff; cursor: pointer; }

        .feedback-box { background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 500; }
        
        .filter-group { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .filter-btn { text-decoration: none; padding: 8px 16px; border-radius: 20px; font-size: 0.85em; font-weight: bold; border: 1px solid #8d6e63; transition: 0.2s; }
        .filter-btn.active { background: #8d6e63; color: white; }
        .filter-btn.inactive { background: #fdfaf6; color: #8d6e63; }
        .filter-btn:hover { opacity: 0.9; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Bougivon Admin</h2>
        <div><a href="logout.php" class="logout-btn">Terminar Sessão</a></div>
    </div>

    <div class="welcome-banner">
        <h1><?= $saudacao ?>, <?= htmlspecialchars($nome_completo) ?>!</h1>
        <p>Bem-vindo ao sistema de gestão das tuas velas artesanais 🌿</p>
    </div>

    <div class="container">

        <?php if (isset($_SESSION['feedback_vela'])): ?>
            <div class="feedback-box">
                <?= $_SESSION['feedback_vela'] ?>
            </div>
            <?php unset($_SESSION['feedback_vela']); ?>
        <?php endif; ?>

        <?php if (!empty($pending_customs)): ?>
            <div id="pending-alert" style="max-width:1200px; margin:0 auto 30px auto; padding:20px; background:#fff8e1; border:1px solid #ffecb3; border-radius:12px;">
                <h3 style="margin-top:0; color:#8d6e63; font-family:'Georgia', serif;">🔔 Pedidos de Velas Personalizadas por Avaliar</h3>
                <p style="color:#6b4f3f; margin-bottom:15px;">Existem <strong><?= count($pending_customs) ?></strong> pedidos aguardando aprovação:</p>
                
                <?php foreach($pending_customs as $pc): ?>
                    <div class="vela-item" id="vela-card-<?= $pc['id'] ?>" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; border: 1px solid #e0d4cc; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <div class="vela-topo" style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="vela-info" style="flex-grow: 1;">
                                <strong style="font-size:1.15em; color:#5d4037;"><?= htmlspecialchars($pc['nome_produto']) ?></strong> 
                                <span style="font-size: 0.85em; color: #888; margin-left: 5px;">(Por Usuário ID: <?= htmlspecialchars($pc['created_by'] ?? 'Cliente') ?>)</span><br>
                                <span style="font-size:0.9em; color:#666; display:inline-block; margin-top:5px;">
                                    <strong>Aroma:</strong> <?= htmlspecialchars($pc['aroma'] ?? 'Nenhum') ?> | 
                                    <strong>Cor:</strong> <?= htmlspecialchars($pc['cor'] ?? 'Nenhuma') ?>
                                </span>
                            </div>
                            
                            <div id="botoes-iniciais-<?= $pc['id'] ?>">
                                <button type="button" style="background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; padding: 10px 18px; border-radius: 6px; font-weight: bold; cursor: pointer;" onclick="mostrarPainelAceitar(<?= $pc['id'] ?>)">✅ Aceitar</button>
                                <button type="button" style="background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; padding: 10px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-left: 10px;" onclick="rejeitarDireto(<?= $pc['id'] ?>, '<?= htmlspecialchars($pc['nome_produto'], ENT_QUOTES) ?>', '<?= htmlspecialchars($pc['aroma'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($pc['cor'] ?? '', ENT_QUOTES) ?>')">❌ Rejeitar</button>
                            </div>
                        </div>

                        <div class="bloco-preco-input" id="bloco-preco-<?= $pc['id'] ?>" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd; background: #fafafa; padding: 15px; border-radius: 6px;">
                            <form action="" method="POST" style="margin:0;">
                                <input type="hidden" name="id_vela" value="<?= $pc['id'] ?>">
                                <input type="hidden" name="nome_vela_aviso" value="<?= htmlspecialchars($pc['nome_produto']) ?>">
                                <input type="hidden" name="acao_final" value="confirmar_aceitar">
                                
                                <label style="font-weight:bold; color:#5d4037; font-size:0.9em; margin-right: 5px;">Definir Preço Unitário (€):</label>
                                <input type="number" name="preco_vela" style="width: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 6px;" step="0.01" min="0.01" placeholder="0.00" required>
                                
                                <button type="submit" style="background: #28a745; color: white; border: none; padding: 9px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-left: 10px;">✓ Confirmar e Ativar Preço</button>
                                <button type="button" style="background:#eee; border:1px solid #ccc; color:#333; padding: 9px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-left: 10px;" onclick="cancelarDecisao(<?= $pc['id'] ?>)">Cancelar</button>
                            </form>
                        </div>

                        <form id="form-rejeitar-<?= $pc['id'] ?>" action="" method="POST" style="display:none;">
                            <input type="hidden" name="id_vela" value="<?= $pc['id'] ?>">
                            <input type="hidden" name="nome_vela_aviso" value="<?= htmlspecialchars($pc['nome_produto']) ?>">
                            <input type="hidden" name="acao_final" value="confirmar_rejeitar">
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="kpi-grid">
            <div class="kpi-card">
                <h3>Total Faturado</h3>
                <p><?= number_format($total_vendas, 2, ',', '.') ?>€</p>
            </div>
            <div class="kpi-card">
                <h3>Pedidos Pendentes</h3>
                <p><?= $encomendas_pendentes ?> un.</p>
            </div>
            <div class="kpi-card <?= ($artigos_esgotados > 0) ? 'alert' : '' ?>">
                <h3>Artigos Sem Stock</h3>
                <p><?= $artigos_esgotados ?> artigos</p>
            </div>
            <div class="kpi-card">
                <h3>Utilizadores Registados</h3>
                <p><?= $total_utilizadores ?> utilizadores</p>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- GESTÃO DE UTILIZADORES -->
        <!-- ========================================================================= -->
        <div class="header-actions">
            <h2 style="color: #5d4037; margin: 0;">Gestão de Utilizadores</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo de Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt_users = $pdo->query("SELECT * FROM velas_clientes ORDER BY id DESC");
                if ($stmt_users->rowCount() == 0):
                ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #888; padding: 25px;">Nenhum utilizador encontrado.</td>
                </tr>
                <?php else: ?>
                    <?php while($u = $stmt_users->fetch()): 
                        // Verifica se existe a coluna perfil_role ou fallback para role
                        $perfil_bruto = isset($u['perfil_role']) ? $u['perfil_role'] : ($u['role'] ?? 'cliente');
                        $user_role = strtolower(trim($perfil_bruto));
                        $is_admin = ($user_role === 'admin' || $user_role === 'administrador');
                    ?>
                    <tr>
                        <td><strong>#<?= $u['id'] ?></strong></td>
                        <td><?= htmlspecialchars($u['nome_completo'] ?? $u['nome'] ?? 'Cliente') ?></td>
                        <td><?= htmlspecialchars($u['email'] ?? 'Sem Email') ?></td>
                        <td>
                            <span class="badge <?= $is_admin ? 'role-admin' : 'role-user' ?>">
                                <?= $is_admin ? 'ADMIN' : 'USER' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <?php if((int)$u['id'] === (int)$id_admin_atual): ?>
                                <span style="font-size: 0.8em; color: #aaa; font-style: italic;">Conta Atual</span>
                            <?php else: ?>
                                <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Tens a certeza que desejas ELIMINAR permanentemente o utilizador \'<?= htmlspecialchars($u['nome_completo'] ?? $u['email'], ENT_QUOTES) ?>\'?')">
                                    <input type="hidden" name="eliminar_utilizador_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="delete">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ========================================================================= -->
        <!-- GESTÃO DE PEDIDOS -->
        <!-- ========================================================================= -->
        <div class="header-actions" style="flex-direction: column; align-items: flex-start; gap: 10px;">
            <h2 style="color: #5d4037; margin: 0;">Gestão de Pedidos</h2>
            
            <div class="filter-group">
                <a href="?filtro_status=ativos" class="filter-btn <?= $filtro_atual === 'ativos' ? 'active' : 'inactive' ?>">Ativos</a>
                <a href="?filtro_status=pendente" class="filter-btn <?= $filtro_atual === 'pendente' ? 'active' : 'inactive' ?>">Pendentes</a>
                <a href="?filtro_status=pago" class="filter-btn <?= $filtro_atual === 'pago' ? 'active' : 'inactive' ?>">Pagos</a>
                <a href="?filtro_status=enviado" class="filter-btn <?= $filtro_atual === 'enviado' ? 'active' : 'inactive' ?>">Enviados</a>
                <a href="?filtro_status=concluido" class="filter-btn <?= $filtro_atual === 'concluido' ? 'active' : 'inactive' ?>">Concluídos</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nº Pedido</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Estado Atual</th>
                    <?php if ($filtro_atual !== 'concluido'): ?>
                        <th>Alterar Estado</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($filtro_atual === 'ativos') {
                    $sql_pedidos = "SELECT p.*, c.nome_completo FROM velas_pedidos p JOIN velas_clientes c ON p.id_utilizador = c.id WHERE LOWER(p.status_pagamento) != 'concluido' ORDER BY p.id DESC LIMIT 25";
                    $stmt_pedidos = $pdo->query($sql_pedidos);
                } else {
                    $sql_pedidos = "SELECT p.*, c.nome_completo FROM velas_pedidos p JOIN velas_clientes c ON p.id_utilizador = c.id WHERE LOWER(p.status_pagamento) = ? ORDER BY p.id DESC LIMIT 25";
                    $stmt_pedidos = $pdo->prepare($sql_pedidos);
                    $stmt_pedidos->execute([$filtro_atual]);
                }
                
                if ($stmt_pedidos->rowCount() == 0):
                    $total_colunas = ($filtro_atual === 'concluido') ? 5 : 6;
                ?>
                <tr>
                    <td colspan="<?= $total_colunas ?>" style="text-align: center; color: #888; padding: 25px;">Nenhum pedido encontrado com este estado de momento.</td>
                </tr>
                <?php else: ?>
                    <?php while($p = $stmt_pedidos->fetch()): 
                        $estado_limpo = strtolower(trim($p['status_pagamento']));
                    ?>
                    <tr>
                        <td><strong>#<?= $p['id'] ?></strong></td>
                        <td><?= htmlspecialchars($p['nome_completo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['data_encomenda'])) ?></td>
                        <td><strong><?= number_format($p['valor_total'], 2, ',', '.') ?>€</strong></td>
                        <td><span class="status-badge status-<?= $estado_limpo ?>"><?= htmlspecialchars($p['status_pagamento']) ?></span></td>
                        
                        <?php if ($filtro_atual !== 'concluido'): ?>
                        <td>
                            <form action="" method="POST" style="margin:0;">
                                <input type="hidden" name="id_encomenda" value="<?= $p['id'] ?>">
                                <input type="hidden" name="filter_retorno" value="<?= htmlspecialchars($filtro_atual) ?>">
                                <select name="novo_estado" class="status-select" onchange="this.form.submit()">
                                    <option value="pendente" <?= $estado_limpo == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="pago" <?= $estado_limpo == 'pago' ? 'selected' : '' ?>>Pago</option>
                                    <option value="enviado" <?= $estado_limpo == 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                    <option value="concluido" <?= $estado_limpo == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                </select>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ========================================================================= -->
        <!-- GESTÃO DE INVENTÁRIO -->
        <!-- ========================================================================= -->
        <div class="header-actions">
            <h2 style="color: #5d4037; margin: 0;">Gestão de Inventário</h2>
            <a href="add_candle.php" class="btn-add">+ Adicionar Nova Vela</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Aroma</th>
                    <th>Preço</th>
                    <th>Stock</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM velas_artigos WHERE pending_price = 0 ORDER BY id DESC");
                while($v = $stmt->fetch()): 
                    $stock_class = "";
                    if ($v['stock_atual'] <= 0) { $stock_class = "stock-out"; } 
                    elseif ($v['stock_atual'] <= 3) { $stock_class = "stock-low"; }
                ?>
                <tr>
                    <td>#<?= $v['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($v['nome_produto']) ?></strong>
                        <?php if($v['estilo'] === 'Personalizada' || ($v['created_by'] !== 'admin' && !empty($v['created_by']))): ?>
                            <span style="font-size:0.75em; background:#f3e5f5; color:#7b1fa2; padding:2px 6px; border-radius:4px; margin-left:5px; font-weight:bold;">Custom</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($v['aroma'] ?? 'Vários') ?></td>
                    <td><?= number_format($v['preco'], 2, ',', '.') ?>€</td>
                    <td><span class="badge <?= $stock_class ?>"><?= $v['stock_atual'] ?> un.</span></td>
                    <td class="actions">
                        <a href="edit_candle.php?id=<?= $v['id'] ?>" class="edit">Editar</a>
                        <a href="delete_candle.php?id=<?= $v['id'] ?>" class="delete" onclick="return confirm('Tem a certeza que deseja eliminar este artigo?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    function mostrarPainelAceitar(id) {
        document.getElementById('botoes-iniciais-' + id).style.display = 'none';
        document.getElementById('bloco-preco-' + id).style.display = 'block';
    }

    function cancelarDecisao(id) {
        document.getElementById('botoes-iniciais-' + id).style.display = 'block';
        document.getElementById('bloco-preco-' + id).style.display = 'none';
    }

    function rejeitarDireto(id, nomeVela, aroma, cor) {
        var mensagemConfirmacao = "Tens a certeza que desejas REJEITAR o pedido para a vela \"" + nomeVela + "\"?\n\nResumo do pedido:\n- Aroma: " + aroma + "\n- Cor: " + cor + "\n\nEsta ação irá apagá-la permanentemente.";
        if (confirm(mensagemConfirmacao)) {
            document.getElementById('form-rejeitar-' + id).submit();
        }
    }
    </script>
</body>
</html>