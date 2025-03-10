<?php
    /**
     *  include         = include again and again every time you refresh the page
     *  include_once    = include only once
     * 
     *  require         = require again and again and will stop the script whern there's error
     *  require_once    = require once only and will stop the script when there's error
     */

    require_once "Database.php";

    //Database has $conn property, so User also has $conn property to keep the connection condition
    class User extends Database
    {
        public function store($request)                         
        {
            $first_name = $request['first_name'];
            $last_name = $request['last_name'];
            $username = $request['username'];
            $password = $request['password'];

            $password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (first_name, last_name, username, password) VALUES ('$first_name', '$last_name', '$username', '$password')";

            if($this->conn->query($sql)){
                header('location: ../views');
                exit;
            } else {
                die('Error in creating the user: ' . $this->conn->error);
            }
        }

        public function login($request)
        {
            $username = $request['username'];   //not (), but [] since $request is an array
            $password = $request['password'];

            $sql = "SELECT * FROM users WHERE username = '$username' ";

            $result = $this->conn->query($sql);

            #check the username
            if($result->num_rows == 1){     //num_rows is a propery of mysqli_result, which will be returned after d
                #check if the password is correct
                $user = $result->fetch_assoc();

                if(password_verify($password, $user['password'])){
                    #create session variable for future use
                    session_start();

                    $_SESSION['id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['first_name'] . " " . $user['last_name'];

                    header('location: ../views/dashboard.php');
                    exit;
                } else {
                    die('Password is incorrect');
                }
            } else {
                die('Username not found.');
            }
        }

        public function logout()
        {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../views');
        exit;
        }

        public function getAllUsers()
        {
            $sql = "SELECT id, first_name, last_name, username, photo FROM users";

            if($result = $this->conn->query($sql)){
                return $result;
            } else {
                die ('Error retrieving all users: ' . $this->conn->error);
            }
        }

        public function getUser($id)
        {
            $sql = "SELECT * FROM users WHERE id = $id";

            if($result = $this->conn->query($sql)){
                return $result->fetch_assoc();
            } else {
                die('Error in retrieving the user: ' . $this->conn->error);
            }
        }

        public function update($request, $files)
        {
            session_start();
            $id = $_SESSION['id'];
            
            $first_name = $request['first_name'];
            $last_name = $request['last_name'];
            $username = $request['username'];
            $photo = $files['photo']['name'];      //[name = "photo"][key of an array]
            $tmp_photo = $files['photo']['tmp_name'];

            $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = $id ";

            if($this->conn->query($sql)){
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = "$first_name $last_name";

                #if there is an uploaded photo file, save it to the db and save this file to images folder
                if($photo){
                    $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
                    $destination = "../assets/images/$photo";

                    //save the image name to db
                    if($this->conn->query($sql)){
                        //save the file to images folder
                        if(move_uploaded_file($tmp_photo, $destination)){   //(from, to)
                            header('location: ../views/dashboard.php');
                            exit;
                        } else {
                            die('Error moving the photo.');
                        }
                    } else {
                        die('Error uploading the photo: ' . $this->conn->error);
                    }    
                }

                //if there's no file, go to dashboard without updating photo
                header('location: ../views/dashboard.php');
                exit;

            //if query to update name and username didn't work well, it is an updating error
            } else {
                die('Error updating the user: ' . $this->conn->error);
            }

        } //end of update() function

        public function delete()
        {
            session_start();

            $id = $_SESSION['id'];

            $sql = "DELETE FROM users WHERE id = $id";

            if($this->conn->query($sql)){
                $this->logout();
            } else {
                die('Error deleting your account: ' . $this->conn->error);
            }

        }





    } //end of class

?>