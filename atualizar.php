<?php
require_once "includes/funcoes.php";
$conexao = criar_conexao();

$resposta = "";
if(!isset($_GET['id_praia'])){
    $sql        = "SELECT * FROM praias";
    $stmt       = $conexao->query($sql);
    $resposta   = $stmt->fetchAll();
}

if($_SERVER["REQUEST_METHOD"]=="GET" && isset($_GET['id_praia'])){
    $sql            = "SELECT * FROM regioes";
    $stmt           = $conexao->query($sql);
    $regioes        = $stmt->fetchAll();

    $sql            = "SELECT * FROM praias WHERE id_praia=?";
    $stmt           = $conexao->prepare($sql);
    $stmt->execute([$_GET['id_praia']]);
    $dados_praia    = $stmt->fetch();
    $id             = $dados_praia['id_praia'];
    $regiao_id      = $dados_praia['id_regiao'];
    $nome           = $dados_praia['nome_praia'];
    $localizacao    = $dados_praia['localizacao'];
    $concelho       = $dados_praia['concelho'];
    $estacionamento = $dados_praia['possui_estacionamento'];
    $restaurante    = $dados_praia['possui_restaurante'];
    $descricao      = $dados_praia['descricao'];

    if($restaurante == 0){
        $radioRestauranteSimSelected = "";
        $radioRestauranteNaoSelected = "checked";
    }elseif ($restaurante == 1){
        $radioRestauranteSimSelected = "checked";
        $radioRestauranteNaoSelected = "";   
    }
    
    if($estacionamento == 0){
        $radioEstacionamentoSimSelected = "";
        $radioEstacionamentoNaoSelected = "checked";
    }elseif ($estacionamento == 1){
        $radioEstacionamentoSimSelected = "checked";
        $radioEstacionamentoNaoSelected = "";   
    }
    
}




if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['fnome'])){
    $sql   = "UPDATE praias SET nome_praia=?, localizacao=?, concelho=?, possui_estacionamento=?, possui_restaurante=?, descricao=?, id_regiao=? WHERE id_praia=?";
    $atArray = [
        $_POST['fnome'],
        $_POST['flocalizacao'],
        $_POST['fconcelho'],
        $_POST['festacionamento'],
        $_POST['frestaurante'],
        $_POST['fdescricao'],
        $_POST['fregiao'],
        $_POST['fid']

    ];

    $stmt = $conexao->prepare($sql);
    $stmt->execute($atArray);
    echo "Praia atualizada";
}


?>

<!DOCTYPE html>
<html lang="pt">

<?php require_once "includes/head.php"; ?>

<body>
    <?php require_once "includes/header.php"; ?>
    <main>
        <?php require_once "includes/pesquisa_e_nav.php"; ?>

        <p id="p_titulo_01">Atualizar praia</p>
        <br>
        <div>

            <!-- 1º passo -->
            <?php if(!isset($_GET['id_praia'])): ?>
                <?php foreach($resposta as $praias): ?>
                    <a href="atualizar.php?id_praia=<?= $praias['id_praia'] ?>" class="lista-link"> <?= $praias['nome_praia'] ?> </a> <br><br>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- 2º passo -->
            <?php if($_SERVER["REQUEST_METHOD"]=="GET" && isset($_GET['id_praia'])): ?>
            <form method="POST" action="">
                <input type="hidden" name="fid" value="<?=$id?>">
                <strong>Nome da praia</strong> <br><br>
                <input type="text" name="fnome" placeholder="Nome da praia" class="class-inputs" value="<?=$nome?>" required> <br><br>
                <select name="fregiao" required> 
                    <option value="" disabled>Escolha uma região</option>
                    <?php foreach($regioes AS $regiao): ?>
                        <?php 
                        if ($regiao_id == $regiao['id_regiao']){
                            $select = "selected";
                        }else{
                            $select = "";
                        }
                        ?>
                        <option value="<?php echo $regiao['id_regiao']?>" <?=$select?>><?=$regiao['nome_regiao']?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <strong>Localização</strong> <br><br>
                <input type="text" name="flocalizacao" placeholder="Localizacao" class="class-inputs" required value="<?=$localizacao?>"> <br><br>
                <strong>Concelho</strong> <br><br>
                <input type="text" name="fconcelho" placeholder="Concelho" class="class-inputs" required value="<?=$concelho?>"> <br><br>
                <strong>Estacionamento:</strong> <br><br>
                <input type="radio" name="festacionamento" value="1" 
                <?=$radioEstacionamentoSimSelected?>> Sim <br><br>
                <input type="radio" name="festacionamento" value="0" 
                <?=$radioEstacionamentoNaoSelected?>> Não <br><br>
                <strong>Restaurante:</strong> <br><br>
                <input type="radio" name="frestaurante" value="1" 
                <?=$radioRestauranteSimSelected?>> Sim <br><br>
                <input type="radio" name="frestaurante" value="0" 
                <?=$radioRestauranteNaoSelected?>> Não <br><br>
                <strong>Texto descritivo</strong> <br><br>
                <textarea name="fdescricao" placeholder="Texto descritivo" class="class-inputs" required><?=$descricao?></textarea> <br><br>
                <input type="submit" value="atualizar"> 
                <input type="reset" value="apagar">
            </form>
            <?php endif; ?>
            <br><br><br><br>
        </div>
    </main>
     <?php require_once "includes/footer.php" ?>  
     <?php require_once "includes/janela_avisos.php" ?> 
</body>
</html>