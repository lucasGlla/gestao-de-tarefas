<?php
include_once("../config/conexao.php");

// Consultar todas as tarefas
$sql = "
    SELECT t.id, t.nome, t.custo, t.dataLimite
    FROM tarefas t
    ORDER BY t.ordem ASC";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$result_tarefas = $stmt->get_result();

// Iniciar a variável de retorno
$dados = '';

// Verificação se a consulta retornou resultados
if ($result_tarefas && $result_tarefas->num_rows > 0) {
    $dados = "<table class='content-table'>";
    $dados .= "<thead><tr><td>ID</td><td>Nome</td><td>Custo</td><td>Data limite</td><td>Ações</td></tr></thead><tbody>";

    while ($tarefa = $result_tarefas->fetch_assoc()) {
        $dados .= "<tr id='tarefa-" . htmlspecialchars($tarefa['id']) . "'>";
        $dados .= "<td>" . htmlspecialchars($tarefa['id']) . "</td>";
        $dados .= "<td>" . htmlspecialchars($tarefa['nome']) . "</td>";
        $dados .= "<td>" . htmlspecialchars($tarefa['custo']) . "</td>";
        $dados .= "<td>" . htmlspecialchars($tarefa['dataLimite']) . "</td>";
        $dados .= "<td>
            <a href='#' onclick='editTarefa(" . htmlspecialchars($tarefa['id']) . ")'>
            <button class='open-modal' data-modal='modal-2'>Editar</button></a> 
            <a href='./controller/deletarController.php?id=" . htmlspecialchars($tarefa['id']) . "' 
            onclick='return confirm(\"Tem certeza que deseja deletar?\")'>Excluir</a>
            <button onclick='moverTarefa(" . htmlspecialchars($tarefa['id']) . ", \"up\")'>Subir</button>
            <button onclick='moverTarefa(" . htmlspecialchars($tarefa['id']) . ", \"down\")'>Descer</button>
            </td>";
        $dados .= "</tr>";
    }
    $dados .= "</tbody></table>";

    $retorna = ['status' => true, 'dados' => $dados];
} else {
    $retorna = ['status' => false, 'msg' => "<p>Nenhum chamado encontrado!</p>"];
}

// Retorno em formato JSON
echo json_encode($retorna);
?>
