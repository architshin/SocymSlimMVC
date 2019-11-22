<?php
use SocymSlim\MVC\controllers\MemberController;

$app->any("/goMemberAdd", MemberController::class.":goMemberAdd");
