<?php
include "conecta.php";

/* Buscar organizações para o select */
$orgs = mysqli_query($conn, "SELECT id, nome FROM organizacao");

/* Inserção do usuário */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $perfil = $_POST["perfil"];
    $ativo = $_POST["ativo"];
    $organizacao_id = $_POST["organizacao_id"];

    $sql = "INSERT INTO usuario (nome, perfil, ativo, organizacao_id)
            VALUES ('$nome', '$perfil', '$ativo', '$organizacao_id')";

    if (mysqli_query($conn, $sql)) {
        echo "<p><strong>Usuário cadastrado com sucesso!</strong></p>";
    } else {
        echo "<p>Erro ao cadastrar usuário.</p>";
    }
}
?>

<h2>Cadastro de Usuário</h2>

<form method="post">
    <label>Nome:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Perfil:</label><br>
    <select name="perfil" required>
        <option value="">Selecione</option>
        <option value="organizador">Organizador</option>
        <option value="bilheteria">Bilheteria</option>
        <option value="financeiro">Financeiro</option>
        <option value="portaria">Portaria</option>
        <option value="admin">Admin</option>
    </select><br><br>

    <label>Ativo:</label><br>
    <select name="ativo">
        <option value="1">Sim</option>
        <option value="0">Não</option>
    </select><br><br>

    <label>Organização:</label><br>
    <select name="organizacao_id" required>
        <option value="">Selecione</option>
        <?php
        while ($org = mysqli_fetch_assoc($orgs)) {
            echo "<option value='".$org["id"]."'>".$org["nome"]."</option>";
        }
        ?>
    </select><br><br>

    <button type="submit">Cadastrar</button>
</form>

<br>
<a href="lista_usuario.php">Ver usuários cadastrados</a>
