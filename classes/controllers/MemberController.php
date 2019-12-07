<?php
namespace SocymSlim\MVC\controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use SocymSlim\MVC\entities\Member;
use SocymSlim\MVC\daos\MemberDAO;

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
		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// MemberDAOインスタンスを生成。
			$memberDAO = new MemberDAO($db);
			// 全件データを取得。
			$memberList = $memberDAO->findAll();
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

		// リクエストパラメータをエンティティに格納。
		$member = new Member();
		$member->setMbNameLast($addMbNameLast);
		$member->setMbNameFirst($addMbNameFirst);
		$member->setMbBirth($addMbBirth);
		$member->setMbType($addMbType);

		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// MemberDAOインスタンスを生成。
			$memberDAO = new MemberDAO($db);
			// データ登録。
			$mbId = $memberDAO->insert($member);
			// SQL実行が成功した場合。
			if($mbId !== -1) {
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
		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// MemberDAOインスタンスを生成。
			$memberDAO = new MemberDAO($db);
			// 主キーによる検索を実行。
			$member = $memberDAO->findByPK($mbId);
			// データが存在した場合。
			if(isset($member)) {
				//テンプレート変数としてMemberエンティティを格納。
				$assign["memberInfo"] = $member;
			}
			// データが存在しなかった場合。
			else {
				$assign["msg"] = "指定の会員情報は存在しません。";
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
		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// MemberDAOインスタンスを生成。
			$memberDAO = new MemberDAO($db);
			// 全データを連想配列として取得。
			$allList = $memberDAO->findAll2Array();
			// SQL実行が成功した場合。
			if(!empty($allList)) {
				// 成功メッセージをJSON用配列に格納。
				$jsonArray["msg"] = "データ取得に成功しました。";
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
