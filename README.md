# Настройка проекта

1. В корне проекта создать файл config.php (или использовать шаблон config.sample.php)
2. Прописать внутри файла следущие константы:
* HOST - хост базы данных
* USERNAMEDB - логин базы данных
* PASSWORDDB - пароль от базы данных
* DBNAME - название базы данных
* LOGIN - логин для входа в кабинет
* PASSWORDCABINET - пароль для входа в кабинет
* URLFORREPEAT - url для повторений;
* PATHFORLOG - путь к логу;
* LOGNAME - название логов;
3. Создать таблицы processing_accounts, settings и wallets в базе данных скриптами:
* CREATE TABLE `processing_accounts` (
 `code` int(11) NOT NULL AUTO_INCREMENT,
 `uid` bigint(15) NOT NULL,
 `name` varchar(255) NOT NULL,
 `login` varchar(255) NOT NULL,
 `password` varchar(255) NOT NULL,
 `keyt` varchar(20) NOT NULL,
 PRIMARY KEY (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8
* CREATE TABLE `settings` (
 `inc` int(11) NOT NULL AUTO_INCREMENT,
 `form_instant` int(11) DEFAULT NULL,
 `notice_url` varchar(255) DEFAULT NULL,
 `processing_url` varchar(255) DEFAULT NULL,
 `processing_program` int(11) DEFAULT NULL,
 `processing_skeys` varchar(255) DEFAULT NULL,
 `min_balance` decimal(14,2) NOT NULL DEFAULT '0.00',
 `webhook_url` varchar(255) NOT NULL,
 `qiwi_acq_percent` decimal(5,2) NOT NULL,
 PRIMARY KEY (`inc`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8
* CREATE TABLE `wallets` (
 `code` int(11) NOT NULL,
 `wallet_phone` varchar(15) NOT NULL,
 `wallet_token` varchar(255) NOT NULL,
 `wallet_token_valid_date` date NOT NULL,
 `processing_account` int(11) NOT NULL,
 `card_token` varchar(255) NOT NULL,
 `hook_id` varchar(255) NOT NULL,
 `secret_key` varchar(255) NOT NULL,
 `last_error` varchar(255) DEFAULT NULL,
 `removed` tinyint(1) NOT NULL DEFAULT '0',
 PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
4. Создать в корне проекта директорию tmp и дать права на запись.