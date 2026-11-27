CREATE DATABASE IF NOT EXISTS interior_studio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE interior_studio;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS project_updates;
DROP TABLE IF EXISTS design_requests;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','designer','client') NOT NULL DEFAULT 'client',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(180) NULL,
    category_id INT UNSIGNED NULL,
    client_id INT UNSIGNED NULL,
    designer_id INT UNSIGNED NULL,
    status ENUM('Planning','In Progress','On Hold','Completed') NOT NULL DEFAULT 'Planning',
    progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_project_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_project_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_project_designer FOREIGN KEY (designer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE design_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    budget VARCHAR(120) NULL,
    preferred_date DATE NULL,
    status ENUM('Submitted','Reviewing','Approved','Declined','Completed') NOT NULL DEFAULT 'Submitted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_request_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_request_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE project_updates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_update_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_update_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(180) NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO categories (name) VALUES
('Residential'), ('Kitchen & Dining'), ('Living Spaces'), ('Bedroom'), ('Commercial');

-- Demo password for all three accounts is Studio123!
-- Change these passwords immediately after first login.
INSERT INTO users (name,email,password,role) VALUES
('Studio Administrator','admin@interiorstudio.test','$2y$12$B4kuYcSiSC5wqHw/goaqkuiQePuvUXYn/7/C7IM8JbK7Iy6zMFroG','admin'),
('Alex Designer','designer@interiorstudio.test','$2y$12$B4kuYcSiSC5wqHw/goaqkuiQePuvUXYn/7/C7IM8JbK7Iy6zMFroG','designer'),
('Jamie Client','client@interiorstudio.test','$2y$12$B4kuYcSiSC5wqHw/goaqkuiQePuvUXYn/7/C7IM8JbK7Iy6zMFroG','client');

INSERT INTO projects (title,description,location,category_id,client_id,designer_id,status,progress,is_published,image) VALUES
('The Calm Residence','A warm, tactile home built around natural light, soft neutrals and carefully layered materials.','Dublin, Ireland',1,3,2,'In Progress',65,1,NULL),
('A Quiet Kitchen','A practical kitchen refresh with oak detailing, understated stone and generous everyday storage.','Wicklow, Ireland',2,3,2,'Completed',100,1,NULL),
('Soft Modern Living','A relaxed living space balancing sculptural forms with comfortable, durable finishes.','Galway, Ireland',3,NULL,NULL,'Planning',10,1,NULL);
