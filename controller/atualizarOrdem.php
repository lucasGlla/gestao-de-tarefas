<?php
include_once("../config/conexao.php");

// Verificar se a requisição contém os parâmetros necessários
if (isset($_POST['id']) && isset($_POST['direction'])) {
    $tarefaId = (int) $_POST['id'];
    $direction = $_POST['direction'];

    // Consultar a ordem atual da tarefa
    $sql = "SELECT ordem FROM tarefas WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $tarefaId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $tarefa = $result->fetch_assoc();
        $ordemAtual = $tarefa['ordem'];

        // Determinar a nova posição com base na direção
        $novaOrdem = ($direction === 'up') ? $ordemAtual - 1 : $ordemAtual + 1;

        // Verificar se a nova posição existe
        $sqlAdjacente = "SELECT id FROM tarefas WHERE ordem = ?";
        $stmtAdjacente = $conexao->prepare($sqlAdjacente);
        $stmtAdjacente->bind_param("i", $novaOrdem);
        $stmtAdjacente->execute();
        $resultAdjacente = $stmtAdjacente->get_result();

        if ($resultAdjacente->num_rows > 0) {
            // Obter o ID da tarefa adjacente
            $tarefaAdjacente = $resultAdjacente->fetch_assoc();
            $idAdjacente = $tarefaAdjacente['id'];

            // Trocar as ordens das tarefas
            $conexao->begin_transaction();
            try {
                // Atualizar a ordem da tarefa adjacente
                $sqlUpdateAdjacente = "UPDATE tarefas SET ordem = ? WHERE id = ?";
                $stmtUpdateAdjacente = $conexao->prepare($sqlUpdateAdjacente);
                $stmtUpdateAdjacente->bind_param("ii", $ordemAtual, $idAdjacente);
                $stmtUpdateAdjacente->execute();

                // Atualizar a ordem da tarefa atual
                $sqlUpdateAtual = "UPDATE tarefas SET ordem = ? WHERE id = ?";
                $stmtUpdateAtual = $conexao->prepare($sqlUpdateAtual);
                $stmtUpdateAtual->bind_param("ii", $novaOrdem, $tarefaId);
                $stmtUpdateAtual->execute();

                // Confirmar as mudanças
                $conexao->commit();

                echo json_encode(['status' => true]);
            } catch (Exception $e) {
                // Reverter as mudanças em caso de erro
                $conexao->rollback();
                echo json_encode(['status' => false, 'msg' => 'Erro ao mover a tarefa.']);
            }
        } else {
            echo json_encode(['status' => false, 'msg' => 'Não é possível mover nesta direção.']);
        }
    } else {
        echo json_encode(['status' => false, 'msg' => 'Tarefa não encontrada.']);
    }
} else {
    echo json_encode(['status' => false, 'msg' => 'Dados incompletos.']);
}
?>