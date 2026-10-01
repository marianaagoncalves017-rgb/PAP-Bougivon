<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$mensagem = "";

// Pré-preenchimento se chamado com ?id=123
$pref_nome = '';
$pref_aroma = '';
$pref_cor = '';
$pref_estilo = 'Simples';
$pref_stock = 1;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    try {
        $stmt_prod = $pdo->prepare("SELECT nome_produto, aroma, cor, estilo, stock_atual FROM velas_artigos WHERE id = ? LIMIT 1");
        $stmt_prod->execute([$_GET['id']]);
        $prod = $stmt_prod->fetch(PDO::FETCH_ASSOC);
        if ($prod) {
            $pref_nome = $prod['nome_produto'];
            $pref_aroma = $prod['aroma'];
            $pref_cor = $prod['cor'];
            $pref_estilo = $prod['estilo'] ?? 'Simples';
            $pref_stock = max(1, (int)$prod['stock_atual']);
        }
    } catch (PDOException $e) {
        // Ignora o erro e usa os valores por defeito
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_produto = trim($_POST['nome_produto'] ?? 'Vela Personalizada');
    $aroma = trim($_POST['aroma'] ?? 'Sem Aroma');
    $cor = trim($_POST['cor'] ?? 'Branco');
    $estilo = trim($_POST['estilo'] ?? 'Simples');
    $mensagem_personalizada = trim($_POST['mensagem_personalizada'] ?? '');
    $quantidade = (int)($_POST['stock_atual'] ?? 1);

    if (empty($nome_produto)) {
        $mensagem = "<div class='alert alert-danger'>❌ Por favor, dê um nome ou título à sua personalização.</div>";
    } else {
        try {
            // Nota: Se for o utilizador a pedir, guardamos o estilo escolhido e preço 0.00 para o admin avaliar
            $stmt = $pdo->prepare("INSERT INTO velas_artigos (nome_produto, aroma, cor, estilo, preco, stock_atual, created_by) VALUES (?, ?, ?, ?, 0.00, ?, ?)");
            $stmt->execute([$nome_produto, $aroma, $cor, $estilo, $quantidade, $user_id]);

            $mensagem = "
            <div class='alert alert-success' style='text-align:center;'>
                🎉 <strong>Pedido enviado com sucesso!</strong><br>
                O administrador vai analisar o teu pedido, definir o preço ideal e, assim que for aprovado, a vela aparecerá no teu Catálogo para adicionares ao carrinho! 🌿
            </div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alert alert-danger'>❌ Erro ao submeter o pedido: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Personalizar Vela | Bougivon</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fdfaf6; margin: 0; color: #333; }
        .navbar { background: #fff; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; color: #8d6e63; font-family: 'Georgia', serif; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #5d4037; font-weight: bold; font-size: 0.95em; transition: 0.3s; }
        .nav-links a:hover { color: #8d6e63; }
        
        .container { max-width: 650px; margin: 40px auto; background: white; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f0e6df; }
        h1 { font-family: 'Georgia', serif; color: #5d4037; text-align: center; margin-top: 0; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #888; font-size: 0.95em; margin-bottom: 30px; line-height: 1.4; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #5d4037; font-size: 0.95em; }
        input[type="text"], input[type="number"], select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 1em; box-sizing: border-box; background: #fff; transition: border 0.3s; }
        input:focus, select:focus, textarea:focus { border-color: #8d6e63; outline: none; box-shadow: 0 0 0 3px rgba(141,110,99,0.1); }
        
        .btn-submit { 
            background: #8d6e63; 
            color: white; 
            padding: 14px 30px; 
            border: none; 
            border-radius: 8px; 
            font-weight: bold; 
            font-size: 1em; 
            cursor: pointer; 
            transition: 0.3s; 
            
            /* CENTRADO CORRETAMENTE */
            display: block;
            margin: 20px auto 10px auto; 
        }
        .btn-submit:hover { background: #6d4c41; }
        
        .btn-back { 
            display: inline-block; 
            text-align: center; 
            color: #8d6e63; 
            text-decoration: none; 
            font-weight: bold; 
            margin-top: 20px; 
            font-size: 0.9em; 
        }
        .btn-back:hover { text-decoration: underline; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-size: 0.95em; line-height: 1.5; }
        .alert-success { background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; }
        .alert-danger { background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Bougivon Loja</h2>
        <div class="nav-links">
            <a href="profile.php">👤 O Meu Perfil</a>
            <a href="user.php">Catálogo</a>
            <a href="cart.php">🛒 O meu Carrinho</a>
            <a href="logout.php" style="color: #dc3545;">Sair</a>
        </div>
    </div>

    <div class="container">
        <h1>Cria a Tua Vela Personalizada</h1>
        <p class="subtitle">Escolhe a fragrância, a cor do fundo e deixa uma mensagem ou notas especiais. O nosso administrador irá avaliar o pedido e atribuir um preço à tua medida! 🌿</p>

        <?= $mensagem; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="nome_produto">Identificação / Título da Vela:</label>
                <input type="text" id="nome_produto" name="nome_produto" placeholder="Ex: Vela de Aniversário da Maria, Lembrança de Batizado" value="<?= htmlspecialchars($pref_nome) ?>" required>
            </div>

            <div class="form-group">
                <label for="aroma">Escolha o Aroma:</label>
<select id="aroma" name="aroma">
                <option value="Baunilha">🌼 Baunilha 🌼</option>
                <option value="Chocolate">🍫 Chocolate 🍫</option>
                <option value="Morango">🍓 Morango 🍓</option>
                <option value="Framboesa">🍓🍇 Framboesa 🍓🍇</option>
                <option value="Eucalipto">🌿 Eucalipto 🌿</option>
                <option value="Coco">🥥 Coco 🥥</option>
                <option value="Papaya e Manga">🍈🥭 Papaya e Manga 🍈🥭</option>
                <option value="Jasmim e Bamboo">🌸🎋 Jasmim e Bamboo 🌸🎋</option>
                <option value="Lavanda">🪻 Lavanda 🪻</option>
                <option value="Citronela">🌿🦟 Citronela 🌿🦟</option>
                <option value="Oceano">🌊 Oceano 🌊</option>
                <option value="Black Citrus">🍋🍋‍🟩 Black Citrus 🍋🍋‍🟩</option>
                <option value="Ozono Spa">🫧🍃 Ozono Spa 🫧🍃</option>
                <option value="Pinho">🌲 Pinho 🌲</option>
                <option value="Maçã Verde">🍏 Maçã Verde 🍏</option>
                <option value="Lemongrass"> 🍋🌿 Lemongrass 🍋🌿</option>
                <option value="Cânfora">🌿 Cânfora 🌿</option>
                <option value="Flores Brancas">💐 Flores Brancas 💐</option>
                <option value="Luxury">💍 Luxury 💍</option>
                <option value="Maracujá">🫐🍋 Maracujá 🫐🍋</option>
                <option value="Árvore Nevada">🏔️ Árvore Nevada 🏔️</option>
                <option value="Black Cherry">🍒 Black Cherry 🍒</option>
                <option value="Caramelo">🍯 Caramelo 🍯</option>
                <option value="Chocolate com Leite">🍫🥛 Chocolate com Leite 🍫🥛</option>
                <option value="Doces de Natal">🍬🎄 Doces de Natal 🍬🎄</option>
                <option value="Azevinho">🎄🍒 Azevinho 🎄🍒</option>
                <option value="Boneco de Gengibre">🍪🎄 Boneco de Gengibre 🍪🎄</option>
                <option value="Canela">🪵🍯 Canela 🪵🍯</option>
                <option value="Cookie de Chocolate">🍪🍫 Cookie de Chocolate 🍪🍫</option>
                <option value="Bengala Doce">🍬🌿 Bengala Doce 🍬🌿</option>
                <option value="Lótus">🪷 Lótus 🪷</option>
                <option value="Frutos Vermelhos">🍒🍓 Frutos Vermelhos 🍒🍓</option>
                <option value="Café Extra">☕ Café Extra ☕</option>
            </select>
            </div>

            <div class="form-group">
                <label for="cor">Cor da Vela:</label>
             <select id="cor" name="cor">
                <option value="Branco">⚪ Branco / Natural ⚪</option>
                <option value="Bege">🥟 Bege / Creme 🥟</option>
                <option value="Rosa">🌸 Rosa Pastel / Rosa Velho 🌸</option>
                <option value="Azul">🩵🔷 Azul Celeste / Azul Escuro 🩵🔷</option>
                <option value="Verde">🌿 Verde Sálvia / Verde Menta 🌿</option>
                <option value="Lavanda">🪻 Lavanda / Roxo 🪻</option>
                <option value="Amarelo">💛 Amarelo Pastel 💛</option>
                <option value="Laranja">🍊 Laranja / Terracota 🍊</option>
                <option value="Vermelho">❤️ Vermelho / Borgonha ❤️</option>
                <option value="Castanho">🪵🍫 Castanho / Chocolate 🪵🍫</option>
                <option value="Preto">🖤🩶 Preto / Cinza Escuro 🖤🩶</option>
            </select>
            </div>

            <div class="form-group">
                <label for="estilo">Estilo / Tema da Vela:</label>
                <select id="estilo" name="estilo">
                    <option value="Simples" <?= $pref_estilo == 'Simples' ? 'selected' : '' ?>>🕯️ Básico / Simples 🕯️</option>
                    <option value="Copo" <?= $pref_estilo == 'Copo' ? 'selected' : '' ?>>🫙 No Copo 🫙</option>
                    <option value="Natal" <?= $pref_estilo == 'Natal' ? 'selected' : '' ?>>🎄 Natal 🎄</option>
                    <option value="Halloween" <?= $pref_estilo == 'Halloween' ? 'selected' : '' ?>>🎃 Halloween 🎃</option>
                    <option value="Páscoa" <?= $pref_estilo == 'Páscoa' ? 'selected' : '' ?>>🐰 Páscoa 🐰</option>
                    <option value="S. Valentim" <?= $pref_estilo == 'S. Valentim' ? 'selected' : '' ?>>❤️ Dia dos Namorados ❤️</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mensagem_personalizada">Texto / Detalhes Adicionais da Personalização:</label>
                <textarea id="mensagem_personalizada" name="mensagem_personalizada" rows="3" placeholder="Escreva aqui frases, nomes, datas ou ideias para o design da vela..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="stock_atual">Quantidade Desejada:</label>
                <input type="number" id="stock_atual" name="stock_atual" min="1" value="<?= htmlspecialchars($pref_stock) ?>" required>
            </div>

            <button type="submit" class="btn-submit">Enviar Pedido de Vela Personalizada</button>
        </form>

        <div style="text-align:center;">
            <a href="user.php" class="btn-back">← Voltar para o Catálogo</a>
        </div>
    </div>

</body>
</html>