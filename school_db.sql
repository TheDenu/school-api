--Создание таблицы ролей--
create table if not exists roles (
    id_role INT auto_increment primary key,
    role varchar(50) not null unique
);

--Создание таблицы пользователей
create table if not exists users (
    id_user int auto_increment primary key,
    id_role int not null,
    name varchar(255) not null,
    email varchar(255) not null unique,
    password varchar(255) not null,
    foreign key (id_role) references roles(id_role) on delete restrict
);

--Создание таблицы токенов
create table if not exists user_tokens (
    id_token int auto_increment primary key,
    id_user int not null,
    token varchar(500) not null unique,
    created_at datetime default current_timestamp,
    expires_at datetime not null,
    foreign key (id_user) references users(id_user) on delete cascade
);

--Создание таблицы курсов
create table if not exists courses (
    id int auto_increment primary key,
    name varchar(30) not null,
    description varchar(100),
    hours int check(hours between 1 and 10),
    price decimal(6,2) not null check (price >= 100),
    start_date date not null,
    end_date date not null,
    check (start_date <= end_date),
    img varchar(255) not null
);

--Создание таблицы уроков
create table if not exists lessons (
    id int auto_increment primary key,
    id_course int not null,
    name varchar(50) not null,
    description text not null,
    video_link varchar(255),
    hours int not null check (hours between 1 and 4),
    foreign key (id_course) references courses(id) on delete cascade
);

--Создание таблицы типов оплаты
create table if not exists statuses_payment (
    id_status_payment int auto_increment primary key,
    status_payment varchar(255) not null
);

--Создание таблицы записей на курс
create table if not exists orders (
    id_order int auto_increment primary key,
    id_user int not null,
    id_course int not null,
    id_status_payment int not null,
    foreign key (id_user) references users(id_user) on delete cascade,
    foreign key (id_course) references courses(id) on delete cascade,
    foreign key (id_status_payment) references statuses_payment(id_status_payment) on delete restrict
);