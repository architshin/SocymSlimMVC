<?php
namespace SocymSlim\MVC\controllers;

use PDO;
use PDOException;
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
	public function goMemberAdd(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
	{
		// Twigインスタンスをコンテナから取得。
		$twig = $this->container->get("view");
		// memberAdd.htmlをもとにしたレスポンスオブジェクトを生成。
		$response = $twig->render($response, "memberAdd.html");
		// レスポンスオブジェクトをリターン。
		return $response;
	}

	// 会員情報登録メソッド。
	public function memberAdd(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
	{
		// リクエストパラメータを取得。
		$postParams = $request->getParsedBody();
		$addMbNameLast = $postParams["addMbNameLast"];
		$addMbNameFirst = $postParams["addMbNameFirst"];
		$addMbBirth = $postParams["addMbBirth"];
		$addMbType = $postParams["addMbType"];
		// 氏名データについてはtrimを実行。
		$addMbNameLast = trim($addMbNameLast);
		$addMbNameFirst = trim($addMbNameFirst);

		//登録用SQL文字列を用意。
		$sqlInsert = "INSERT INTO members (mb_name_last, mb_name_first, mb_birth, mb_type) VALUES (:mb_name_last, :mb_name_first, :mb_birth, :mb_type)";

		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// プリペアドステートメントインスタンスを取得。
			$stmt = $db->prepare($sqlInsert);
			// 変数をバインド。
			$stmt->bindValue(":mb_name_last", $addMbNameLast, PDO::PARAM_STR);
			$stmt->bindValue(":mb_name_first", $addMbNameFirst, PDO::PARAM_STR);
			$stmt->bindValue(":mb_birth", $addMbBirth, PDO::PARAM_STR);
			$stmt->bindValue(":mb_type", $addMbType, PDO::PARAM_INT);
			// SQLの実行。
			$result = $stmt->execute();
			// SQL実行が成功した場合。
			if($result) {
				// 連番主キーを取得。
				$mbId = $db->lastInsertId();
				// 成功メッセージを作成。
				$content = "ID ".$mbId."で登録が完了しました。";
			}
			// SQL実行が失敗した場合。
			else {
				// 失敗メッセージを作成。
				$content = "登録に失敗しました。";
			}
		}
		// 例外処理。
		catch(PDOException $ex) {
			// 障害発生メッセージを作成。
			$content = "障害が発生しました。";
			var_dump($ex);
		}
		finally {
			// DB切断。
			$db = null;
		}
		
		//表示メッセージをレスポンスオブジェクトに格納。
		$responseBody = $response->getBody();
		$responseBody->write($content);
		// レスポンスオブジェクトをリターン。
		return $response;
	}
}
