/**
 * Mostra o ano atual no rodapé.
 */
function mostrarAno() {
	var ano = new Date().getFullYear();
	var rodape = document.getElementById("ano");
	if (rodape != null) {
		rodape.innerHTML = ano;
	}
}

/**
 * Mostra o aviso do botão Comprar.
 *
 * @param {string} nome Nome do produto.
 */
function adicionarCarrinho(nome) {
	alert("Produto adicionado ao carrinho: " + nome);
}

mostrarAno();
