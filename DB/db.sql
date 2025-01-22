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
(1, 'assets/nft1', 'Scarabocchio Azzurro', 'Un mix di colori azzurro, blu e viola. Un intreccio di linee fluide e curve morbide. Le forme si sovrappongono e si intrecciano, creando un senso di movimento e spontaneità. Alcuni tratti sono più intensi, quasi come se volessero emergere.', 30.40),
(2, 'assets/nft2', 'Scarabocchio Viola', 'Un insieme di colori viola, blu e azzurro. Le linee sinuose e i contorni irregolari si intrecciano in un gioco di forme astratte, evocando un senso di fantasia e sogno. Il viola, ricco e profondo, richiama un campo di lavanda in fiore, trasmettendo una sensazione di calma e introspezione.', 32.50),
(3, 'assets/nft3', 'Scarabocchio Solare', 'Colori giallo, arancione, rosso e viola mescolati con energia. Le linee ondulate e i tratti audaci si intrecciano in un turbinio di energia e gioia, evocando la luce del sole e la freschezza dei fiori di campo. Il giallo, luminoso e radioso, trasmette un senso di ottimismo e vitalità.', 35.75),
(4, 'assets/nft4', 'Scarabocchio Blu', 'Un dipinto astratto con tonalità di azzurro, blu e viola. Profondo e sereno, evoca la grandezza del cielo notturno e la tranquillità delle acque tranquille, trasmettendo una sensazione di pace e introspezione.', 29.99),
(5, 'assets/nft5', 'Scarabocchio Caldo', 'Un mix di colori viola, rosso e arancione. Vibrante e potente, richiama le emozioni del fuoco e la forza della vita, trasmettendo energia contagiosa. Alcuni tratti sono più spessi e decisi, come se volessero esprimere un forte desiderio.', 31.20),
(6, 'assets/nft6', 'Rettangoli Monocromatici', 'Forme rettangolari astratte in bianco e nero. La semplicità delle forme geometriche invita a riflettere sulla bellezza della minimalismo, mentre la scelta del colore monocromatico enfatizza unità e la coesione.', 40.10),
(7, 'assets/nft7', 'Rettangoli Vivaci', 'Una rappresentazione astratta con forme rettangolari in rosso, bianco e nero. Esplosione di creatività, un invito a celebrare la gioia ed energia che i colori possono portare nella nostra vita.', 42.30),
(8, 'assets/nft8', 'Scimmia Sorridente', 'Una scimmia con un sorriso luminoso. In perfetta sintonia con la natura che la circonda, e il suo sorriso contagioso invita chiunque la osservi a unirsi alla sua gioia e spensieratezza.', 50.00),
(9, 'assets/nft9', 'Scimmia Arrabbiata', 'Una scimmia che esprime rabbia con colori intensi. Immagine potente di emozioni autentiche, un promemoria che anche le creature più giocose possono provare frustrazione e rabbia.', 52.00),
(10, 'assets/nft10', 'Scimmia Stranita', 'Una espressione stranita e colori vivaci. Ricorda a tutti noi quanto sia affascinante e sorprendente il mondo che ci circonda. La scimmia, con la sua espressione curiosa, invita a esplorare.', 48.50),
(11, 'assets/nft11', 'Scimmia Dubbiosa', 'Una scimmia dubbiosa, ma con uno stile unico.', 49.99),
(12, 'assets/nft12', 'Scimmia Intrigata', 'Una scimmia che sembra approvare con entusiasmo.', 51.20),
(13, 'assets/nft13', 'Broly', 'Un gattino pixelato dai colori verdi. Il suo pelo, di un verde brillante e vivace, sembra quasi brillare sotto la luce, creando un contrasto sorprendente con il mondo circostante.', 35.00),
(14, 'assets/nft14', 'Gatto Blu', 'Un gattino pixelato con tonalità di blu. Questo gatto blu si muove con eleganza, le sue zampe agili e silenziose lo rendono un cacciatore esperto, anche se il suo aspetto incantevole lo fa sembrare più un esploratore che un predatore.', 36.00),
(15, 'assets/nft15', 'Gatto Viola', 'Un gattino pixelato di colore viola. Simbolo di creatività e originalità, un invito a vedere la bellezza nella sua unicità. Questo felino straordinario porta con sé un senso di magia e meraviglia', 37.50),
(16, 'assets/nft16', 'Gatto Rosa', 'Un gattino pixelato rosa e adorabile.', 38.00),
(17, 'assets/nft17', 'Gatto Lilla', 'Un gattino pixelato con tonalità di lilla.', 39.50),
(18, 'assets/nft18', 'Fenicottero Elegante', 'Un fenicottero rosa su uno sfondo turchese a pallini.', 60.00);

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
('2024-03-22 12:50:05', 'user', 'Fanstastico!', 13, 5),
('2024-03-23 12:50:05', 'user', 'Bello', 1, 4),
('2024-03-24 12:50:05', 'user', 'Meh', 4, 2);
