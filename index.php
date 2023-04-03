<?php

// 连接数据库
$conn = new mysqli('localhost', 'p230047', 'p230047', 'cs130');
if ($conn->connect_error) {
    die('数据库连接失败：' . $conn->connect_error);
}
// 设置字符集
$conn->set_charset('utf8');

// 获取数据
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 获取用户列表
    if (isset($_GET['action']) && $_GET['action']== "getUserList"){
        $sql = 'SELECT * FROM users ';
        if (isset($_GET['keyword']) && $_GET['keyword']){
            $sql = $sql.' WHERE CONCAT(`title`,`first_name`,`sur_name`,`mobile`,`email`) LIKE "%'.$_GET['keyword'].'%"';
        }
        $sql = $sql.' ORDER BY id DESC';
        var_dump($sql);
        die;

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['title'] . '</td>';
                echo '<td>' . $row['first_name'] . '</td>';
                echo '<td>' . $row['sur_name'] . '</td>';
                echo '<td>' . $row['mobile'] . '</td>';
                echo '<td>' . $row['email'] . '</td>';
                echo '<td>';
                echo '<button class="btn btn-primary showAddressBtn" data-pid="' . $row['id'] . '">地址列表</button> ';
                echo '<button class="btn btn-primary editUserBtn" data-id="' . $row['id'] . '">编辑</button> ';
                echo '<button class="btn btn-danger deleteUserBtn" data-id="' . $row['id'] . '">删除</button>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">暂无数据</td></tr>';
        }
    }
    // 获取用户详情
    if (isset($_GET['action']) && $_GET['action']== "getUserDetail"){
        $sql = "SELECT * FROM users WHERE id=".$_GET['id'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $result = ['success' => true, 'data' => $user];
        } else {
            $result = ['success' => false, 'message' => '获取用户信息失败：' . $conn->error];
        }
        echo json_encode($result);
    }

    // 获取地址列表
    if (isset($_GET['action']) && $_GET['action']== "getAddressList"){
        $sql = 'SELECT * FROM users_address WHERE pid='.$_GET['pid'].' ORDER BY id DESC';
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['home_address'] . '</td>';
                echo '<td>' . $row['shipping_address'] . '</td>';
                echo '<td>' . $row['city_town'] . '</td>';
                echo '<td>' . $row['county'] . '</td>';
                echo '<td>' . $row['eircode'] . '</td>';
                echo '<td>';
                echo '<button class="btn btn-primary editAddressBtn" data-id="'. $row['id'] .'" data-pid="'. $row['pid'] .'">编辑</button> ';
                echo '<button class="btn btn-danger deleteAddressBtn" data-id="'. $row['id'] .'" data-pid="'. $row['pid'] .'">删除</button>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">暂无数据</td></tr>';
        }
    }
    // 获取地址详情
    if (isset($_GET['action']) && $_GET['action']== "getAddressDetail"){
        $sql = "SELECT * FROM users_address WHERE id=".$_GET['id'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $result = ['success' => true, 'data' => $user];
        } else {
            $result = ['success' => false, 'message' => '获取地址信息失败：' . $conn->error];
        }
        echo json_encode($result);
    }

}

// api请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 添加/编辑用户
    if (isset($_GET['action']) && $_GET['action']== "saveUser"){
        $title = $_POST['title'];
        $first_name = $_POST['first_name'];
        $sur_name = $_POST['sur_name'];
        $mobile = $_POST['mobile'];
        $email = $_POST['email'];
        $id = $_POST['id'];

        // 检查必填字段是否都填写了
        if (empty($title) || empty($first_name) || empty($sur_name) || empty($mobile) || empty($email)) {
            $result = ['success' => false, 'message' => '请填写所有必填字段'];
            echo json_encode($result);
            exit;
        }

        // 更新
        if ($id && $id != 'undefined'){
            $sql = "UPDATE users SET title='$title', first_name='$first_name', sur_name='$sur_name', mobile='$mobile', email='$email' WHERE id=".$id;
        }
        // 新增
        else{
            $sql = "INSERT INTO users (title, first_name, sur_name, mobile, email) VALUES ('$title', '$first_name', '$sur_name', '$mobile', '$email')";
        }
        if ($conn->query($sql) === TRUE) {
            $result = ['success' => true];
        } else {
            $result = ['success' => false, 'message' => '操作失败：' . $conn->error];
        }
        echo json_encode($result);
    }

    // 删除用户
    if (isset($_GET['action']) && $_GET['action']== "delUser"){
        $sql = "DELETE FROM users WHERE id=".$_GET['id'];
        if ($conn->query($sql) === TRUE) {
            $result = ['success' => true];
        } else {
            $result = ['success' => false, 'message' => '删除用户失败：' . $conn->error];
        }
        echo json_encode($result);
    }

    // 添加/编辑地址
    if (isset($_GET['action']) && $_GET['action']== "saveAddress"){
        $home_address = $_POST['home_address'];
        $shipping_address = $_POST['shipping_address'];
        $city_town = $_POST['city_town'];
        $county = $_POST['county'];
        $eircode = $_POST['eircode'];
        $pid = $_POST['pid'];
        $id = $_POST['id'];

        // 检查必填字段是否都填写了
        if (empty($home_address) || empty($city_town) || empty($county)) {
            $result = ['success' => false, 'message' => '请填写所有必填字段'];
            echo json_encode($result);
            exit;
        }

        // 更新
        if ($id && $id != 'undefined'){
            $sql = "UPDATE users_address SET home_address='$home_address', pid='$pid', shipping_address='$shipping_address', city_town='$city_town', county='$county', eircode='$eircode' WHERE id=".$id;
        }
        // 新增
        else{
            $sql = "INSERT INTO users_address (home_address, pid, shipping_address, city_town, county, eircode) VALUES ('$home_address', '$pid', '$shipping_address', '$city_town', '$county', '$eircode')";
        }
        if ($conn->query($sql) === TRUE) {
            $result = ['success' => true];
        } else {
            $result = ['success' => false, 'message' => '操作失败：' . $conn->error];
        }
        echo json_encode($result);
    }

    // 删除地址
    if (isset($_GET['action']) && $_GET['action']== "delAddress"){
        $sql = "DELETE FROM users_address WHERE id=".$_GET['id'];
        if ($conn->query($sql) === TRUE) {
            $result = ['success' => true];
        } else {
            $result = ['success' => false, 'message' => '删除地址失败：' . $conn->error];
        }
        echo json_encode($result);
    }
}

// 关闭数据库连接
$conn->close();

?>