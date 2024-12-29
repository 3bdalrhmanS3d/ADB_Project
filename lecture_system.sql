Create database lecture_system;
use lecture_system;

-- Create tables

CREATE TABLE lectures (
    lecture_id INT AUTO_INCREMENT PRIMARY KEY,
    lecture_name VARCHAR(255) NOT NULL
);

CREATE TABLE questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    lecture_id INT NOT NULL,
    question_text TEXT NOT NULL,
    code_option TEXT NULL,
    FOREIGN KEY (lecture_id) REFERENCES lectures(lecture_id)
);

CREATE TABLE options (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(question_id)
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL
);

CREATE TABLE user_answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option_id INT NOT NULL,
    is_correct BOOLEAN NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (question_id) REFERENCES questions(question_id),
    FOREIGN KEY (selected_option_id) REFERENCES options(option_id)
);

CREATE TABLE tests (
    test_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    total_score INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Stored Procedures

-- 1. Select all lectures from the lectures table
-- 2. Return the result
DELIMITER $$ 
	create PROCEDURE AvailableLectures()
    BEGIN 
    		SELECT * FROM lectures ;
    END $$ 
DELIMITER ; -- 

-- 1. Insert the user_name into the users table
-- 2. Set the user_id to the last inserted user_id
-- 3. Return the user_id
DELIMITER $$
CREATE PROCEDURE register_user(IN user_name VARCHAR(255), OUT user_id INT)
BEGIN
    INSERT INTO users (user_name) VALUES (user_name);
    SET user_id = LAST_INSERT_ID();
END$$
DELIMITER ;

-- ##  
DELIMITER $$
CREATE PROCEDURE get_questions_by_lecture(IN lecture_id INT)
BEGIN
    SELECT q.question_id, q.question_text, q.code_option
    FROM questions q
    WHERE q.lecture_id = lecture_id;

    SELECT o.option_id, o.question_id, o.option_text, o.is_correct
    FROM options o
    INNER JOIN questions q ON o.question_id = q.question_id
    WHERE q.lecture_id = lecture_id;
END$$
DELIMITER ;

-- ## Insert user answer
DELIMITER $$
CREATE PROCEDURE insert_user_answer(
    IN user_id INT,
    IN question_id INT,
    IN selected_option_id INT,
    OUT is_correct BOOLEAN
)
BEGIN
    SELECT is_correct INTO is_correct
    FROM options
    WHERE option_id = selected_option_id;

    INSERT INTO user_answers (user_id, question_id, selected_option_id, is_correct)
    VALUES (user_id, question_id, selected_option_id, is_correct);
END$$
DELIMITER ;

-- ## 
DELIMITER $$
CREATE PROCEDURE calculate_total_score(IN user_id INT, OUT total_score INT)
BEGIN
    SELECT SUM(CASE WHEN is_correct = 1 THEN 10 ELSE 0 END) INTO total_score
    FROM user_answers
    WHERE user_id = user_id;
END$$
DELIMITER ;
