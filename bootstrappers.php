<?php
use SocymSlim\MVC\exceptions\CustomErrorRenderer;
// 環境変数DATABASE_URLを取得。
$displayErrorDetails = getenv("DEV_MODE");
// ルーティングミドルウェアを設定。
$app->addRoutingMiddleware();
//エラーミドルウェアを設定。
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);
// デフォルトのエラーハンドラを取得。
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
// HTMLのエラーレンダラとして独自クラスを設定。
$errorHandler->registerErrorRenderer("text/html", CustomErrorRenderer::class);
