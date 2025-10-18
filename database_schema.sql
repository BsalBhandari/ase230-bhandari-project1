-- ASE230 Project 1 Database Schema
-- Database: ase230_project1

USE ase230_project1;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('student', 'instructor', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    instructor_id INT NOT NULL,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    credits INT DEFAULT 3,
    semester VARCHAR(20),
    year YEAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'dropped', 'completed') DEFAULT 'active',
    UNIQUE KEY unique_enrollment (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Assignments table
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATETIME,
    max_points DECIMAL(10,2) DEFAULT 100.00,
    assignment_type ENUM('homework', 'project', 'exam', 'quiz') DEFAULT 'homework',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Grades table
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    assignment_id INT NOT NULL,
    points_earned DECIMAL(10,2) DEFAULT 0.00,
    feedback TEXT,
    graded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_grade (student_id, assignment_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
);

-- Quizzes table
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    time_limit INT DEFAULT 60, -- minutes
    max_attempts INT DEFAULT 1,
    available_from DATETIME,
    available_until DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Quiz Questions table
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false', 'short_answer') DEFAULT 'multiple_choice',
    points DECIMAL(5,2) DEFAULT 1.00,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Quiz Answers table
CREATE TABLE IF NOT EXISTS quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO users (username, email, password_hash, first_name, last_name, role) VALUES
('admin', 'admin@ase230.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin'),
('instructor1', 'instructor1@ase230.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', 'instructor'),
('student1', 'student1@ase230.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Doe', 'student'),
('student2', 'student2@ase230.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Johnson', 'student');

INSERT INTO courses (title, description, instructor_id, course_code, credits, semester, year) VALUES
('Web Development Fundamentals', 'Introduction to web development using PHP, HTML, CSS, and JavaScript', 2, 'ASE230', 3, 'Fall', 2025),
('Database Design', 'Database design principles and SQL implementation', 2, 'ASE240', 3, 'Fall', 2025);

INSERT INTO enrollments (student_id, course_id) VALUES
(3, 1),
(4, 1),
(3, 2);

INSERT INTO assignments (course_id, title, description, due_date, max_points, assignment_type) VALUES
(1, 'Project 1: Basic PHP Application', 'Create a basic PHP application with user authentication', '2025-11-15 23:59:59', 100.00, 'project'),
(1, 'Midterm Exam', 'Midterm examination covering PHP fundamentals', '2025-10-30 14:00:00', 100.00, 'exam'),
(2, 'Database Design Assignment', 'Design and implement a database schema', '2025-11-20 23:59:59', 100.00, 'homework');

INSERT INTO notifications (user_id, title, message, type) VALUES
(3, 'Welcome to ASE230', 'Welcome to Web Development Fundamentals! Check the course materials.', 'info'),
(4, 'Welcome to ASE230', 'Welcome to Web Development Fundamentals! Check the course materials.', 'info'),
(2, 'New Student Enrollment', 'A new student has enrolled in your course.', 'info');
