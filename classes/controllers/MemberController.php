<?php
namespace SocymSlim\MVC\controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use SocymSlim\MVC\entities\Member;

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

	public function showMemberList(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
	{
		// テンプレート変数を格納する連想配列を用意。
		$assign = [];
		// 会員情報リストを格納する連想配列の用意。
		$memberList = [];
		
		// ［1］データ取得SQL文字列を用意。
		$sqlSelect = "SELECT * FROM members ORDER BY id";
		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// ［2］プリペアドステートメントインスタンスを取得。
			$stmt = $db->prepare($sqlSelect);
			// ［4］SQLの実行。
			$result = $stmt->execute();
			// SQL実行が成功した場合。
			if($result) {
				// ［5］フェッチループ。
				while($row = $stmt->fetch()) {
					// 各カラムデータの取得。
					$id = $row["id"];
					$mbNameLast = $row["mb_name_last"];
					$mbNameFirst = $row["mb_name_first"];
					$mbBirth = $row["mb_birth"];
					$mbType = $row["mb_type"];

					// Memberエンティティインスタンスを生成。
					$member = new Member();
					// Memberエンティティに各カラムデータを格納。
					$member->setId($id);
					$member->setMbNameLast($mbNameLast);
					$member->setMbNameFirst($mbNameFirst);
					$member->setMbBirth($mbBirth);
					$member->setMbType($mbType);
					// Memberエンティティを会員情報リスト連想配列に格納。
					$memberList[$id] = $member;
				}
			}
			// SQL実行が失敗した場合。
			else {
				// 失敗メッセージを作成。
				$assign["msg"] = "データ取得に失敗しました。";
			}
		}
		// 例外処理。
		catch(PDOException $ex) {
			// 障害発生メッセージを作成。
			$assign["msg"] = "障害が発生しました。";
			var_dump($ex);
		}
		finally {
			// DB切断。
			$db = null;
		}
		//テンプレート変数として会員情報リストを格納。
		$assign["memberList"] = $memberList;

		// Twigインスタンスをコンテナから取得。
		$twig = $this->container->get("view");
		// memberAdd.htmlをもとにしたレスポンスオブジェクトを生成。
		$response = $twig->render($response, "memberList.html", $assign);
		// レスポンスオブジェクトをリターン。
		return $response;
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

	// 会員情報詳細表示メソッド。
	public function showMemberDetail(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
	{
		// テンプレート変数を格納する連想配列を用意。
		$assign = [];
		// URL中のパラメータを取得。
		$mbId = $args["id"];

		// ［1］データ取得SQL文字列を用意。
		$sqlSelect = "SELECT * FROM members WHERE id = :id";

		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// ［2］プリペアドステートメントインスタンスを取得。
			$stmt = $db->prepare($sqlSelect);
			// ［3］変数をバインド。
			$stmt->bindValue(":id", $mbId, PDO::PARAM_INT);
			// ［4］SQLの実行。
			$result = $stmt->execute();
			// SQL実行が成功した場合。
			if($result) {
				// ［5］データ取得。
				if($row = $stmt->fetch()) {
					// 各カラムデータの取得。
					$id = $row["id"];
					$mbNameLast = $row["mb_name_last"];
					$mbNameFirst = $row["mb_name_first"];
					$mbBirth = $row["mb_birth"];
					$mbType = $row["mb_type"];

					// Memberエンティティインスタンスを生成。
					$member = new Member();
					// Memberエンティティに各カラムデータを格納。
					$member->setId($id);
					$member->setMbNameLast($mbNameLast);
					$member->setMbNameFirst($mbNameFirst);
					$member->setMbBirth($mbBirth);
					$member->setMbType($mbType);
					//テンプレート変数としてMemberエンティティを格納。
					$assign["memberInfo"] = $member;
				}
				// データが存在しなかった場合。
				else {
					$assign["msg"] = "指定の会員情報は存在しません。";
				}
			}
			// SQL実行が失敗した場合。
			else {
				// 失敗メッセージを作成。
				$assign["msg"] = "データ取得に失敗しました。";
			}
		}
		// 例外処理。
		catch(PDOException $ex) {
			// 障害発生メッセージを作成。
			$assign["msg"] = "障害が発生しました。";
			var_dump($ex);
		}
		finally {
			// DB切断。
			$db = null;
		}

		// Twigインスタンスをコンテナから取得。
		$twig = $this->container->get("view");
		// memberDetail.htmlをもとにしたレスポンスオブジェクトを生成。
		$response = $twig->render($response, "memberDetail.html", $assign);
		// レスポンスオブジェクトをリターン。
		return $response;
	}

	// 全会員情報をJSONとして取得するメソッド。
	public function getAllMembersJSON(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
	{
		// ［1］データ取得SQL文字列を用意。
		$sqlSelect = "SELECT * FROM members";

		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// ［2］プリペアドステートメントインスタンスを取得。
			$stmt = $db->prepare($sqlSelect);
			// ［4］SQLの実行。
			$result = $stmt->execute();
			// SQL実行が成功した場合。
			if($result) {
				// 成功メッセージをJSON用配列に格納。
				$jsonArray["msg"] = "データ取得に成功しました。";
				// SQLの結果表の全データを連想配列形式で取得。
				$allList = $stmt->fetchAll();
				// JSON用配列に全データ連想配列を格納。
				$jsonArray["members"] = $allList;
			}
			// SQL実行が失敗した場合。
			else {
				// 失敗メッセージをJSON用配列に格納。
				$jsonArray["msg"] = "データ取得に失敗しました。";
			}
		}
		// 例外処理。
		catch(PDOException $ex) {
			// 障害発生メッセージをJSON用配列に格納。
			$jsonArray["msg"] = "障害が発生しました。";
			var_dump($ex);
		}
		finally {
			// DB切断。
			$db = null;
		}

		// JSON用配列をエンコード。
		$jsonData = json_encode($jsonArray);
		// JSONデータをレスポンスオブジェクトに格納。
		$responseBody = $response->getBody();
		$responseBody->write($jsonData);
		// コンテントタイプをJSONに設定。
		$response = $response->withHeader("Content-Type", "application/json");
		// レスポンスオブジェクトをリターン。
		return $response;
	}
}
