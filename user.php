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
$nome_cliente = $_SESSION['user_name'];

$pdo->exec("SET NAMES utf8mb4");

date_default_timezone_set('Europe/Lisbon');
$hora = date('H');
if ($hora >= 5 && $hora < 12) { $saudacao = "Bom Dia"; }
elseif ($hora >= 12 && $hora < 20) { $saudacao = "Boa Tarde"; }
else { $saudacao = "Boa Noite"; }

$stmt_notificar = $pdo->prepare("SELECT id, tracking_id FROM velas_pedidos WHERE id_utilizador = ? AND status_pagamento = 'concluido' LIMIT 1");
$stmt_notificar->execute([$id_utilizador]);
$encomenda_concluida = $stmt_notificar->fetch();

if ($encomenda_concluida) {
    $_SESSION['alerta_encomenda'] = "A tua encomenda com o código de rastreio <strong>" . htmlspecialchars($encomenda_concluida['tracking_id']) . "</strong> foi concluída e enviada com sucesso! 🌿";
    
    $stmt_delete = $pdo->prepare("DELETE FROM velas_pedidos WHERE id = ?");
    $stmt_delete->execute([$encomenda_concluida['id']]);
}

$stmt_check_custom = $pdo->prepare("SELECT COUNT(*) FROM velas_artigos WHERE estilo = 'Personalizada' AND created_by = ? AND preco > 0");
$stmt_check_custom->execute([$id_utilizador]);
$has_approved_candle = $stmt_check_custom->fetchColumn() > 0;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja de Velas Artesanais | Bougivon</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fdfaf6; margin: 0; color: #333; }
        .navbar { background: #fff; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .navbar h2 { margin: 0; color: #8d6e63; font-family: 'Georgia', serif; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #5d4037; font-weight: bold; font-size: 0.95em; transition: 0.3s; }
        .nav-links a:hover { color: #8d6e63; }
        .btn-cart { background: #8d6e63; color: white !important; padding: 8px 18px; border-radius: 20px; }
        .btn-cart:hover { background: #5d4037; }

        .welcome-banner { background: #8d6e63; color: white; padding: 40px 40px; text-align: left; }
        .welcome-banner h1 { margin: 0; font-family: 'Georgia', serif; font-size: 2em; }
        .welcome-banner p { margin-top: 5px; font-size: 1em; opacity: 0.9; }

        .container { padding: 30px 40px; max-width: 1200px; margin: auto; }
        
        .alert-box { max-width: 1200px; margin: 0 auto 20px auto; padding: 15px; border-radius: 10px; font-size: 0.95em; position: relative; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .alert-success { background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; }
        .alert-info { background: #e3f2fd; border: 1px solid #bbdefb; color: #0d47a1; }
        .close-alert-btn { background: none; border: none; color: inherit; font-size: 1.3em; font-weight: bold; cursor: pointer; padding: 0 5px; }

        .section-title { font-family: 'Georgia', serif; color: #5d4037; font-size: 1.6em; margin-bottom: 25px; border-bottom: 2px solid #e0d4cc; padding-bottom: 8px; }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; align-items: stretch; }
        
        .product-card { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.04); 
            border: 1px solid #f0e6df; 
            display: flex; 
            flex-direction: column; 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
            height: 100%;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(141,110,99,0.12); }
        
        .product-thumb-container { 
            width: 100%; 
            flex-shrink: 0; 
            overflow: hidden; 
            background: linear-gradient(135deg, #fdfbf7 0%, #f4eae1 100%); 
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            border-bottom: 1px solid #f0e6df;
        }

        @media (min-width: 769px) {
            .product-thumb-container {
                height: 230px;
            }
            .product-thumb-container img { 
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), filter 0.4s ease; 
            }
            .product-thumb-container:hover img { 
                transform: scale(1.08); 
                filter: brightness(0.95);
            }

            .product-thumb-container::after {
                content: "🔍 Ampliar";
                position: absolute;
                bottom: 10px;
                right: 10px;
                background: rgba(141, 110, 99, 0.85);
                color: white;
                font-size: 0.75em;
                padding: 4px 8px;
                border-radius: 12px;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                backdrop-filter: blur(2px);
            }
            .product-thumb-container:hover::after {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .container { padding: 15px; }
            .welcome-banner { padding: 25px 20px; }
            
            .product-thumb-container { 
                height: auto; 
                min-height: 200px; 
                padding: 12px; 
            }
            .product-thumb-container img { 
                width: 100%; 
                height: auto; 
                max-height: 280px; 
                object-fit: contain; 
            }
        }

        .product-details { 
            padding: 18px; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
        }
        .product-details h3 { margin: 0; font-family: 'Georgia', serif; color: #5d4037; font-size: 1.2em; }
        
        .product-meta { font-size: 0.88em; color: #555; line-height: 1.5; margin-top: 6px; }
        .product-description { color: #6d4c41; font-style: italic; margin-top: 6px; font-size: 0.9em; word-break: break-word; }

        .product-options-area { display: flex; flex-direction: column; gap: 8px; }
        .product-options-area:empty { display: none; }

        .product-checkbox-wrapper, .form-group { 
            margin: 0; 
            background: #fdf8f5; 
            padding: 6px 10px; 
            border-radius: 6px; 
            border: 1px dashed #d7ccc8;
            display: flex;
        }
        .product-checkbox-wrapper { align-items: center; }
        .product-checkbox-wrapper input[type="checkbox"] { 
            width: 15px; height: 15px; accent-color: #8d6e63; margin-right: 8px; cursor: pointer;
        }
        .product-checkbox-wrapper label { font-size: 0.85em; font-weight: bold; color: #5d4037; cursor: pointer; user-select: none; }

        .form-group { flex-direction: column; align-items: flex-start; gap: 2px; }
        .form-group label { font-size: 0.78em; font-weight: bold; color: #5d4037; }
        .form-group select { width: 100%; padding: 5px; border-radius: 4px; border: 1px solid #d7ccc8; background: #fff; color: #333; font-size: 0.85em; outline: none; }

        .product-action-area { 
            border-top: 1px solid #f0e6df; 
            padding-top: 12px; 
            margin-top: auto; 
        }
        
        .price-stock-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .product-price { font-size: 1.35em; font-weight: bold; color: #8d6e63; }
        
        .product-stock-status { font-size: 0.8em; font-weight: bold; }
        .stock-disponivel { color: #2e7d32; }
        .stock-limitado { color: #ef6c00; }
        .stock-esgotado { color: #c62828; }

        .form-add-cart { 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            background: #fdf6f0; 
            padding: 6px; 
            border-radius: 8px; 
            border: 1px solid #e0d4cc;
        }

        .input-qtd { 
            width: 50px; 
            height: 38px;
            padding: 2px 5px; 
            border: 1px solid #8d6e63; 
            border-radius: 6px; 
            text-align: center; 
            font-weight: bold; 
            font-size: 0.95em; 
            background: #fff;
            color: #5d4037;
            outline: none;
        }

        .btn-add-cart { 
            background: #8d6e63; 
            color: white; 
            border: none; 
            height: 38px;
            padding: 0 12px; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            flex-grow: 1; 
            transition: 0.2s; 
            font-size: 0.9em; 
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        .btn-add-cart:hover { background: #5d4037; }
        .btn-add-cart:disabled { background: #ccc; cursor: not-allowed; box-shadow: none; }

        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); display:none; justify-content:center; align-items:center; z-index:1000; }
        .modal-box { background: white; padding: 25px; border-radius: 12px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 5px 25px rgba(0,0,0,0.2); }
        .modal-box h3 { font-family: 'Georgia', serif; color: #2e7d32; margin-top: 0; font-size: 1.3em; }
        .modal-box p { color: #666; margin-bottom: 20px; line-height: 1.4; font-size: 0.95em; }
        .btn-modal-checkout { background: #28a745; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-right: 10px; display: inline-block; font-size: 0.9em; }
        .btn-modal-close { background: #eee; color: #333; padding: 10px 15px; border-radius: 6px; border: none; font-weight: bold; cursor: pointer; font-size: 0.9em; }

        .image-modal-overlay { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0, 0, 0, 0.85); 
            backdrop-filter: blur(5px);
            display: none; 
            justify-content: center; 
            align-items: center; 
            z-index: 2000; 
            cursor: zoom-out; 
            animation: fadeIn 0.25s ease;
        }
        .image-modal-content { 
            max-width: 88%; 
            max-height: 88%; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            object-fit: contain; 
            animation: scaleUp 0.25s ease;
        }
        .image-modal-close { 
            position: absolute; 
            top: 20px; 
            right: 30px; 
            color: white; 
            font-size: 35px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: transform 0.2s;
        }
        .image-modal-close:hover { transform: scale(1.2); }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { transform: scale(0.9); } to { transform: scale(1); } }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Bougivon Loja</h2>
        <div class="nav-links">
            <a href="profile.php">👤 O Meu Perfil</a>
            <a href="add_personalized_candle.php" style="color: #c62828;">✨ Personalizar Vela</a>
            <a href="cart.php" class="btn-cart">🛒 O meu Carrinho</a>
            <a href="logout.php" style="color: #dc3545;">Sair</a>
        </div>
    </div>

    <div class="welcome-banner">
        <h1><?= $saudacao ?>, <?= htmlspecialchars($nome_cliente) ?>! 🕯️</h1>
        <p>Encontra o aroma e o aconchego perfeito para o teu lar com as nossas velas artesanais.</p>
    </div>

    <div class="container">

        <?php if (isset($_SESSION['alerta_encomenda'])): ?>
            <div class="alert-box alert-success" id="container-alerta">
                <div>🌿 <?= $_SESSION['alerta_encomenda'] ?></div>
                <button class="close-alert-btn" onclick="fecharNotificacao()">×</button>
            </div>
            <?php unset($_SESSION['alerta_encomenda']); ?>
        <?php endif; ?>

        <?php if ($has_approved_candle): ?>
            <div class="alert-box alert-info" id="container-personalized">
                <div>✨ <strong>Boas notícias!</strong> O administrador já avaliou o teu pedido de vela personalizada. Já a podes adicionar ao carrinho em baixo!</div>
                <button class="close-alert-btn" onclick="fecharNotificacaoPersonalized()">×</button>
            </div>
        <?php endif; ?>

        <div class="section-title">As Nossas Velas Disponíveis</div>

        <div class="products-grid">
            <?php
            $ids_sem_extras = [
                13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 43, 44, 45, 46, 47, 48, 49,
                50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 92, 93
            ];

            $ids_com_cor_aroma = [
                13, 14, 15, 16, 17, 18, 19, 25, 26, 27, 28, 29, 30, 41, 42,
                60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74,
                75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85
            ];

            $stmt = $pdo->prepare("SELECT * FROM velas_artigos WHERE estilo != 'Personalizada' OR (estilo = 'Personalizada' AND created_by = ? AND preco > 0) ORDER BY id DESC");
            $stmt->execute([$id_utilizador]);
            
            while($v = $stmt->fetch()):
                $nome_imagem = (!empty($v['imagem'])) ? strtolower($v['imagem']) : 'default_candle.jpg';
                $preco_base = (float)$v['preco'];
                $id_produto = (int)$v['id'];
                $descricao_completa = $v['descricao'] ?? '';

                $qtd_stock = (int)$v['stock_atual'];
                if ($qtd_stock <= 0) {
                    $stock_label = "❌ Esgotado";
                    $stock_style = "stock-esgotado";
                    $disabled = "disabled";
                } elseif ($qtd_stock <= 3) {
                    $stock_label = "⚠️ Últimas {$qtd_stock} un!";
                    $stock_style = "stock-limitado";
                    $disabled = "";
                } else {
                    $stock_label = "✅ Disponível: {$qtd_stock} un.";
                    $stock_style = "stock-disponivel";
                    $disabled = "";
                }

                $permite_cor_aroma = (in_array($id_produto, $ids_com_cor_aroma) || str_contains($descricao_completa, 'cor e aroma personalizáveis') || str_contains($descricao_completa, 'Personalizável -> cor e aroma'));
                $permite_tronco = str_contains($descricao_completa, 'tronco madeira');
                $permite_azevinho = str_contains($descricao_completa, 'azevinho');

                $aroma_verificar = mb_strtolower($v['aroma']);
                $elemento_nome = "elemento decorativo"; 

                if (str_contains($aroma_verificar, 'baunilha') || str_contains($aroma_verificar, 'lavanda')) { $elemento_nome = "flor"; }
                elseif (str_contains($aroma_verificar, 'framboesa')) { $elemento_nome = "framboesa"; }
                elseif (str_contains($aroma_verificar, 'jasmim') || str_contains($aroma_verificar, 'bamboo')) { $elemento_nome = "bamboo"; }
                elseif (str_contains($aroma_verificar, 'manga') || str_contains($aroma_verificar, 'papaya')) { $elemento_nome = "manga"; }
                elseif (str_contains($aroma_verificar, 'coco')) { $elemento_nome = "palmeira"; }
                elseif (str_contains($aroma_verificar, 'morango')) { $elemento_nome = "morango"; }
                elseif (str_contains($aroma_verificar, 'eucalipto')) { $elemento_nome = "folhas"; }
                elseif (str_contains($aroma_verificar, 'citronela')) { $elemento_nome = "mosquito"; }
                elseif (str_contains($aroma_verificar, 'oceano')) { $elemento_nome = "conchas"; }
            ?>
            <div class="product-card">
                <div class="product-thumb-container" onclick="abrirImagemZoom('imagens/<?= htmlspecialchars($nome_imagem) ?>')">
                    <img src="imagens/<?= htmlspecialchars($nome_imagem) ?>" alt="<?= htmlspecialchars($v['nome_produto']) ?>">
                </div>
                
                <div class="product-details">
                    <div>
                        <h3><?= htmlspecialchars($v['nome_produto']) ?></h3>
                        <div class="product-meta">
                            <strong>Aroma:</strong> <?= htmlspecialchars($v['aroma']) ?> | <strong>Cor:</strong> <?= htmlspecialchars($v['cor']) ?><br>
                            <strong>Estilo:</strong> <?= htmlspecialchars($v['estilo']) ?>
                            <?php if(!empty($descricao_completa)): ?>
                                <div class="product-description"><?= htmlspecialchars($descricao_completa) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-options-area">
                        <?php if ($id_produto === 92): ?>
                            <div class="form-group">
                                <select id="duo-vela1-<?= $id_produto ?>" name="duo_vela1">
                                    <option value="Gingerbread Cookie">🍪 Vela 1: Gingerbread Cookie</option>
                                    <option value="Anjo Pequeno">👼 Vela 1: Anjo Pequeno</option>
                                    <option value="Flocos de Neve">❄️ Vela 1: Flocos de Neve</option>
                                    <option value="Pai Natal Pequeno">🎅 Vela 1: Pai Natal Pequeno</option>
                                    <option value="Gnomo de Natal">🍄 Vela 1: Gnomo de Natal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="duo-vela2-<?= $id_produto ?>" name="duo_vela2">
                                    <option value="Gingerbread Cookie">🍪 Vela 2: Gingerbread Cookie</option>
                                    <option value="Anjo Pequeno">👼 Vela 2: Anjo Pequeno</option>
                                    <option value="Flocos de Neve">❄️ Vela 2: Flocos de Neve</option>
                                    <option value="Pai Natal Pequeno">🎅 Vela 2: Pai Natal Pequeno</option>
                                    <option value="Gnomo de Natal">🍄 Vela 2: Gnomo de Natal</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($permite_cor_aroma): ?>
                            <div class="form-group">
                                <select id="aroma-<?= $id_produto ?>" name="aroma">
                                    <option value="Baunilha">🌼 Aroma: Baunilha</option>
                                    <option value="Chocolate">🍫 Aroma: Chocolate</option>
                                    <option value="Morango">🍓 Aroma: Morango</option>
                                    <option value="Framboesa">🍓 Aroma: Framboesa</option>
                                    <option value="Eucalipto">🌿 Aroma: Eucalipto</option>
                                    <option value="Coco">🥥 Aroma: Coco</option>
                                    <option value="Lavanda">🪻 Aroma: Lavanda</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="cor-<?= $id_produto ?>" name="cor">
                                    <option value="Branco">⚪ Cor: Branco Natural</option>
                                    <option value="Bege">🥟 Cor: Bege Creme</option>
                                    <option value="Rosa">🌸 Cor: Rosa Pastel</option>
                                    <option value="Azul">🩵 Cor: Azul Celeste</option>
                                    <option value="Verde">🌿 Cor: Verde Menta</option>
                                    <option value="Vermelho">❤️ Cor: Vermelho</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($id_produto === 14): ?>
                            <div class="product-checkbox-wrapper">
                                <input type="checkbox" id="bubble-grande-<?= $id_produto ?>" class="check-bubble-tamanho" data-preco="5.00" onchange="calcularPrecoTotal(<?= $id_produto ?>, <?= $preco_base ?>)">
                                <label for="bubble-grande-<?= $id_produto ?>">Tamanho Grande (+5,00€)</label>
                            </div>
                            <div class="product-checkbox-wrapper">
                                <input type="checkbox" id="bubble-pequeno-<?= $id_produto ?>" class="check-bubble-tamanho" data-preco="2.50" onchange="calcularPrecoTotal(<?= $id_produto ?>, <?= $preco_base ?>)" checked>
                                <label for="bubble-pequeno-<?= $id_produto ?>">Tamanho Pequeno (+2,50€)</label>
                            </div>
                        <?php endif; ?>

                        <?php if ($permite_tronco): ?>
                            <div class="product-checkbox-wrapper">
                                <input type="checkbox" id="extra-tronco-<?= $id_produto ?>" data-preco="2.00" onchange="calcularPrecoTotal(<?= $id_produto ?>, <?= $preco_base ?>)">
                                <label for="extra-tronco-<?= $id_produto ?>">🪵 Tronco de Madeira (+2,00€)</label>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($permite_azevinho): ?>
                            <div class="product-checkbox-wrapper">
                                <input type="checkbox" id="extra-azevinho-<?= $id_produto ?>" data-preco="0.80" onchange="calcularPrecoTotal(<?= $id_produto ?>, <?= $preco_base ?>)">
                                <label for="extra-azevinho-<?= $id_produto ?>">🌿 Azevinho (+0,80€)</label>
                            </div>
                        <?php endif; ?>

                        <?php if (!$permite_tronco && !$permite_azevinho && !in_array($id_produto,$ids_sem_extras)): ?>
                            <div class="product-checkbox-wrapper">
                                <input type="checkbox" id="flor-<?= $id_produto ?>" data-preco="1.00" onchange="calcularPrecoTotal(<?= $id_produto ?>, <?= $preco_base ?>)">
                                <label for="flor-<?= $id_produto ?>">Com <?= $elemento_nome ?> (+1,00€)</label>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-action-area">
                        <div class="price-stock-row">
                            <div class="product-price" id="preco-exibido-<?= $id_produto ?>"><?= number_format(($id_produto === 14 ? 2.50 :$preco_base), 2, ',', '.') ?>€</div>
                            <div class="product-stock-status <?= $stock_style ?>"><?= $stock_label ?></div>
                        </div>

                        <form class="form-add-cart">
                            <input type="hidden" name="id" value="<?= $id_produto ?>">
                            <input type="hidden" name="permite_cor_aroma" value="<?= $permite_cor_aroma ? '1' : '0' ?>">
                            <input type="number" name="qtd" class="input-qtd" value="<?= ($qtd_stock > 0 ? 1 : 0) ?>" min="1" max="<?= max(1, $qtd_stock) ?>" <?= $disabled ?> title="Quantidade">
                            <button type="submit" class="btn-add-cart" <?= $disabled ?>>🛒 Adicionar</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="modal-overlay" id="cart-modal">
        <div class="modal-box">
            <h3>Vela Adicionada! 🌿</h3>
            <p>O artigo foi colocado com sucesso no teu carrinho de compras.</p>
            <a href="cart.php" class="btn-modal-checkout">Ir para o Carrinho</a>
            <button class="btn-modal-close" onclick="fecharModal()">Continuar</button>
        </div>
    </div>

    <div class="image-modal-overlay" id="image-modal" onclick="fecharImagemZoom()">
        <span class="image-modal-close">&times;</span>
        <img class="image-modal-content" id="image-modal-img" src="" alt="Imagem Ampliada">
    </div>

    <script>
    function fecharNotificacao() { document.getElementById('container-alerta').style.display = 'none'; }
    function fecharNotificacaoPersonalized() { document.getElementById('container-personalized').style.display = 'none'; }
    function fecharModal() { document.getElementById('cart-modal').style.display = 'none'; }

    function abrirImagemZoom(src) {
        document.getElementById('image-modal-img').src = src;
        document.getElementById('image-modal').style.display = 'flex';
    }

    function fecharImagemZoom() {
        document.getElementById('image-modal').style.display = 'none';
    }

    function calcularPrecoTotal(idVela, precoBase) {
        let precoFinal = precoBase;

        if (parseInt(idVela) === 14) {
            precoFinal = 0.00;
            let checkGrande = document.getElementById('bubble-grande-' + idVela);
            let checkPequeno = document.getElementById('bubble-pequeno-' + idVela);
            if (checkGrande && checkGrande.checked) precoFinal += parseFloat(checkGrande.getAttribute('data-preco'));
            if (checkPequeno && checkPequeno.checked) precoFinal += parseFloat(checkPequeno.getAttribute('data-preco'));
        } else {
            let checkFlor = document.getElementById('flor-' + idVela);
            if (checkFlor && checkFlor.checked) precoFinal += parseFloat(checkFlor.getAttribute('data-preco'));
        }

        let checkTronco = document.getElementById('extra-tronco-' + idVela);
        let checkAzevinho = document.getElementById('extra-azevinho-' + idVela);
        if (checkTronco && checkTronco.checked) precoFinal += parseFloat(checkTronco.getAttribute('data-preco'));
        if (checkAzevinho && checkAzevinho.checked) precoFinal += parseFloat(checkAzevinho.getAttribute('data-preco'));

        document.getElementById('preco-exibido-' + idVela).innerText = precoFinal.toFixed(2).replace('.', ',') + '€';
    }

    document.querySelectorAll('.form-add-cart').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = this.querySelector('input[name="id"]').value;
            const permiteCorAroma = this.querySelector('input[name="permite_cor_aroma"]').value === '1';
            const qtdInput = this.querySelector('input[name="qtd"]');
            const qtd = qtdInput ? qtdInput.value : 1;
            
            let opcaoPersonalizada = 'simples';
            let sufixoExtras = '';

            const checkTronco = document.getElementById('extra-tronco-' + id);
            const checkAzevinho = document.getElementById('extra-azevinho-' + id);
            if (checkTronco && checkTronco.checked) sufixoExtras += '_tronco:sim';
            if (checkAzevinho && checkAzevinho.checked) sufixoExtras += '_azevinho:sim';

            if (parseInt(id) === 92) {
                const vela1 = document.getElementById('duo-vela1-' + id).value;
                const vela2 = document.getElementById('duo-vela2-' + id).value;
                opcaoPersonalizada = `caixa_duo_vela1:${vela1}_vela2:${vela2}`;
            } else if (parseInt(id) === 14) {
                const corElement = document.getElementById('cor-' + id);
                const aromaElement = document.getElementById('aroma-' + id);
                const corSelecionada = corElement ? corElement.value : 'Padrão';
                const aromaSelecionado = aromaElement ? aromaElement.value : 'Padrão';
                const checkGrande = document.getElementById('bubble-grande-' + id);
                const checkPequeno = document.getElementById('bubble-pequeno-' + id);
                let tamanhos = [];
                if (checkGrande && checkGrande.checked) tamanhos.push('grande');
                if (checkPequeno && checkPequeno.checked) tamanhos.push('pequeno');
                if (tamanhos.length === 0) { alert('Seleciona pelo menos um tamanho!'); return; }
                opcaoPersonalizada = `bubble_cor:${corSelecionada}_aroma:${aromaSelecionado}_tamanho:${tamanhos.join('_')}${sufixoExtras}`;
            } else if (permiteCorAroma) {
                const corElement = document.getElementById('cor-' + id);
                const aromaElement = document.getElementById('aroma-' + id);
                const corSelecionada = corElement ? corElement.value : 'Padrão';
                const aromaSelecionado = aromaElement ? aromaElement.value : 'Padrão';
                opcaoPersonalizada = `custom_cor:${corSelecionada}_aroma:${aromaSelecionado}${sufixoExtras}`;
            } else {
                const checkboxFlor = document.getElementById('flor-' + id);
                opcaoPersonalizada = (checkboxFlor && checkboxFlor.checked) ? 'decorada' : 'simples';
                if (sufixoExtras !== '') opcaoPersonalizada += sufixoExtras;
            }
            
            // Criação do corpo do pedido seguro usando FormData (POST)
            const formData = new FormData();
            formData.append('id', id);
            formData.append('qtd', qtd);
            formData.append('opcao', opcaoPersonalizada);

            fetch('add_cart.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById('cart-modal').style.display = 'flex';
                } else {
                    alert('Erro ao adicionar ao carrinho. Tenta novamente.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro de ligação ao servidor.');
            });
        });
    });
    </script>
</body>
</html>