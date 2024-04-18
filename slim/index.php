<?php
//CONFIGURACION
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

//LOCALIDADES
    //CREAR
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
    //EDITAR
    //ELIMINAR
    //LISTAR
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

//TIPO PROPIEDAD
    //CREAR
    
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
    //EDITAR
    //ELIMINAR
    //LISTAR
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


//INQUILINO
    //CREAR
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
    //EDITAR
        $app->put('/inquilinos/{id}', function(Request $request,Response $response){    

            $connection = getConnection();
        
            try{
        
                $id = $request->getAttribute('id');
        
                $stmt = $connection->prepare("SELECT * FROM inquilinos WHERE id = :id");
                $stmt->bindParam(':id',$id);
                $stmt->execute();
        
                //chequeo que exista el id del inquilino
                if($stmt->rowCount() > 0){
        
                    $params = $request->getParsedBody();
                    
        
                    //chequeo que ninguno de los campos que mando para modificar este vacio
                    $keys = ["nombre","apellido","documento","email","activo"];
                    $emptyFields = [];
                
                    //mientras exista la calve y este vacio, la agrego a emptyFields.
                    foreach ($keys as $key) {
                        if (isset($params[$key]) && empty($params[$key])) {
                            $emptyFields[] = $key;
                        }
                    }
                    
                    //si habia un cambio vacio, no entra, y envia caul es.
                    if(count($emptyFields) == 0){
        
                        //chequeo la cantidad de caracteres de los nuevos campos
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
        
                            $documentoNoEsta = true;
                            //chequeo si traje un valor en el campo documento, en caso de hacerlo, chequeo si ya esta en la base de datos.
                            if(isset($params['documento'])){
        
                                $stmtDocumento = $connection->prepare("SELECT COUNT(*) FROM inquilinos WHERE documento = :documento");
                                $stmtDocumento->bindParam(':documento', $params['documento']);
                                $stmtDocumento->execute();
                                $documentoNoEsta = $stmtDocumento->fetchColumn() == 0;
                                
                            }
        
                            //en caso de de que no se haya mandado ningun documento sigo.En caso de que si y no este en la base de datos también.
                            //solamente cuando el documento se mando y ya esta en la base de datos esta condicion resulta falsa.
                            if($documentoNoEsta){
                                
                                //ahora que paso todas las pruebas, genero la consulta sql dinámicamente
        
                                
                                $sql = "UPDATE inquilinos SET ";
        
                                foreach ($params as $campo => $valor) {
                                    $sql .= "`$campo` = :$campo, ";
                                }
        
                                // eliminamos la coma al final
                                $sql = rtrim($sql, ', ');
        
                                $sql .= " WHERE id = :id";
        
                                $stmt = $connection->prepare($sql);
        
                                //vinculo los valores a los parámetros
                                $stmt->bindParam(':id', $id);
                                foreach ($params as $campo => $valor) {
                                    $stmt->bindParam(":$campo", $params[$campo]);
                                }
        
                                $stmt->execute();
        
                                
                                $payload = json_encode([
                                    'message' => 'El inquilino con id '.$id." actualizo los siguientes datos correctamente!",
                                    'status' => 'success',
                                    'code' => 200,
                                    'data' => $params
                                ]);
        
                            } else {
                                $payload = json_encode([
                                    'message' => 'Este documento ya se encuentra en la base de datos.',
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
                            'message' => 'Los siguientes campos estan vacios.',
                            'status' => 'Error',
                            'code' => 400,
                            'data' => $emptyFields
                        ]);
                    }
        
                } else {
        
                    $payload = json_encode([
                        'message' => 'No existe un inquilino con ese id',
                        'status' => 'Error',
                        'code' => 400,
                    ]);
        
                }
            
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type','application/json');
        
        
            } catch (PDOException $e){
                $json = json_encode([
                    'status' => 'Error',
                    'code' => 400,
                ]);
        
                $response->getBody()->write($json);
                return $response-> withHeader('Content-Type','application/json');
        
            }
        
        });
    
    //ELIMINAR
    //LISTAR
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
    //VER INQUILINO
    
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
    //HISTORIAL

//PROPIEDAD
    //CREAR
        $app -> post ('/propiedades', function (Request $request, Response $response){
            $connection = getConnection();
            $datos = $request -> getParsedBody();
            $params= [
                "domicilio" => trim($datos["domicilio"]),
                "localidad_id" => trim($datos["localidad_id"]),
                "cantidad_huespedes" => trim($datos["cantidad_huespedes"]),
                "fecha_inicio_disponibilidad" => trim($datos["fecha_inicio_disponibilidad"]),
                "cantidad_dias" => trim($datos["cantidad_dias"]),
                "disponible" => trim($datos["disponible"]),
                "valor_noche" => trim($datos["valor_noche"]),
                "tipo_propiedad_id" => trim($datos["tipo_propiedad_id"]),
            ];
            $requiredKeys = ["domicilio","localidad_id","cantidad_huespedes","fecha_inicio_disponibilidad","cantidad_dias","disponible","valor_noche","tipo_propiedad_id"];
            $missingKeys = [];
        
            foreach($params as $key => $value){
                if (in_array($key,$requiredKeys)){
                    if(empty($value)){
                        $missingKeys[] = $key;
                    }
                }
            }
            
            if(empty($missingKeys)){
        
                $stmt = $connection->prepare("SELECT * FROM localidades WHERE id = :localidad_id");
                $stmt->bindParam(':localidad_id',$params['id']);
                $stmt->execute();
        
            if (!$stmt->rowCount() > 0) {
        
                $stmt = $connection->prepare("SELECT * FROM tipo_propiedades WHERE id = :tipo_propiedad_id");
                $stmt->bindParam(':tipo_propiedad_id',$params['id']);
                $stmt->execute();
        
                if (!$stmt->rowCount() > 0) {
        
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
            } else {
        
                $payload = json_encode([
                    'message' => 'El tipo de propiedad no existe.',
                    'status' => 'Error',
                    'code' => 400,
                ]);
            }
            } else {
        
                $payload = json_encode([
                    'message' => 'La localidad no existe.',
                    'status' => 'Error',
                    'code' => 400,
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
    //EDITAR
    //ELIMINAR
    //LISTAR
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
    //VER PROPIEDAD

//RESERVA
    //CREAR
    //EDITAR
    //ELIMINAR
    //LISTAR
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
















$app->run();
