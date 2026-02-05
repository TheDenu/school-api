<?php

function validate($name, $description, $hours, $price, $start_date, $end_date){
    $errors = [];

    if (empty($name)) $errors['name'] = 'Заполните название';
    if (empty($start_date)) $errors['start_date'] = 'Заполните дату начала';
    if (empty($end_date)) $errors['end_date'] = 'Заполните дату окончания';
    if ($hours < 1 || $hours > 10) $errors['hours'] = 'Часы от 1 до 10';
    if ($price < 100) $errors['price'] = 'Цена минимум 100₽';
    if (strlen($name) > 30) $errors['name'] = 'Название не больше 30 символов';
    if (strlen($description) > 100) $errors['description'] = 'Описание не больше 100 символов';
    if (date('Y-m-d') > $start_date) $errors['start_date'] = 'Дата начала курса должна быть не позже ' . date('d-m-Y');
    if (date('Y-m-d') > $end_date) $errors['end_date'] = 'Дата конца курса должна быть не позже ' . date('d-m-Y');
    if ($start_date > $end_date) $errors['end_date'] = 'Дата конца курса не может быть раньше даты начала';

    return $errors;
}