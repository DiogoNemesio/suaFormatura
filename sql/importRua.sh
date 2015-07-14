#!/bin/bash


read -p "Digite a senha do usuário dbAppUser: " senha

mysql -u suaFormaturaUser --password="$senha" suaFormatura < 1_Localidade.sql
#mysql -u suaFormaturaUser --password="$senha" suaFormatura < 2_Bairros.sql
#mysql -u suaFormaturaUser --password="$senha" suaFormatura < 3.1_Logradouros_Maceio.sql
mysql -u suaFormaturaUser --password="$senha" suaFormatura < 3.2_Logradouros_Brasil.sql
