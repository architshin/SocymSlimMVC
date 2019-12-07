<?php
use SocymSlim\MVC\controllers\MemberController;

$app->any("/showMemberList", MemberController::class.":showMemberList");
$app->any("/goMemberAdd", MemberController::class.":goMemberAdd");
$app->any("/memberAdd", MemberController::class.":memberAdd");
$app->any("/showMemberDetail/{id}", MemberController::class.":showMemberDetail");
$app->any("/prepareMemberEdit", MemberController::class.":prepareMemberEdit");
$app->any("/getAllMembersJSON", MemberController::class.":getAllMembersJSON");
