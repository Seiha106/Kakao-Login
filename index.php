<?php
session_start();
echo $_SESSION['name'];
?>
<a href="https://kauth.kakao.com/oauth/authorize?response_type=code&client_id=40670aebc93fd11224124e4ef32b7b48&redirect_uri=http://localhost/">카카오 로그인</a>