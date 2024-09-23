# Instalação e Execução da Aplicação

## Requisitos
- Visual Studio Code (VSCode)

## Instalação do PHP
1. Abra o terminal.
2. Execute os seguintes comandos para instalar o PHP 7.4:

    ```bash
    sudo apt-get update
    sudo add-apt-repository ppa:ondrej/php
    sudo apt -y install php7.4
    php --version
    ```

## Instalação do Composer
1. Baixe e instale o Composer:
    ```bash
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer
    composer --version
    ```

2. **Resolvendo possíveis erros**:
    - Caso ocorra um erro como `could not find drive (SQL: PRAGMA foreign_keys = on;)` durante a execução de `php artisan migrate`, modifique o arquivo `php.ini`:
      - Remova o `;` da linha `extension:pdo_sqlite`.
      - O arquivo `php.ini` pode ser localizado com o comando:
        
        ```bash
        php -i | grep 'php.ini'
        ```

## Instalação do MySQL Workbench
1. Acesse [MySQL Workbench](https://www.mysql.com/).
2. Faça o download do **MySQL APT Repository** para Ubuntu.
3. Instale o pacote com os seguintes comandos:

    ```bash
    cd ~/Downloads/
    sudo dpkg -i mysql-apt-config_0.8.32-1_all.deb
    sudo apt update
    sudo apt install mysql-server
    ```

4. Defina a senha de acesso durante a instalação e selecione **Use Strong Password Encryption**.
5. Após a instalação, execute:

    ```bash
    sudo mysql_secure_installation
    ```

    Responda as perguntas conforme abaixo:
    - **VALIDATE PASSWORD COMPONENT**: No
    - **Change the password for root?**: No
    - **Reload privilege tables now?**: Yes

6. Instale o MySQL Workbench:
    ```bash
    sudo apt install mysql-workbench-community
    ```

## Configurando o Banco de Dados

1. Abra o MySQL Workbench e configure uma nova conexão:
    - **Host Name**: Deixe como está.
    - **User Name**: root.
    - **Password**: Configure e salve a senha do usuário root.
2. Configure a base de dados da aplicação:
    - No arquivo `brix_investimentos_API/config/database.php`, configure a conexão no arquivo `.env`:

      ```env
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE="nome_do_banco"
      DB_USERNAME="root"
      DB_PASSWORD="senha_root"
      ```

3. Verifique se a extensão `pdo_mysql` está ativa:

    ```bash
    php -r "var_dump(extension_loaded('pdo_mysql'));"
    ```

    O retorno deve ser `bool(true)`.

4. Execute a migração:

    ```bash
    php artisan migrate
    ```

## Instalação do Node.js

1. Acesse o site oficial do [Node.js](https://nodejs.org/pt) e faça o download para Linux.
2. Extraia o arquivo e configure o caminho no terminal:

    ```bash
    cd ~/Downloads/
    sudo tar -xJvf node-"versão".tar.xz -C /usr/local/lib
    cd ~
    code .bashrc
    ```

3. Adicione o caminho ao final do arquivo `.bashrc`:

    ```bash
    export PATH=$PATH:/usr/local/lib/node-"versão"/bin
    ```

4. Verifique a versão instalada:

    ```bash
    node -v
    npm -v
    ```

## Configurações Finais

1. Abra a pasta `Trabalho_BD-master` no VSCode e instale as dependências:

    ```bash
    npm install
    npm install vuetify@next @mdi/font -S
    ```

2. Abra a pasta `brix_investimentos_API` no VSCode e inicie o servidor:

    ```bash
    php artisan serve
    ```

3. Na aba com a pasta `Trabalho_BD-master`, inicie o servidor de desenvolvimento:

    ```bash
    npm run dev
    ```

4. Acesse o endereço gerado no terminal (algo como `localhost:36004`) para usar a aplicação final.

