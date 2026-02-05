<?php
function validate($name, $description, $hours, $video_link)
{
    $errors = [];

    if (empty($name)) $errors['name'] = 'Название обязательно';
    if (empty($description)) $errors['description'] = 'Описание обязательно';
    if (strlen($name) > 50) $errors['name'] = 'Название не больше 50 символов';
    if(!empty($video_link)){
        if (!filter_var($video_link, FILTER_VALIDATE_URL)) $errors['video_link'] = 'Ссылка не валидна';
    }
    if ($hours < 1 || $hours > 4) $errors['hours'] = 'Часы от 1 до 4';
    
    return $errors;
}
