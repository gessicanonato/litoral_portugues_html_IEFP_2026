<?php
require_once "includes/funcoes.php";
$conexao = criar_conexao();

// Validação do id_praia obrigatório
if (!isset($_GET['id_praia']) || !is_numeric($_GET['id_praia'])) {
    header("Location: index.php");
    exit;
}

$id_praia = (int)$_GET['id_praia'];

// Buscar dados da praia + região
$sql  = "SELECT p.*, r.nome_regiao, r.id_regiao FROM praias p JOIN regioes r ON p.id_regiao = r.id_regiao WHERE p.id_praia = ?";
$stmt = $conexao->prepare($sql);
$stmt->execute([$id_praia]);
$praia = $stmt->fetch();

if (!$praia) {
    header("Location: index.php");
    exit;
}

$mensagem = "";
$erro     = "";

// ── APAGAR FOTO ──────────────────────────────────────────────
if (isset($_GET['apagar']) && is_numeric($_GET['apagar'])) {
    $id_foto = (int)$_GET['apagar'];

    $sql  = "SELECT nome_ficheiro FROM fotos_praias WHERE id_foto = ? AND id_praia = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id_foto, $id_praia]);
    $foto = $stmt->fetch();

    if ($foto) {
        $caminho = "uploads/praias/" . $foto['nome_ficheiro'];
        if (file_exists($caminho)) {
            unlink($caminho);
        }
        $sql  = "DELETE FROM fotos_praias WHERE id_foto = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$id_foto]);
        $mensagem = "Foto eliminada com sucesso.";
    }
}

// ── UPLOAD DE FOTOS ──────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['fotos'])) {
    $pasta        = "uploads/praias/";
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    $maxTamanho   = 5 * 1024 * 1024; // 5 MB

    // Criar pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    // Buscar próxima ordem
    $sql  = "SELECT COALESCE(MAX(ordem), 0) + 1 AS proxima FROM fotos_praias WHERE id_praia = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id_praia]);
    $proxima_ordem = (int)$stmt->fetchColumn();

    $total    = count($_FILES['fotos']['name']);
    $enviadas = 0;
    $erros    = [];

    for ($i = 0; $i < $total; $i++) {
        if ($_FILES['fotos']['error'][$i] !== UPLOAD_ERR_OK) continue;

        $tipo    = $_FILES['fotos']['type'][$i];
        $tamanho = $_FILES['fotos']['size'][$i];
        $tmp     = $_FILES['fotos']['tmp_name'][$i];

        // Validações
        if (!in_array($tipo, $tiposPermitidos)) {
            $erros[] = $_FILES['fotos']['name'][$i] . ": tipo não permitido (use JPG, PNG ou WEBP).";
            continue;
        }
        if ($tamanho > $maxTamanho) {
            $erros[] = $_FILES['fotos']['name'][$i] . ": ficheiro demasiado grande (máx. 5MB).";
            continue;
        }

        // Gerar nome único
        $extensao      = strtolower(pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION));
        $nome_ficheiro = "praia_{$id_praia}_" . uniqid() . "." . $extensao;
        $destino       = $pasta . $nome_ficheiro;

        if (move_uploaded_file($tmp, $destino)) {
            $sql  = "INSERT INTO fotos_praias (id_praia, nome_ficheiro, ordem) VALUES (?, ?, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->execute([$id_praia, $nome_ficheiro, $proxima_ordem]);
            $proxima_ordem++;
            $enviadas++;
        }
    }

    if ($enviadas > 0) {
        $mensagem = "$enviadas foto(s) adicionada(s) com sucesso.";
    }
    if (!empty($erros)) {
        $erro = implode("<br>", $erros);
    }
}

// ── BUSCAR FOTOS ACTUAIS ──────────────────────────────────────
$sql  = "SELECT * FROM fotos_praias WHERE id_praia = ? ORDER BY ordem ASC";
$stmt = $conexao->prepare($sql);
$stmt->execute([$id_praia]);
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

        <p id="p_titulo_01">Fotos — <?= htmlspecialchars($praia['nome_praia']) ?></p>

        <?php if ($mensagem): ?>
            <div class="aviso-sucesso"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="aviso-erro"><?= $erro ?></div>
        <?php endif; ?>

        <!-- Upload -->
        <div class="fotos-upload-wrap">
            <p class="fotos-secao-titulo">Adicionar fotos</p>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="upload-area" id="uploadArea">
                    <span class="upload-icone">📁</span>
                    <p class="upload-texto">Clique ou arraste as fotos para aqui</p>
                    <p class="upload-sub">JPG, PNG ou WEBP · máx. 5MB cada · várias de uma vez</p>
                    <input
                        type="file"
                        name="fotos[]"
                        id="inputFotos"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        style="display:none"
                    >
                </div>
                <div id="preview-wrap" class="preview-wrap"></div>
                <button type="submit" class="btn-upload" id="btnUpload" style="display:none">
                    Enviar fotos selecionadas
                </button>
            </form>
        </div>

        <!-- Galeria existente -->
        <div class="fotos-galeria-wrap">
            <p class="fotos-secao-titulo">
                Fotos atuais
                <span class="fotos-count"><?= count($fotos) ?></span>
            </p>

            <?php if (empty($fotos)): ?>
                <p class="sem-fotos">Ainda não há fotos para esta praia.</p>
            <?php else: ?>
                <div class="fotos-admin-grid">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="foto-admin-card">
                            <img
                                src="uploads/praias/<?= htmlspecialchars($foto['nome_ficheiro']) ?>"
                                alt="Foto da praia"
                                class="foto-admin-img"
                                loading="lazy"
                            >
                            <div class="foto-admin-acoes">
                                <span class="foto-ordem">Foto <?= $foto['ordem'] ?></span>
                                <a
                                    href="fotos.php?id_praia=<?= $id_praia ?>&apagar=<?= $foto['id_foto'] ?>"
                                    class="btn-apagar-foto"
                                    onclick="return confirm('Apagar esta foto?')"
                                >🗑️ Apagar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <br><br>

    </main>

    <?php require_once "includes/footer.php"; ?>

    <script>
    const area    = document.getElementById('uploadArea');
    const input   = document.getElementById('inputFotos');
    const preview = document.getElementById('preview-wrap');
    const btn     = document.getElementById('btnUpload');

    area.addEventListener('click', () => input.click());

    area.addEventListener('dragover', e => {
        e.preventDefault();
        area.classList.add('drag-over');
    });
    area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
    area.addEventListener('drop', e => {
        e.preventDefault();
        area.classList.remove('drag-over');
        input.files = e.dataTransfer.files;
        mostrarPreview(input.files);
    });

    input.addEventListener('change', () => mostrarPreview(input.files));

    function mostrarPreview(files) {
        preview.innerHTML = '';
        if (!files.length) { btn.style.display = 'none'; return; }
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `<img src="${e.target.result}" alt="${file.name}"><span>${file.name}</span>`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
        btn.style.display = 'block';
    }
    </script>
</body>
</html>