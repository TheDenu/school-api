create table users (
    user_id int AUTO_INCREMENT PRIMARY KEY,
    full_name varchar(255) not null,
    email varchar(255) not null unique,
    password text not null
);

create table user_tokens (
    token_id int AUTO_INCREMENT PRIMARY KEY,
    user_id int not null,
    token text not null,
    created_at datetime not null,
    expired_at datetime not null,
    FOREIGN KEY (user_id) REFERENCES users(user_id) on delete cascade
);

create table courses (
    course_id int AUTO_INCREMENT PRIMARY KEY,
    name varchar(30) not null,
    description varchar(100),
    hours int not null,
    price decimal(8,2) not null,
    start_date date not null,
    end_date date not null,
    img varchar(255) not null
);

create table lessons (
    lesson_id int AUTO_INCREMENT PRIMARY KEY,
    course_id int not null,
    name varchar(50) not null,
    description text not null,
    video_link varchar(255),
    hours int not null,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) on delete RESTRICT
);

create table payments (
    payment_id int AUTO_INCREMENT PRIMARY KEY,
    name varchar(50) not null
);

create table orders (
    order_id int AUTO_INCREMENT PRIMARY KEY,
    user_id int not null,
    course_id int not null,
    order_date date not null,
    payment_id int not null,
    FOREIGN KEY (user_id) REFERENCES users(user_id) on delete cascade,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) on delete RESTRICT,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id) on delete RESTRICT
);