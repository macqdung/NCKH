<?php
include_once 'connect.php';

class data_user
{
    public function insert_user($tendangnhap, $matkhau, $sdt, $email, $role = 'khách hàng')
    {
        global $conn;
        // Hash the password before storing
        $hashed_password = password_hash($matkhau, PASSWORD_DEFAULT);

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (tendangnhap, matkhau, sdt, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $tendangnhap, $hashed_password, $sdt, $email, $role);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function check_username_exists($tendangnhap)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT tendangnhap FROM users WHERE tendangnhap = ?");
        $stmt->bind_param("s", $tendangnhap);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
?>
