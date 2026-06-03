<?php
require_once "includes/funcoes.php";
$conexao = criar_conexao();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$sql  = "SELECT p.*, r.nome_regiao, r.id_regiao FROM praias p JOIN regioes r ON p.id_regiao = r.id_regiao WHERE p.id_praia = ?";
$stmt = $conexao->prepare($sql);
$stmt->execute([$id]);
$praia = $stmt->fetch();

if (!$praia) {
    header("Location: index.php");
    exit;
}

// Buscar todas as fotos
$sql  = "SELECT * FROM fotos_praias WHERE id_praia = ? ORDER BY ordem ASC";
$stmt = $conexao->prepare($sql);
$stmt->execute([$id]);
$fotos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<?php require_once "includes/head.php"; ?>
<body>
    <?php require_once "includes/header.php"; ?>

    <main>
        <?php require_once "includes/pesquisa_e_nav.php"; ?>

        <div class="detalhes-topo">
            <a href="detalhes.php?regiaoID=<?= $praia['id_regiao'] ?>" class="btn-voltar">&#8592; <?= htmlspecialchars($praia['nome_regiao']) ?></a>
        </div>

        <div class="praia-detalhe-wrap">

            <!-- Cabeçalho -->
            <div class="praia-detalhe-header">
                <div class="praia-detalhe-icon">🏖️</div>
                <div>
                    <p class="praia-detalhe-nome"><?= htmlspecialchars($praia['nome_praia']) ?></p>
                    <p class="praia-detalhe-regiao">📍 <?= htmlspecialchars($praia['localizacao']) ?> &bull; <?= htmlspecialchars($praia['nome_regiao']) ?></p>
                </div>
            </div>

            <!-- GALERIA DE FOTOS -->
            <?php if (!empty($fotos)): ?>
                <div class="praia-galeria">
                    <p class="praia-galeria-titulo">📷 Fotos (<?= count($fotos) ?>)</p>

                    <!-- Foto principal ampliada -->
                    <div class="galeria-capa-wrap">
                        <img
                            src="uploads/praias/<?= htmlspecialchars($fotos[0]['nome_ficheiro']) ?>"
                            alt="<?= htmlspecialchars($praia['nome_praia']) ?>"
                            class="galeria-capa-img"
                            id="fotoCapa"
                            onclick="abrirLightbox(this.src)"
                        >
                    </div>

                    <!-- Miniaturas -->
                    <?php if (count($fotos) > 1): ?>
                        <div class="galeria-thumbs">
                            <?php foreach ($fotos as $i => $foto): ?>
                                <img
                                    src="uploads/praias/<?= htmlspecialchars($foto['nome_ficheiro']) ?>"
                                    alt="Foto <?= $i+1 ?>"
                                    class="galeria-thumb <?= $i === 0 ? 'thumb-active' : '' ?>"
                                    onclick="trocarCapa(this)"
                                    loading="lazy"
                                >
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="praia-galeria">
                    <p class="praia-galeria-titulo">📷 Fotos</p>
                    <p class="sem-fotos">Ainda não há fotos. <a href="fotos.php?id_praia=<?= $praia['id_praia'] ?>">Adicionar fotos →</a></p>
                </div>
            <?php endif; ?>

            <!-- Informações rápidas -->
            <div class="praia-info-grid">
                <div class="praia-info-card">
                    <span class="info-label">Concelho</span>
                    <span class="info-valor"><?= htmlspecialchars($praia['concelho']) ?></span>
                </div>
                <div class="praia-info-card">
                    <span class="info-label">Região</span>
                    <span class="info-valor"><?= htmlspecialchars($praia['nome_regiao']) ?></span>
                </div>
                <div class="praia-info-card <?= $praia['possui_estacionamento'] ? 'info-sim' : 'info-nao' ?>">
                    <span class="info-label">🅿️ Estacionamento</span>
                    <span class="info-valor"><?= $praia['possui_estacionamento'] ? '✔ Disponível' : '✘ Não disponível' ?></span>
                </div>
                <div class="praia-info-card <?= $praia['possui_restaurante'] ? 'info-sim' : 'info-nao' ?>">
                    <span class="info-label">🍽️ Restaurante</span>
                    <span class="info-valor"><?= $praia['possui_restaurante'] ? '✔ Disponível' : '✘ Não disponível' ?></span>
                </div>
            </div>

            <!-- Descrição -->
            <?php if (!empty($praia['descricao'])): ?>
                <div class="praia-descricao">
                    <p class="praia-descricao-titulo">Sobre esta praia</p>
                    <p class="praia-descricao-texto"><?= nl2br(htmlspecialchars($praia['descricao'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Ações admin -->
            <div class="praia-acoes">
                <a href="fotos.php?id_praia=<?= $praia['id_praia'] ?>" class="btn-acao btn-fotos-gerir">📷 Gerir fotos</a>
                <a href="atualizar.php?id_praia=<?= $praia['id_praia'] ?>" class="btn-acao btn-editar">✏️ Editar</a>
                <a href="#" onclick="confirmaEliminar('<?= htmlspecialchars($praia['nome_praia']) ?>', <?= $praia['id_praia'] ?>)" class="btn-acao btn-eliminar">🗑️ Eliminar</a>
            </div>

        </div>
    </main>

    <!-- Lightbox -->
    <div id="lightbox" onclick="fecharLightbox()">
        <button id="lb-fechar" onclick="fecharLightbox()">&#x2715;</button>
        <img id="lb-img" src="" alt="Foto ampliada">
    </div>

    <?php require_once "includes/footer.php"; ?>
    <?php require_once "includes/janela_avisos.php"; ?>

    <script>
    function trocarCapa(thumb) {
        document.getElementById('fotoCapa').src = thumb.src;
        document.querySelectorAll('.galeria-thumb').forEach(t => t.classList.remove('thumb-active'));
        thumb.classList.add('thumb-active');
    }
    function abrirLightbox(src) {
        document.getElementById('lb-img').src = src;
        document.getElementById('lightbox').classList.add('lb-open');
    }
    function fecharLightbox() {
        document.getElementById('lightbox').classList.remove('lb-open');
    }
    document.addEventListener('keydown', e => { if(e.key === 'Escape') fecharLightbox(); });
    </script>
</body>
</html>