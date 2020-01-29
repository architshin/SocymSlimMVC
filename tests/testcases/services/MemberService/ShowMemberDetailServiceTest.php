<?php
namespace SocymSlim\MVC\tests\testcases\services\MemberService;

use PHPUnit\Framework\TestCase;
use DI\Container;
use SocymSlim\MVC\daos\MemberDAO;
use SocymSlim\MVC\services\MemberService;
use SocymSlim\MVC\entities\Member;

class ShowMemberDetailServiceTest extends TestCase
{
	// MemberDAOのテストダブルプロパティ。
	private $stubMemberDAO;
	// MemberServiceプロパティ。
	private $memberService;

	// セットアップメソッド。
	protected function setUp(): void
	{
		// MemberDAOのテストダブルを作成。
		$stubMemberDAO = $this->createMock(MemberDAO::class);
		// MemberDAOのテストダブルをプロパティに代入。
		$this->stubMemberDAO = $stubMemberDAO;
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
		// MemberServiceインスタンスを生成。
		$this->memberService = new MemberService($container);
	}
	public function testSuccess()
	{
		// ダミーの会員情報エンティティを作成。
		$member = new Member();
		$member->setId(5);
		$member->setMbNameLast("中村");
		$member->setMbNameFirst("恵");
		$member->setMbBirth("1996-05-14");
		$member->setMbType(2);
		// MemberDAOのテストダブルのメソッドfindByPK()の戻り値を設定。
		$this->stubMemberDAO->method("findByPK")->willReturn($member);
		// showMemberDetailService()メソッドを実行。
		$returnArray = $this->memberService->showMemberDetailService(5);
		// 想定値のテンプレート変数を生成。
		$expectedMember = new Member();
		$expectedMember->setId(5);
		$expectedMember->setMbNameLast("中村");
		$expectedMember->setMbNameFirst("恵");
		$expectedMember->setMbBirth("1996-05-14");
		$expectedMember->setMbType(2);
		$expectedReturnArray = [
			"memberInfo" => $expectedMember
		];
		// アサーション。
		$this->assertEquals($returnArray, $expectedReturnArray);
	}
}
