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
// $app->delete ('/tipos_propiedad/{$id}', function (Request $request, Response $response){
//     $connection = getConecction();
//     try{
//         $id = (int) $request->getAttribute('id');
//         $query = $connection->prepare('DELETE FROM tipo_propiedades WHERE id =:id');
//         $query->bindParam(':id', $id, PDO::PARAM_INT);
//         $query->execute();

//         $deletedRows = $query->rowCount();

//          if ($deletedRows > 0) {
//             $response -> withjson(['el id fue eliminado correctamente']);
//              return $response->withStatus(200); // OK (sin cuerpo)
//      } else {
//             $response -> withjson(['el id no fue encontrado']);
//              return $response->withStatus(404); // No encontrado
//     }
//   } catch (Exception $e) {
//     return $response->withStatus(500) // Error interno del servidor
//                    ->withJson([
//                      'error' => true,
//                      'message' => 'Error al eliminar el registro: ' . $e->getMessage()
//                    ]);
//   }
//         return $response -> withStatus(204);
        
    
// });
//public function eliminarRegistro($id) {
//    $sql = "DELETE FROM localidades WHERE id = :id";
//   $stmt = $this->connection->prepare($sql);
//    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
//    $stmt->execute();
//    return $stmt->rowCount() > 0; // Retorna true si se elimina el registro, false en caso contrario
//}

//public function __destruct() {
 //   $this->connection->close();
//}


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
        $query = $connection->query('SELECT * FROM propiedades p 
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


// $app->post('/localidades', function(Request $request,Response $response){
    
//     try{

//         $connection = getConnection();
//         $nombre = $request->getParsedBody()['nombre'];

//         //chequeo que no este vacio
//         if(isset($nombre)&&!empty($nombre)){
    
//             //chequeo si el nombre existe
//             $stmt = $connection->prepare("SELECT COUNT(*) FROM localidades WHERE nombre = :nombre");
//             $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
//             $stmt->execute();
//             $count = $stmt->fetchColumn();
    
//             if($count > 0){
//                 $payload = json_encode([
//                     'status' => 'error',
//                     'code' => 400,
//                     'message' => 'el nombre ya existe en la base de datos' 
//                 ]);
//             } else {
//                 //inserto la localidad en la base de datos

//                 $stmt = $connection->prepare("INSERT INTO localidades (nombre) VALUES (:nombre)");
//                 $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
//                 $stmt->execute();
        
//                 $payload = json_encode([
//                     'message'=> 'localidad insertada perfectamente',
//                     'status' => 'success',
//                     'code' => 201,
//                     'data' => $nombre
//                 ]);
//             }
//         } else {
//             $payload = json_encode ([
//                 'status' => 'error',
//                 'code' => 400,
//                 'message' => 'No ingreso ningun dato.'
//             ]);
//         }

//         $response->getBody()->write($payload);
//         return $response->withHeader('Content-Type','application/json');

//     } catch (PDOException $e){
//         $json = json_encode([
//             'status' => 'error',
//             'code' => 400,
//         ]);

//         $response->getBody()->write($json);
//         return $response-> withHeader('Content-Type','application/json');

//     }

// });

// $app->post('/tipos_propiedad',function(Request $request,Response $response){

//     try{
//         $connection = getConnection();

//         $nombre = $request->getParsedBody()['nombre'];

//         if(isset($nombre)&&!empty($nombre)){

//             //chequeo si el tipo esta en la base de datos

//             $stmt = $connection->prepare("SELECT COUNT(*) FROM tipo_propiedades WHERE nombre = :nombre");
//             $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
//             $stmt->execute();
//             $count = $stmt->fetchColumn();

//             if($count > 0){
//                 $payload = json_encode([
//                     'status' => 'error',
//                     'code' => 400,
//                     'message' => 'el nombre ya existe. Este debe ser único.'
//                 ]);
//             } else {

//                 $stmt = $connection->prepare("INSERT INTO tipo_propiedades (nombre) VALUES (:nombre)");
//                 $stmt->bindParam(':nombre',$nombre,PDO::PARAM_STR);
//                 $stmt->execute();

//                 $payload = json_encode([
//                     'message' => 'El tipo de propiedad se inserto correctamente',
//                     'status' => 'success',
//                     'code' => 201,
//                     'data' => $nombre
//                 ]);
//             }
//         } else {
//             $payload = json_encode([
//                 'message' => 'No ingreso el nombre',
//                 'code' => 400,
//                 'status' => 'error',
//             ]);
//         }

        
//         $response->getBody()->write($payload);
//         return $response->withHeader('Content+Type','application/json');

//     } catch (PDOException $e){
//         $json = json_encode([
//             'status' => 'error',
//             'code' => 400,
//         ]);

//         $response->getBody()->write($json);
//         return $response-> withHeader('Content-Type','application/json');

//     }

// });

// $app->post('/inquilinos',function(Request $request,Response $response){

//     $connection = getConnection();

//     $documento = $request->getParsedBody()['documento'];
//     $nombre = $request->getParsedBody()['nombre'];
//     $apellido = $request->getParsedBody()['apellido'];
//     $email = $request->getParsedBody()['email'];
//     $activo = $request->getParsedBody()['activo'];

//     $stmt = $connection->prepare("INSERT INTO inquilinos(documento,nombre,apellido,email,activo) VALUES (?,?,?,?,?)");

//     $stmt->bindParam(1,$documento,PDO::PARAM_STR);
//     $stmt->bindParam(2,$nombre,PDO::PARAM_STR);
//     $stmt->bindParam(3,$apellido,PDO::PARAM_STR);
//     $stmt->bindParam(4,$email,PDO::PARAM_STR);
//     $stmt->bindParam(5,$activo,PDO::PARAM_STR);

//     $stmt->execute();

//     $data = [
//         'message' => 'Se inserto el nuevo inquilino!',
//         'documento' => $documento,
//         'nombre' => $nombre,
//         'apellido' => $apellido,
//         'email' => $email,
//         'activo' => $activo
//     ];

//     $payload = json_encode($data);

//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type','application/json');

// });



        

$app->run();
