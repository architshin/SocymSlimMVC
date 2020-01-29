<?php
namespace SocymSlim\MVC\tests\testcases\controllers\MemberController;

use PDOException;
use PHPUnit\Framework\TestCase;
use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use SocymSlim\MVC\daos\MemberDAO;
use SocymSlim\MVC\controllers\MemberController;

class GetAllMembersJSONTest extends TestCase
{
	// findAll2Array()が正常な場合にリターンするダミーデータ。
	private $memberList = [
		0 => [
			"id" => 1,
			"mb_name_last" => "田中",
			"mb_name_first" => "由美",
			"mb_birth" => "1999-12-17",
			"mb_type" => 1,
		],
		1 => [
			"id" => 2,
			"mb_name_last" => "中田",
			"mb_name_first" => "真央",
			"mb_birth" => "2000-12-16",
			"mb_type" => 2,
		],
		2 => [
			"id" => 3,
			"mb_name_last" => "中山",
			"mb_name_first" => "香澄",
			"mb_birth" => "1998-06-12",
			"mb_type" => 3,
		],
	];
	// MemberDAOのテストダブルプロパティ。
	private $stubMemberDAO;
	// ServerRequestInterfaceのテストダブルプロパティ。
	private $stubRequest;
	// レスポンスプロパティ。
	private $response;
	// MemberControllerプロパティ。
	private $memberController;

	// セットアップメソッド。
	protected function setUp(): void
	{
		// MemberDAOのテストダブルを作成。
		$stubMemberDAO = $this->createMock(MemberDAO::class);
		// MemberDAOのテストダブルをプロパティに代入。
		$this->stubMemberDAO = $stubMemberDAO;
		// ServerRequestInterfaceのテストダブルを作成。
		$this->stubRequest = $this->createMock(ServerRequestInterface::class);
		// レスポンスインスタンスを生成。
		$this->response = new Response();
		// コンテナインスタンスを生成。
		$container = new Container();
		// dbインスタンスの生成処理を登録。
		$container->set("db",
			function() {
				return null;
			}
		);
		// memberDAOインスタンスの生成処理を登録。
		$container->set("memberDAO",
			\DI\value(function($db) use ($stubMemberDAO) {
				return $stubMemberDAO;
			})
		);
		// MemberControllerインスタンスを生成。
		$this->memberController = new MemberController($container);
	}
	
	// 正常系のテスト。会員リストが返される場合。
	public function testSuccess()
	{
		// MemberDAOのテストダブルのメソッドfindAll2Array()の戻り値を設定。
		$this->stubMemberDAO->method("findAll2Array")->willReturn($this->memberList);
		// getAllMembersJSON()メソッドを実行。
		$returnResponse = $this->memberController->getAllMembersJSON($this->stubRequest, $this->response, []);
		// レスポンスボディに格納されたJSON文字列を取得。
		$responseBody = (string) $returnResponse->getBody();
		// 想定値のJSON配列を生成。
		$expectedReturnArray = [
			"msg" => "データ取得に成功しました。",
			"members" => $this->memberList
		];
		// 想定値のJSON配列をJSON文字列化。
		$expectedReturnJSON = json_encode($expectedReturnArray);
		// アサーション。
		$this->assertSame($responseBody, $expectedReturnJSON);
	}
	// 非正常系のテスト。空の会員リストが返される場合。
	public function testEmpty()
	{
		$this->stubMemberDAO->method("findAll2Array")->willReturn([]);
		$returnResponse = $this->memberController->getAllMembersJSON($this->stubRequest, $this->response, []);
		$responseBody = (string) $returnResponse->getBody();
		$expectedReturnArray = [
			"msg" => "データ取得に失敗しました。"
		];
		$expectedReturnJSON = json_encode($expectedReturnArray);
		$this->assertSame($responseBody, $expectedReturnJSON);
	}
	// 非正常系テスト。PDOExceptionが発生する場合。
	public function testException()
	{
		$this->stubMemberDAO->method("findAll2Array")->will($this->throwException(new PDOException()));
		$returnResponse = $this->memberController->getAllMembersJSON($this->stubRequest, $this->response, []);
		$responseBody = (string) $returnResponse->getBody();
		$expectedReturnArray = [
			"msg" => "障害が発生しました。"
		];
		$expectedReturnJSON = json_encode($expectedReturnArray);
		$this->assertSame($responseBody, $expectedReturnJSON);
	}
}
