<?php
require_once "includes/funcoes.php";

$conexao = criar_conexao();

$sql = "SELECT * FROM regioes";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetchAll();

//pre($resultado);
?>

<!DOCTYPE html>
<html lang="pt">

<?php require_once "includes/head.php"; ?>

<body>

    <?php require_once "includes/header.php"; ?>

    <main>

    <?php require_once "includes/pesquisa_e_nav.php"; ?>

    <div class="intro-descricao">
        <p class="intro-texto">
            O litoral português é um verdadeiro paraíso à beira-mar, repleto de paisagens deslumbrantes, águas cristalinas e praias capazes de encantar qualquer visitante. Do Algarve à Costa Vicentina, cada praia revela cenários únicos entre falésias douradas, areias extensas e o azul intenso do oceano Atlântico. Seja para relaxar ao sol, explorar grutas naturais, praticar surf ou simplesmente admirar o pôr do sol, as praias de Portugal oferecem experiências inesquecíveis para todos os gostos.
        </p>
    </div>

    <div class="destinos-header">
        <h2 class="destinos-titulo">Escolha o seu destino</h2>
        <span class="destinos-linha"></span>
    </div>

    <div class="cxFlex100">

    <?php
            $emojis = ['','','⛵','🐚','🌅','🦀','🪸','🌿'];

            $bgRegioes = [
            'Algarve'         => 'imgs/fundo.jpg',
            'Costa Vicentina' => 'imgs/capa_costa_vicentina.jpg',
            ];

            $i = 0;
        foreach($resultado as $Regiao):
            $nomRegiao = $Regiao['nome_regiao'];
            $temFoto = isset($bgRegioes[$nomRegiao]);
            $bgStyle = $temFoto ? 'style="background-image: url(\'' . $bgRegioes[$nomRegiao] . '\');"' : '';
    ?>
    <div class="cxinhaRegiao <?= $temFoto ? 'cxinha-com-foto' : '' ?>" <?= $bgStyle ?>>
        <a href="detalhes.php?regiaoID=<?=$Regiao['id_regiao']?>" class="regiao-link">
            <span class="regiao-emoji"><?= $emojis[$i % count($emojis)] ?></span>
            <p class="tituloCxinha"><?= $nomRegiao ?></p>
            <span class="regiao-ver">Ver praias →</span>
        </a>
    </div>
    <?php $i++; endforeach; ?>

    </div>

</main>

    <?php require_once "includes/footer.php"; ?>

</body>

</html>