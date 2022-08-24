<?php
session_start();
$session_id = $_SESSION['id'];
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://kapi.kakao.com//v1/user/unlink',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'target_id_type=user_id&target_id='.$session_id.'',
  CURLOPT_HTTPHEADER => array(
    "Authorization: KakaoAK ##KAKAO_AK_HEADER##",
    "Content-Type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$response = json_decode($response, true);
$response = $response['id'];

curl_close($curl);

if($response == $session_id){
    unset($_SESSION['id']);
    unset($_SESSION['name']);
    unset($_SESSION['uid']);
    unset($_SESSION['email']);
    echo "로그아웃 완료";
    echo '<script>alert("안전하게 로그아웃을 했습니다")</script>';
    echo '<script>history.back();</script>';    
    exit;
}else{
    echo "로그아웃 오류";
    echo '<script>alert("로그아웃도중 오류가 발생했습니다")</script>';
    echo '<script>history.back();</script>';    
    exit;
}


