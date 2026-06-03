# 🏖️ Praias de Portugal

Aplicação web para descobrir, gerir e explorar praias do litoral português, organizada por regiões.

---

## 📋 Descrição

Sistema CRUD completo desenvolvido em **PHP + MySQL** que permite listar praias por região, ver os seus detalhes, adicionar novas, editar, eliminar e gerir fotografias.

---

## ✨ Funcionalidades

- 🗺️ **Explorar por região** — praias organizadas por regiões (Algarve, Costa Vicentina, etc.)
- 🔍 **Pesquisa** — barra de pesquisa integrada em todas as páginas
- 📄 **Detalhe de praia** — localização, concelho, estacionamento, restaurante e descrição
- 📷 **Galeria de fotos** — upload múltiplo, pré-visualização e lightbox
- ➕ **Adicionar praia** — formulário completo com seleção de região
- ✏️ **Editar praia** — atualização de todos os campos
- 🗑️ **Eliminar praia** — com confirmação antes de apagar

---

## 🗂️ Estrutura de ficheiros

```
├── index.php               # Página inicial — listagem de regiões
├── detalhes.php            # Praias de uma região
├── praia.php               # Detalhe de uma praia
├── adicionar.php           # Formulário para adicionar praia
├── atualizar.php           # Formulário para editar praia
├── eliminar.php            # Eliminar praia
├── fotos.php               # Gestão de fotos de uma praia
├── template.php            # Template base para novas páginas
├── estilos.css             # Folha de estilos principal
├── uploads/
│   └── praias/             # Fotos enviadas pelos utilizadores
└── includes/
    ├── funcoes.php         # Funções auxiliares (conexão BD, etc.)
    ├── head.php            # <head> HTML partilhado
    ├── header.php          # Cabeçalho do site
    ├── footer.php          # Rodapé do site
    ├── pesquisa_e_nav.php  # Barra de pesquisa e navegação
    └── janela_avisos.php   # Modal de avisos/confirmações
```

---

## 🛠️ Tecnologias

| Camada     | Tecnologia          |
|------------|---------------------|
| Backend    | PHP (PDO)           |
| Base de dados | MySQL / MariaDB  |
| Frontend   | HTML5, CSS3, JS     |
| Upload     | PHP `move_uploaded_file` |

---

## 🗄️ Base de dados

### Tabelas necessárias

```sql
CREATE TABLE regioes (
    id_regiao   INT AUTO_INCREMENT PRIMARY KEY,
    nome_regiao VARCHAR(100) NOT NULL
);

CREATE TABLE praias (
    id_praia              INT AUTO_INCREMENT PRIMARY KEY,
    nome_praia            VARCHAR(150) NOT NULL,
    localizacao           VARCHAR(150),
    concelho              VARCHAR(100),
    descricao             TEXT,
    possui_estacionamento TINYINT(1) DEFAULT 0,
    possui_restaurante    TINYINT(1) DEFAULT 0,
    id_regiao             INT,
    FOREIGN KEY (id_regiao) REFERENCES regioes(id_regiao)
);

CREATE TABLE fotos_praias (
    id_foto        INT AUTO_INCREMENT PRIMARY KEY,
    id_praia       INT NOT NULL,
    nome_ficheiro  VARCHAR(255) NOT NULL,
    ordem          INT DEFAULT 1,
    FOREIGN KEY (id_praia) REFERENCES praias(id_praia) ON DELETE CASCADE
);
```

---

## ⚙️ Instalação

1. **Clonar o repositório**
   ```bash
   git clone https://github.com/teu-utilizador/praias-portugal.git
   ```

2. **Mover para a pasta do servidor web** (ex: `htdocs` no XAMPP ou `www` no WAMP)

3. **Criar a base de dados** e executar o SQL acima

4. **Configurar a ligação** em `includes/funcoes.php`:
   ```php
   function criar_conexao() {
       return new PDO("mysql:host=localhost;dbname=praias;charset=utf8", "utilizador", "password");
   }
   ```

5. **Criar a pasta de uploads** e garantir permissões de escrita:
   ```bash
   mkdir -p uploads/praias
   chmod 755 uploads/praias
   ```

6. Abrir no browser: `http://localhost/praias-portugal`

---

## 📸 Upload de fotos

- Formatos suportados: **JPG, PNG, WEBP**
- Tamanho máximo por ficheiro: **5 MB**
- Suporte a upload múltiplo com pré-visualização
- Drag & drop disponível

---

## 📄 Licença

Projeto de uso livre para fins académicos e pessoais.
