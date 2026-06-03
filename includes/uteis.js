function confirmaEliminar(nome_praia,id){

    console.log(12)
    let mensagem = "Tens a certeza que pretende eliminar a praia?" + nome_praia + "?";

    let href = "eliminar.php?id_praia=" + id;

    document.getElementById("textoAviso_id").innerText = mensagem;

    document.getElementById("aviso_ok_id").href = href;

    document.getElementById("janelaAvisos_id").style.display = "flex";

}

function removerJanelaAviso(){ 
    document.getElementById("janelaAvisos_id").style.display = "none";
}