<?php
require_once 'db.php';

// Inserção do produto
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? '0'));
    $quantidade = intval($_POST['quantidade'] ?? 0);

    if ($nome === '') {
        $mensagem = 'O nome do produto é obrigatório.';
    } else {
        $sql = 'INSERT INTO produtos (nome, descricao, preco, quantidade) VALUES (:nome, :descricao, :preco, :quantidade)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => (float)$preco,
            ':quantidade' => $quantidade,
        ]);
        $mensagem = 'Produto cadastrado com sucesso!';
        $_POST = [];
    }
}

// Buscar produtos cadastrados
$stmt = $pdo->query("SELECT * FROM produtos ORDER BY criado_em DESC");
$produtos = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Produtos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<main class="container">
    <header class="cabecalho">
        <h1>Sistema de Produtos</h1>
        <p class="lead">Cadastre e visualize seus produtos facilmente.</p>
    </header>

    <section class="card form-card">
        <h2>Novo Produto</h2>

        <?php if ($mensagem): ?>
            <div class="alert"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="row">
                <label>Nome</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            </div>

            <div class="row">
                <label>Descrição</label>
                <textarea name="descricao"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
            </div>

            <div class="row horizontal">
                <div class="col">
                    <label>Preço (ex: 49.90)</label>
                    <input type="text" name="preco" value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>">
                </div>
                <div class="col">
                    <label>Quantidade</label>
                    <input type="number" name="quantidade" min="0" value="<?= htmlspecialchars($_POST['quantidade'] ?? '0') ?>">
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn">Salvar produto</button>
                <button type="reset" class="btn ghost">Limpar</button>
            </div>
        </form>
    </section>

    <section class="card list-card">
        <h2>Produtos Cadastrados <small>(<?= count($produtos) ?>)</small></h2>

        <?php if (count($produtos) === 0): ?>
            <p class="muted">Nenhum produto cadastrado ainda.</p>
        <?php else: ?>
            <table class="produtos-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Criado em</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                        <td><?= (int)$p['quantidade'] ?></td>
                        <td><?= $p['criado_em'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <footer class="rodape">
        <p>Projeto de exemplo — Sistema de Produtos em PHP + MySQL</p>
    </footer>
</main>
</body>
</html>
