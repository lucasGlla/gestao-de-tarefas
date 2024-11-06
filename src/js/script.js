const openButtons = document.querySelectorAll('.open-modal');

openButtons.forEach(button => {
    button.addEventListener('click',() => {
        const modalId = button.getAttribute('data-modal');
        const modal = document.getElementById(modalId);

        modal.showModal();
    });
});

const closeButtons = document.querySelectorAll('.close-modal');

closeButtons.forEach(button =>{
    button.addEventListener('click',() => {
        const modalId = button.getAttribute('data-modal');
        const modal = document.getElementById(modalId);

        modal.close();
    });
});

//listar tarefas

const listarTarefas = async () => {
    const dados = await fetch("../controller/listarController.php");
    const resposta = await dados.json();

    if(!resposta['status']){
        document.getElementById("msgAlerta").innerHTML = resposta['msg'];
    } else {
        const conteudo = document.querySelector(".listar-tarefas");
        
        if (resposta['dados']) {
            conteudo.innerHTML = resposta['dados'];
        }
    }
}

listarTarefas();

//cadastrar tarefa

const adTarefaForm =  document.getElementById('tarefa-form');


// Somente acessa o IF quando existir o SELETOR "tarefa-form"
if(adTarefaForm){
    adTarefaForm.addEventListener("submit",async(e) => {
        // Não permitir a atualização da pagina
        e.preventDefault();

        const dadosForm = new FormData(adTarefaForm);

        const dados = await fetch("../controller/adicionarController.php",{
            method: "POST",
            body: dadosForm
        });

        const resposta = await dados.json();
        console.log(resposta);

        if(!resposta['status']){
            const msgErro = document.getElementById("msgAlertaErro");
                msgErro.innerHTML = resposta['msg'];
                msgErro.classList.add("alert-error");
                msgErro.style.display = "block";

                const msgAlerta = document.getElementById("msgAlerta");
                msgAlerta.innerHTML = "";
                msgAlerta.classList.remove("alert-success");
                msgAlerta.style.display = "none";
            } else {
                const msgErro = document.getElementById("msgAlertaErro");
                msgErro.innerHTML = "";
                msgErro.classList.remove("alert-error");
                msgErro.style.display = "none";

                const msgAlerta = document.getElementById("msgAlerta");
                msgAlerta.innerHTML = resposta['msg'];
                msgAlerta.classList.add("alert-success");
                msgAlerta.style.display = "block";

                adTarefaForm.reset();
                listarTarefas();

                const modal = document.getElementById("modal-1");
                if (modal) {
                    modal.close(); // Fecha o modal
                }
            }
    })
}

// Recuperar dados para editar a Tarefa

async function editTarefa(id){
    const dados = await fetch('../controller/visualizarController.php?id=' + id);
    const resposta = await dados.json();
    console.log(resposta);

    if(!resposta['status']){
        document.getElementById("msgAlerta").innerHTML = resposta['msg'];
    } else{
     
        
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
if(editFormTarefa){
    editFormTarefa.addEventListener("submit",async(e) => {
        e.preventDefault();

        const dadosForm = new FormData(editFormTarefa);

        const dados = await fetch("../controller/editarController.php",{
            method: "POST",
            body: dadosForm
        });

        const resposta = await dados.json();

        if(!resposta['status']){
            const msgErro = document.getElementById("msgAlertaErroEdit");
            msgErro.innerHTML = resposta['msg'];
            msgErro.classList.add("alert-error");
            msgErro.style.display = "block";

            const msgAlerta = document.getElementById("msgAlerta");
            msgAlerta.innerHTML = "";
            msgAlerta.classList.remove("alert-success");
            msgAlerta.style.display = "none";
        } else{
            const msgErro = document.getElementById("msgAlertaErroEdit");
                msgErro.innerHTML = "";
                msgErro.classList.remove("alert-error");
                msgErro.style.display = "none";

                const msgAlerta = document.getElementById("msgAlerta");
                msgAlerta.innerHTML = resposta['msg'];
                msgAlerta.classList.add("alert-success");
                msgAlerta.style.display = "block";

                editFormTarefa.reset();
                listarTarefas();

                const modal = document.getElementById("modal-2");
                if (modal) {
                    modal.close(); // Fecha o modal
                }
        }
    })
}

// Apagar Tarefa
async function apagarTarefa(id) {

    var confirmar = confirm("Tem certeza que deseja apagar essa Tarefa?");

    if(confirmar == true){
        const dados = await fetch('../controller/deletarController.php?id=' + id);
        const resposta = await dados.json();
    
        if(!resposta['status']){
            const msgErro = document.getElementById("msgAlertaErro");
            msgErro.innerHTML = resposta['msg'];
            msgErro.classList.add("alert-error");
            msgErro.style.display = "block";

            const msgAlerta = document.getElementById("msgAlerta");
            msgAlerta.innerHTML = "";
            msgAlerta.classList.remove("alert-success");
            msgAlerta.style.display = "none";
        } else{
            const msgErro = document.getElementById("msgAlertaErro");
            msgErro.innerHTML = "";
            msgErro.classList.remove("alert-error");
            msgErro.style.display = "none";

            const msgAlerta = document.getElementById("msgAlerta");
            msgAlerta.innerHTML = resposta['msg'];
            msgAlerta.classList.add("alert-success");
            msgAlerta.style.display = "block";

            listarTarefas();
        }
    }
}

function moverTarefa(id, direction) {
    $.ajax({
        url: './controller/atualizarOrdem.php',
        method: 'POST',
        data: { id: id, direction: direction },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.status) {
                // Atualize a tabela
                atualizarTabela();
            } else {
                alert(result.msg);
            }
        }
    });
}

function atualizarTabela() {
    $.ajax({
        url: './controller/listarController.php',
        method: 'GET',
        success: function(response) {
            const result = JSON.parse(response);
            if (result.status) {
                $('#tabelaTarefas').html(result.dados);
            } else {
                $('#tabelaTarefas').html(result.msg);
            }
        }
    });
}
