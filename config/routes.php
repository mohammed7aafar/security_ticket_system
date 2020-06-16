<?php

use Slim\App;
use App\Action\PreflightAction;
use App\Admin;
use App\User;
use App\Ticket;
use App\Category;
use App\Admin_log;
use App\Admin_rating;
use App\Blog_category;
use App\Blog_post;
use App\XssAttack;
use App\Middleware\JwtMiddleware;
use App\Middleware\JwtMiddleware2;
use App\Middleware\JwtMiddleware3;
use App\Middleware\JwtMiddleware4;
use App\Middleware\JwtMiddleware5;
use App\Ticket_response;
use Slim\Routing\RouteCollectorProxy;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use App\Auth\FileResponse;
use Illuminate\Database\Capsule\Manager as DB;



return function (App $app) {





/*
//////////////////////////
////////////////
/////////////
///////
       => Public Authentication & Verification  
///////
////////////
////////////////
//////////////////////////
*/





     //authentication
     $app->post('/dashboard/api/verify', \App\Action\TokenCreateAction::class);

     //Before the browser sends the real request a preflight request is sent to the same URL using the http OPTIONS method
     //The API must answer this options request with the status code 200.
     // Allow preflight requests for /api/admin/login
  

     $app->post('/dashboard/api/login', \App\Action\LoginAction::class);
    
   


/*
//////////////////////////
////////////////
/////////////
///////
       =>>>>  Shared Routes   <<<<=   
///////
////////////
////////////////
//////////////////////////
*/




$app->group('/dashboard/api', function (RouteCollectorProxy $group) {


   
 //get all
 $group->get('/admins', \App\Action\GetUserAction::class);


    //get all

 $group->get('/posts', function (ServerRequest $request, Response $response, $args) {     
  $row =  Blog_post::get()
  ->count();
    
return $response->withJson($row)->withStatus(201);
});


$group->post('/post/logs', \App\Action\postAdminLogsAction::class);


$group->post('/send/notification', function (ServerRequest $request, Response $response, $args) {     
  
  $data = (array)$request->getParsedBody();

  $config = HTMLPurifier_Config::createDefault();

  $purifier = new HTMLPurifier($config);
  

  $values = [
      'admin_id' => $purifier->purify($data['admin_id']),
      'title' => $data['title'],
      'text' => $purifier->purify($data['text']),
      'sender_id' => $purifier->purify($data['sender_id']),
      'date' =>  date('Y-m-d H:i:s')
  ];
  
  $notification_id = DB::table("admin_notification")
  ->insertGetId($values); 

  $result = [
      'notification_id' => $notification_id,
      'status' => "notification sent",
  ];
  
return $response->withJson($result)->withStatus(201);
});


  
$group->get('/notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
      
  
      
$tickets = DB::
table('admin_notification')
->join('admins','admins.id','=','admin_notification.sender_id')
->where(['admin_id' => $args['id']])
->select('admin_notification.*','admins.email','admins.level')
->get();
 
    
return $response->withJson($tickets)->withStatus(201);
});




$group->delete('/delete_notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  DB::
table('admin_notification')
->where(['id' => $args['id']])
->delete();

  $result =[
      "status" => "deleted"
  ];
return $response->withJson($result)->withStatus(201);
});




$group->get('/get_visters', function (ServerRequest $request, Response $response, $args) {     
   
  
  /// inner join two tables(users,categories) to the tickets table
 
$tickets = DB::
table('visters')
->get();


      
return $response->withJson($tickets)->withStatus(201);
});


$group->get('/get_tickets', function (ServerRequest $request, Response $response, $args) {     
   
  
  /// inner join two tables(users,categories) to the tickets table
 
$tickets = DB::
table('tickets')
->get()
->count();


      
return $response->withJson($tickets)->withStatus(201);
});


$group->get('/get_users', function (ServerRequest $request, Response $response, $args) {     
  $row =  User::get()
  ->count();
      
     
    
return $response->withJson($row)->withStatus(201);
});

 })->add(JwtMiddleware5::class);














/*
//////////////////////////
////////////////
/////////////
///////
       => owner MiddleWare  
///////
////////////
////////////////
//////////////////////////
*/




     $app->group('/dashboard/api/owner', function (RouteCollectorProxy $group) {

      
    //create
        $group->post('/create', \App\Action\UserCreateAction::class);



     //get all
     $group->get('/admins', \App\Action\GetUserAction::class);


       
    //delete
      
        $group->delete('/dele/[{id}]', function (ServerRequest $request, Response $response, $args) {     
            Admin::destroy($args['id']);
            $result =[
                "status" => "deleted"
            ];
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withJson($result)->withStatus(201);
        });

      //get one
        $group->get('/get/[{id}]', function (ServerRequest $request, Response $response, $args) {     
            $row =  Admin::select('id','email','level')
            ->where('id',$args['id'])->get();
                
               
              
          return $response
          ->withHeader('Content-Type', 'application/json')
          ->withJson($row)->withStatus(201);
          });
       //update

        $group->put('/up/[{id}]', \App\Action\UpdateUserAction::class);
      


        /// Blog_post

 //create
 $group->post('/post', \App\Action\AddBlogPostAction::class);

 //update
 $group->put('/post/[{id}]', \App\Action\UpdateBlogPostsAction::class);


 //get all

 $group->get('/posts', function (ServerRequest $request, Response $response, $args) {     
    $row =  Blog_post::select('id','title','text','cover','date','admin_id','category_id')
    ->get();
    
        
       
      
  return $response->withJson($row)->withStatus(201);
  });

// get one
  $group->get('/posts/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    $row =  Blog_post::select('id','title','text','cover','date','admin_id','category_id')
    ->where('id',$args['id'])->get();
        
    
  return $response->withJson($row)->withStatus(201);
  });

//delete

$group->delete('/delete/post/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    Blog_post::destroy($args['id']);
    $result =[
        "status" => "deleted"
    ];
return $response->withJson($result)->withStatus(201);
});



$group->get('/admin_posts/[{id}]', \App\Action\GetAdminPostsAction::class);


 




/// admin logs /// 


$group->get('/admin_logs/[{id}]', \App\Action\GetAdminsAction::class);

$group->post('/post/logs', \App\Action\postAdminLogsAction::class);


$group->delete('/logs/[{id}]', function (ServerRequest $request, Response $response, $args) {     
     Admin_log::destroy($args['id']);

     $result =[
         "status" => "deleted"
     ];
return $response->withJson($result)->withStatus(201);
});




  //// Blog Category 


    /// insert
    $group->post('/add_category', function (ServerRequest $request, Response $response, $args) {     
  

             
    $uploaded_name = $_FILES[ 'category_icon' ][ 'name' ]; 
		$uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
		$uploaded_size = $_FILES[ 'category_icon' ][ 'size' ]; 
		$uploaded_type = $_FILES[ 'category_icon' ][ 'type' ]; 
		$uploaded_tmp  = $_FILES[ 'category_icon' ][ 'tmp_name' ]; 



		// Where are we going to be writing to? 
		$target_path   = 'uploads/'; 
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
      $category_name = $_POST["category_name"];
     
    
    
      $values = [
        'category_name' => $category_name,
        'category_icon' => $target_file,
        
    ];
    
    $blog= Blog_category::insertGetId($values);

      $status = "uploaded";
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
     
     
  
      $result = [
          'user_id' => $blog,
          'status' => $status,
      ];
      
    return $response->withJson($result)->withStatus(201);
    });
  

  
  ///update
    $group->put('/update_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    
      $data = (array)$request->getParsedBody();
  
      $values = [
          'category_name' => $data['category_name'],
          'category_icon' => $data['category_icon']
          
      ];
      




      $blog= Blog_category::findOrFail($args['id'])
      ->update($values);
  
      $result = [
          'user_id' => $blog,
          'status' => "blog updated",
      ];
      
    return $response->withJson($result)->withStatus(201);
    });
  
  
  ///delete
    $group->delete('/delete_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
      Blog_category::destroy($args['id']);
      $result =[
          "status" => "deleted"
      ];
  return $response->withJson($result)->withStatus(201);
  });
  
  
  ///get all
  $group->get('/get_category', function (ServerRequest $request, Response $response, $args) {     
      $row =  Blog_category::select('id','category_name','category_icon')
      ->get();
          
        
    return $response->withJson($row)->withStatus(201);
    });
  
      //get one
    $group->get('/get_categories/[{id}]', function (ServerRequest $request, Response $response, $args) {     
      $row =  Blog_category::select('id','category_name','category_icon')
      ->where('id',$args['id'])->get();
          
        
    return $response->withJson($row)->withStatus(201);
    });
     
  
  /// relationship
  
    $group->get('/category_posts/[{id}]', function (ServerRequest $request, Response $response, $args) {     
      
      $posts =  Blog_post::where(['category_id' => $args['id']])->get();
          
$tickets = DB::
  table('blog_posts')
  ->join('blog_categories','blog_categories.id','=','blog_posts.category_id')
  ->where(['category_id' => $args['id']])
  ->select('blog_posts.*','blog_categories.category_name','blog_categories.category_icon')
  ->get();
     
        
    return $response->withJson($tickets)->withStatus(201);
    });
  
  


//// Admin ratings

/// insert
$group->post('/rate_admin', function (ServerRequest $request, Response $response, $args) {     
  
  $data = (array)$request->getParsedBody();

  $values = [
      'admin_id' => $data['admin_id'],
      'ticket_id' => $data['ticket_id'],
      'rating' => $data['rating'],
      'description' =>$data['description'],
      'date' =>  date('Y-m-d H:i:s')
      
  ];
  
  $rating= Admin_rating::insertGetId($values);

  $result = [
      'admin_rating_id' => $rating,
      'status' => "admin rated",
  ];
  
return $response->withJson($result)->withStatus(201);
});


$group->put('/update_rate_admin/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  
  $data = (array)$request->getParsedBody();

  $values = [
      'admin_id' => $data['admin_id'],
      'ticket_id' => $data['ticket_id'],
      'rating' => $data['rating'],
      'description' =>$data['description'],
      'date' =>  date('Y-m-d H:i:s')
      
  ];
  
  $rating= Admin_rating::findOrFail($args['id'])
  ->update($values);

  $result = [
      'admin_rating_id' => $rating,
      'status' => "admin rate updated",
  ];
  
return $response->withJson($result)->withStatus(201);
});


  ///delete
  $group->delete('/delete_admin_rate/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    Admin_rating::destroy($args['id']);
    $result =[
        "status" => "deleted"
    ];
return $response->withJson($result)->withStatus(201);
});


// get last admin rate for one ticket

$group->get('/get_admin_rate_last/{id}/{id2}', function (ServerRequest $request, Response $response, $args) {  
  
  $id =$request->getAttribute('id');
  $id2 =$request->getAttribute('id2');

  $row =  Admin_rating::select('id','rating','description','admin_id','ticket_id')
  ->where('admin_id',$id)
  ->where('ticket_id',$id2)
  ->get()->last();
    
return $response->withJson($row)->withStatus(201);
});

// get all rating for single ticket

$group->get('/get_admin_rate/[{id}]', function (ServerRequest $request, Response $response, $args) {  
  

  $tickets = DB::
  table('admin_ratings')
  ->join('admins','admins.id','=','admin_ratings.admin_id')
  ->join('tickets','tickets.id','=','admin_ratings.ticket_id')
  ->where(['ticket_id' => $args['id']])
  ->select('admin_ratings.*','admins.email')
  ->get();
   

    
return $response->withJson($tickets)->withStatus(201);
});


///update

$group->put('/update_ticket/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  
  $data = (array)$request->getParsedBody();

  $values = [
     
      'status' => $data['status'],

  ];
  
  $ticket = Ticket::findOrFail($args['id'])
  ->update($values);

  $result = [
      'ticket_id' => $ticket,
      'status' => "ticket updated",
  ];
  
return $response->withJson($result)->withStatus(201);
});
     ///delete
 
$group->delete('/delete_ticket/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  Ticket::destroy($args['id']);
  $result =[
      "status" => "deleted"
  ];
return $response->withJson($result)->withStatus(201);
});





$group->post('/send/notification', function (ServerRequest $request, Response $response, $args) {     
  
  $data = (array)$request->getParsedBody();

  $values = [
      'admin_id' => $data['admin_id'],
      'title' => $data['title'],
      'text' => $data['text'],
      'sender_id' => $data['sender_id'],
      'date' =>  date('Y-m-d H:i:s')
  ];
  
  $notification_id = DB::table("admin_notification")
  ->insertGetId($values); 

  $result = [
      'notification_id' => $notification_id,
      'status' => "notification sent",
  ];
  
return $response->withJson($result)->withStatus(201);
});


  
$group->get('/notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
      
  
      
$tickets = DB::
table('admin_notification')
->join('admins','admins.id','=','admin_notification.sender_id')
->where(['admin_id' => $args['id']])
->select('admin_notification.*','admins.email','admins.level')
->get();
 
    
return $response->withJson($tickets)->withStatus(201);
});




$group->delete('/delete_notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  DB::
table('admin_notification')
->where(['id' => $args['id']])
->delete();

  $result =[
      "status" => "deleted"
  ];
return $response->withJson($result)->withStatus(201);
});




$group->get('/get_visters', function (ServerRequest $request, Response $response, $args) {     
   
  
  /// inner join two tables(users,categories) to the tickets table
 
$tickets = DB::
table('visters')
->get();


      
return $response->withJson($tickets)->withStatus(201);
});


$group->get('/get_tickets', function (ServerRequest $request, Response $response, $args) {     
   
  
  /// inner join two tables(users,categories) to the tickets table
 
$tickets = DB::
table('tickets')
->join('categories','categories.id','=','tickets.category_id')
->join('users','users.id','=','tickets.user_id')
->select('tickets.*','categories.category_name','categories.category_icon','categories.parent_id','users.username','users.profile_name','users.profile_photo','users.birthdate','users.user_status','users.fcm_token')
->get();


      
return $response->withJson($tickets)->withStatus(201);
});


$group->get('/get_users', function (ServerRequest $request, Response $response, $args) {     
  $row =  User::select('id','username','profile_name','profile_photo','birthdate','user_status')
  ->get();
      
     
    
return $response->withJson($row)->withStatus(201);
});

///delete
$group->delete('/delete_user/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  User::destroy($args['id']);
  $result =[
      "status" => "deleted"
  ];
return $response->withJson($result)->withStatus(201);
});



$group->delete('/delete_users/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  User::destroy($args['id']);
  $result =[
      "status" => "deleted"
  ];
return $response->withJson($result)->withStatus(201);
});



$group->get('/get_tickets/[{id}]', function (ServerRequest $request, Response $response, $args) {     
   
  
  /// inner join two tables(users,categories) to the tickets table
 
$tickets = DB::
table('tickets')
->join('categories','categories.id','=','tickets.category_id')
->join('users','users.id','=','tickets.user_id')
->where(['category_id' => $args['id']])
->select('tickets.*','categories.category_name','categories.category_icon','categories.parent_id','users.username','users.profile_name','users.profile_photo','users.birthdate','users.user_status','users.fcm_token')
->get();


      
return $response->withJson($tickets)->withStatus(201);
});





    //// Ticket Category 


    /// insert
    $group->post('/add_ticket_category', function (ServerRequest $request, Response $response, $args) {     
  
     
          
      $uploaded_name = $_FILES[ 'icon_image' ][ 'name' ]; 
      $uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
      $uploaded_size = $_FILES[ 'icon_image' ][ 'size' ]; 
      $uploaded_type = $_FILES[ 'icon_image' ][ 'type' ]; 
      $uploaded_tmp  = $_FILES[ 'icon_image' ][ 'tmp_name' ]; 
  
  
  
      // Where are we going to be writing to? 
      $target_path   = 'uploads/'; 
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
        $img = imagecreatefromjpeg( $uploaded_tmp); 
  

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
        $category_name = $_POST["category_name"];
        $parent_id = $_POST["parent_id"];
      
      
            $values = [
                'category_name' => $category_name,
                'category_icon' => $target_file,
                'parent_id' => $parent_id
                
            ];
            
            $category= Category::insertGetId($values);
  
        $status = "uploaded";
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
       
  
   
    
        $result = [
           "status" => $status,
            'user_id' => $category,
            
        ];
  
       
        
        
      return $response->withJson($result)->withStatus(201);
      });
    

/// get main categories which parent_id  == 0

$group->get('/get_main_categories', function (ServerRequest $request, Response $response, $args) {     
  $row =  Category::select('id','category_name','category_icon','parent_id')
  ->where('parent_id',0)->get();
      
    
return $response->withJson($row)->withStatus(201);
});



 //get sub menue of main category

 $group->get('/ticket_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  $row =  Category::select('id','category_name','category_icon','parent_id')
  ->where('parent_id',$args['id'])->get();
      
    
return $response->withJson($row)->withStatus(201);
});
    
  ///delete sub category
  $group->delete('/ticket_category_delete/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    Category::destroy($args['id']);
    $result =[
        "status" => "deleted"
    ];
return $response->withJson($result)->withStatus(201);
});



///delete main category's sub menues 
$group->delete('/ticket_sub_categories_delete/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  Category::where('parent_id',$args['id'])

  ->delete();
  $result =[
      "status" => "deleted"
  ];
return $response->withJson($result)->withStatus(201);
});


$group->post('/replay_ticket/[{token}]', function (ServerRequest $request, Response $response, $args) {     
  
  $data = (array)$request->getParsedBody();

  $values = [
      'admin_id' => $data['admin_id'],
      'user_id' => $data['user_id'],
      'ticket_id' => $data['ticket_id'],
      'response_text' => $data['response_text'],
      'response_date' => date('Y-m-d H:i:s'),
      'current' => $data['current'],
      'attachment_photo' => ""

  ];



  

  // API access key from Google API's Console
define('API_ACCESS_KEY2', 'AAAA_Wl9msE:APA91bFJ3OicimCLvUNmIEggjLWKY3diBELWQFbv3AgE74uu0jZ8nfpzKv5-fcmCgLj_50fq-AmGIzj_9EishIGQApriH6tUMw1O174h8icA_6SJesAVOo6Cb_bYcis-hShc5loOFY-Q');
// prep the bundle
$msg = array
(
'ticket_id' =>  $data['ticket_id'],
'priority' => 'high',
'sound' => 'default',
'time_to_live' => 3600
);
$fields = array('to' => $args['token'], 'notification' => $msg);

$headers = array
(
'Authorization: key=' . API_ACCESS_KEY2,
'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
$result = curl_exec($ch);
curl_close($ch);



  
  $ticket = Ticket_response::insertGetId($values);

  $result = [
      'replay_ticket_id' => $ticket,
      'status' => "response sent",
      'notification' => $result
  ];
  
return $response->withJson($result)->withStatus(201);
});






// all 
$group->get('/admin_response/{id}/{id2}/{id3}', function (ServerRequest $request, Response $response, $args) {    
  
  $id =$request->getAttribute('id');
  $id2 =$request->getAttribute('id2');
  $id3 =$request->getAttribute('id3');

  $row =  Ticket_response::select('*')
  ->where('admin_id',$id)
  ->where('ticket_id',$id2)
  ->where('user_id',$id3)
  ->get();



    
return $response->withJson($row)->withStatus(201);
});





$group->post('/add_attachment_photo/[{token}]', function (ServerRequest $request, Response $response, $args) {     
  
     
          
  $uploaded_name = $_FILES[ 'attachment_photo' ][ 'name' ]; 
  $uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
  $uploaded_size = $_FILES[ 'attachment_photo' ][ 'size' ]; 
  $uploaded_type = $_FILES[ 'attachment_photo' ][ 'type' ]; 
  $uploaded_tmp  = $_FILES[ 'attachment_photo' ][ 'tmp_name' ]; 



  // Where are we going to be writing to? 
  $target_path   = 'uploads/'; 
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
    



    $admin_id = $_POST["admin_id"];
    $user_id = $_POST["user_id"];
    $ticket_id = $_POST["ticket_id"];
    $response_text = $_POST["response_text"];
    $current = $_POST["current"];
    


    $values = [
        'admin_id' => $admin_id,
        'user_id' => $user_id,
        'ticket_id' => $ticket_id,
        'response_text' => $response_text,
        'response_date' => date('Y-m-d H:i:s'),
        'current' => $current,
        'attachment_photo' => $target_file
        
    ];
  
  
  
    
  
    // API access key from Google API's Console
  define('API_ACCESS_KEY', 'AAAA_Wl9msE:APA91bFJ3OicimCLvUNmIEggjLWKY3diBELWQFbv3AgE74uu0jZ8nfpzKv5-fcmCgLj_50fq-AmGIzj_9EishIGQApriH6tUMw1O174h8icA_6SJesAVOo6Cb_bYcis-hShc5loOFY-Q');
  // prep the bundle
  $msg = array
  (
  'ticket_id' =>  $_POST['ticket_id'],
  'priority' => 'high',
  'sound' => 'default',
  'time_to_live' => 3600
  );
  $fields = array('to' => $args['token'], 'notification' => $msg);
  
  $headers = array
  (
  'Authorization: key=' . API_ACCESS_KEY,
  'Content-Type: application/json'
  );
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
  $result = curl_exec($ch);
  curl_close($ch);
  
  
  
    
    $ticket = Ticket_response::insertGetId($values);
  



    $status = "uploaded";
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
   



$result = [
  'replay_ticket_id' => $ticket,
  'status' => $status,
  'notification' => $result
];

   
    
    
  return $response->withJson($result)->withStatus(201);
  });





        // Allow preflight requests for /api/admin/
        // Due to the behaviour of browsers when sending a request,
        // you must add the OPTIONS method.
       
    })->add(JwtMiddleware::class);


/*
//////////////////////////
////////////////
/////////////
///////
       => admin MiddleWare  
///////
////////////
////////////////
//////////////////////////
*/








 $app->group('/dashboard/api/admin', function (RouteCollectorProxy $group) {

  
//create
    $group->post('/create', \App\Action\UserCreateAction::class);



//get all
    $group->get('/admins', \App\Action\GetUserAction::class);

   
//delete
  
    $group->delete('/dele/[{id}]', function (ServerRequest $request, Response $response, $args) {     
        Admin::destroy($args['id']);
        $result =[
            "status" => "deleted"
        ];
    return $response
    ->withHeader('Content-Type', 'application/json')
    ->withJson($result)->withStatus(201);
    });

  //get one
    $group->get('/get/[{id}]', function (ServerRequest $request, Response $response, $args) {     
        $row =  Admin::select('id','email','level')
        ->where('id',$args['id'])->get();
            
           
          
      return $response
      ->withHeader('Content-Type', 'application/json')
      ->withJson($row)->withStatus(201);
      });
  

    /// Blog_post

//create
$group->post('/post', \App\Action\AddBlogPostAction::class);

//update
$group->put('/post/[{id}]', \App\Action\UpdateBlogPostsAction::class);


//get all

$group->get('/posts', function (ServerRequest $request, Response $response, $args) {     
$row =  Blog_post::select('id','title','text','cover','date','admin_id','category_id')
->get();
    
   
  
return $response->withJson($row)->withStatus(201);
});

// get one
$group->get('/posts/[{id}]', function (ServerRequest $request, Response $response, $args) {     
$row =  Blog_post::select('id','title','text','cover','date','admin_id','category_id')
->where('id',$args['id'])->get();
    

return $response->withJson($row)->withStatus(201);
});

//delete

$group->delete('/delete/post/[{id}]', function (ServerRequest $request, Response $response, $args) {     
Blog_post::destroy($args['id']);
$result =[
    "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});



$group->get('/admin_posts/[{id}]', \App\Action\GetAdminPostsAction::class);







/// admin logs /// 


$group->get('/admin_logs/[{id}]', \App\Action\GetAdminsAction::class);




$group->delete('/logs/[{id}]', function (ServerRequest $request, Response $response, $args) {     
 Admin_log::destroy($args['id']);

 $result =[
     "status" => "deleted"
 ];
return $response->withJson($result)->withStatus(201);
});




//// Blog Category 


/// insert
$group->post('/add_category', function (ServerRequest $request, Response $response, $args) {     


         
$uploaded_name = $_FILES[ 'category_icon' ][ 'name' ]; 
$uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
$uploaded_size = $_FILES[ 'category_icon' ][ 'size' ]; 
$uploaded_type = $_FILES[ 'category_icon' ][ 'type' ]; 
$uploaded_tmp  = $_FILES[ 'category_icon' ][ 'tmp_name' ]; 



// Where are we going to be writing to? 
$target_path   = 'uploads/'; 
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
  $category_name = $_POST["category_name"];
 


  $values = [
    'category_name' => $category_name,
    'category_icon' => $target_file,
    
];

$blog= Blog_category::insertGetId($values);

  $status = "uploaded";
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
 
 

  $result = [
      'user_id' => $blog,
      'status' => $status,
  ];
  
return $response->withJson($result)->withStatus(201);
});



///update
$group->put('/update_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     

  $data = (array)$request->getParsedBody();

  $values = [
      'category_name' => $data['category_name'],
      'category_icon' => $data['category_icon']
      
  ];
  




  $blog= Blog_category::findOrFail($args['id'])
  ->update($values);

  $result = [
      'user_id' => $blog,
      'status' => "blog updated",
  ];
  
return $response->withJson($result)->withStatus(201);
});


///delete
$group->delete('/delete_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  Blog_category::destroy($args['id']);
  $result =[
      "status" => "deleted"
  ];
return $response->withJson($result)->withStatus(201);
});


///get all
$group->get('/get_category', function (ServerRequest $request, Response $response, $args) {     
  $row =  Blog_category::select('id','category_name','category_icon')
  ->get();
      
    
return $response->withJson($row)->withStatus(201);
});

  //get one
$group->get('/get_categories/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  $row =  Blog_category::select('id','category_name','category_icon')
  ->where('id',$args['id'])->get();
      
    
return $response->withJson($row)->withStatus(201);
});
 

/// relationship

$group->get('/category_posts/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  
  $posts =  Blog_post::where(['category_id' => $args['id']])->get();
      
$tickets = DB::
table('blog_posts')
->join('blog_categories','blog_categories.id','=','blog_posts.category_id')
->where(['category_id' => $args['id']])
->select('blog_posts.*','blog_categories.category_name','blog_categories.category_icon')
->get();
 
    
return $response->withJson($tickets)->withStatus(201);
});




//// Admin ratings

/// insert
$group->post('/rate_admin', function (ServerRequest $request, Response $response, $args) {     

$data = (array)$request->getParsedBody();

$values = [
  'admin_id' => $data['admin_id'],
  'ticket_id' => $data['ticket_id'],
  'rating' => $data['rating'],
  'description' =>$data['description'],
  'date' =>  date('Y-m-d H:i:s')
  
];

$rating= Admin_rating::insertGetId($values);

$result = [
  'admin_rating_id' => $rating,
  'status' => "admin rated",
];

return $response->withJson($result)->withStatus(201);
});


$group->put('/update_rate_admin/[{id}]', function (ServerRequest $request, Response $response, $args) {     

$data = (array)$request->getParsedBody();

$values = [
  'admin_id' => $data['admin_id'],
  'ticket_id' => $data['ticket_id'],
  'rating' => $data['rating'],
  'description' =>$data['description'],
  'date' =>  date('Y-m-d H:i:s')
  
];

$rating= Admin_rating::findOrFail($args['id'])
->update($values);

$result = [
  'admin_rating_id' => $rating,
  'status' => "admin rate updated",
];

return $response->withJson($result)->withStatus(201);
});


///delete
$group->delete('/delete_admin_rate/[{id}]', function (ServerRequest $request, Response $response, $args) {     
Admin_rating::destroy($args['id']);
$result =[
    "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});


// get last admin rate for one ticket

$group->get('/get_admin_rate_last/{id}/{id2}', function (ServerRequest $request, Response $response, $args) {  

$id =$request->getAttribute('id');
$id2 =$request->getAttribute('id2');

$row =  Admin_rating::select('id','rating','description','admin_id','ticket_id')
->where('admin_id',$id)
->where('ticket_id',$id2)
->get()->last();

return $response->withJson($row)->withStatus(201);
});

// get all rating for single ticket

$group->get('/get_admin_rate/[{id}]', function (ServerRequest $request, Response $response, $args) {  


$tickets = DB::
table('admin_ratings')
->join('admins','admins.id','=','admin_ratings.admin_id')
->join('tickets','tickets.id','=','admin_ratings.ticket_id')
->where(['ticket_id' => $args['id']])
->select('admin_ratings.*','admins.email')
->get();



return $response->withJson($tickets)->withStatus(201);
});


///update

$group->put('/update_ticket/[{id}]', function (ServerRequest $request, Response $response, $args) {     

$data = (array)$request->getParsedBody();

$values = [
 
  'status' => $data['status'],

];

$ticket = Ticket::findOrFail($args['id'])
->update($values);

$result = [
  'ticket_id' => $ticket,
  'status' => "ticket updated",
];

return $response->withJson($result)->withStatus(201);
});
 ///delete

$group->delete('/delete_ticket/[{id}]', function (ServerRequest $request, Response $response, $args) {     
Ticket::destroy($args['id']);
$result =[
  "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});





$group->post('/send/notification', function (ServerRequest $request, Response $response, $args) {     

$data = (array)$request->getParsedBody();

$values = [
  'admin_id' => $data['admin_id'],
  'title' => $data['title'],
  'text' => $data['text'],
  'sender_id' => $data['sender_id'],
  'date' =>  date('Y-m-d H:i:s')
];

$notification_id = DB::table("admin_notification")
->insertGetId($values); 

$result = [
  'notification_id' => $notification_id,
  'status' => "notification sent",
];

return $response->withJson($result)->withStatus(201);
});



$group->get('/notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  

  
$tickets = DB::
table('admin_notification')
->join('admins','admins.id','=','admin_notification.sender_id')
->where(['admin_id' => $args['id']])
->select('admin_notification.*','admins.email','admins.level')
->get();


return $response->withJson($tickets)->withStatus(201);
});




$group->delete('/delete_notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
DB::
table('admin_notification')
->where(['id' => $args['id']])
->delete();

$result =[
  "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});




$group->get('/get_visters', function (ServerRequest $request, Response $response, $args) {     


/// inner join two tables(users,categories) to the tickets table

$tickets = DB::
table('visters')
->get();


  
return $response->withJson($tickets)->withStatus(201);
});


$group->get('/get_tickets', function (ServerRequest $request, Response $response, $args) {     


/// inner join two tables(users,categories) to the tickets table

$tickets = DB::
table('tickets')
->join('categories','categories.id','=','tickets.category_id')
->join('users','users.id','=','tickets.user_id')
->select('tickets.*','categories.category_name','categories.category_icon','categories.parent_id','users.username','users.profile_name','users.profile_photo','users.birthdate','users.user_status','users.fcm_token')
->get();


  
return $response->withJson($tickets)->withStatus(201);
});


$group->get('/get_users', function (ServerRequest $request, Response $response, $args) {     
$row =  User::select('id','username','profile_name','profile_photo','birthdate','user_status')
->get();
  
 

return $response->withJson($row)->withStatus(201);
});

///delete
$group->delete('/delete_user/[{id}]', function (ServerRequest $request, Response $response, $args) {     
User::destroy($args['id']);
$result =[
  "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});



$group->delete('/delete_users/[{id}]', function (ServerRequest $request, Response $response, $args) {     
User::destroy($args['id']);
$result =[
  "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});



$group->get('/get_tickets/[{id}]', function (ServerRequest $request, Response $response, $args) {     


/// inner join two tables(users,categories) to the tickets table

$tickets = DB::
table('tickets')
->join('categories','categories.id','=','tickets.category_id')
->join('users','users.id','=','tickets.user_id')
->where(['category_id' => $args['id']])
->select('tickets.*','categories.category_name','categories.category_icon','categories.parent_id','users.username','users.profile_name','users.profile_photo','users.birthdate','users.user_status','users.fcm_token')
->get();


  
return $response->withJson($tickets)->withStatus(201);
});





//// Ticket Category 


/// insert
$group->post('/add_ticket_category', function (ServerRequest $request, Response $response, $args) {     

 
      
  $uploaded_name = $_FILES[ 'icon_image' ][ 'name' ]; 
  $uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
  $uploaded_size = $_FILES[ 'icon_image' ][ 'size' ]; 
  $uploaded_type = $_FILES[ 'icon_image' ][ 'type' ]; 
  $uploaded_tmp  = $_FILES[ 'icon_image' ][ 'tmp_name' ]; 



  // Where are we going to be writing to? 
  $target_path   = 'uploads/'; 
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
    $img = imagecreatefromjpeg( $uploaded_tmp); 


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
    $category_name = $_POST["category_name"];
    $parent_id = $_POST["parent_id"];
  
  
        $values = [
            'category_name' => $category_name,
            'category_icon' => $target_file,
            'parent_id' => $parent_id
            
        ];
        
        $category= Category::insertGetId($values);

    $status = "uploaded";
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
   



    $result = [
       "status" => $status,
        'user_id' => $category,
        
    ];

   
    
    
  return $response->withJson($result)->withStatus(201);
  });


/// get main categories which parent_id  == 0

$group->get('/get_main_categories', function (ServerRequest $request, Response $response, $args) {     
$row =  Category::select('id','category_name','category_icon','parent_id')
->where('parent_id',0)->get();
  

return $response->withJson($row)->withStatus(201);
});



//get sub menue of main category

$group->get('/ticket_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
$row =  Category::select('id','category_name','category_icon','parent_id')
->where('parent_id',$args['id'])->get();
  

return $response->withJson($row)->withStatus(201);
});

///delete sub category
$group->delete('/ticket_category_delete/[{id}]', function (ServerRequest $request, Response $response, $args) {     
Category::destroy($args['id']);
$result =[
    "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});



///delete main category's sub menues 
$group->delete('/ticket_sub_categories_delete/[{id}]', function (ServerRequest $request, Response $response, $args) {     
Category::where('parent_id',$args['id'])

->delete();
$result =[
  "status" => "deleted"
];
return $response->withJson($result)->withStatus(201);
});


$group->post('/replay_ticket/[{token}]', function (ServerRequest $request, Response $response, $args) {     

$data = (array)$request->getParsedBody();

$values = [
  'admin_id' => $data['admin_id'],
  'user_id' => $data['user_id'],
  'ticket_id' => $data['ticket_id'],
  'response_text' => $data['response_text'],
  'response_date' => date('Y-m-d H:i:s'),
  'current' => $data['current'],
  'attachment_photo' => ""

];





// API access key from Google API's Console
define('API_ACCESS_KEY', 'AAAA_Wl9msE:APA91bFJ3OicimCLvUNmIEggjLWKY3diBELWQFbv3AgE74uu0jZ8nfpzKv5-fcmCgLj_50fq-AmGIzj_9EishIGQApriH6tUMw1O174h8icA_6SJesAVOo6Cb_bYcis-hShc5loOFY-Q');
// prep the bundle
$msg = array
(
'ticket_id' =>  $data['ticket_id'],
'priority' => 'high',
'sound' => 'default',
'time_to_live' => 3600
);
$fields = array('to' => $args['token'], 'notification' => $msg);

$headers = array
(
'Authorization: key=' . API_ACCESS_KEY,
'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
$result = curl_exec($ch);
curl_close($ch);




$ticket = Ticket_response::insertGetId($values);

$result = [
  'replay_ticket_id' => $ticket,
  'status' => "response sent",
  'notification' => $result
];

return $response->withJson($result)->withStatus(201);
});






// all 
$group->get('/admin_response/{id}/{id2}/{id3}', function (ServerRequest $request, Response $response, $args) {    

$id =$request->getAttribute('id');
$id2 =$request->getAttribute('id2');
$id3 =$request->getAttribute('id3');

$row =  Ticket_response::select('*')
->where('admin_id',$id)
->where('ticket_id',$id2)
->where('user_id',$id3)
->get();




return $response->withJson($row)->withStatus(201);
});





$group->post('/add_attachment_photo/[{token}]', function (ServerRequest $request, Response $response, $args) {     

 
      
$uploaded_name = $_FILES[ 'attachment_photo' ][ 'name' ]; 
$uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
$uploaded_size = $_FILES[ 'attachment_photo' ][ 'size' ]; 
$uploaded_type = $_FILES[ 'attachment_photo' ][ 'type' ]; 
$uploaded_tmp  = $_FILES[ 'attachment_photo' ][ 'tmp_name' ]; 



// Where are we going to be writing to? 
$target_path   = 'uploads/'; 
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




$admin_id = $_POST["admin_id"];
$user_id = $_POST["user_id"];
$ticket_id = $_POST["ticket_id"];
$response_text = $_POST["response_text"];
$current = $_POST["current"];



$values = [
    'admin_id' => $admin_id,
    'user_id' => $user_id,
    'ticket_id' => $ticket_id,
    'response_text' => $response_text,
    'response_date' => date('Y-m-d H:i:s'),
    'current' => $current,
    'attachment_photo' => $target_file
    
];





// API access key from Google API's Console
define('API_ACCESS_KEY', 'AAAA_Wl9msE:APA91bFJ3OicimCLvUNmIEggjLWKY3diBELWQFbv3AgE74uu0jZ8nfpzKv5-fcmCgLj_50fq-AmGIzj_9EishIGQApriH6tUMw1O174h8icA_6SJesAVOo6Cb_bYcis-hShc5loOFY-Q');
// prep the bundle
$msg = array
(
'ticket_id' =>  $_POST['ticket_id'],
'priority' => 'high',
'sound' => 'default',
'time_to_live' => 3600
);
$fields = array('to' => $args['token'], 'notification' => $msg);

$headers = array
(
'Authorization: key=' . API_ACCESS_KEY,
'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
$result = curl_exec($ch);
curl_close($ch);




$ticket = Ticket_response::insertGetId($values);




$status = "uploaded";
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




$result = [
'replay_ticket_id' => $ticket,
'status' => $status,
'notification' => $result
];




return $response->withJson($result)->withStatus(201);
});





    // Allow preflight requests for /api/admin/
    // Due to the behaviour of browsers when sending a request,
    // you must add the OPTIONS method.
   
})->add(JwtMiddleware2::class);










/*
//////////////////////////
////////////////
/////////////
///////
       => Editor MiddleWare  
///////
////////////
////////////////
//////////////////////////
*/








$app->group('/dashboard/api/editor', function (RouteCollectorProxy $group) {

  
 
  

  
  //create
  $group->post('/post', \App\Action\AddBlogPostAction::class);
  
 
  
  //get all
  
  $group->get('/posts', function (ServerRequest $request, Response $response, $args) {     
  $row =  Blog_post::select('id','title','text','cover','date','admin_id','category_id')
  ->get();
      
     
    
  return $response->withJson($row)->withStatus(201);
  });
  
  // get one
  $group->get('/posts/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  $row =  Blog_post::select('id','title','text','cover','date','admin_id','category_id')
  ->where('id',$args['id'])->get();
      
  
  return $response->withJson($row)->withStatus(201);
  });
  
  //delete
  
  $group->delete('/delete/post/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  Blog_post::destroy($args['id']);
  $result =[
      "status" => "deleted"
  ];
  return $response->withJson($result)->withStatus(201);
  });
  
  
  
  $group->get('/admin_posts/[{id}]', \App\Action\GetAdminPostsAction::class);
  
  
  
  
  //// Blog Category 
  
  
  /// insert
  $group->post('/add_category', function (ServerRequest $request, Response $response, $args) {     
  
  
           
  $uploaded_name = $_FILES[ 'category_icon' ][ 'name' ]; 
  $uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
  $uploaded_size = $_FILES[ 'category_icon' ][ 'size' ]; 
  $uploaded_type = $_FILES[ 'category_icon' ][ 'type' ]; 
  $uploaded_tmp  = $_FILES[ 'category_icon' ][ 'tmp_name' ]; 
  
  
  
  // Where are we going to be writing to? 
  $target_path   = 'uploads/'; 
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
    $category_name = $_POST["category_name"];
   
  
  
    $values = [
      'category_name' => $category_name,
      'category_icon' => $target_file,
      
  ];
  
  $blog= Blog_category::insertGetId($values);
  
    $status = "uploaded";
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
   
   
  
    $result = [
        'user_id' => $blog,
        'status' => $status,
    ];
    
  return $response->withJson($result)->withStatus(201);
  });
  
  
  
  ///update
  $group->put('/update_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  
    $data = (array)$request->getParsedBody();
  
    $values = [
        'category_name' => $data['category_name'],
        'category_icon' => $data['category_icon']
        
    ];
    
  
    $blog= Blog_category::findOrFail($args['id'])
    ->update($values);
  
    $result = [
        'user_id' => $blog,
        'status' => "blog updated",
    ];
    
  return $response->withJson($result)->withStatus(201);
  });
  
  
  ///delete
  $group->delete('/delete_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    Blog_category::destroy($args['id']);
    $result =[
        "status" => "deleted"
    ];
  return $response->withJson($result)->withStatus(201);
  });
  
  
  ///get all
  $group->get('/get_category', function (ServerRequest $request, Response $response, $args) {     
    $row =  Blog_category::select('id','category_name','category_icon')
    ->get();
        
      
  return $response->withJson($row)->withStatus(201);
  });
  
    //get one
  $group->get('/get_categories/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    $row =  Blog_category::select('id','category_name','category_icon')
    ->where('id',$args['id'])->get();
        
      
  return $response->withJson($row)->withStatus(201);
  });
   
  
  /// relationship
  
  $group->get('/category_posts/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    
    $posts =  Blog_post::where(['category_id' => $args['id']])->get();
        
  $tickets = DB::
  table('blog_posts')
  ->join('blog_categories','blog_categories.id','=','blog_posts.category_id')
  ->where(['category_id' => $args['id']])
  ->select('blog_posts.*','blog_categories.category_name','blog_categories.category_icon')
  ->get();
   
      
  return $response->withJson($tickets)->withStatus(201);
  });
  
  
  
  
  $group->post('/send/notification', function (ServerRequest $request, Response $response, $args) {     
  
  $data = (array)$request->getParsedBody();
  
  $values = [
    'admin_id' => $data['admin_id'],
    'title' => $data['title'],
    'text' => $data['text'],
    'sender_id' => $data['sender_id'],
    'date' =>  date('Y-m-d H:i:s')
  ];
  
  $notification_id = DB::table("admin_notification")
  ->insertGetId($values); 
  
  $result = [
    'notification_id' => $notification_id,
    'status' => "notification sent",
  ];
  
  return $response->withJson($result)->withStatus(201);
  });
  
  
  
  $group->get('/notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    
  
    
  $tickets = DB::
  table('admin_notification')
  ->join('admins','admins.id','=','admin_notification.sender_id')
  ->where(['admin_id' => $args['id']])
  ->select('admin_notification.*','admins.email','admins.level')
  ->get();
  
  
  return $response->withJson($tickets)->withStatus(201);
  });
  
  
  
  
  $group->delete('/delete_notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
  DB::
  table('admin_notification')
  ->where(['id' => $args['id']])
  ->delete();
  
  $result =[
    "status" => "deleted"
  ];
  return $response->withJson($result)->withStatus(201);
  });
  
  
  
  
  $group->get('/get_visters', function (ServerRequest $request, Response $response, $args) {     
  
  
  /// inner join two tables(users,categories) to the tickets table
  
  $tickets = DB::
  table('visters')
  ->get();
  
  
    
  return $response->withJson($tickets)->withStatus(201);
  });
  
  
  $group->get('/get_tickets', function (ServerRequest $request, Response $response, $args) {     
  
  
  /// inner join two tables(users,categories) to the tickets table
  
  $tickets = DB::
  table('tickets')
  ->join('categories','categories.id','=','tickets.category_id')
  ->join('users','users.id','=','tickets.user_id')
  ->select('tickets.*','categories.category_name','categories.category_icon','categories.parent_id','users.username','users.profile_name','users.profile_photo','users.birthdate','users.user_status','users.fcm_token')
  ->get();
  
  
    
  return $response->withJson($tickets)->withStatus(201);
  });
  
  
  $group->get('/get_users', function (ServerRequest $request, Response $response, $args) {     
  $row =  User::select('id','username','profile_name','profile_photo','birthdate','user_status')
  ->get();
    
   
  
  return $response->withJson($row)->withStatus(201);
  });
  
  
  
  })->add(JwtMiddleware3::class);
  









/*
//////////////////////////
////////////////
/////////////
///////
       => supporter MiddleWare  
///////
////////////
////////////////
//////////////////////////
*/











  $app->group('/dashboard/api/supporter', function (RouteCollectorProxy $group) {

  
  
    
    //get all
        $group->get('/admins', \App\Action\GetUserAction::class);
    
       
    
    
    // get last admin rate for one ticket
    
    $group->get('/get_admin_rate_last/{id}/{id2}', function (ServerRequest $request, Response $response, $args) {  
    
    $id =$request->getAttribute('id');
    $id2 =$request->getAttribute('id2');
    
    $row =  Admin_rating::select('id','rating','description','admin_id','ticket_id')
    ->where('admin_id',$id)
    ->where('ticket_id',$id2)
    ->get()->last();
    
    return $response->withJson($row)->withStatus(201);
    });
    
    // get all rating for single ticket
    
    $group->get('/get_admin_rate/[{id}]', function (ServerRequest $request, Response $response, $args) {  
    
    
    $tickets = DB::
    table('admin_ratings')
    ->join('admins','admins.id','=','admin_ratings.admin_id')
    ->join('tickets','tickets.id','=','admin_ratings.ticket_id')
    ->where(['ticket_id' => $args['id']])
    ->select('admin_ratings.*','admins.email')
    ->get();
    
    
    
    return $response->withJson($tickets)->withStatus(201);
    });
    
    
   
     ///delete
    
    $group->delete('/delete_ticket/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    Ticket::destroy($args['id']);
    $result =[
      "status" => "deleted"
    ];
    return $response->withJson($result)->withStatus(201);
    });
    
    
    
    
    
    $group->post('/send/notification', function (ServerRequest $request, Response $response, $args) {     
    
    $data = (array)$request->getParsedBody();
    
    $values = [
      'admin_id' => $data['admin_id'],
      'title' => $data['title'],
      'text' => $data['text'],
      'sender_id' => $data['sender_id'],
      'date' =>  date('Y-m-d H:i:s')
    ];
    
    $notification_id = DB::table("admin_notification")
    ->insertGetId($values); 
    
    $result = [
      'notification_id' => $notification_id,
      'status' => "notification sent",
    ];
    
    return $response->withJson($result)->withStatus(201);
    });
    
    
    
    $group->get('/notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
      
    
      
    $tickets = DB::
    table('admin_notification')
    ->join('admins','admins.id','=','admin_notification.sender_id')
    ->where(['admin_id' => $args['id']])
    ->select('admin_notification.*','admins.email','admins.level')
    ->get();
    
    
    return $response->withJson($tickets)->withStatus(201);
    });
    
    
    
    
    $group->delete('/delete_notification/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    DB::
    table('admin_notification')
    ->where(['id' => $args['id']])
    ->delete();
    
    $result =[
      "status" => "deleted"
    ];
    return $response->withJson($result)->withStatus(201);
    });
    
    
    
    
    $group->get('/get_visters', function (ServerRequest $request, Response $response, $args) {     
    
    
    /// inner join two tables(users,categories) to the tickets table
    
    $tickets = DB::
    table('visters')
    ->get();
    
    
      
    return $response->withJson($tickets)->withStatus(201);
    });
    
    
    $group->get('/get_tickets', function (ServerRequest $request, Response $response, $args) {     
    
    
    /// inner join two tables(users,categories) to the tickets table
    
    $tickets = DB::
    table('tickets')
    ->join('categories','categories.id','=','tickets.category_id')
    ->join('users','users.id','=','tickets.user_id')
    ->select('tickets.*','categories.category_name','categories.category_icon','categories.parent_id','users.username','users.profile_name','users.profile_photo','users.birthdate','users.user_status','users.fcm_token')
    ->get();
    
    
      
    return $response->withJson($tickets)->withStatus(201);
    });
    
    
    $group->get('/get_users', function (ServerRequest $request, Response $response, $args) {     
    $row =  User::select('id','username','profile_name','profile_photo','birthdate','user_status')
    ->get();
      
     
    
    return $response->withJson($row)->withStatus(201);
    });
    
    
    
    
    $group->get('/get_tickets/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    
    
    /// inner join two tables(users,categories) to the tickets table
    
    $tickets = DB::
    table('tickets')
    ->join('categories','categories.id','=','tickets.category_id')
    ->join('users','users.id','=','tickets.user_id')
    ->where(['category_id' => $args['id']])
    ->select('tickets.*','categories.category_name','categories.category_icon','categories.parent_id','users.username','users.profile_name','users.profile_photo','users.birthdate','users.user_status','users.fcm_token')
    ->get();
    
    
      
    return $response->withJson($tickets)->withStatus(201);
    });
    
    
    
    
    
    //// Ticket Category 
    
    
    /// insert
    $group->post('/add_ticket_category', function (ServerRequest $request, Response $response, $args) {     
    
     
          
      $uploaded_name = $_FILES[ 'icon_image' ][ 'name' ]; 
      $uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
      $uploaded_size = $_FILES[ 'icon_image' ][ 'size' ]; 
      $uploaded_type = $_FILES[ 'icon_image' ][ 'type' ]; 
      $uploaded_tmp  = $_FILES[ 'icon_image' ][ 'tmp_name' ]; 
    
    
    
      // Where are we going to be writing to? 
      $target_path   = 'uploads/'; 
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
        $img = imagecreatefromjpeg( $uploaded_tmp); 
    
    
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
        $category_name = $_POST["category_name"];
        $parent_id = $_POST["parent_id"];
      
      
            $values = [
                'category_name' => $category_name,
                'category_icon' => $target_file,
                'parent_id' => $parent_id
                
            ];
            
            $category= Category::insertGetId($values);
    
        $status = "uploaded";
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
       
    
    
    
        $result = [
           "status" => $status,
            'user_id' => $category,
            
        ];
    
       
        
        
      return $response->withJson($result)->withStatus(201);
      });
    
    
    /// get main categories which parent_id  == 0
    
    $group->get('/get_main_categories', function (ServerRequest $request, Response $response, $args) {     
    $row =  Category::select('id','category_name','category_icon','parent_id')
    ->where('parent_id',0)->get();
      
    
    return $response->withJson($row)->withStatus(201);
    });
    
    
    
    //get sub menue of main category
    
    $group->get('/ticket_category/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    $row =  Category::select('id','category_name','category_icon','parent_id')
    ->where('parent_id',$args['id'])->get();
      
    
    return $response->withJson($row)->withStatus(201);
    });
    
    ///delete sub category
    $group->delete('/ticket_category_delete/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    Category::destroy($args['id']);
    $result =[
        "status" => "deleted"
    ];
    return $response->withJson($result)->withStatus(201);
    });
    
    
    
    ///delete main category's sub menues 
    $group->delete('/ticket_sub_categories_delete/[{id}]', function (ServerRequest $request, Response $response, $args) {     
    Category::where('parent_id',$args['id'])
    
    ->delete();
    $result =[
      "status" => "deleted"
    ];
    return $response->withJson($result)->withStatus(201);
    });
    
    
    $group->post('/replay_ticket/[{token}]', function (ServerRequest $request, Response $response, $args) {     
    
    $data = (array)$request->getParsedBody();
    
    $values = [
      'admin_id' => $data['admin_id'],
      'user_id' => $data['user_id'],
      'ticket_id' => $data['ticket_id'],
      'response_text' => $data['response_text'],
      'response_date' => date('Y-m-d H:i:s'),
      'current' => $data['current'],
      'attachment_photo' => ""
    
    ];
    
    
    
    
    
    // API access key from Google API's Console
    define('API_ACCESS_KEY4', 'AAAA_Wl9msE:APA91bFJ3OicimCLvUNmIEggjLWKY3diBELWQFbv3AgE74uu0jZ8nfpzKv5-fcmCgLj_50fq-AmGIzj_9EishIGQApriH6tUMw1O174h8icA_6SJesAVOo6Cb_bYcis-hShc5loOFY-Q');
    // prep the bundle
    $msg = array
    (
    'ticket_id' =>  $data['ticket_id'],
    'priority' => 'high',
    'sound' => 'default',
    'time_to_live' => 3600
    );
    $fields = array('to' => $args['token'], 'notification' => $msg);
    
    $headers = array
    (
    'Authorization: key=' . API_ACCESS_KEY4,
    'Content-Type: application/json'
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    
    
    
    
    $ticket = Ticket_response::insertGetId($values);
    
    $result = [
      'replay_ticket_id' => $ticket,
      'status' => "response sent",
      'notification' => $result
    ];
    
    return $response->withJson($result)->withStatus(201);
    });
    
    
    
    
    
    
    // all 
    $group->get('/admin_response/{id}/{id2}/{id3}', function (ServerRequest $request, Response $response, $args) {    
    
    $id =$request->getAttribute('id');
    $id2 =$request->getAttribute('id2');
    $id3 =$request->getAttribute('id3');
    
    $row =  Ticket_response::select('*')
    ->where('admin_id',$id)
    ->where('ticket_id',$id2)
    ->where('user_id',$id3)
    ->get();
    
    
    
    
    return $response->withJson($row)->withStatus(201);
    });
    
    
    
    
    
    $group->post('/add_attachment_photo/[{token}]', function (ServerRequest $request, Response $response, $args) {     
    
     
          
    $uploaded_name = $_FILES[ 'attachment_photo' ][ 'name' ]; 
    $uploaded_ext  = substr( $uploaded_name, strrpos( $uploaded_name, '.' ) + 1); 
    $uploaded_size = $_FILES[ 'attachment_photo' ][ 'size' ]; 
    $uploaded_type = $_FILES[ 'attachment_photo' ][ 'type' ]; 
    $uploaded_tmp  = $_FILES[ 'attachment_photo' ][ 'tmp_name' ]; 
    
    
    
    // Where are we going to be writing to? 
    $target_path   = 'uploads/'; 
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
    
    
    
    
    $admin_id = $_POST["admin_id"];
    $user_id = $_POST["user_id"];
    $ticket_id = $_POST["ticket_id"];
    $response_text = $_POST["response_text"];
    $current = $_POST["current"];
    
    
    
    $values = [
        'admin_id' => $admin_id,
        'user_id' => $user_id,
        'ticket_id' => $ticket_id,
        'response_text' => $response_text,
        'response_date' => date('Y-m-d H:i:s'),
        'current' => $current,
        'attachment_photo' => $target_file
        
    ];
    
    
    
    
    
    // API access key from Google API's Console
    define('API_ACCESS_KEY', 'AAAA_Wl9msE:APA91bFJ3OicimCLvUNmIEggjLWKY3diBELWQFbv3AgE74uu0jZ8nfpzKv5-fcmCgLj_50fq-AmGIzj_9EishIGQApriH6tUMw1O174h8icA_6SJesAVOo6Cb_bYcis-hShc5loOFY-Q');
    // prep the bundle
    $msg = array
    (
    'ticket_id' =>  $_POST['ticket_id'],
    'priority' => 'high',
    'sound' => 'default',
    'time_to_live' => 3600
    );
    $fields = array('to' => $args['token'], 'notification' => $msg);
    
    $headers = array
    (
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    
    
    
    
    $ticket = Ticket_response::insertGetId($values);
    
    
    
    
    $status = "uploaded";
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
    
    
    
    
    $result = [
    'replay_ticket_id' => $ticket,
    'status' => $status,
    'notification' => $result
    ];
    
    
    
    
    return $response->withJson($result)->withStatus(201);
    });
    
    
    
    
    
        // Allow preflight requests for /api/admin/
        // Due to the behaviour of browsers when sending a request,
        // you must add the OPTIONS method.
       
    })->add(JwtMiddleware4::class);
    
    


};




///// the end  ///// 

/***
 * 
 * 
 * 
 * 
 */
// 21/03/2020  ///
/// server side completed // 
/// mohammed_7aafar // 






