# Настройка проекта

1. В корне проекта создать файл config.php (или использовать шаблон config.sample.php)
2. Прописать внутри файла следущие константы:
* MYSQL_HOST - хост базы данных
* MYSQL_USERNAME - логин базы данных
* MYSQL_PASSWORD - пароль от базы данных
* MYSQL_NAME - название базы данных
* URL_FOR_REPEAT - url для повторений;
* PATH_FOR_LOG - путь к логу;
* LOG_NAME - название логов;
3. Создать таблицы income_webhooks, income_webhooks_archive, processing_accounts, settings, users и wallets в базе данных скриптами:
* CREATE TABLE `income_webhooks` (
    `inc` int NOT NULL AUTO_INCREMENT,
    `hook_all` text NOT NULL,
    `hook_txnId` varchar(45) NOT NULL,
    `hook_date` datetime NOT NULL,
    `hook_errorCode` int NOT NULL,
    `hook_personId` varchar(45) NOT NULL,
    `hook_sum` decimal(14,2) NOT NULL,
    `next_operation` varchar(45) NOT NULL,
    `account_balance` decimal(14,2) DEFAULT NULL,
    `dkcp_sum` decimal(14,2) DEFAULT NULL,
    `dkcp_fields` text,
    `dkcp_result` int DEFAULT NULL,
    `dkcp_result_text` varchar(255) DEFAULT NULL,
    `dkcp_transact` varchar(25) DEFAULT NULL,
    `user` int NOT NULL DEFAULT '1',
    PRIMARY KEY (`inc`)
  ) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8
* CREATE TABLE `income_webhooks_archive` (
    `inc` int NOT NULL AUTO_INCREMENT,
    `hook_all` text NOT NULL,
    `hook_txnId` varchar(45) NOT NULL,
    `hook_date` datetime NOT NULL,
    `hook_errorCode` int NOT NULL,
    `hook_personId` varchar(45) NOT NULL,
    `hook_sum` decimal(14,2) NOT NULL,
    `next_operation` varchar(45) NOT NULL,
    `account_balance` decimal(14,2) DEFAULT NULL,
    `dkcp_sum` decimal(14,2) DEFAULT NULL,
    `dkcp_fields` text,
    `dkcp_result` int DEFAULT NULL,
    `dkcp_result_text` varchar(255) DEFAULT NULL,
    `dkcp_transact` varchar(25) DEFAULT NULL,
    `user` int NOT NULL DEFAULT '1',
    PRIMARY KEY (`inc`)
  ) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8
* CREATE TABLE `processing_accounts` (
    `code` int NOT NULL AUTO_INCREMENT,
    `uid` bigint NOT NULL,
    `name` varchar(255) NOT NULL,
    `login` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `keyt` varchar(20) NOT NULL,
    `user` int NOT NULL DEFAULT '1',
    PRIMARY KEY (`code`)
  ) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8
* CREATE TABLE `settings` (
    `inc` int NOT NULL AUTO_INCREMENT,
    `form_instant` int DEFAULT NULL,
    `notice_url` varchar(255) DEFAULT NULL,
    `processing_url` varchar(255) DEFAULT NULL,
    `processing_program` int DEFAULT NULL,
    `processing_skeys` varchar(255) DEFAULT NULL,
    `min_balance` decimal(14,2) NOT NULL DEFAULT '0.00',
    `webhook_url` varchar(255) NOT NULL,
    `qiwi_acq_percent` decimal(5,2) NOT NULL,
    PRIMARY KEY (`inc`)
  ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8
* CREATE TABLE `users` (
    `code` int NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `login` varchar(255) NOT NULL,
    `password_hash` varchar(255) NOT NULL,
    PRIMARY KEY (`code`)
  ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8
* CREATE TABLE `wallets` (
    `code` int NOT NULL AUTO_INCREMENT,
    `wallet_phone` varchar(15) NOT NULL,
    `wallet_token` varchar(255) NOT NULL,
    `wallet_token_valid_date` date NOT NULL,
    `processing_account` int NOT NULL,
    `card_token` varchar(255) NOT NULL,
    `hook_id` varchar(255) NOT NULL,
    `secret_key` varchar(255) NOT NULL,
    `last_error` varchar(255) DEFAULT NULL,
    `removed` tinyint(1) NOT NULL DEFAULT '0',
    `user` int NOT NULL DEFAULT '1',
    PRIMARY KEY (`code`)
  ) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8
4. Создать в корне проекта директорию tmp и дать права на запись.
5. Создать по указанному в конфиге пути папку logs и дать права на запись.