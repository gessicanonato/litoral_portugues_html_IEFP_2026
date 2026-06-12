<?php
require_once "includes/funcoes.php";
$conexao = criar_conexao();

$resposta = "";
if(!isset($_GET['id_praia'])){
    $sql      = "SELECT * FROM praias ORDER BY nome_praia ASC";
    $stmt     = $conexao->query($sql);
    $resposta = $stmt->fetchAll();
}

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id_praia'])){
    $sql  = "SELECT * FROM regioes";
    $stmt = $conexao->query($sql);
    $regioes = $stmt->fetchAll();

    $sql  = "SELECT * FROM praias WHERE id_praia=?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$_GET['id_praia']]);
    $dados_praia = $stmt->fetch();

    $id             = $dados_praia['id_praia'];
    $regiao_id      = $dados_praia['id_regiao'];
    $nome           = $dados_praia['nome_praia'];
    $localizacao    = $dados_praia['localizacao'];
    $concelho       = $dados_praia['concelho'];
    $estacionamento = $dados_praia['possui_estacionamento'];
    $restaurante    = $dados_praia['possui_restaurante'];
    $descricao      = $dados_praia['descricao'];
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fnome'])){
    $sql = "UPDATE praias SET nome_praia=?, localizacao=?, concelho=?, possui_estacionamento=?, possui_restaurante=?, descricao=?, id_regiao=? WHERE id_praia=?";
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

        <div class="detalhes-topo">
            <a href="index.php" class="btn-voltar">&#8592; Voltar</a>
        </div>

        <p id="p_titulo_01">Atualizar praia</p>

        <!-- 1º passo: escolher praia -->
        <?php if(!isset($_GET['id_praia'])): ?>
            <p class="detalhes-subtitulo"><?= count($resposta) ?> praia<?= count($resposta) != 1 ? 's' : '' ?> disponíve<?= count($resposta) != 1 ? 'is' : 'l' ?></p>
            <div class="atualizar-lista">
                <?php foreach($resposta as $praia): ?>
                    <div class="atualizar-item">
                        <span class="atualizar-nome">🏖️ <?= htmlspecialchars($praia['nome_praia']) ?></span>
                        <a href="atualizar.php?id_praia=<?= $praia['id_praia'] ?>" class="btn-editar-item">✏️ Editar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- 2º passo: formulário de edição -->
        <?php if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id_praia'])): ?>
            <div class="form-wrap">
                <form method="POST" action="">
                    <input type="hidden" name="fid" value="<?= $id ?>">

                    <div class="form-row">
                        <div class="form-section">
                            <label class="form-label" for="fnome">Nome da praia</label>
                            <input type="text" id="fnome" name="fnome" class="form-input" value="<?= htmlspecialchars($nome) ?>" required>
                        </div>
                        <div class="form-section">
                            <label class="form-label" for="fregiao">Região</label>
                            <select id="fregiao" name="fregiao" class="form-select" required>
                                <option value="" disabled>Escolha uma região</option>
                                <?php foreach($regioes as $regiao): ?>
                                    <option value="<?= $regiao['id_regiao'] ?>" <?= $regiao_id == $regiao['id_regiao'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($regiao['nome_regiao']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-section">
                            <label class="form-label" for="flocalizacao">Localização</label>
                            <input type="text" id="flocalizacao" name="flocalizacao" class="form-input" value="<?= htmlspecialchars($localizacao) ?>" required>
                        </div>
                        <div class="form-section">
                            <label class="form-label" for="fconcelho">Concelho</label>
                            <input type="text" id="fconcelho" name="fconcelho" class="form-input" value="<?= htmlspecialchars($concelho) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-section">
                            <label class="form-label">🅿️ Estacionamento</label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="festacionamento" value="1" <?= $estacionamento == 1 ? 'checked' : '' ?>> Sim
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="festacionamento" value="0" <?= $estacionamento == 0 ? 'checked' : '' ?>> Não
                                </label>
                            </div>
                        </div>
                        <div class="form-section">
                            <label class="form-label">🍽️ Restaurante</label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="frestaurante" value="1" <?= $restaurante == 1 ? 'checked' : '' ?>> Sim
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="frestaurante" value="0" <?= $restaurante == 0 ? 'checked' : '' ?>> Não
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="form-label" for="fdescricao">Descrição</label>
                        <textarea id="fdescricao" name="fdescricao" class="form-textarea" required><?= htmlspecialchars($descricao) ?></textarea>
                    </div>

                    <div class="form-acoes">
                        <button type="submit" class="btn-form-submit">💾 Guardar alterações</button>
                        <button type="reset" class="btn-form-reset">Repor</button>
                    </div>

                </form>
            </div>
        <?php endif; ?>

    </main>

    <?php require_once "includes/footer.php"; ?>
    <?php require_once "includes/janela_avisos.php"; ?>
</body>
</html>
