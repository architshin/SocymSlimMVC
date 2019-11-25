<?php
use SocymSlim\MVC\controllers\MemberController;

$app->any("/goMemberAdd", MemberController::class.":goMemberAdd");
$app->any("/memberAdd", MemberController::class.":memberAdd");
