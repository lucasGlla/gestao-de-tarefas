<?php
include_once("../config/conexao.php");

// Consultar todas as tarefas e obter as maiores e menores ordens
$sql = "
    SELECT t.id, t.nome, t.custo, t.dataLimite, t.ordem,
        (SELECT MIN(ordem) FROM tarefas) AS menor_ordem,
        (SELECT MAX(ordem) FROM tarefas) AS maior_ordem
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
        if($tarefa['custo'] >= 1000){
        $dados .= "<tr id='tarefa-" . htmlspecialchars($tarefa['id']) . "'>";
        $dados .= "<td class='amarelo'>" . htmlspecialchars($tarefa['id']) . "</td>";
        $dados .= "<td class='amarelo'>" . htmlspecialchars($tarefa['nome']) . "</td>";
        $dados .= "<td class='amarelo'>" . htmlspecialchars($tarefa['custo']) . "</td>";
        $dados .= "<td class='amarelo'>" . htmlspecialchars($tarefa['dataLimite']) . "</td>";
        
        // Botões de ação
        $dados .= "<td class='amarelo'>
            <a href='#' onclick='editTarefa(" . htmlspecialchars($tarefa['id']) . ")'>
            <button class='open-modal' data-modal='modal-2'>
                <i class='fa-solid fa-pen-to-square'></i>
            </button></a> 
            <a href='./controller/deletarController.php?id=" . htmlspecialchars($tarefa['id']) . "' 
            onclick='return confirm(\"Tem certeza que deseja deletar?\")'>
                <i class='fa-solid fa-trash'></i>
            </a>";

        if ($tarefa['ordem'] > $tarefa['menor_ordem']) {
            $dados .= "<button onclick='moverTarefa(" . htmlspecialchars($tarefa['id']) . ", \"up\")'>
	<i class='fa-solid fa-chevron-up'></i>
</button>";
        }
        if ($tarefa['ordem'] < $tarefa['maior_ordem']) {
            $dados .= "<button onclick='moverTarefa(" . htmlspecialchars($tarefa['id']) . ", \"down\")'>
	<i class='fa-solid fa-chevron-down'></i>
</button>";
        }

        $dados .= "</td></tr>";
    } else{
        $dados .= "<tr id='tarefa-" . htmlspecialchars($tarefa['id']) . "'>";
        $dados .= "<td>" . htmlspecialchars($tarefa['id']) . "</td>";
        $dados .= "<td>" . htmlspecialchars($tarefa['nome']) . "</td>";
        $dados .= "<td>" . htmlspecialchars($tarefa['custo']) . "</td>";
        $dados .= "<td>" . htmlspecialchars($tarefa['dataLimite']) . "</td>";
        
        // Botões de ação
        $dados .= "<td>
            <a href='#' onclick='editTarefa(" . htmlspecialchars($tarefa['id']) . ")'>
            <button class='open-modal' data-modal='modal-2'>
                <i class='fa-solid fa-pen-to-square'></i>
            </button></a> 
            <a href='./controller/deletarController.php?id=" . htmlspecialchars($tarefa['id']) . "' 
            onclick='return confirm(\"Tem certeza que deseja deletar?\")'>
                <i class='fa-solid fa-trash'></i>
            </a>";

        if ($tarefa['ordem'] > $tarefa['menor_ordem']) {
            $dados .= "<button onclick='moverTarefa(" . htmlspecialchars($tarefa['id']) . ", \"up\")'>
	<i class='fa-solid fa-chevron-up'></i>
</button>";
        }
        if ($tarefa['ordem'] < $tarefa['maior_ordem']) {
            $dados .= "<button onclick='moverTarefa(" . htmlspecialchars($tarefa['id']) . ", \"down\")'>
	<i class='fa-solid fa-chevron-down'></i>
</button>";
        }

    }
    }
    $dados .= "</tbody></table>";

    $retorna = ['status' => true, 'dados' => $dados];
} else {
    $retorna = ['status' => false, 'msg' => "<p>Nenhum chamado encontrado!</p>"];
}

// Retorno em formato JSON
echo json_encode($retorna);
?>
