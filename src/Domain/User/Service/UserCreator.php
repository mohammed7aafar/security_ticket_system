<?php

namespace App\Domain\User\Service;

use App\Domain\User\Data\AdminLogsData;
use App\Domain\User\Data\BlogPostsData;
use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Repository\UserCreatorRepository;
use UnexpectedValueException;
use \Sonata\GoogleAuthenticator\GoogleAuthenticator;
use App\Domain\User\Data\UserData;
/**
 * Service.
 */
final class UserCreator
{
    /**
     * @var UserCreatorRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param UserCreatorRepository $repository The repository
     */
    public function __construct(UserCreatorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new user.
     *
     * @param UserCreateData $user The user data
     *
     * @return int The new user ID
     */


     // Admins ///

    public function createUser(UserCreateData $user): int
    {
      
        // Insert user

        if(!$this->repository->isUserExists($user)){

        $userId = $this->repository->insertUser($user);

        }
        else{
            return False;
        }


        return $userId;
    }


    public function getusers()
    {
     
        // get user
        $userId = $this->repository->getUsers();



        return $userId;
    }


   public function updateUser(UserCreateData $user,$id)

{

    if(!$this->repository->isUserExists($user)){

        $userId = $this->repository->updateUser($user,$id);

        }
        else{
            return False;
        }


        return $userId;

}
    
   


    public function checkLogin(UserCreateData $user){

        if($this->repository->isUserExists($user)){

        $salt = $this->repository->fetchUser($user);
        $res = json_decode($salt,true);

        // print_r($res[0]['salt']);
        
       $full_salt = substr($res[0]['salt'], 0, 29);
       $new_hash = crypt($user->password, $full_salt);

         if ($res[0]['salt'] == $new_hash) {
                // User password is correct
                return True;
            } else {
                // user password is incorrect
                return False;
            }

        }
        else{

            // user not existed with the email
            return false;
        }

    }



    public function verifyCode(UserCreateData $user,$code){


        $salt = $this->returnSalt($user);

       $g = new GoogleAuthenticator();

       // print_r($salt[0]["salt"]);

       if ($g->checkCode($salt, $code)) {
        return True;
      } else {
        return false;
      }


    }






    public function returnSalt(UserCreateData $user){


        $salt = $this->repository->fetchUser($user);

        $res = json_decode($salt,true);

        
        return $res[0]["secret"];

    }




  
/// Admin logs ///


  public function createAdminLogs(AdminLogsData $log): int
    {

    return $this->repository->insertAdminLogs($log);

    }
  



 public function getAdminLogs($id){

    return $this->repository->getAdminLogs($id);
 }




 //// Blog Posts

 public function createPost(BlogPostsData $post): int
    {
      
        // Insert user

      
        $userId = $this->repository->addPost($post);


        return $userId;
    }


    public function updatePost(BlogPostsData $post,$id)
    {
      
        // Insert user

      
       return $this->repository->updatePost($post,$id);


    
    }

    public function getAdminPosts($id){

        return $this->repository->getAdminPosts($id);
     }



     //// User  //// 


     public function AddUser(UserData $user): int
     {
       
         // Insert user
 
         if(!$this->repository->UserNameExists($user)){
 
         $userId = $this->repository->AddUser($user);
 
         }
         else{
             return False;
         }
 
 
         return $userId;
     }




     public function checkUserLogin(UserData $user){



        if($this->repository->UserNameExists($user)){

        $salt = $this->repository->fetchUserName($user);
        $res = json_decode($salt,true);

        // print_r($res[0]['salt']);
        
       $full_salt = substr($res[0]['salt'], 0, 29);
       $new_hash = crypt($user->password, $full_salt);

         if ($res[0]['salt'] == $new_hash) {
                // User password is correct
                return True;
            } else {
                // user password is incorrect
                return False;
            }

        }
        else{

            // user not existed with the email
            return false;
        }

    }
    

    public function updateUserName(UserData $user,$id)

    {
    
        if(!$this->repository->UserNameExists($user)){
    
            $userId = $this->repository->updateUserName($user,$id);
    
            }
            else{
                return False;
            }
    
    
            return $userId;
    
    }

}



