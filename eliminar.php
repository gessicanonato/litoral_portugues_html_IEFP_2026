<?php
require_once "includes/funcoes.php";

$conexao = criar_conexao();

$resposta = "";

/* LISTAR PRAIAS */
if(!isset($_GET['id_praia'])){
    $sql  = "SELECT * FROM praias ORDER BY nome_praia ASC";
    $stmt = $conexao->query($sql);
    $resposta = $stmt->fetchAll();
}

/* ELIMINAR PRAIA */
if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id_praia'])){
    $sql  = "DELETE FROM praias WHERE id_praia=?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$_GET['id_praia']]);
    echo "Praia eliminada com sucesso";
}
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
        </div>

        <p id="p_titulo_01">Eliminar praia</p>

        <?php if(!isset($_GET['id_praia'])): ?>
            <p class="detalhes-subtitulo"><?= count($resposta) ?> praia<?= count($resposta) != 1 ? 's' : '' ?> registada<?= count($resposta) != 1 ? 's' : '' ?></p>
            <div class="eliminar-lista">
                <?php foreach($resposta as $praia): ?>
                    <div class="eliminar-item">
                        <span class="eliminar-nome">🏖️ <?= htmlspecialchars($praia['nome_praia']) ?></span>
                        <a
                            href="#"
                            onclick="confirmaEliminar('<?= htmlspecialchars($praia['nome_praia'], ENT_QUOTES) ?>', <?= $praia['id_praia'] ?>)"
                            class="btn-eliminar-item"
                        >🗑️ Eliminar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <?php require_once "includes/footer.php"; ?>
    <?php require_once "includes/janela_avisos.php"; ?>
</body>
</html>
