<?php
    date_default_timezone_set('America/Sao_Paulo');
    $date = date('H:i');
    echo $date.' Inicio.'.PHP_EOL;
    error_reporting(E_ALL);


    set_time_limit(0);
   
    ob_implicit_flush();
    
    static $byte = '1024'; //1024, 4096, 65536
    static $address;
    static $port;
    
    $address = fopen('hosts.conf', 'r');
    $address = fgets($address,$byte);
    
    $port = fopen('ports.conf', 'r');
    $port = fgets($port,$byte);

    
    $buffer = '';
    
    
    
    
    if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {

        echo "Ops, ocorreu um erro no create do socket: " . socket_strerror(socket_last_error()) . "\n";
    }

    if(socket_bind($socket, $address, intval($port)) === false){

        echo "Ops, ocorreu um erro no bind do socket: " . socket_strerror(socket_last_error()) . "\n";
    }

    if (socket_listen($socket, 1) === false) {

        echo "Ops, ocorreu um erro no listen do socket: " . socket_strerror(socket_last_error()) . "\n";
    }
    
    
    
    do{
        if (($conn = socket_accept($socket)) === false) {            
            echo "Ops, ocorreu um erro no accept do socket: " . socket_strerror(socket_last_error($socket)) . "\n";
            break;
        }
	$header_client = socket_read($conn, $byte);
	list($type_request, $request, $protocol) = explode(" ", $header_client);
        
        if($request == '/' || $request == ''){
		$request = '/index.php';
	}
 	if(file_exists(getcwd().$request)){
		$code_http = '200';
	}else{
		$code_http = '404';
	}
	if($code_http == '404'){
		$buffer = shell_exec('php 404.php');
	}else{

		//$texto = @fopen(getcwd().$request, 'r');

	        $texto = shell_exec('php '.getcwd().$request);    
		$header_server = shell_exec('php 200.php');
 			           
            	$buffer = $header_server.$texto;             	
       		
       		echo $buffer;
	
	}

        

        socket_write($conn, $buffer, strlen($buffer));
        
        socket_close($conn);
        $date = date('H:i');
        echo $date.' '. socket_strerror(socket_last_error());

        $buffer = '';
    }while(true);

    
    socket_close($socket);


