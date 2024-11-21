-- Eliminazione di entrambe le tabelle in caso esistano
DROP DATABASE db;
CREATE DATABASE db;
USE db;

-- Creazione tabelle
CREATE TABLE utente (
       username VARCHAR(30) PRIMARY KEY,
       password VARCHAR(30) NOT NULL,
       email VARCHAR(30) NOT NULL,
       isAdmin BOOLEAN
);

CREATE TABLE categoria (
       nome VARCHAR(20) PRIMARY KEY,
       descrizione VARCHAR(150) NOT NULL
);

CREATE TABLE iscrizione (
       utente VARCHAR(30) NOT NULL,
       categoria VARCHAR(20) NOT NULL,

       PRIMARY KEY (utente, categoria),
       FOREIGN KEY (utente) REFERENCES utente(username) ON DELETE CASCADE,
       FOREIGN KEY (categoria) REFERENCES categoria(nome) ON DELETE CASCADE
);

CREATE TABLE opera (
      id INT UNSIGNED PRIMARY KEY,
      path VARCHAR(30) NOT NULL,
      nome VARCHAR(30) NOT NULL,
      descrizione VARCHAR(150) NOT NULL,
      prezzo DECIMAL(10,2) NOT NULL,
      possessore VARCHAR(30),

      FOREIGN KEY (possessore) REFERENCES utente(username) ON DELETE SET NULL
);

CREATE TABLE recensione (
       utente VARCHAR(30),
       opera INT UNSIGNED,
       voto TINYINT UNSIGNED NOT NULL CHECK (voto>0 AND voto<=5),

       PRIMARY KEY (utente, opera),
       FOREIGN KEY (utente) REFERENCES utente(username) ON DELETE CASCADE,
       FOREIGN KEY (opera) REFERENCES opera(id) ON DELETE CASCADE
);

CREATE TABLE acquisto (
       utente VARCHAR(30),
       opera INT UNSIGNED,
       prezzo DECIMAL(10,2) NOT NULL,
       data TIMESTAMP NOT NULL,

       PRIMARY KEY (utente, opera),
       FOREIGN KEY (utente) REFERENCES utente(username) ON DELETE CASCADE,
       FOREIGN KEY (opera) REFERENCES opera(id) ON DELETE CASCADE
);

CREATE TABLE appartenenza (
       categoria VARCHAR(20),
       opera INT UNSIGNED,

       PRIMARY KEY (categoria, opera),
       FOREIGN KEY (categoria) REFERENCES categoria(nome) ON DELETE CASCADE,
       FOREIGN KEY (opera) REFERENCES opera(id) ON DELETE CASCADE
);

CREATE TABLE commento (
       timestamp TIMESTAMP,
       utente VARCHAR(30),
       testo VARCHAR(200) NOT NULL,
       opera INT UNSIGNED NOT NULL,
       timerisp TIMESTAMP,
       utenterisp VARCHAR(30),

       PRIMARY KEY (timestamp, utente),
       FOREIGN KEY (utente) REFERENCES utente(username) ON DELETE CASCADE,
       FOREIGN KEY (opera) REFERENCES opera(id) ON DELETE CASCADE,
       FOREIGN KEY (timerisp, utenterisp) REFERENCES commento(timestamp, utente) ON DELETE CASCADE
);
