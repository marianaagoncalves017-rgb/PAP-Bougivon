<?php
// Ativar a exibição de erros ocultos do servidor
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o utilizador está logado (ajuste 'user_id' conforme o nome da sua variável de sessão)
$is_logged = isset($_SESSION['user_id']) || isset($_SESSION['user_name']);

// Consulta todas as velas para mostrar no catálogo da página principal
$stmt = $pdo->query("SELECT * FROM velas_artigos ORDER BY nome_produto ASC");
$velas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bougivon | Velas Artesanais de Soja</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background-color: #fdfaf6; 
            margin: 0; 
            padding: 0; 
            color: #333; 
        }
        
        /* 1. PARTE DE INICIAR SESSÃO (Navbar) */
        .navbar { 
            background: #fff; 
            padding: 15px 40px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .navbar h2 { 
            margin: 0; 
            color: #8d6e63; 
            font-family: 'Georgia', serif; 
        }
        .nav-links a { 
            text-decoration: none; 
            color: #8d6e63; 
            font-weight: bold; 
            margin-left: 20px; 
        }
        .nav-links a.btn-login {
            background: #8d6e63;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .nav-links a.btn-logout {
            color: #c62828;
        }

        /* 2. PARTE DAS REDES SOCIAIS (Hero) */
        .hero { 
            background: #8d6e63; 
            color: white; 
            padding: 50px 20px; 
            text-align: center; 
        }
        .hero h1 { 
            margin: 0; 
            font-family: 'Georgia', serif; 
            font-size: 2.8em; 
            letter-spacing: 2px;
        }
        .hero p { 
            font-size: 1.2em; 
            margin-top: 10px; 
            color: #f5ebe6; 
        }
        nav {
            margin-top: 25px;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 12px;
            font-weight: bold;
            background: rgba(255,255,255,0.18);
            padding: 10px 22px;
            border-radius: 25px;
            display: inline-block;
            transition: 0.3s;
            letter-spacing: 1px;
            font-size: 0.85em;
            cursor: pointer;
        }
        nav a:hover {
            background: white;
            color: #8d6e63;
        }

        /* 3. O COISO DE 100% CERA DE SOJA ETC... (Features) */
        .features {
            display: flex;
            justify-content: space-around;
            max-width: 1100px;
            margin: -30px auto 40px auto;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
            z-index: 10;
        }
        .features-item { flex: 1; padding: 10px; }
        .features-item span { font-size: 2em; display: block; margin-bottom: 5px; }
        .features-item h4 { margin: 5px 0; color: #5d4037; font-family: 'Georgia', serif; }
        .features-item p { margin: 0; font-size: 0.85em; color: #777; }

        /* 4. AS VELAS (Catálogo) */
        .container { 
            max-width: 1100px; 
            margin: 50px auto; 
            padding: 0 20px;
        }
        .container h2 {
            color: #5d4037;
            font-family: 'Georgia', serif;
            text-align: center;
            margin-bottom: 10px;
            font-size: 2em;
        }
        .subtitle {
            text-align: center;
            color: #888;
            margin-bottom: 40px;
            font-style: italic;
        }
        .products-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 30px; 
        }
        .candle-card { 
            background: white; 
            padding: 25px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); 
            text-align: center; 
            border: 1px solid #f3ebe6; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            transition: 0.3s;
            position: relative;
        }
        .candle-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(141, 110, 99, 0.1); 
        }
        .candle-image-wrapper { 
            width: 100%;
            height: 220px; 
            margin-bottom: 15px; 
            overflow: hidden;
            border-radius: 10px;
            background-color: #fcfbfa;
        }
        .candle-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover; 
            transition: 0.5s ease;
        }
        .candle-card:hover .candle-image-wrapper img {
            transform: scale(1.05); 
        }
        .candle-title { font-family: 'Georgia', serif; font-size: 1.3em; color: #5d4037; margin: 10px 0 5px 0; }
        
        /* Formatação em lista para as especificações visuais e técnicas */
        .candle-meta { 
            font-size: 0.85em; 
            color: #6d4c41; 
            margin-bottom: 15px; 
            line-height: 1.6; 
            text-align: left;
            background: #faf6f2;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px dashed #e0d4cc;
        }
        
        .candle-price { font-size: 1.4em; font-weight: bold; color: #8d6e63; margin-bottom: 15px; display: block; }
        .stock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.75em;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: bold;
            z-index: 10;
        }
        .badge-in { background: #e8f5e9; color: #2e7d32; }
        .badge-low { background: #fff3e0; color: #ef6c00; }
        .badge-out { background: #ffebee; color: #c62828; }

        /* Estilos dos Menus de Opções */
        .option-group {
            margin-bottom: 12px;
            text-align: left;
        }
        .option-group label {
            display: block;
            font-size: 0.8em;
            color: #7c6c64;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .option-select {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #d1c7bd;
            background: #faf8f6;
            color: #5c4d46;
            font-size: 0.9em;
            outline: none;
            box-sizing: border-box;
        }
        .qty-input {
            width: 65px;
            padding: 7px;
            border-radius: 8px;
            border: 1px solid #d1c7bd;
            text-align: center;
            font-size: 0.9em;
            color: #5c4d46;
        }
        .btn-add-cart {
            width: 100%;
            background: #8d6e63;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 0.95em;
            margin-top: 10px;
        }
        .btn-add-cart:hover {
            background: #5d4037;
        }
        .btn-add-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .login-alert-box {
            font-size: 0.85em;
            color: #c62828;
            background: #ffebee;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            border: 1px solid #ffcdd2;
            margin-top: 10px;
        }

        /* 5. A MAGIA POR TRÁS DA BOUGIVON COM A FOTO EMBAIXO TOTALMENTE VISÍVEL */
        .about-block {
            max-width: 800px;
            margin: 60px auto;
            padding: 0 20px;
            text-align: center;
        }
        .about-block h2 {
            font-family: 'Georgia', serif;
            color: #5d4037;
            font-size: 2.2em;
            margin-bottom: 20px;
        }
        .about-block p {
            color: #555;
            line-height: 1.8;
            font-size: 1.05em;
            margin-bottom: 30px;
        }
        .about-block .highlight {
            color: #8d6e63;
            font-weight: bold;
        }
        .about-block-image {
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(141, 110, 99, 0.12);
            background: transparent;
        }
        .about-block-image img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain; /* Garante que a imagem nunca é cortada */
        }

        /* 6. FINALMENTE: RODAPÉ */
        footer {
            background: #fff;
            color: #777;
            text-align: center;
            padding: 30px 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            margin-top: 60px;
        }
        footer p { margin: 5px 0; }

        /* Responsividade */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; flex-direction: column; gap: 10px; text-align: center; }
            .nav-links a { margin: 0 10px; font-size: 0.9em; }
            .hero h1 { font-size: 2em; }
            .features { flex-direction: column; margin: -20px 15px 30px 15px; gap: 15px; }
            .candle-image-wrapper { height: 180px; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Bougivon</h2>
        <div class="nav-links">
            <?php if ($is_logged): ?>
                <a href="carrinho.php">🛒 O meu Carrinho</a>
                <a href="logout.php" class="btn-logout">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Entrar / Iniciar Sessão</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="hero">
        <h1>Bougivon</h1>
        <p>Velas Artesanais produzidas com cera de soja e lembranças personalizadas 🌿</p>
        
        <nav>
            <a href="https://www.instagram.com/bougivon" target="_blank">INSTAGRAM</a>
            <a href="https://www.tiktok.com/@bougivon" target="_blank">TIKTOK</a>
            <a href="https://www.facebook.com/bougivon" target="_blank">FACEBOOK</a>
        </nav>
    </div>

    <div class="features">
        <div class="features-item">
            <span>🌱</span>
            <h4>100% Cera de Soja</h4>
            <p>Queima limpa e amiga do ambiente.</p>
        </div>
        <div class="features-item">
            <span>✨</span>
            <h4>Feito à Mão</h4>
            <p>Vertidas artesanalmente com amor.</p>
        </div>
        <div class="features-item">
            <span>🐰</span>
            <h4>Cruelty Free</h4>
            <p>Sem qualquer teste em animais.</p>
        </div>
    </div>

    <div class="container">
        <h2>As Nossas Velas Artesanais</h2>
        <p class="subtitle">Escolha o aroma perfeito para iluminar o seu dia</p>

        <div class="products-grid">
            <?php if (empty($velas)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: #888; padding: 40px;">De momento não existem velas disponíveis no catálogo.</p>
            <?php else: ?>
                <?php foreach ($velas as $v): 
                    $campo_estilo = $v['estilo_festividade'] ?? $v['estilo'] ?? 'Simples';
                    
                    // CORREÇÃO: strtolower() força o nome a ficar em minúsculas prevenindo conflito de maiúsculas/minúsculas do Linux
                    $nome_imagem = (!empty($v['imagem'])) ? strtolower($v['imagem']) : 'default_candle.jpg';
                    
                    $is_esgotado = (isset($v['stock_atual']) && $v['stock_atual'] <= 0);
                ?>
                    <div class="candle-card">
                        
                        <?php if (isset($v['stock_atual'])): ?>
                            <?php if ($v['stock_atual'] <= 0): ?>
                                <span class="stock-badge badge-out">Esgotado</span>
                            <?php elseif ($v['stock_atual'] <= 3): ?>
                                <span class="stock-badge badge-low">Últimas <?= $v['stock_atual'] ?>!</span>
                            <?php else: ?>
                                <span class="stock-badge badge-in">Disponível</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div style="margin-bottom: 15px;">
                            <div class="candle-image-wrapper">
                                <img src="imagens/<?= htmlspecialchars($nome_imagem) ?>" alt="<?= htmlspecialchars($v['nome_produto']) ?>">
                            </div>

                            <h3 class="candle-title"><?= htmlspecialchars($v['nome_produto']) ?></h3>
                            
                            <div class="candle-meta">
                                <strong>✨ Estilo:</strong> <?= htmlspecialchars($campo_estilo) ?><br>
                                <strong>🎨 Cor:</strong> <?= htmlspecialchars($v['cor'] ?? 'Natural') ?><br>
                                <strong>👃 Aroma:</strong> <?= htmlspecialchars($v['aroma'] ?? 'Baunilha') ?><br>
                                <?php if(!empty($v['peso'])): ?>
                                    <strong>⚖️ Peso:</strong> <?= htmlspecialchars($v['peso']) ?>g<br>
                                <?php endif; ?>
                                <?php if(!empty($v['altura']) || !empty($v['largura'])): ?>
                                    <strong>📏 Dimensões:</strong> 
                                    <?= !empty($v['altura']) ? number_format($v['altura'], 1, ',', '.') . ' cm (Alt.)' : '' ?>
                                    <?= !empty($v['largura']) ? ' x ' . number_format($v['largura'], 1, ',', '.') . ' cm (Larg.)' : '' ?><br>
                                <?php endif; ?>
                                <strong>📦 Stock:</strong> 
                                <?php if (isset($v['stock_atual'])): ?>
                                    <?php if ($v['stock_atual'] <= 0): ?>
                                        <span style="color: #c62828; font-weight: bold;">Esgotado</span>
                                    <?php else: ?>
                                        <span><?= $v['stock_atual'] ?> un.</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #888;">Não definido</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($is_logged): ?>
                            <form action="carrinho.php" method="POST">
                                <input type="hidden" name="produto_id" value="<?= $v['id'] ?>">

                                <div class="option-group">
                                    <label for="aroma-<?= $v['id'] ?>">Mudar Aroma (Opcional):</label>
                                    <select name="aroma" id="aroma-<?= $v['id'] ?>" class="option-select" required <?= $is_esgotado ? 'disabled' : '' ?>>
                                        <option value="<?= htmlspecialchars($v['aroma']) ?>" selected>Original (<?= htmlspecialchars($v['aroma'] ?? 'Baunilha') ?>)</option>
                                        <option value="Baunilha">Baunilha 🌼</option>
                                        <option value="Chocolate">Chocolate 🍫</option>
                                        <option value="Coco">Coco 🥥</option>
                                        <option value="Eucalipto">Eucalipto 🌿</option>
                                        <option value="Framboesa">Framboesa 🍓</option>
                                        <option value="Jasmim e Bamboo">Jasmim e Bamboo 🎋</option>
                                    </select>
                                </div>

                                <div class="option-group">
                                    <label for="cor-<?= $v['id'] ?>">Mudar Cor (Opcional):</label>
                                    <select name="cor" id="cor-<?= $v['id'] ?>" class="option-select" required <?= $is_esgotado ? 'disabled' : '' ?>>
                                        <option value="<?= htmlspecialchars($v['cor']) ?>" selected>Original (<?= htmlspecialchars($v['cor'] ?? 'Natural') ?>)</option>
                                        <option value="Branco / Natural">Branco / Natural 🤍</option>
                                        <option value="Amarelo">Amarelo 💛</option>
                                        <option value="Castanho">Castanho 🤎</option>
                                        <option value="Verde">Verde 💚</option>
                                        <option value="Rosa">Rosa 💗</option>
                                    </select>
                                </div>

                                <div class="option-group" style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px;">
                                    <label for="qtd-<?= $v['id'] ?>" style="margin-bottom: 0;">Qtd:</label>
                                    <input type="number" name="quantidade" id="qtd-<?= $v['id'] ?>" class="qty-input" value="1" min="1" max="<?= $v['stock_atual'] ?? 10 ?>" required <?= $is_esgotado ? 'disabled' : '' ?>>
                                </div>

                                <div style="margin-top: 15px;">
                                    <span class="candle-price"><?= number_format($v['preco'], 2, ',', '.') ?>€</span>
                                    <button type="submit" name="adicionar_carrinho" class="btn-add-cart" <?= $is_esgotado ? 'disabled' : '' ?>>
                                        <?= $is_esgotado ? '❌ Esgotado' : '🛒 Adicionar ao Carrinho' ?>
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div>
                                <span class="candle-price"><?= number_format($v['preco'], 2, ',', '.') ?>€</span>
                                <div class="login-alert-box">
                                    🔒 Inicie sessão para personalizar e comprar
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="about-block">
        <h2>A Magia por trás da Bougivon</h2>
        <p>A Bougivon nasceu da paixão por transformar ambientes e criar momentos de puro aconchego através de produtos sustentáveis. Cada uma das nossas velas é desenhada e produzida <span class="highlight">artesanalmente em Portugal</span>.<br><br>
        Utilizamos exclusivamente cera de soja 100% natural, pavios de algodão premium e fragrâncias delicadamente selecionadas. Livre de toxinas e parafinas, garantimos uma queima limpa, duradoura e totalmente amiga do planeta. 🌿✨</p>
        
        <div class="about-block-image">
            <img src="imagens/iara.png" alt="Produção Artesanal Bougivon">
        </div>
    </div>

    <footer>
        <p><strong>&copy; 2026 Bougivon.</strong> Todos os direitos reservados.</p>
        <p style="font-size: 0.85em;">Produzido com orgulho em Portugal 🇵🇹 | Velas de Cera de Soja Ecológicas.</p>
    </footer>

</body>
</html>