<?php

namespace App\Action;


use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use App\Domain\User\Data\UserData;
final class InsertUserAction
{
    private $userCreator;

    public function __construct(UserCreator $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    public function __invoke(ServerRequest $request, Response $response): Response
    {
      
        



        $uploaded_name = $_FILES[ 'profile_photo' ][ 'name' ]; 
		$uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
		$uploaded_size = $_FILES[ 'profile_photo' ][ 'size' ]; 
		$uploaded_type = $_FILES[ 'profile_photo' ][ 'type' ]; 
		$uploaded_tmp  = $_FILES[ 'profile_photo' ][ 'tmp_name' ]; 



		// Where are we going to be writing to? 
		$target_path   = 'cdn/photos/'; 
		$target_file   =  md5( uniqid() . $uploaded_name ) . '.' . $uploaded_ext; 
		$temp_file     = ( ( ini_get( 'upload_tmp_dir' ) == '' ) ? ( sys_get_temp_dir() ) : ( ini_get( 'upload_tmp_dir' ) ) ); 
		$temp_file    .=   md5( uniqid() . $uploaded_name ) . '.' . $uploaded_ext; 

       

    // Is it an image? 
		if( ( strtolower( $uploaded_ext ) == 'jpg' || strtolower( $uploaded_ext ) == 'jpeg' || strtolower( $uploaded_ext ) == 'png'  || strtolower($uploaded_ext) == 'gif') && 
    ( $uploaded_size < 1000000 ) && 
    ( $uploaded_type == 'image/jpeg' || $uploaded_type == 'image/png'  || $uploaded_type == 'image/gif') && 
    getimagesize( $uploaded_tmp ) ) { 
    
    
    
    // Strip any metadata, by re-encoding image (Note, using php-Imagick is recommended over php-GD) 
    if( $uploaded_type == 'image/jpeg' ) { 

     /// Create a new image from uploaded the image 
      $img = imagecreatefromjpeg( $uploaded_tmp ); 

      //store new image to temp file
      imagejpeg( $img, $temp_file, 100); 
            }
      else if($uploaded_type == 'image/gif'){

        $img  = imagecreatefromgif($uploaded_tmp);

        imagegif($img,$temp_file);

      }       
    else { 
      $img = imagecreatefrompng( $uploaded_tmp ); 
      
      imagepng( $img, $temp_file, 9); 
    } 
    imagedestroy( $img ); 

  


    // Can we move the file to the web root from the temp folder? 
    if( rename( $temp_file, ( getcwd() . DIRECTORY_SEPARATOR . $target_path . $target_file ) ) ) { 
      // Yes! 
      $target = getcwd() . DIRECTORY_SEPARATOR . $target_path . $target_file;

      
      $user = new UserData();
      $user->username = $_POST['username'];
      $user->password = $_POST['password'];
      $user->profile_photo = $target_file;
      $user->profile_name = $_POST['profile_name'];
      $user->birthdate = $_POST['birthdate'];
      $user->status = $_POST['user_status'];
      $user->fcm_token = $_POST['fcm_token'];

        // Invoke the Domain with inputs and retain the result
        $userId = $this->userCreator->AddUser($user);
     

        $status = "uploaded";
       $result = [
           'user_id' => $userId,
           'status' => "username is used",
           'file_status' => $status
       ];

       if (!$userId) {
           // Invalid authentication credentials
           return $response
               ->withHeader('Content-Type', 'application/json')
               ->withStatus(401, 'Unauthorized')
               ->withJson($result);
               
       }


     
    } 
    else { 
      // No 

      
      $status = "not uploaded";
    } 
  
  
    // Delete any temp files 
    if( file_exists( $temp_file ) ) 
      unlink( $temp_file ); 
  } 
  else { 
    // Invalid file 
    $status = "not accepted";
  } 

 
     







        // Transform the result into the JSON representation
        $result = [
            'user_id' => $userId,
            'status' => "user created",
            'file_status' => $status
        ];

        // Build the HTTP response
        return $response->withJson($result)->withStatus(201);
    }
}