const openButtons = document.querySelectorAll('.open-modal');

openButtons.forEach(button => {
    button.addEventListener('click', () => {
        const modalId = button.getAttribute('data-modal');
        const modal = document.getElementById(modalId);

        modal.showModal();
    });
});

const closeButtons = document.querySelectorAll('.close-modal');

closeButtons.forEach(button => {
    button.addEventListener('click', () => {
        const modalId = button.getAttribute('data-modal');
        const modal = document.getElementById(modalId);

        modal.close();
    });
});

function exibirAlerta(msg, tipo = "success") {
    const msgErro = document.getElementById("msgAlertaErro");
    const msgAlerta = document.getElementById("msgAlerta");

    if (tipo === "error") {
        msgErro.innerHTML = msg;
        msgErro.classList.add("alert-error");
        msgErro.style.display = "block";
        msgAlerta.style.display = "none";

        setTimeout(() => {
            msgErro.style.display = "none";
        }, 3000); // 3 segundos
    } else {
        msgAlerta.innerHTML = msg;
        msgAlerta.classList.add("alert-success");
        msgAlerta.style.display = "block";
        msgErro.style.display = "none";

        setTimeout(() => {
            msgAlerta.style.display = "none";
        }, 3000); // 3 segundos
    }
}

// Listar tarefas
const listarTarefas = async () => {
    const dados = await fetch("../controller/listarController.php");
    const resposta = await dados.json();

    if (!resposta['status']) {
        exibirAlerta(resposta['msg'], "error");
    } else {
        const conteudo = document.querySelector(".listar-tarefas");

        if (resposta['dados']) {
            conteudo.innerHTML = resposta['dados'];
        }
    }
}

listarTarefas();

// Cadastrar tarefa
const adTarefaForm = document.getElementById('tarefa-form');

if (adTarefaForm) {
    adTarefaForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const dadosForm = new FormData(adTarefaForm);

        const dados = await fetch("../controller/adicionarController.php", {
            method: "POST",
            body: dadosForm
        });

        const resposta = await dados.json();
        console.log(resposta);

        if (!resposta['status']) {
            exibirAlerta(resposta['msg'], "error");
        } else {
            exibirAlerta(resposta['msg'], "success");

            adTarefaForm.reset();
            listarTarefas();
            fecharModal("modal-1");
        }
    });
}

// Recuperar dados para editar a tarefa
async function editTarefa(id) {
    const dados = await fetch('../controller/visualizarController.php?id=' + id);
    const resposta = await dados.json();
    console.log(resposta);

    if (!resposta['status']) {
        exibirAlerta(resposta['msg'], "error");
    } else {
        document.getElementById('editIdTarefa').value = resposta['dados'].id;
        document.getElementById('editNomeTarefa').value = resposta['dados'].nome;
        document.getElementById('editCustoTarefa').value = resposta['dados'].custo;
        document.getElementById('editDataLimiteTarefa').value = resposta['dados'].dataLimite;

        const modal = document.getElementById('modal-2');
        if (modal) {
            modal.showModal();
        }
    }
}

// Editar tarefa
const editFormTarefa = document.getElementById("editTarefa-form");
if (editFormTarefa) {
    editFormTarefa.addEventListener("submit", async (e) => {
        e.preventDefault();

        const dadosForm = new FormData(editFormTarefa);

        const dados = await fetch("../controller/editarController.php", {
            method: "POST",
            body: dadosForm
        });

        const resposta = await dados.json();

        if (!resposta['status']) {
            exibirAlerta(resposta['msg'], "error");
        } else {
            exibirAlerta(resposta['msg'], "success");

            editFormTarefa.reset();
            listarTarefas();
            fecharModal("modal-2");
        }
    });
}

// Apagar tarefa
async function apagarTarefa(id) {
    var confirmar = confirm("Tem certeza que deseja apagar essa Tarefa?");

    if (confirmar) {
        try {
            const dados = await fetch('../controller/deletarController.php?id=' + id);
            
            if (!dados.ok) {
                throw new Error("Erro ao comunicar com o servidor.");
            }
            
            const resposta = await dados.json();

            if (!resposta['status']) {
                exibirAlerta(resposta['msg'], "error");
            } else {
                exibirAlerta(resposta['msg'], "success");
                listarTarefas(); // Atualiza a lista de tarefas
            }
        } catch (error) {
            exibirAlerta("Ocorreu um erro ao tentar apagar a tarefa: " + error.message, "error");
        }
    }
}

// Fechar modal pelo ID
function fecharModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.close();
    }
}

// Mover tarefa
function moverTarefa(id, direction) {
    $.ajax({
        url: './controller/atualizarOrdem.php',
        method: 'POST',
        data: { id: id, direction: direction },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.status) {
                listarTarefas();
            } else {
                alert(result.msg);
            }
        }
    });
}
