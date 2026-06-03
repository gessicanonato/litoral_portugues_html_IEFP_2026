<?php
require_once "includes/funcoes.php";
$conexao = criar_conexao();


if(isset($_POST['fnome'])){
    $sql= "INSERT INTO praias (nome_praia, localizacao, concelho, descricao, possui_estacionamento, possui_restaurante,id_regiao) VALUES (?,?,?,?,?,?,?)";

    $adArray = [
        $_POST['fnome'],
        $_POST['flocalizacao'],
        $_POST['fconcelho'],
        $_POST['fdescricao'],
        $_POST['festacionamento'],
        $_POST['frestaurante'],
        $_POST['fregiao']
    ];

    $stmt = $conexao->prepare($sql);
    $stmt->execute($adArray);

    echo "Praia adicionada";
}else{
    $sql= "SELECT * FROM regioes";
    $stmt = $conexao->query($sql);
    $regioes = $stmt->fetchAll();
}


?>

<!DOCTYPE html>
<html lang="pt">

<?php require_once "includes/head.php"; ?>

<body>
    <?php require_once "includes/header.php"; ?>
    <main>
        <?php require_once "includes/pesquisa_e_nav.php"; ?>

        <p id="p_titulo_01">Adicionar</p>
        <br>
        <div>
            <form method="POST" action="">
                <input type="text" name="fnome" placeholder="Nome da praia" class="class-inputs" required> <br><br>
                <select name="fregiao" required> 
                    <option value="" disabled>Escolha uma região</option>
                    <?php foreach($regioes AS $regiao): ?>
                        <option value="<?php echo $regiao['id_regiao']?>"><?=$regiao['nome_regiao']?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <input type="text" name="flocalizacao" placeholder="Localizacao" class="class-inputs" required> <br><br>
                <input type="text" name="fconcelho" placeholder="Concelho" class="class-inputs" required> <br><br>
                <strong>Estacionamento:</strong> <br><br>
                <input type="radio" name="festacionamento" value="1" class="class-inputs" required> Sim <br><br>
                <input type="radio" name="festacionamento" value="0" class="class-inputs" required> Não <br><br>
                <strong>Restaurante:</strong> <br><br>
                <input type="radio" name="frestaurante" value="1" class="class-inputs" required> Sim <br><br>
                <input type="radio" name="frestaurante" value="0" class="class-inputs" required> Não <br><br>
                <textarea name="fdescricao" placeholder="Texto descritivo" class="class-inputs" required></textarea> <br><br>
                <input type="submit" value="adicionar"> 
                <input type="reset" value="apagar">
            </form>
            <br><br><br><br>
        </div>
    </main>
     <?php require_once "includes/footer.php" ?>  
     <?php require_once "includes/janela_avisos.php" ?> 
</body>
</html>