<?php
namespace SocymSlim\MVC\tests\testcases\controllers\MemberController;

use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;
use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use SocymSlim\MVC\daos\MemberDAO;
use SocymSlim\MVC\controllers\MemberController;

class GetAllMembersJSONTest extends TestCase
{
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

	// 正常系のテスト。会員リストが返される場合。
	public function testSuccess()
	{
		$stubRequest = $this->createMock(ServerRequestInterface::class);
		$response = new Response();
		$stubMemberDAO = $this->createMock(MemberDAO::class);
		$stubMemberDAO->method("findAll2Array")->willReturn($this->memberList);
		$container = new Container();
		$container->set("db",
			function() {
				return null;
			}
		);
		$container->set("memberDAO",
			\DI\value(function($db) use ($stubMemberDAO) {
				return $stubMemberDAO;
			})
		);
		$memberCotroller = new MemberController($container);
		$returnResponse = $memberCotroller->getAllMembersJSON($stubRequest, $response, []);
		$responseBody = (string) $returnResponse->getBody();
		$expectedReturnArray = [
			"msg" => "データ取得に成功しました。",
			"members" => $this->memberList
		];
		$expectedReturnJSON = json_encode($expectedReturnArray);
		$this->assertSame($responseBody, $expectedReturnJSON);
	}
	// 非正常系のテスト。空の会員リストが返される場合。
	public function testEmpty()
	{
		$stubRequest = $this->createMock(ServerRequestInterface::class);
		$response = new Response();
		$stubMemberDAO = $this->createMock(MemberDAO::class);
		$stubMemberDAO->method("findAll2Array")->willReturn([]);
		$container = new Container();
		$container->set("db",
			function() {
				return null;
			}
		);
		$container->set("memberDAO",
			\DI\value(function($db) use ($stubMemberDAO) {
				return $stubMemberDAO;
			})
		);
		$memberCotroller = new MemberController($container);
		$returnResponse = $memberCotroller->getAllMembersJSON($stubRequest, $response, []);
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
		$stubRequest = $this->createMock(ServerRequestInterface::class);
		$response = new Response();
		$stubMemberDAO = $this->createMock(MemberDAO::class);
		$stubMemberDAO->method("findAll2Array")->will($this->throwException(new PDOException()));
		$container = new Container();
		$container->set("db",
			function() {
				return null;
			}
		);
		$container->set("memberDAO",
			\DI\value(function($db) use ($stubMemberDAO) {
				return $stubMemberDAO;
			})
		);
		$memberCotroller = new MemberController($container);
		$returnResponse = $memberCotroller->getAllMembersJSON($stubRequest, $response, []);
		$responseBody = (string) $returnResponse->getBody();
		$expectedReturnArray = [
			"msg" => "障害が発生しました。"
		];
		$expectedReturnJSON = json_encode($expectedReturnArray);
		$this->assertSame($responseBody, $expectedReturnJSON);
	}
}