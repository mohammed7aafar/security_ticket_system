<?php

namespace App\Domain\User\Repository;


use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Data\BlogPostsData;
use App\Domain\User\Data\UserData;
use App\Admin;
use App\User;
use App\Admin_log;
use App\Blog_post;
use App\Domain\User\Data\AdminLogsData;
use DateTime;
use Exception;
//use PDO;
use UnexpectedValueException;
use \Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Illuminate\Database\Connection;

/**
 * Repository.
 */
class UserCreatorRepository
{
    /**
     * @var Connection
     */
    private $connection;
       // blowfish
       private static $algo = '$2a';
       // cost parameter
       private static $cost = '$10';

      
       /**
     * The constructor.
     *
     * @param Connection $connection The database connection
     */
   
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
       
    }

    // /**
    //  * Insert user row.
    //  *
    //  * @param UserCreateData $user The user
    //  *
    //  * @return int The new ID
    //  */

   
    
    /// Admins /// 

public function insertUser(UserCreateData $user): int
    {
        $g = new GoogleAuthenticator();

        $secret = $g->generateSecret();

        $salt = crypt($user->password, self::$algo .
        self::$cost .
        '$' . self::unique_salt());


    


    $values = [
        'email' => $user->email,
        'salt' => $salt,
        'level' => $user->level,
        'secret' => $secret
    ];
    
   return Admin::insertGetId($values);

}




public function updateUser(UserCreateData $user,$id)

{

    $g = new GoogleAuthenticator();

    $secret = $g->generateSecret();

    $salt = crypt($user->password, self::$algo .
    self::$cost .
    '$' . self::unique_salt());

    $values = [
        'email' => $user->email,
        'salt' => $salt,
        'level' => $user->level,
        'secret' => $secret
    ];
    

    /// findOrFail =>  if find returns null you obviously can't update the model.
  return Admin::findOrFail($id)
    ->update($values);

    
}



public static function unique_salt() {
    return substr(sha1(mt_rand()), 0, 22);
}


public function isUserExists(UserCreateData $user) {
   
    return Admin::where('email',$user->email)->exists();


    }


public function getUsers()
    {
    return  Admin::select('id','email','level')->get();

    }

public function fetchUser(UserCreateData $user){

        try{

        
            //// change 1

    return Admin::select('salt','secret')->where('email',$user->email)
    ->limit(1)
    ->get();
    }
    catch (Exception $e){
        
        echo "Failed: " . $e->getMessage();
    }

    return $user;
}


/// Admin Logs /// 


//insert



public function insertAdminLogs(AdminLogsData $log){
    $values = [
        'url' => $log->url,
        'admin_id' => $log->admin_id,
        'time' => date('Y-m-d H:i:s')
        
    ];


   return Admin_log::insertGetId($values);

}


/// get ///

public function getAdminLogs($id){
    

$logs =  Admin_log::where(['admin_id' => $id])->get();

return $logs;

}





/// Blog Posts ///


/// add post

  // /**
    //  * Insert blog post.
    //  *
    //  * @param BlogPostsData $post The blog_post
    //  *
    //  * @return int The new ID
    //  */



    //insert

public function addPost(BlogPostsData $post): int
    {
      

    $values = [
        'title' => $post->title,
        'text' => $post->text,
        'cover' => $post->cover,
        'date' => date('Y-m-d H:i:s'),
        'views' => $post->views,
        'admin_id' => $post->admin_id,
        'category_id' => $post->category_id
    ];
    
   return Blog_post::insertGetId($values);

}

//update

public function updatePost(BlogPostsData $post,$id)

{

    $values = [
        'title' => $post->title,
        'text' => $post->text,
        'cover' => $post->cover,
        'date' => date('Y-m-d H:i:s'),
        'views' => $post->views,
        'admin_id' => $post->admin_id,
        'category_id' => $post->category_id
    ];

    /// findOrFail =>  if find returns null you obviously can't update the model.
  return Blog_post::findOrFail($id)
    ->update($values);

    

}



public function getAdminPosts($id){
    
$posts =  Blog_post::where(['admin_id' => $id])->get();
    
  return $posts;

    }




 /// user
 


 // insert user

 public function AddUser(UserData $user): int
    {
       

        $salt = crypt($user->password, self::$algo .
        self::$cost .
        '$' . self::unique_salt());

    $values = [
        'username' => $user->username,
        'salt' => $salt,
        'profile_name' => $user->profile_name,
        'profile_photo' => $user->profile_photo,
        'birthdate' => $user->birthdate,
        'user_status' => $user->status,
        'fcm_token' => $user->fcm_token,
        
        

    ];
    
   return User::insertGetId($values);

}
 


public function UserNameExists(UserData $user) {
   
    return User::where('username',$user->username)->exists();


    }



    public function fetchUserName(UserData $user){

        try{

    return User::select('salt')->where('username',$user->username)
    ->limit(1)
    ->get();
    }
    catch (Exception $e){
        
        echo "Failed: " . $e->getMessage();
    }

    return $user;
}


public function updateUserName(UserData $user,$id)

{

  
    $salt = crypt($user->password, self::$algo .
    self::$cost .
    '$' . self::unique_salt());

    $values = [
        'username' => $user->username,
        'salt' => $salt,
        'profile_name' => $user->profile_name,
        'profile_photo' => $user->profile_photo,
        'birthdate' => $user->birthdate,
        'user_status' => $user->status,
        'fcm_token' =>$user->fcm_token,
    ];
    

    /// findOrFail =>  if find returns null you obviously can't update the model.
  return User::findOrFail($id)
    ->update($values);

    

}




}