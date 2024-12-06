-- Eliminazione di entrambe le tabelle in caso esistano
DROP DATABASE db;
CREATE DATABASE db;
USE db;

-- Creazione tabelle
CREATE TABLE utente (
       username VARCHAR(30) PRIMARY KEY,
       password VARCHAR(30) NOT NULL,
       email VARCHAR(30) NOT NULL,
       isAdmin BOOLEAN NOT NULL,
       saldo DECIMAL(10,5) NOT NULL
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

-- Inserimento Utenti
INSERT INTO utente VALUES
('admin', 'admin', 'amdin@studenti.unipd.it', true, 9.92341),
('user', 'user', 'user@studenti.unipd.it', false, 8.07022);

-- Inserimento Categorie
INSERT INTO categoria VALUES
('Abstract', 'DESC'),
('Animals', 'DESC'),
('Pixel Art', 'DESC'),
('Black&White', 'DESC'),
('Photo', 'DESC');

-- Inserimento Iscrizioni
INSERT INTO iscrizione VALUES
('admin', 'Pixel Art'),
('user', 'Abstract'),
('user', 'Animals'),
('user', 'Photo');

-- Inserimento Opere
INSERT INTO opera VALUES
(1, './assets/nft1', 'Nome', 'DESC', 30.4, NULL),
(2, './assets/nft2', 'Nome', 'DESC', 30.4, NULL),
(3, './assets/nft3', 'Nome', 'DESC', 30.4, NULL),
(4, './assets/nft4', 'Nome', 'DESC', 30.4, NULL),
(5, './assets/nft5', 'Nome', 'DESC', 30.4, NULL),
(6, './assets/nft6', 'Nome', 'DESC', 30.4, NULL),
(7, './assets/nft7', 'Nome', 'DESC', 30.4, NULL),
(8, './assets/nft8', 'Nome', 'DESC', 30.4, NULL),
(9, './assets/nft9', 'Nome', 'DESC', 30.4, NULL),
(10, './assets/nft10', 'Nome', 'DESC', 30.4, NULL),
(11, './assets/nft11', 'Nome', 'DESC', 30.4, NULL),
(12, './assets/nft12', 'Nome', 'DESC', 30.4, NULL),
(13, './assets/nft13', 'Nome', 'DESC', 30.4, NULL),
(14, './assets/nft14', 'Nome', 'DESC', 30.4, NULL),
(15, './assets/nft15', 'Nome', 'DESC', 30.4, NULL),
(16, './assets/nft16', 'Nome', 'DESC', 30.4, NULL),
(17, './assets/nft17', 'Nome', 'DESC', 30.4, NULL);

-- Inserimento Recensioni
INSERT INTO recensione VALUES
('admin', 1, 1);

-- Inserimento Acquisti
INSERT INTO acquisto VALUES
('admin', 1, 50.10, '2024-03-22 12:50:05');

-- Inserimento Appartenenze
INSERT INTO appartenenza VALUES
('Abstract', 1),
('Abstract', 2),
('Abstract', 3),
('Abstract', 4),
('Abstract', 5),
('Abstract', 6),
('Pixel Art', 6),
('Abstract', 7),
('Pixel Art', 7),
('Animals', 8),
('Animals', 9),
('Animals', 10),
('Animals', 11),
('Animals', 12),
('Animals', 13),
('Pixel Art', 13),
('Animals', 14),
('Pixel Art', 14),
('Animals', 15),
('Pixel Art', 15),
('Animals', 16),
('Pixel Art', 16),
('Animals', 17),
('Pixel Art', 17);

-- Inserimento Commenti
INSERT INTO commento VALUES
('2024-03-22 12:50:05', 'user', 'Bello', 1, NULL, NULL);