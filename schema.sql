CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    gender ENUM('M', 'F') NOT NULL,
    skill_level INT ,
    strength INT ,
    speed INT ,
    reaction_time INT
);

CREATE TABLE tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gender ENUM('M', 'F') NOT NULL,
    winner_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (winner_id) REFERENCES players(id) ON DELETE CASCADE
);

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT,
    round_number INT NOT NULL,
    player1_id INT NOT NULL,
    player2_id INT NOT NULL,
    winner_id INT NOT NULL,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (player1_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (player2_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES players(id) ON DELETE CASCADE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    access_token VARCHAR(255),
    token_expires_at DATETIME
);
