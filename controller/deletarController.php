<?php 

if(!empty($_GET['id'])){
    include_once('../config/conexao.php');

    $id = $_GET['id'];

    $sqlSelect = "SELECT * FROM tarefas WHERE id=$id";

    $result = $conexao->query($sqlSelect);

    if($result->num_rows > 0){
        $sqlDelete = "DELETE FROM tarefas WHERE id=$id";
        $resultDelete = $conexao->query($sqlDelete);
}
}
    header('Location: ../index.php');


?>