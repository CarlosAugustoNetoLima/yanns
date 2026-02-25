# Projeto YANNS

Este é o repositório do projeto **YANNS**, focado no design de interfaces e componentes de uma aplicação moderna para web.

## 🛠 Tecnologias Utilizadas

Este projeto foi construído utilizando uma abordagem leve, sem frameworks pesados no Back-End.

*   **Linguagem Principal:** PHP (puro)
*   **Frontend:** HTML5, CSS3, e JavaScript Vanilla. (*Não* utiliza frameworks CSS como Tailwind ou Bootstrap).
*   **Arquitetura:** Componentização baseada em funções PHP contidas na pasta `app/Views/Components/` e renderizadas nas páginas da pasta `app/Views/Pages/`.
*   **Estilizações:** Arquitetura CSS personalizada, focada em fidelidade com o design base no Figma da aplicação (variáveis customizadas, arquivos isolados por componente e tipografia avançada).
*   **Roteamento:** Implementado de forma simples, sendo o ponto de entrada principal o arquivo `public/index.php`.

## ⚙️ Como executar localmente o projeto

Não é necessário instalar ferramentas complexas (como Docker, Node Modules ou Composer). Você só precisará do PHP em sua máquina.

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/CarlosAugustoNetoLima/YANNS.git
    cd YANNS
    ```

2.  **Inicie o servidor embutido do PHP apontando para a pasta `public`:**
    ```bash
    php -S localhost:8000 -t public
    ```

3.  **Acesse no seu navegador:**
    Abra `http://localhost:8000`

## 📁 Estrutura de Diretórios (Resumo)

*   **`app/Views/Components/`**: Contém todos os componentes visuais reutilizáveis em PHP (Header, Footer, Botões, Player de Vídeo, etc).
*   **`app/Views/Pages/`**: Contém as páginas principais que englobam os componentes (Home, Styleguide, etc).
*   **`public/`**: Pasta raiz do servidor web. Contém o `index.php` principal, arquivos de CSS (`public/assets/css`), JavaScript (`public/assets/js`), imagens (`public/assets/img`) e vídeos e ícones SVG.
*   **`public/styleguide.php`**: Página auxiliar que documenta como foi estruturada a parte visual do projeto (uma vitrine que lista todos os botões, inputs, modais e componentes de vídeo feitos).

## ⚠️ Observações e Avisos

*   **Vídeos Pesados**:  O repositório contém arquivos de mídia em `/public/assets/video/` que, dependendo do crescimento do projeto, poderão exceder o tamanho padrão otimizado de envio do Git (>50MB). Se planeja inserir mais vídeos futuramente, é recomendado utilizar o [Git LFS (Large File Storage)](https://git-lfs.github.com/).
