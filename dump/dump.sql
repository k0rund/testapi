CREATE TABLE `api`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(250) NOT NULL COMMENT 'Пользователь',
  `pass_hash` VARCHAR(250) NOT NULL COMMENT 'hash',
  PRIMARY KEY (`id`)
)
DEFAULT CHARACTER SET = utf8
COMMENT = 'Таблица пользователей';

CREATE TABLE `api`.`files` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'Название файла (оригинальнал)',
  `ext` VARCHAR(6) NOT NULL COMMENT 'Расширение файла',
  `hash_name` VARCHAR(32) NOT NULL COMMENT 'Хеш (md5) от имени файла',
  `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время создания',
  `update_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Время последнего обновления',
  `user_id` INT NOT NULL COMMENT 'Id пользователя',
   PRIMARY KEY (`id`)
)
DEFAULT CHARACTER SET = utf8
COMMENT = 'Таблица c данными по файлам';

ALTER TABLE `api`.`files` 
ADD CONSTRAINT `fk_files_user_id`
  FOREIGN KEY (`user_id`)
  REFERENCES `api`.`users` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;