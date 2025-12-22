<?php
// Start the session
session_start();


?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="tela_comssario">
    <link rel="icon" href="src/img/favicon.ico">

    <title>Tela comissário</title>
    <link rel="stylesheet" href="../styles/home.css" />

    <link
      rel="stylesheet"
      type="text/css"
      href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css"
    />
    <link
      rel="stylesheet"
      type="text/css"
      href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css"
    />

    <script src="../scripts/home.js" defer></script>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <link href="styles/auth_new.css" rel="stylesheet">
  </head>
  <!--<body>
            
        <header class="header">
      <h1 class="title">OLÁ, <i class="contrast"><?php $_SESSION["login"]?> </i>!</h1>
      <button class="settings-button" id="settings-button">
        <i class="ph-fill ph-gear"></i>
      </button>

      <div class="dropdown-menu" id="dropdown">
        <h1 class="title">CONFIGURAÇÃO</h1>
        <button class="button">Alterar</button>
        <button class="button">Criar evento</button>
        <button class="button">Entrar em um evento</button>
      </div>
    </header>-->
    <?php
    include_once('conecta.php');
    ?>
    <main role="main">
      <div class="main">
        <div class="container">
           
    <?php
    
    
         /* if(isset($_POST['nome']))
          {
            if(isset($_POST['id']))
            {
              $id = $_POST['id'];
              $organizacao_id = $_POST['organizacao_id'];
              $nome = $_POST['nome'];
              $dados = $_POST['dados'];
              $regra_comissao=$_POST['regra_comissao'];
              $sql = "UPDATE comissario SET organizacao_id = $organizacao_id, 
              nome = '$nome', dados = '$dados', regra_comissao='$regra_comissao' where id = $id";
              mysqli_query($bancodedados,$sql);

            }
            else {
              $id = $_POST['id'];
              $organizacao_id = $_POST['organizacao_id'];
              $nome = $_POST['nome'];
              $dados = $_POST['dados'];
              $regra_comissao=$_POST['regra_comissao'];
              $sql = "INSERT INTO comissario(organizacao_id,nome,dados,regra_comissao)
              values('$organizacao_id','$nome','$dados', '$regra_comissao')";
              mysqli_query($bancodedados,$sql);
                
            }
           

          }*/
          
        ?>
        <br>
          <h2 class="title"> Dados Comissário</h2>

          
        </div>
        
        <div class="tabela">
              <table class="table table-striped table-bordered">
        <thead class="cabecalho">
            <tr>
              <th>Organização</th>
              <th>Dados</th>
              <th>Valor a receber</th><!--ver valor na tabela repasse_comissao--> 
              <th>regra de comissao</th>
              
            </tr>
        </thead>
        <tbody>
            
          <?php
            echo "<p>Bem-vindo " .$_SESSION["login"]. ",</p>";//pegar sessao_start em auth.php
            $sql="SELECT comissario.*, organizacao.nome AS organiza, repasse_comissao.valor AS repasse FROM comissario, organizacao, repasse_comissao
            WHERE comissario.organizacao_id = organizacao.id AND repasse_comissao.comissario_id = comissario.id AND comissario.id = 1";//pegar comissario.id por uma varialve php
            $resultado=mysqli_query($bancodedados, $sql);
          if ($linha = mysqli_fetch_array($resultado))
          {
            echo "<tr><td>".$linha['organiza']."</td>";
            echo "<td>".$linha['dados']."</td>";
            echo "<td>".$linha['repasse']."</td>";
            echo "<td>".$linha['regra_comissao']."</td></tr>";  
          }
           /* $sql="SELECT comissario.*, organizacao.nome AS organiza
            FROM comissario, organizacao WHERE comissario.organizacao_id = organizacao.id";
           
            $resultado = mysqli_query($bancodedados,$sql);
           while($linha = mysqli_fetch_array($resultado))
          {
          
            echo "<tr><td>".$linha['nome']."</td>";
            echo "<td>".$linha['organiza']."</td>";
            echo "<td>".$linha['dados']."</td>";
            echo "<td>".$linha['regra_comissao']."</td></tr>";

            }
            */
          ?>
        </tbody>  
        </table>
          </div>
          <div><a href="link_venda.php" class="button">Nova Venda</a></div>
        </div>
        
    </main>
  </body>
</html>