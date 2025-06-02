
-- Table: admins
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: announcements
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date DATE NOT NULL
);

-- Table: employees
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    reg_num VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(100),
    shift_time INT NOT NULL,
    shift_type VARCHAR(50) NOT NULL,
    salary_amount DECIMAL(10,2),
    last_paid_date DATE,
    check_in_time DATETIME,
    check_out_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: employees_salaries
CREATE TABLE employees_salaries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg_num VARCHAR(100) NOT NULL,
    net_salary DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    receipt_no VARCHAR(100) UNIQUE NOT NULL,
    salary_month VARCHAR(20) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    admin_username VARCHAR(100)
);

-- Table: goods_out
CREATE TABLE goods_out (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(255),
    item_type VARCHAR(255),
    location VARCHAR(255),
    destination_location VARCHAR(255),
    emp_contacts VARCHAR(100),
    moved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_date DATETIME,
    payment_method VARCHAR(50),
    payment_status VARCHAR(50),
    quantity VARCHAR(50),
    quantity_out INT NOT NULL,
    total_storage_cost DECIMAL(10,2) DEFAULT 0.00,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    goods_out_date DATE
);

-- Table: payments
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    receipt_no VARCHAR(100) UNIQUE NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_paid DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    destination VARCHAR(255),
    date_paid DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: stock
CREATE TABLE stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    daily_rate DECIMAL(10,2) NOT NULL,
    total_storage_cost DECIMAL(10,2) NOT NULL,
    arrival_date DATE NOT NULL,
    last_updated DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    employee_contact VARCHAR(100)
);
