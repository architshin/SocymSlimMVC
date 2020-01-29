<?php
namespace SocymSlim\MVC\tests\testcases\entities;

use PHPUnit\Framework\TestCase;
use SocymSlim\MVC\entities\Member;

class MemberTest extends TestCase
{
	// getMbBirthStr()のデータありテスト。
	public function testGetMbBirthStr_WithData()
	{
		$member = new Member();
		$member->setMbBirth("1970-06-12");
		$this->assertSame("1970年6月12日", $member->getMbBirthStr());
	}
	// getMbBirthStr()のデータなしテスト。
	public function testGetMbBirthStr_NoData()
	{
		$member = new Member();
		$this->assertSame("", $member->getMbBirthStr());
	}
	/**
	 * @dataProvider mbTypeStrProvider
	 */
	public function testGetMbTypeStr($mbType, $mbTypeStr)
	{
		$member = new Member();
		$member->setMbType($mbType);
		$this->assertSame($mbTypeStr, $member->getMbTypeStr());
	}
	/**
	 * @dataProvider mbNameFullProvider
	 */
	public function testGetMbNameFull($mbNameLast, $mbNameFirst, $mbNameFull)
	{
		$member = new Member();
		$member->setMbNameLast($mbNameLast);
		$member->setMbNameFirst($mbNameFirst);
		$this->assertSame($mbNameFull, $member->getMbNameFull());
	}
	// mbTypeStrのデータプロバイダ
	public function mbTypeStrProvider()
	{
		return [
			"一般会員のパターン" => [1, "一般会員"],
			"優良会員のパターン" => [2, "優良会員"],
			"特別会員のパターン" => [3, "特別会員"],
			"対応関係のない番号のパターン" => [4, ""],
			"データなしのパターン" => [null, ""]
		];
	}
	// mbNameFullのデータプロバイダ
	public function mbNameFullProvider()
	{
		return [
			"姓名両方ありの場合" => ["田中", "由美", "田中 由美"],
			"姓だけの場合" => ["田中", "", "田中 "],
			"名だけの場合" => ["", "由美", " 由美"],
			"姓名両方なしの場合" => ["", "", " "],
		];
	}
}
