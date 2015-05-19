create database DBApp;
CREATE USER 'DBAppUser'@'localhost' IDENTIFIED BY 'zageSenha';
GRANT ALL PRIVILEGES ON DBApp.* TO 'DBAppUser'@'localhost';
