<?php
include_once 'connect.php';

class data_user_forgot
{
    public function get_user_by_email($email)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function get_user_by_sdt($sdt)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE sdt = ?");
        $stmt->bind_param("s", $sdt);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function update_password_by_email($email, $new_password)
    {
        global $conn;
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET matkhau = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update_password($sdt, $new_password)
    {
        global $conn;
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET matkhau = ? WHERE sdt = ?");
        $stmt->bind_param("ss", $hashed_password, $sdt);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
?>
