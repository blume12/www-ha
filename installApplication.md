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
4. Choose the SQL Dialect
    1. File -> Settings -> Languages & Frameworks -> SQL Dialects
    2. Choose for all in the Project sqlite.


## Add Less compiler
1. Download LESS CSS Compiler (https://plugins.jetbrains.com/plugin/7059)
2. Add the zip to the plugins:
    1. Go to File -> Settings -> Plugins
    2. Click "Install plugin from Disk"
    3. Select the zip and install this. 
    4. Restart PhpStorm
3. Go to File -> Settings -> Other Settings -> LESS Profiles
4. Add a new LESS Profile
    1. Less source directory: The css folder of this project (public/css/)
    2. Add a css output directory: the css folder of this project (public/css/)
    3. Check at "Compile automatically on save"
    4. No check at "Compress CSS output"
    
**Note:** The css files must add to the git. So it can be used without installing winLess.
    