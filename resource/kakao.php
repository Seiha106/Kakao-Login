<?php
error_reporting(E_ALL^ E_WARNING);
$code = htmlspecialchars($_GET['code']);
$client_id = "##KAKAO_CLIENT_ID##";
$client_secret = "##KAKAO_CLIENT_SECRET##";
$token = json_decode(file_get_contents("https://kauth.kakao.com/oauth/token?=authorization_code&client_id=$client_id&code=$code&client_secret=$client_secret&grant_type=authorization_code"), true);
$access_token = $token['access_token'];
$refresh_token = $token['refresh_token'];
if(empty($access_token)){
    echo "인증오류";
    exit;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://kapi.kakao.com/v2/user/me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$headers = array();
$headers[] = "Authorization: Bearer $access_token";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result_ = curl_exec($ch);
$result = json_decode($result_, true);

$email_verified = $result['kakao_account']['is_email_verified'];
$email_valid = $result['kakao_account']['is_email_valid'];
$has_phone_number = $result['kakao_account']['has_phone_number'];
$id = $result['id'];
$nickname = $result['kakao_account']['profile']['nickname'];
$phone_number = $result['kakao_account']['phone_number'];
$email = $result['kakao_account']['email'];
$uid = uniqid('cat_');
$date = date("Y-m-d H:i:s");

if($has_phone_number == false){
    echo "카카오 계정에 휴대폰 번호를 등록해주세요";
    echo '<script>alert("카카오 계정에 휴대폰 번호를 등록해주세요")</script>';
    echo '<script>history.back();</script>';
    exit;
}
if($email_valid == false){
    echo "해당 카카오메일은 다른 계정에서 사용하여 만료되었습니다";
    echo '<script>alert("해당 카카오메일은 다른 계정에서 사용하여 만료되었습니다")</script>';
    echo '<script>history.back();</script>';
    exit;
}
if($email_verified == false){
    echo "이메일 인증을 해주세요";
    echo '<script>alert("이메일 인증을 해주세요")</script>';
    echo '<script>history.back();</script>';
    exit;
}
if (strpos($phone_number,"+82") === false){
    echo "한국전화번호가 아닙니다";
    echo '<script>alert("한국전화번호가 아닙니다")</script>';
    echo '<script>history.back();</script>';
    exit;
}


$make_dir = mkdir("user_db/$id" ,true);

if($make_dir){
    echo "사용자 정보를 생성했습니다";
    $array = array(
        "id" => $id,
        "nickname" => $nickname,
        "phone_number" => $phone_number,
        "email" => $email,
        "uid" => $uid,
    );

    $login_log = array(
        "status" => "register",
        "time" => $date,
        "ip" => $_SERVER['REMOTE_ADDR'],
        "user-agent" => $_SERVER['HTTP_USER_AGENT'],
    );

    file_put_contents("user_db/$id/userdata", json_encode($array));
    file_put_contents("user_db/$id/$uid", $uid);
    file_put_contents("user_db/$id/login_log", json_encode($login_log));
    session_start();
    $_SESSION['id'] = $id;
    $_SESSION['name'] = $nickname;
    $_SESSION['uid'] = $uid;
    $_SESSION['email'] = $email;
    echo '<script>alert("'.$nickname.'님 사용자 정보를 생성했습니다\nUID : '.$uid.'")</script>';
    echo '<script>history.back();</script>';
}else{
    $data = file_get_contents("user_db/$id/userdata");
    $data = json_decode($data, true);
    $check_id = $data[id];
    $check_nickname = $data['nickname'];
    $check_uid = $data['uid'];
    $check_email = $data['email'];
    if($id == $check_id){
        $login_log = array(
            "status" => "login-success",
            "time" => $date,
            "ip" => $_SERVER['REMOTE_ADDR'],
            "user-agent" => $_SERVER['HTTP_USER_AGENT'],
        );
        file_put_contents("user_db/$id/login_log", json_encode($login_log));
        echo "".$check_nickname."님으로 로그인을 합니다";
        echo '<script>alert("'.$check_nickname.'님으로 로그인을 합니다\nUID : '.$check_uid.'")</script>';
        session_start();
        $_SESSION['id'] = $check_id;
        $_SESSION['name'] = $check_nickname;
        $_SESSION['uid'] = $check_uid;
        $_SESSION['email'] = $check_email;
        echo '<script>history.back();</script>';
    }else{
        echo "알 수 없는 오류입니다";
    }
}
