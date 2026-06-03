<?php
require_once "includes/funcoes.php";
$conexao = criar_conexao();

if (!isset($_GET['regiaoID']) || !is_numeric($_GET['regiaoID'])) {
    header("Location: index.php");
    exit;
}

$regiaoID = (int)$_GET['regiaoID'];

$sql  = "SELECT nome_regiao FROM regioes WHERE id_regiao = ?";
$stmt = $conexao->prepare($sql);
$stmt->execute([$regiaoID]);
$regiao = $stmt->fetch();

if (!$regiao) {
    header("Location: index.php");
    exit;
}

// Praias com a primeira foto (LEFT JOIN)
$sql = "
    SELECT
        p.*,
        f.nome_ficheiro AS foto_capa
    FROM praias p
    LEFT JOIN fotos_praias f
        ON f.id_praia = p.id_praia
        AND f.ordem = (
            SELECT MIN(f2.ordem)
            FROM fotos_praias f2
            WHERE f2.id_praia = p.id_praia
        )
    WHERE p.id_regiao = ?
    ORDER BY p.nome_praia ASC
";
$stmt = $conexao->prepare($sql);
$stmt->execute([$regiaoID]);
$praias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<?php require_once "includes/head.php"; ?>
<body>
    <?php require_once "includes/header.php"; ?>

    <main>
        <?php require_once "includes/pesquisa_e_nav.php"; ?>

        <div class="detalhes-topo">
            <a href="index.php" class="btn-voltar">&#8592; Voltar</a>
            <p id="p_titulo_01"><?= htmlspecialchars($regiao['nome_regiao']) ?></p>
            <p class="detalhes-subtitulo">
                <?= count($praias) ?> praia<?= count($praias) != 1 ? 's' : '' ?> encontrada<?= count($praias) != 1 ? 's' : '' ?>
            </p>
        </div>

        <?php if (empty($praias)): ?>
            <div class="sem-resultados">
                <p>Ainda não existem praias registadas nesta região.</p>
                <a href="adicionar.php" class="btn-adicionar">+ Adicionar praia</a>
            </div>
        <?php else: ?>
            <div class="cxFlex100 praias-grid">
                <?php foreach ($praias as $praia): ?>
                    <div class="card-praia">

                        <!-- FOTO DE CAPA -->
                        <div class="card-praia-img">
                            <?php if (!empty($praia['foto_capa'])): ?>
                                <img
                                    src="uploads/praias/<?= htmlspecialchars($praia['foto_capa']) ?>"
                                    alt="<?= htmlspecialchars($praia['nome_praia']) ?>"
                                    class="card-praia-foto"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                <span class="card-praia-emoji">🏖️</span>
                            <?php endif; ?>
                        </div>

                        <div class="card-praia-corpo">
                            <p class="card-praia-nome"><?= htmlspecialchars($praia['nome_praia']) ?></p>

                            <div class="card-praia-meta">
                                <?php if (!empty($praia['localizacao'])): ?>
                                    <span class="meta-item">📍 <?= htmlspecialchars($praia['localizacao']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($praia['concelho'])): ?>
                                    <span class="meta-item">🏛️ <?= htmlspecialchars($praia['concelho']) ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($praia['descricao'])): ?>
                                <p class="card-praia-desc">
                                    <?= htmlspecialchars(mb_strimwidth($praia['descricao'], 0, 120, '…')) ?>
                                </p>
                            <?php endif; ?>

                            <div class="card-praia-tags">
                                <span class="tag-praia <?= $praia['possui_estacionamento'] ? 'tag-sim' : 'tag-nao' ?>">
                                    🅿️ Estacionamento: <?= $praia['possui_estacionamento'] ? 'Sim' : 'Não' ?>
                                </span>
                                <span class="tag-praia <?= $praia['possui_restaurante'] ? 'tag-sim' : 'tag-nao' ?>">
                                    🍽️ Restaurante: <?= $praia['possui_restaurante'] ? 'Sim' : 'Não' ?>
                                </span>
                            </div>

                            <div class="card-praia-botoes">
                                <a href="praia.php?id=<?= $praia['id_praia'] ?>" class="card-praia-btn">
                                    Ver detalhes &#8594;
                                </a>
                                <a href="fotos.php?id_praia=<?= $praia['id_praia'] ?>" class="card-praia-btn btn-fotos" title="Gerir fotos">
                                    📷
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
    <?php require_once "includes/footer.php"; ?>
</body>
</html>