CREATE TABLE user_table(
   id_user INT AUTO_INCREMENT,
   user_firstname VARCHAR(30),
   user_lastname VARCHAR(30),
   user_email VARCHAR(100),
   user_password VARCHAR(255),
   user_creation_date DATETIME,
   user_statut BOOLEAN,
   user_role VARCHAR(50),
   user_lastconn DATETIME,
   PRIMARY KEY(id_user)
);

CREATE TABLE meet_table(
   meet_id INT AUTO_INCREMENT,
   meet_title VARCHAR(50),
   meet_date DATETIME,
   id_user INT NOT NULL,
   PRIMARY KEY(meet_id),
   FOREIGN KEY(id_user) REFERENCES user_table(id_user)
);

CREATE TABLE conversation_table(
   conv_id INT AUTO_INCREMENT,
   conv_private BOOLEAN NOT NULL,
   conv_name VARCHAR(255),
   PRIMARY KEY(conv_id)
);

CREATE TABLE shared_folder_table(
   sf_id INT AUTO_INCREMENT,
   sf_name TEXT NOT NULL,
   sf_url TEXT NOT NULL,
   sf_size DECIMAL(15,2),
   sf_type VARCHAR(255) NOT NULL,
   sf_date DATETIME NOT NULL,
   id_user INT NOT NULL,
   PRIMARY KEY(sf_id),
   FOREIGN KEY(id_user) REFERENCES user_table(id_user)
);

CREATE TABLE message_table(
   id_message INT AUTO_INCREMENT,
   message_content TEXT,
   message_date DATETIME,
   message_private BOOLEAN,
   conv_id INT NOT NULL,
   id_user INT NOT NULL,
   PRIMARY KEY(id_message),
   FOREIGN KEY(conv_id) REFERENCES conversation_table(conv_id),
   FOREIGN KEY(id_user) REFERENCES user_table(id_user)
);

CREATE TABLE attachment_table(
   attachement_id INT AUTO_INCREMENT,
   pj_name VARCHAR(255),
   pj_url VARCHAR(255),
   pj_size BIGINT,
   attachement_private BOOLEAN,
   pj_type VARCHAR(50),
   pj_date DATETIME NOT NULL,
   id_message INT NOT NULL,
   PRIMARY KEY(attachement_id),
   FOREIGN KEY(id_message) REFERENCES message_table(id_message)
);

CREATE TABLE user_accept_meet(
   id_user INT,
   meet_id INT,
   accepted BOOLEAN,
   PRIMARY KEY(id_user, meet_id),
   FOREIGN KEY(id_user) REFERENCES user_table(id_user),
   FOREIGN KEY(meet_id) REFERENCES meet_table(meet_id)
);

CREATE TABLE user_conversation(
   id_user INT,
   conv_id INT,
   PRIMARY KEY(id_user, conv_id),
   FOREIGN KEY(id_user) REFERENCES user_table(id_user),
   FOREIGN KEY(conv_id) REFERENCES conversation_table(conv_id)
);

CREATE TABLE user_read_message(
   id_user INT,
   id_message INT,
   is_read BOOLEAN,
   PRIMARY KEY(id_user, id_message),
   FOREIGN KEY(id_user) REFERENCES user_table(id_user),
   FOREIGN KEY(id_message) REFERENCES message_table(id_message)
);
