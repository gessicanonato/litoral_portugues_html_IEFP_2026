<?php
require_once "includes/funcoes.php";

$conexao = criar_conexao();

$resposta = "";

/* LISTAR PRAIAS */
if(!isset($_GET['id_praia'])){

    $sql = "SELECT * FROM praias";

    $stmt = $conexao->query($sql);

    $resposta = $stmt->fetchAll();
}


/* ELIMINAR PRAIA */
if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id_praia'])){

    $sql = "DELETE FROM praias WHERE id_praia=?";

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

        <p id="p_titulo_01">
            Eliminar praia
        </p>

        <br>

        <div>

            <!-- LISTA DE PRAIAS -->
            <?php if(!isset($_GET['id_praia'])): ?>

                <?php foreach($resposta as $praia): ?>

                    <a 
                        href="#"
                        onclick="confirmaEliminar('<?=$praia['nome_praia']?>', <?=$praia['id_praia']?>)"
                        class="lista-link"
                    >
                        <?=$praia['nome_praia']?>
                    </a>

                    <br><br>

                <?php endforeach; ?>

            <?php endif; ?>

            <br><br><br><br>

        </div>

    </main>

    <?php require_once "includes/footer.php"; ?>
    <?php require_once "includes/janela_avisos.php" ?> 

</body>

</html>