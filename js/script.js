function mostrarAno() {
	var ano = new Date().getFullYear();
	var rodape = document.getElementById("ano");
	if (rodape != null) {
		rodape.innerHTML = ano;
	}
}

function adicionarCarrinho(nome) {
	alert("Produto adicionado ao carrinho: " + nome);
}

mostrarAno();
