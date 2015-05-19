#!/bin/bash


read -p "Digite a senha do usuário dbAppUser: " senha

mysql -u DBAppUser --password="$senha" DBApp < 1_Localidade.sql
mysql -u DBAppUser --password="$senha" DBApp < 2_Bairros.sql
mysql -u DBAppUser --password="$senha" DBApp < 3.1_Logradouros_Maceio.sql
