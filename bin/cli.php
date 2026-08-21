<?php
//php_sapi_name();   // devuelve "cli" en terminal, "cli-server"/"apache2handler"/etc. en web
if(php_sapi_name()!=="cli") exit(1);

date_default_timezone_set('UTC');
require_once __DIR__.'/../vendor/autoload.php';

use App\Shared\Infrastructure\Bootstrap\EnvironmentLoader;
use App\Shared\Infrastructure\Di\ContainerConfig;
use App\Auth\Application\UseCase\CreateAdminUser;
use App\Shared\Application\Port\EventDispatcherInterface;
use App\Auth\Domain\Events\UserRegistered;
use App\Auth\Infrastructure\EventListener\SendEmailConfirmation;
use App\Auth\Application\DTO\RegisterUserRequestDTO;
use App\Auth\Domain\ValueObject\RoleType;
use App\Auth\Domain\Entity\Role;
use App\Auth\Domain\Repository\RoleRepositoryInterface; 

try {
    EnvironmentLoader::load();
    $container = ContainerConfig::create();

    $dispatcher = $container->get(EventDispatcherInterface::class);
    $dispatcher->addListener(UserRegistered::class,$container->get(SendEmailConfirmation::class));

    if(!isset($argv[1])){
        fwrite(STDERR, "Sin argumentos válidos\n");      // → STDERR (errores)
        exit(1);
    }
    
    switch ($argv[1]) {
        case "seed-roles":
            echo "Registrando los roles...\n";
            try {
                $roleRepository = $container->get(RoleRepositoryInterface::class);
                $adminRole = Role::create(RoleType::Admin);
                $roleRepository->save($adminRole);
                $userRole = Role::create(RoleType::User);
                $roleRepository->save($userRole);
            } catch (\Throwable $th) {
                fwrite(STDERR, "algo falló al intentar registrar los roles:\n".$th);      // → STDERR (errores)
                exit(1);
            }
            
            echo "Roles registrados...\n";
            exit(0);
            break;
        case "create-admin":
            $useCase = $container->get(CreateAdminUser::class);
            

            echo "Ingresa el nombre(s) del usuario:\n";
            $userName = fgets(STDIN);
            

            while (trim($userName)==="") {
                echo "El nombre no puede ir vacío, vuelva a ingresar uno válido:\n";
                $userName = fgets(STDIN);
            }
            
            echo "Ingresa los apellidos del usuario:\n";
            $lastName = fgets(STDIN);

            while (trim($lastName)==="") {
                echo "El campo de los apelidos no puede ir vacío, vuelva a ingresar los valores:\n";
                $lastName = fgets(STDIN);
            }
            
            
            $emailConfirm = false;
            do {
                echo "Ingresa la dirección email del usuario:\n";
                $email = fgets(STDIN);

                while (trim($email)==="") {
                    echo "El email no puede ir vacío, vuelva a ingresar los valores:\n";
                    $email = fgets(STDIN);
                }

                echo "El email ingresado es: ".trim($email)."¿Es correcto? (Y/n)\n";
                $confirmation = fgets(STDIN);

                if(trim($confirmation[0])!=="N" && trim($confirmation[0])!=="n"){
                    $emailConfirm = true;
                }
            } while (!$emailConfirm);

            try {
                echo "Ingrese su contraseña\n";
                shell_exec('stty -echo');   // apagar eco
                $rawPass = fgets(STDIN);
                shell_exec('stty echo');    // encender de nuevo
                echo PHP_EOL;               // el Enter del usuario no se imprimió, hazlo tú
            } finally{
                shell_exec('stty echo');
            }
            
            echo "Registrando usuario...\n";
            $registerUserRequestDTO = new RegisterUserRequestDTO(
                trim($userName),
                trim($lastName),
                trim($email),
                trim($rawPass)
            );

            $useCase->execute($registerUserRequestDTO);
            echo "Usuario registrado correctamente.\n";
            exit(0);
            break;
        default:
            fwrite(STDERR, "Argumento no válido:\n Argumentos válidos: [ seed-roles, create-admin ].");      // → STDERR (errores)
            exit(1);
            break;
    }
} catch (\Throwable $th) {
    fwrite(STDERR, "algo falló al ejecutar el script:\n".$th);      // → STDERR (errores)
    exit(1);
}