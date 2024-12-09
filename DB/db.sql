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

-- Inserimento Utenti
INSERT INTO utente VALUES
('efraine0', 'zV1<Y~TY,qOA', 'mbolf0@nyu.edu', false),
('njobke1', 'zQ3<)fRz)xn+LG''', 'vchristofides1@dmoz.org', true),
('delegood2', 'bN1)5s%nQoXnK+M', 'gelliker2@addtoany.com', true),
('achaundy3', 'qY4\L9M.WTd20', 'mbeelby3@blogspot.com', false),
('jhadlington4', 'wX4`0a@#\)>G~/', 'glabusch4@twitter.com', true),
('nferreli5', 'zR1/=7xvtq@sE$gc', 'tbaudains5@nature.com', true),
('dnarey6', 'oK7#}ZFi49x/', 'sscheu6@forbes.com', false),
('sroft7', 'iY1/OJjo.FRF`%', 'xcurrer7@psu.edu', false),
('hmilkins8', 'sN5{3AnWEVlK_W', 'xgiorgio8@wordpress.org', false),
('akelleher9', 'jY2(PN$P&O0/RI', 'ldunton9@wisc.edu', true),
('bburgisa', 'lJ9''ukEWnbo~0E', 'mhatza@amazon.co.uk', false),
('tpantlingb', 'bX5<>38lMG', 'kbullanb@npr.org', false),
('mabbesc', 'iJ5%qwB@Je', 'bgogginsc@miibeian.gov.cn', false),
('wduffetd', 'sG7}5I/94F', 'asineyd@timesonline.co.uk', false),
('kjurriese', 'fY5?yBcud', 'ybiddlese@amazon.co.uk', false),
('cmccourtf', 'pM3$QD7)SkSVtGk.', 'cjaggarf@japanpost.jp', false),
('wgavang', 'zW8*h%q8~h`ZK+''', 'tstofflerg@cdbaby.com', true),
('ypatilloh', 'nI3!{9fa5', 'swhifeh@1688.com', false),
('ceshelbyi', 'hG1.JUXw', 'fesmeadi@webeden.co.uk', false),
('icharplingj', 'jK1_~Bv,l7', 'kslotj@woothemes.com', true);

-- Inserimento Categorie
INSERT INTO categoria VALUES
('CAT1', 'DESC'),
('CAT2', 'DESC'),
('CAT3', 'DESC'),
('CAT4', 'DESC'),
('CAT5', 'DESC');

-- Inserimento Iscrizioni
INSERT INTO iscrizione VALUES
('icharplingj', 'CAT1'),
('ceshelbyi', 'CAT2'),
('ceshelbyi', 'CAT4'),
('wgavang', 'CAT3');

-- Inserimento Opere
INSERT INTO opera VALUES
(1, './assets/opera1', 'Nome', 'DESC', 30.4, NULL);

-- Inserimento Recensioni
INSERT INTO recensione VALUES
('wgavang', 1, 1),
('icharplingj', 1, 4);

-- Inserimento Acquisti
INSERT INTO acquisto VALUES
('icharplingj', 1, 50.10, '2024-03-22 12:50:05');

-- Inserimento Appartenenze
INSERT INTO appartenenza VALUES
('CAT1', 1),
('CAT2', 1);

-- Inserimento Commenti
INSERT INTO commento VALUES
('2024-03-22 12:50:05', 'icharplingj', 'Bello', 1, NULL, NULL);
