-- Eliminazione di entrambe le tabelle in caso esistano
DROP DATABASE db;
CREATE DATABASE db;
USE db;

-- Creazione tabelle
CREATE TABLE utente (
       username VARCHAR(30) PRIMARY KEY,
       password VARCHAR(255) NOT NULL,
       email VARCHAR(30) NOT NULL,
       isAdmin BOOLEAN NOT NULL,
       saldo DECIMAL(10,5) NOT NULL
);

CREATE TABLE categoria (
       nome VARCHAR(20) PRIMARY KEY,
       descrizione VARCHAR(150) NOT NULL
);

CREATE TABLE opera (
      id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
      path VARCHAR(30) NOT NULL,
      nome VARCHAR(30) NOT NULL,
      descrizione VARCHAR(300) NOT NULL,
      prezzo DECIMAL(10,2) NOT NULL,
      possessore VARCHAR(30) NOT NULL DEFAULT 'admin',

      FOREIGN KEY (possessore) REFERENCES utente(username) ON DELETE SET DEFAULT
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

CREATE TABLE recensione (
       timestamp TIMESTAMP,
       utente VARCHAR(30),
       commento VARCHAR(200),
       opera INT UNSIGNED NOT NULL,
       voto TINYINT UNSIGNED NOT NULL CHECK (voto>0 AND voto<=5),

       PRIMARY KEY (timestamp, utente),
       FOREIGN KEY (utente) REFERENCES utente(username) ON DELETE CASCADE,
       FOREIGN KEY (opera) REFERENCES opera(id) ON DELETE CASCADE
);

-- Inserimento Utenti
INSERT INTO utente VALUES
('admin', '$2y$10$KfQJleNwObRs9vPSr3ToRO33CD.7zUnB.tRMP/6E674upMYQTavS.', 'amdin@studenti.unipd.it', true, 9.92341),
('user', '$2y$10$yiqQZIRp91Gam6l6HIZdb.wPpSgELJSR2uLeho8PwtaTSGM9F.wJC', 'user@studenti.unipd.it', false, 8.07022);

-- Inserimento Categorie
INSERT INTO categoria VALUES
('Abstract', 'Opere astratte che evocano emozioni e immaginazione'),
('Animals', 'Illustrazioni e dipinti del mondo animale'),
('PixelArt', 'Arte digitale in stile pixel'),
('Black&White', 'Fotografie e disegni in bianco e nero'),
('Photo', 'Fotografie artistiche e paesaggi');

-- Inserimento Opere
INSERT INTO opera(id, path, nome, descrizione, prezzo) VALUES
(1, './assets/nft1', 'Scarabocchio Azzurro', 'Un mix di colori azzurro, blu e viola.', 30.40),
(2, './assets/nft2', 'Scarabocchio Viola', 'Un insieme di colori viola, blu e azzurro.', 32.50),
(3, './assets/nft3', 'Scarabocchio Solare', 'Colori giallo, arancione, rosso e viola mescolati con energia.', 35.75),
(4, './assets/nft4', 'Scarabocchio Blu', 'Un dipinto astratto con tonalità di azzurro, blu e viola.', 29.99),
(5, './assets/nft5', 'Scarabocchio Caldo', 'Un mix di colori viola, rosso e arancione.', 31.20),
(6, './assets/nft6', 'Rettangoli Monocromatici', 'Forme rettangolari astratte in bianco e nero.', 40.10),
(7, './assets/nft7', 'Rettangoli Vivaci', 'Una rappresentazione astratta con forme rettangolari in rosso, bianco e nero.', 42.30),
(8, './assets/nft8', 'Scimmia Sorridente', 'Una scimmia con un sorriso luminoso.', 50.00),
(9, './assets/nft9', 'Scimmia Arrabbiata', 'Una scimmia che esprime rabbia con colori intensi.', 52.00),
(10, './assets/nft10', 'Scimmia Stranita', 'Una espressione stranita e colori vivaci.', 48.50),
(11, './assets/nft11', 'Scimmia Dubbiosa', 'Una scimmia dubbiosa, ma con uno stile unico.', 49.99),
(12, './assets/nft12', 'Scimmia Intrigata', 'Una scimmia che sembra approvare con entusiasmo.', 51.20),
(13, './assets/nft13', 'Broly', 'Un gattino pixelato dai colori verdi.', 35.00),
(14, './assets/nft14', 'Gatto Blu', 'Un gattino pixelato con tonalità di blu.', 36.00),
(15, './assets/nft15', 'Gatto Viola', 'Un gattino pixelato di colore viola.', 37.50),
(16, './assets/nft16', 'Gatto Rosa', 'Un gattino pixelato rosa e adorabile.', 38.00),
(17, './assets/nft17', 'Gatto Lilla', 'Un gattino pixelato con tonalità di lilla.', 39.50),
(18, './assets/nft18', 'Fenicottero Elegante', 'Un fenicottero rosa su uno sfondo turchese a pallini.', 60.00);

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
('PixelArt', 6),
('Abstract', 7),
('PixelArt', 7),
('Animals', 8),
('Animals', 9),
('Animals', 10),
('Animals', 11),
('Animals', 12),
('Animals', 13),
('PixelArt', 13),
('Animals', 14),
('PixelArt', 14),
('Animals', 15),
('PixelArt', 15),
('Animals', 16),
('PixelArt', 16),
('Animals', 17),
('PixelArt', 17);

-- Inserimento Recensioni
INSERT INTO recensione VALUES
('2024-03-22 12:50:05', 'user', 'Bello', 1, 4);
