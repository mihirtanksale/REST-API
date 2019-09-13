<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

require '../includes/DbOperations.php';

$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails'=>true
    ]
]);
/*
    endpoint : createuser
    parameters: Dal_name,Kishore,Kumar,Yuvak,Margadarshak
    method: POST
*/
$app->post('/createuser' , function(Request $request,Response $response){
    if(!haveEmptyParameters(array('Dal_name','Kishor','Kumar','Yuvak','Margadarshak'),$request,$response)){
        $request_data = $request->getParsedBody();

        $Dal_name = $request_data['Dal_name'];
        $Kishor = $request_data['Kishor'];
        $Kumar = $request_data['Kumar'];
        $Yuvak = $request_data['Yuvak'];
        $Margadarshak = $request_data['Margadarshak'];

        $db = new DbOperations;
        $result = $db->createUser($Dal_name, $Kishor, $Kumar, $Yuvak, $Margadarshak);
        

        if($result == USER_CREATED){

            $message = array();
            $message['error'] = false;
            $message['message'] = 'USER created successfully';

            $response->write(json_encode($message));

            return $response 
                        ->withHeader('Content-type','application/json')
                        ->withStatus(201);//request completed new resource created


        }else if($result == USER_FAILURE) {

            $message = array();
            $message['error'] = true;
            $message['message'] ='Some error occurred';

            $response->write(json_encode($message));

            return $response 
                        ->withHeader('Content-type','application/json')
                        ->withStatus(422);

            
        }else if($result == USER_EXISTS) {
           
            $message = array();
            $message['error'] = true;
            $message['message'] = 'User Already Exists';

            $response->write(json_encode($message));

            return $response 
                        ->withHeader('Content-type','application/json')
                        ->withStatus(422);

        }
    }

    return $response 
                ->withHeader('Content-type','application/json')
                ->withStatus(422);


});

$app->post('/userlogin', function(Request $request,Response $response){
    if(!haveEmptyParameters(array('Dal_name'),$request, $response)){
        $request_data = $request->getParsedBody();

        $Dal_name = $request_data['Dal_name'];
       
        $db = new DbOperations;

        $result = $db->userLogin($Dal_name);

        if($result == USER_AUTHENTICATED){

            $user=$db->getUserByDalname($Dal_name);
            $response_data=array();

            $response_data['error'] = false;
            $response_data['message'] = 'Dal name correct Login Successful';
            $response_data['user']=$user;

            $response->write(json_encode($response_data));
            
            return $response 
                ->withHeader('Content-type','application/json')
                ->withStatus(422);


        }else if ($result == USER_NOT_FOUND){
            $response_data=array();

            $response_data['error'] = true;
            $response_data['message'] = 'User not exist';
            
            $response->write(json_encode($response_data));
            
            return $response 
                ->withHeader('Content-type','application/json')
                ->withStatus(200);

        }


    }
    return $response 
                ->withHeader('Content-type','application/json')
                ->withStatus(422);
});

$app->get('/allusers', function(Request $request, Response $response){
    $db = new DbOperations;
    $users = $db->getAllUsers();
    $response_data  = array();
    $response_data['error'] = false;
    $response_data['users'] = $users;

    $response->write(json_encode($response_data));
    return $response 
    ->withHeader('Content-type','application/json')
    ->withStatus(200);

});


$app->put('/updateUser/{id}',function(Request $request,Response $response,array $args){
    $id = $args['id'];
    if(!haveEmptyParameters(array('Dal_name', 'Kishor', 'Kumar', 'Yuvak', 'Margadarshak','id'),$request, $response)){
        $request_data = $request->getParsedBody();

        $Dal_name = $request_data['Dal_name'];
        $Kishor = $request_data['Kishor'];
        $Kumar = $request_data['Kumar'];
        $Yuvak = $request_data['Yuvak'];
        $Margadarshak = $request_data['Margadarshak'];
        $id = $request_data['id'];

        $db = new DbOperations;
        if($db->updateUser($Dal_name,$Kishor,$Kumar,$Yuvak, $Margadarshak,$id)){
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = 'User Updated Successfully';
            $user = $db->getUserByDalname($Dal_name);
            $response_data =$user;

            $response->write(json_encode($response_data));
            
            return $response 
                        ->withHeader('Content-type','application/json')
                        ->withStatus(200);
        }else{
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'Try again later';
            $user = $db->getUserByDalname($Dal_name);
            $response_data =$user;

            $response->write(json_encode($response_data));
            
            return $response 
                        ->withHeader('Content-type','application/json')
                        ->withStatus(200);
        }

    }
    return $response 
    ->withHeader('Content-type','application/json')
    ->withStatus(200);
});

$app->delete('/deleteUser/{id}',function(Request $request,Response $response,array $args){
    $id = $args['id'];

    $db=new DbOperations;
    $response_data=array();
    
    if($db->deleteUser($id)){
        $request_data['error'] = false;
        $response_data['message'] = 'User has been deleted';
    }else{
        $request_data['error'] = true;
        $response_data['message'] = 'Try again';
    }

    $response->write(json_encode($response_data));
    return $response
        ->withHeader('Content-type','application/json')
        ->withStatus(200);
});

function haveEmptyParameters($required_params,$request,$response){
    $error = false;
    $error_params = '';
    $request_params = $request->getParsedBody();

    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true;
            $error_params .= $param .', ';
        }
    }

    if($error){
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] ='Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error;
}
$app->run();
