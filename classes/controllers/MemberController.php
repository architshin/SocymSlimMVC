<?php
namespace SocymSlim\MVC\controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class MemberController
{
	// コンテナインスタンス
	private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
		// 引数のコンテナインスタンスをプロパティに格納。
		$this->container = $container;
	}

	// 会員情報登録画面を表示するメソッド。
	public function goMemberAdd(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
		// Twigインスタンスをコンテナから取得。
		$twig = $this->container->get("view");
		// memberAdd.htmlをもとにしたレスポンスオブジェクトを生成。
		$response = $twig->render($response, "memberAdd.html");
		// レスポンスオブジェクトをリターン。
		return $response;
	}
}
