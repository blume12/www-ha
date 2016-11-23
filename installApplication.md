# Install application

## Init composer
1. Go to Tools -> Composer -> Init Composer ... 
2. Init composer
3. Go to the terminal and run the command 
    ```
    composer update
    ```

## Init Database (sqlite)
1. Go to the terminal and run the command:
    ```
    php src/initDb.php
    ```
    
    **Note:** If you run the statement again, the database will be init again. So the saved data will be lost. 
    
## Organize PhpStorm
1. Mark the follow folders as **excluded**:
    1. data
    2. vendor
2. Mark the follow folders as **resource folder**:
    1. public
    
3. Configure PHP Build-in-Server

    