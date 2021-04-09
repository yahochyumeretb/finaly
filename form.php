<?php

$host = 'localhost';
$dbname = 'prakt';
$port = '3306';
$user = 'root';
$password ='12345';

$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);



function getUsers (): array {
    global $conn;
    return $conn->query('select * from users')->fetchAll(PDO::FETCH_ASSOC);

}

function q($post)
{
    global $conn;
    $st = $conn->prepare('insert into users (name, last_name, email, age) values (?, ?, ?, ?)');

    $st->execute([
        $post['name'],
        $post['last_name'],
        $post['email'],
        $post['age'],
    ]);
}
function valid (array $post) : array {
    $validate=[
        'error'=>false,
        'success' =>false,
        'messages'=>[],
    ];

    if (!empty($post['name']) && !empty($post['last_name']) && !empty($post['email']) &&  !empty($post['age'])){

        $name = trim($post ['name']);
        $last_name = trim($post ['last_name']);
        $email = trim($post['email']);
        $age = trim($post['age']);


        $constraints= [
            'age' => 18 ,
            'email' =>4,
        ];

        $validateForm=validLoginAndPassword($name, $last_name, $constraints, $email, $age);

        if (!$validateForm['name']){
            $validate['error'] = true;
            array_push( $validate ['messages'],
                " Имя {$name} некорректно, имя не должно содержать в себе цифры"
            );
        }
        if (!$validateForm['last_name']){
            $validate['error'] = true;
            array_push( $validate ['messages'],
                " Фамилия {$last_name} некорректно, фамилия не должна содержать в себе цифры"
            );

        }
        if (!$validateForm['email']) {
            $validate['error'] = true;
            array_push( $validate ['messages'],
                "Почта должна превышать 4 символа"
            );
        }
        if (!$validateForm['age']) {
            $validate['error'] = true;
            array_push( $validate ['messages'],
                "Вы должны быть не младше 18 лет"
            );
        }
        if (!$validate['error']){
            $validate['success'] = true;
            array_push(
                $validate['messages'],
                "Вы успешно прошли валидацию <br>  Ваше имя: {$name}  <br>  Ваша фамилия: {$last_name}  <br> Ваш email : {$email} <br> Ваш возраст : {$age}"
            );
        }
        return $validate;
    }
    return $validate;
}
function validLoginAndPassword(string $name, string $last_name, array $constraints, string $email, string $age):array{
    $validateForm=[

        'name'=>true,
        'last_name' => true,
        'email' => true,
        'age' =>true,
    ];
    if (($age)<$constraints['age']){
        $validateForm['age'] = false;
    }
    if (strlen($email)<$constraints['email']){
        $validateForm['email']=false;
    }
    if (preg_match("/[0-9]/", $name))
    {
        $validateForm['name'] = false;
    }
    if (preg_match("/[0-9]/", $last_name))
    {
        $validateForm['last_name'] = false;
    }
    return $validateForm;
}