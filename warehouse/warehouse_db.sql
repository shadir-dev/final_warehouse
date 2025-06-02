CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date DATE NOT NULL
);


CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    reg_num VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(100),
    shift_time INT NOT NULL,
    shift_type VARCHAR(100) NOT NULL,
    check_in_time DATETIME,
    check_out_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE goods_out (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255),
    item_type VARCHAR(100),
    quantity VARCHAR(50),
    location VARCHAR(255),
    emp_contacts VARCHAR(100),
    moved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255),
    item_type VARCHAR(100),
    quantity VARCHAR(50),
    location VARCHAR(255),
    emp_contacts VARCHAR(100)
);
