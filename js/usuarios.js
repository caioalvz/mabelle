// Validação no navegador (só ajuda o usuário; quem decide de verdade é o PHP)

var formulario = document.getElementById("form-usuario");

// Mostra (ou limpa) a mensagem de erro embaixo de um campo
function mostrarErro(campo, texto) {
	var aviso = document.getElementById("erro-" + campo.name);
	if (aviso == null) {
		aviso = document.createElement("span");
		aviso.id = "erro-" + campo.name;
		aviso.className = "erro-campo";
		campo.parentNode.insertBefore(aviso, campo.nextSibling);
	}
	aviso.textContent = texto;
}

if (formulario != null) {
	formulario.addEventListener("submit", function (evento) {
		var temErro = false;

		var nome = document.getElementById("nome");
		var email = document.getElementById("email");
		var setor = document.getElementById("setor_id");
		var perfil = document.getElementById("perfil_id");

		// nome
		mostrarErro(nome, "");
		if (nome.value.trim().length < 3) {
			mostrarErro(nome, "O nome deve ter ao menos 3 caracteres.");
			temErro = true;
		}

		// e-mail (precisa ter @ e um ponto depois)
		mostrarErro(email, "");
		if (email.value.indexOf("@") < 1 || email.value.indexOf(".", email.value.indexOf("@")) == -1) {
			mostrarErro(email, "Informe um e-mail válido.");
			temErro = true;
		}

		// setor e perfil
		mostrarErro(setor, "");
		if (setor.value == "") {
			mostrarErro(setor, "Selecione um setor.");
			temErro = true;
		}
		mostrarErro(perfil, "");
		if (perfil.value == "") {
			mostrarErro(perfil, "Selecione um perfil.");
			temErro = true;
		}

		// se tem erro, não envia o formulário
		if (temErro) {
			evento.preventDefault();
		}
	});
}
