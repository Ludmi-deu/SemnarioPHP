<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

function getConnection(){
    $dbhost="db";
    $dbname="seminariophp";
    $dbuser="seminariophp";
    $dbpass="seminariophp";

    $connection = new PDO ("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbpass);
    $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    return $connection;
}


$app->addErrorMiddleware(true, true, true);
$app->add( function ($request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, PATCH, DELETE')
        ->withHeader('Content-Type', 'application/json')
    ;
});

// ACÁ VAN LOS ENDPOINTS


$app->get('/',function (Request $request, Response $response){
    $response->getBody()->write('hola');
    return $response;
});

$app->get('/tipos_propiedad',function (Request $request, Response $response){
    $connection = getConnection();
    try{
        $query = $connection->query('SELECT * FROM tipo_propiedades');
        $tipos = $query -> fetchAll(PDO::FETCH_ASSOC);

        $payload = json_encode([
            'status'=> 'success',
            'code'=> 200,
            'data'=> $tipos
        ]);

        $response ->getBody()->write($payload);
        return $response -> withHeader ('Content+Type','application/json');
} catch (PDOException $e){
    $payload = json_encode([
        'status'=> 'ERROR',
        'code'=> 400,
    ]);

    $response ->getBody()->write ($payload);
    return $response -> withHeader('Content-Type','application/json');
} 

});
/*
$app->delete('/tipos_propiedad/{id}', function (Request $request, Response $response) {
    $connection = getConnection();
    try {
      $id = (int) $request->getAttribute('id');
  
      $query = $connection->prepare('DELETE FROM tipo_propiedades WHERE id =:id');
      $query->bindParam(':id', $id, PDO::PARAM_INT);
      $query->execute();
  
      $deletedRows = $query->rowCount();
  
      if ($deletedRows > 0) {
        return $response->withStatus(200); // OK (sin cuerpo)
      } else {
        return $response->withStatus(404); // No encontrado
      }
    } catch (Exception $e) {
      return $response->withStatus(500) // Error interno del servidor
                     ->withJson([
                       'error' => true,
                       'message' => 'Error al eliminar el registro: ' . $e->getMessage()
                     ]);
    }
  });
*/

$app->get('/localidades',function (Request $request, Response $response){
    $connection = getConnection();
    try{
        $query = $connection->query('SELECT * FROM localidades');
        $tipos = $query -> fetchAll(PDO::FETCH_ASSOC);

        $payload = json_encode([
            'status'=> 'success',
            'code'=> 200,
            'data'=> $tipos
        ]);

        $response ->getBody()->write($payload);
        return $response -> withHeader ('Content+Type','application/json');
} catch (PDOException $e){
    $payload = json_encode([
        'status'=> 'ERROR',
        'code'=> 400,
    ]);

    $response ->getBody()->write ($payload);
    return $response -> withHeader('Content-Type','application/json');
} 

});

$app->get('/inquilinos',function (Request $request, Response $response){
    $connection = getConnection();
    try{
        $query = $connection->query('SELECT * FROM inquilinos');
        $tipos = $query -> fetchAll(PDO::FETCH_ASSOC);

        $payload = json_encode([
            'status'=> 'success',
            'code'=> 200,
            'data'=> $tipos
        ]);

        $response ->getBody()->write($payload);
        return $response -> withHeader ('Content+Type','application/json');
} catch (PDOException $e){
    $payload = json_encode([
        'status'=> 'ERROR',
        'code'=> 400,
    ]);

    $response ->getBody()->write ($payload);
    return $response -> withHeader('Content-Type','application/json');
} 

});

$app->get('/inquilinos/{id}',function (Request $request, Response $response){
    $connection = getConnection();

    try{

        $id = $request->getAttribute('id');
        $query = $connection->prepare("SELECT * FROM inquilinos WHERE id = ?");
        $query->execute([$id]);

        $inquilinos = $query -> fetchAll(PDO::FETCH_ASSOC);
        
        $json = json_encode([
            'status' => 'success',
            'code' => 200,
            'data' => $inquilinos

        ]);

        $json = json_encode($inquilinos);


        $response ->getBody()->write($json);
        return $response -> withHeader('Content+Type','application/json');
    
    } catch (PDOException $e){
        $json = json_encode([
            'status' => 'error',
            'code' => 400,
        ]);

        $response->getBody()->write($json);
        return $response-> withHeader('Content-Type','application/json');

    }
});

$app -> get ('/propiedades', function (Request $request, Response $response){
    $connection = getConnection();
    try{
        $query = $connection->query('SELECT p.id FROM propiedades p 
                                    INNER JOIN localidades l ON p.localidad_id = l.id
                                    INNER JOIN tipo_propiedades tp ON p.tipo_propiedad_id = tp.id');
        $tipos = $query -> fetchAll(PDO::FETCH_ASSOC);

        $payload = json_encode([
            'status'=> 'success',
            'code' => 200,
            'data'=> $tipos
        ]);

        $response -> getBody() -> write ($payload);
        return $response -> withHeader ('Content+Type', 'application/json');
    } catch (PDOException $e){
        $payload = json_encode([
            'status'=> 'ERROR',
            'code'=> 400,
        ]);
    
        $response ->getBody()->write ($payload);
        return $response -> withHeader('Content-Type','application/json');
    }
});

$app -> get ('/reservas', function (Request $request, Response $response){
    $connection = getConnection();
    try{
        $query = $connection->query('SELECT * FROM reservas res 
                                    INNER JOIN propiedades p ON res.propiedad_id = p.id
                                    INNER JOIN inquilinos inq ON res.inquilino_id = inq.id');
        $tipos = $query -> fetchAll(PDO::FETCH_ASSOC);

        $payload = json_encode([
            'status'=> 'success',
            'code' => 200,
            'data'=> $tipos
        ]);

        $response -> getBody() -> write ($payload);
        return $response -> withHeader ('Content+Type', 'application/json');
    } catch (PDOException $e){
        $payload = json_encode([
            'status'=> 'ERROR',
            'code'=> 400,
        ]);
    
        $response ->getBody()->write ($payload);
        return $response -> withHeader('Content-Type','application/json');
    }
});

$app->post('/tipos_propiedad',function(Request $request,Response $response){

    try{
        $connection = getConnection();

        $nombre = $request->getParsedBody()['nombre'];

        if(isset($nombre)&&!empty($nombre)){

            //chequeo si el tipo esta en la base de datos

            if(strlen($nombre) <= 50){ //chequeo con los caracteres sean menores a 50


                //consulto que el nombre no este ya en la base de datos
                $stmt = $connection->prepare("SELECT COUNT(*) FROM tipo_propiedades WHERE nombre = :nombre");
                $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if(!$count > 0){

                    $stmt = $connection->prepare("INSERT INTO tipo_propiedades (nombre) VALUES (:nombre)");
                    $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
                    $stmt->execute();

                    $payload = json_encode([
                        'message' => 'El tipo de propiedad se inserto correctamente',
                        'status' => 'success',
                        'code' => 201,
                        'data' => $nombre
                    ]);

                } else {

                    $payload = json_encode([
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'el nombre ya existe. Este debe ser único.'
                    ]);
                }

            } else {
                $payload = json_encode([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Ingreso más caracteres de los habilitados para el campo nombre.'
                ]);
            }

        } else {
            $payload = json_encode([
                'message' => 'No ingreso el nombre',
                'code' => 400,
                'status' => 'error'
            ]);
        }

        
        $response->getBody()->write($payload);
        return $response->withHeader('Content+Type','application/json');

    } catch (PDOException $e){
        $json = json_encode([
            'status' => 'error',
            'code' => 400,
        ]);

        $response->getBody()->write($json);
        return $response-> withHeader('Content-Type','application/json');

    }

});

$app->post('/localidades', function(Request $request,Response $response){
    
    try{

        $connection = getConnection();
        $nombre = $request->getParsedBody()['nombre'];

        //chequeo que no este vacio
        if(isset($nombre)&&!empty($nombre)){

            if(strlen($nombre) <= 50){
                //chequeo si el nombre existe
                $stmt = $connection->prepare("SELECT COUNT(*) FROM localidades WHERE nombre = :nombre");
                $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
                $stmt->execute();
                $count = $stmt->fetchColumn();
    
                if($count > 0){
                    $payload = json_encode([
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'el nombre ya existe en la base de datos' 
                    ]);
                } else {
                    //inserto la localidad en la base de datos

                    $stmt = $connection->prepare("INSERT INTO localidades (nombre) VALUES (:nombre)");
                    $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
                    $stmt->execute();
        
                    $payload = json_encode([
                        'message'=> 'localidad insertada perfectamente',
                        'status' => 'success',
                        'code' => 200,
                        'data' => $nombre
                    ]);
                }
            } else {

                $payload = json_encode([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Ingreso más caracteres de los habilitados para el campo nombre.'
                ]);
            }
        } else {
            $payload = json_encode ([
                'status' => 'error',
                'code' => 400,
                'message' => 'No se ingreso el nombre.'
            ]);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type','application/json');

    } catch (PDOException $e){
        $json = json_encode([
            'status' => 'error',
            'code' => 400,
        ]);

        $response->getBody()->write($json);
        return $response-> withHeader('Content-Type','application/json');

    }

});

$app->post('/inquilinos',function(Request $request,Response $response){

    $connection = getConnection();

    $params = $request->getParsedBody();

    //verfico que se esten todos los parámetros necesarios para realizar el insert

    $requiredKeys = ["nombre","apellido","documento","email"];
    $missingKeys = []; //almaceno las claves que faltan

    foreach($params as $key => $value){
        if(in_array($key,$requiredKeys)){
            if(empty($value)){
                $missingKeys[] = $key; //las agrego al array
            }
        }
    }

    if(empty($missingKeys)){
        //no falta ninguna clave
        //corroborar que el tamaño de los strings sea el correspondiente. 
        
        $sizeErrorKeys = [];
        $maxChars = [
            "nombre" => 25,
            "apellido" => 15,
            "email" => 20
        ];

        foreach($params as $key => $value){
            if(in_array($key,array_keys($maxChars))){
                if(strlen($value) > $maxChars[$key]){
                    $sizeErrorKeys[] = $key;
                }
            }
        }

        if(empty($sizeErrorKeys)){
            //ningun string excede su tamaño 
            //ahora consulto si el documento ya se encuentra en la base de datos

            $stmt = $connection->prepare("SELECT * FROM inquilinos WHERE documento = :documento");
            $stmt->bindParam(':documento',$params['documento']);
            $stmt->execute();
    
            if(!$stmt->rowCount() > 0){
                // no existe ningun inquilino con ese documento
                // procedo a insertar al usuario en la base de datos

                $stmt = $connection->prepare("INSERT INTO inquilinos (nombre,apellido,email,documento,activo)
                                              VALUES (:nombre,:apellido,:email,:documento,:activo)");
                
                $stmt->bindParam(':nombre',$params['nombre']);
                $stmt->bindParam(':apellido',$params['apellido']);
                $stmt->bindParam(':email',$params['email']);
                $stmt->bindParam(':documento',$params['documento']);
                $stmt->bindParam(':activo',$params['activo']);
                $stmt->execute();

                $payload = json_encode([
                    'message' => 'El usuario se inserto en la base de datos correctamente.',
                    'status' => 'success',
                    'code' => 201,
                    'data' => $params
                ]);
    
                
            } else {

                $payload = json_encode([
                    'message' => 'Ya existe un inquilino con ese documento.',
                    'status' => 'Error',
                    'code' => 400,
                ]);
            }
        } else {

            $payload = json_encode([
                'message' => 'Los siguientes campos exceden la cantidad de caracteres habilitada',
                'status' => 'Error',
                'code' => 400,
                'data' => $sizeErrorKeys
            ]);
        }


    } else {

        $payload = json_encode([
            'message' => 'Falta completar los siguientes campos',
            'status' => 'Error',
            'code' => 400,
            'data' => $missingKeys
        ]);

        $data = 'Faltan los datos: ' . implode(', ',$missingKeys);
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type','application/json');

});

$app -> post ('/propiedades', function (Request $request, Response $response){
    $connection = getConnection();
    $params = $request -> getParsedBody();

    $requiredKeys = ["domicilio","localidad_id","cantidad_huespedes","fecha_inicio_disponibiidad","cantidad_dias","disponible","valor_noche","tipo_propiedad_id"];
    $missingKeys = [];

    foreach($params as $key => $value){
        if (in_array($key,$requiredKeys)){
            if(empty($value)){
                $missingKeys[] = $key;
            }
        }
    }
    //agregar verificacion de q el id se encuentre en la tabla
    if(empty($missingKeys)){
        $stmt = $connection->prepare("INSERT INTO propiedades(domicilio,localidad_id,cantidad_habitaciones,cantidad_banios,cochera,cantidad_huespedes,fecha_inicio_disponibilidad,cantidad_dias,disponible,valor_noche,tipo_propiedad_id,imagen,tipo_imagen)
                                      VALUES (:domicilio, :localidad_id, :cantidad_habitaciones, :cantidad_banios, :cochera, :cantidad_huespedes, :fecha_inicio_disponibilidad, :cantidad_dias, :disponible, :valor_noche, :tipo_propiedad_id, :imagen, :tipo_imagen)");
        $stmt->bindParam(':domicilio',$params['domicilio']);
        $stmt->bindParam(':localidad_id',$params['localidad_id']);
        $stmt->bindParam(':cantidad_habitaciones',$params['cantidad_habitaciones']);
        $stmt->bindParam(':cantidad_banios',$params['cantidad_banios']);
        $stmt->bindParam(':cochera',$params['cochera']);
        $stmt->bindParam(':cantidad_huespedes',$params['cantidad_huespedes']);
        $stmt->bindParam(':fecha_inicio_disponibilidad',$params['fecha_inicio_disponibilidad']);
        $stmt->bindParam(':cantidad_dias',$params['cantidad_dias']);
        $stmt->bindParam(':disponible',$params['disponible']);
        $stmt->bindParam(':valor_noche',$params['valor_noche']);
        $stmt->bindParam(':tipo_propiedad_id',$params['tipo_propiedad_id']);
        $stmt->bindParam(':imagen',$params['imagen']);
        $stmt->bindParam(':tipo_imagen',$params['tipo_imagen']);
        $stmt->execute();

        $payload = json_encode([
            'message' => 'La propiedad se inserto en la base de datos correctamente.',
            'status' => 'success',
            'code' => 201,
            'data' => $params
        ]);

        
    }
})

$app->run();
