<?php
include ('connect.php'); 
class data_login
{

    public function insert_login($username,$password)
    {
        global $conn;
        $sql="insert into users(username,password)
              values ('$username','$password')";
        $run=mysqli_query($conn,$sql);
        return $run;
    }
    public function select_login()
{
    global $conn;
    $sql = "select * from users";
    $run = mysqli_query($conn, $sql);
    return $run;
}

public function delete_login($id){
    global $conn;
    $sql = "delete from users where ID_user = $id";
    $run = mysqli_query($conn, $sql);
    return $run;

}
public function update_login($username,$password,$id){
    global $conn;
    $sql = "update users set username = '$username', password = '$password' where ID_user = $id";
    echo $sql;
    $run = mysqli_query($conn, $sql);
    return $run;
}
public function select_login_id($id){
    global $conn;
    $sql = "select * from users where ID_user = $id";
    $run = mysqli_query($conn, $sql);
    return $run;
}
public function select_login_name($name){
    global $conn;
    $sql = "select * from users where username = '$name'";
    $run = mysqli_query($conn, $sql);
    return $run;
}
}