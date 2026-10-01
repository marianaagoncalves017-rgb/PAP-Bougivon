<?php 
include 'config.php';

// Iniciar a sessão para conseguir ler se o utilizador é admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o utilizador é admin
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Obter os dados da vela atual
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM velas_artigos WHERE id = ?");
$stmt->execute([$id]);
$v = $stmt->fetch();

// Se a vela não existir, volta para o dashboard
if (!$v) {
    header("Location: dashboard.php");
    exit();
}

// Lógica de atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $aroma = $_POST['aroma'];
    $cor = $_POST['cor'];
    $estilo = $_POST['estilo']; // Novo campo adicionado à lógica
    $preco = $_POST['preco'];
    $stock = $_POST['stock'];

    // Atualização correta incluindo o campo estilo
    $sql = "UPDATE velas_artigos SET nome_produto = ?, aroma = ?, cor = ?, estilo = ?, preco = ?, stock_atual = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$nome, $aroma, $cor, $estilo, $preco, $stock, $id]);
    
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Vela | Bougivon</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #fdfaf6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px 0;
        }
        .form-container { 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            width: 100%; 
            max-width: 400px; 
            border: 1px solid #eee;
            box-sizing: border-box;
        }
        h2 { 
            color: #8d6e63; 
            text-align: center; 
            font-family: 'Georgia', serif; 
            margin-bottom: 25px;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
            color: #666; 
            font-size: 0.9em; 
            font-weight: bold;
        }
        input, select { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            box-sizing: border-box; 
            background: #fafafa;
            font-size: 0.95em;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #8d6e63;
            background: #fff;
        }
        .btn-group { 
            display: flex; 
            gap: 10px; 
            margin-top: 25px;
        }
        button { 
            flex: 2; 
            padding: 12px; 
            background-color: #8d6e63; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: 0.3s; 
        }
        button:hover { 
            background-color: #6d544b; 
        }
        .btn-cancel { 
            flex: 1; 
            padding: 12px; 
            background-color: #eee; 
            color: #666; 
            text-decoration: none; 
            text-align: center; 
            border-radius: 8px; 
            font-size: 0.9em; 
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .btn-cancel:hover { 
            background-color: #ddd; 
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Editar Vela</h2>
    <form method="POST">
        
        <div class="form-group">
            <label for="nome">Nome do Produto</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($v['nome_produto']) ?>" required>
        </div>

        <div class="form-group">
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
                <option value="Branco" <?= (isset($v['cor']) && $v['cor'] == 'Branco') ? 'selected' : '' ?>>⚪ Branco / Natural ⚪</option>
                <option value="Bege" <?= (isset($v['cor']) && $v['cor'] == 'Bege') ? 'selected' : '' ?>>🥟 Bege / Creme 🥟</option>
                <option value="Rosa" <?= (isset($v['cor']) && $v['cor'] == 'Rosa') ? 'selected' : '' ?>>🌸 Rosa Pastel / Rosa Velho 🌸</option>
                <option value="Azul" <?= (isset($v['cor']) && $v['cor'] == 'Azul') ? 'selected' : '' ?>>🩵🔷 Azul Celeste / Azul Escuro 🩵🔷</option>
                <option value="Verde" <?= (isset($v['cor']) && $v['cor'] == 'Verde') ? 'selected' : '' ?>>🌿 Verde Sálvia / Verde Menta 🌿</option>
                <option value="Lavanda" <?= (isset($v['cor']) && $v['cor'] == 'Lavanda') ? 'selected' : '' ?>>🪻 Lavanda / Roxo 🪻</option>
                <option value="Amarelo" <?= (isset($v['cor']) && $v['cor'] == 'Amarelo') ? 'selected' : '' ?>>💛 Amarelo Pastel 💛</option>
                <option value="Laranja" <?= (isset($v['cor']) && $v['cor'] == 'Laranja') ? 'selected' : '' ?>>🍊 Laranja / Terracota 🍊</option>
                <option value="Vermelho" <?= (isset($v['cor']) && $v['cor'] == 'Vermelho') ? 'selected' : '' ?>>❤️ Vermelho / Borgonha ❤️</option>
                <option value="Castanho" <?= (isset($v['cor']) && $v['cor'] == 'Castanho') ? 'selected' : '' ?>>🪵🍫 Castanho / Chocolate 🪵🍫</option>
                <option value="Preto" <?= (isset($v['cor']) && $v['cor'] == 'Preto') ? 'selected' : '' ?>>🖤🩶 Preto / Cinza Escuro 🖤🩶</option>
            </select>
        </div>

        <div class="form-group">
            <label for="estilo">Estilo / Formato</label>
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
            <label for="preco">Preço (€)</label>
            <input type="number" step="0.01" name="preco" id="preco" value="<?= $v['preco'] ?>" required>
        </div>

        <div class="form-group">
            <label for="stock">Stock Atual</label>
            <input type="number" name="stock" id="stock" value="<?= $v['stock_atual'] ?>" required>
        </div>

        <div class="btn-group">
            <button type="submit">Atualizar</button>
            <a href="dashboard.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>