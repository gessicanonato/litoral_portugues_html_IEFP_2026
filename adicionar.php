<?php
require_once "includes/funcoes.php";
$conexao = criar_conexao();

if(isset($_POST['fnome'])){
    $sql = "INSERT INTO praias (nome_praia, localizacao, concelho, descricao, possui_estacionamento, possui_restaurante, id_regiao) VALUES (?,?,?,?,?,?,?)";

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
} else {
    $sql    = "SELECT * FROM regioes";
    $stmt   = $conexao->query($sql);
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

        <div class="detalhes-topo">
            <a href="index.php" class="btn-voltar">&#8592; Voltar</a>
        </div>

        <p id="p_titulo_01">Adicionar praia</p>

        <div class="form-wrap">
            <form method="POST" action="">

                <div class="form-row">
                    <div class="form-section">
                        <label class="form-label" for="fnome">Nome da praia</label>
                        <input type="text" id="fnome" name="fnome" class="form-input" placeholder="Ex: Praia da Marinha" required>
                    </div>
                    <div class="form-section">
                        <label class="form-label" for="fregiao">Região</label>
                        <select id="fregiao" name="fregiao" class="form-select" required>
                            <option value="" disabled selected>Escolha uma região</option>
                            <?php foreach($regioes as $regiao): ?>
                                <option value="<?= $regiao['id_regiao'] ?>"><?= htmlspecialchars($regiao['nome_regiao']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-section">
                        <label class="form-label" for="flocalizacao">Localização</label>
                        <input type="text" id="flocalizacao" name="flocalizacao" class="form-input" placeholder="Ex: Lagoa" required>
                    </div>
                    <div class="form-section">
                        <label class="form-label" for="fconcelho">Concelho</label>
                        <input type="text" id="fconcelho" name="fconcelho" class="form-input" placeholder="Ex: Lagoa" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-section">
                        <label class="form-label">🅿️ Estacionamento</label>
                        <div class="radio-group">
                            <label class="radio-item">
                                <input type="radio" name="festacionamento" value="1" required> Sim
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="festacionamento" value="0"> Não
                            </label>
                        </div>
                    </div>
                    <div class="form-section">
                        <label class="form-label">🍽️ Restaurante</label>
                        <div class="radio-group">
                            <label class="radio-item">
                                <input type="radio" name="frestaurante" value="1" required> Sim
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="frestaurante" value="0"> Não
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <label class="form-label" for="fdescricao">Descrição</label>
                    <textarea id="fdescricao" name="fdescricao" class="form-textarea" placeholder="Descreva a praia…" required></textarea>
                </div>

                <div class="form-acoes">
                    <button type="submit" class="btn-form-submit">➕ Adicionar praia</button>
                    <button type="reset" class="btn-form-reset">Limpar</button>
                </div>

            </form>
        </div>
    </main>

    <?php require_once "includes/footer.php"; ?>
    <?php require_once "includes/janela_avisos.php"; ?>
</body>
</html>
