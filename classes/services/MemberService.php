<?php
namespace SocymSlim\MVC\services;

use Psr\Container\ContainerInterface;
use SocymSlim\MVC\daos\MemberDAO;
use SocymSlim\MVC\exceptions\DataAccessException;

class MemberService
{
	// コンテナインスタンス
	private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
		// 引数のコンテナインスタンスをプロパティに格納。
		$this->container = $container;
	}

	// 会員情報詳細表示に必要なデータを揃えるメソッド。
	public function showMemberDetailService(int $mbId): array
	{
		// テンプレート変数を格納する連想配列を用意。
		$assign = [];
		try {
			// PDOインスタンスをコンテナから取得。
			$db = $this->container->get("db");
			// MemberDAOインスタンスをコンテナから取得。
			$memberDAO = $this->container->call("memberDAO", [$db]);
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
			// 発生したPDOExceptionのコードを取得。
			$exCode = $ex->getCode();
			// 新たにDataAccessExceptionを発生。
			throw new DataAccessException("データベース処理中に障害が発生しました。", $exCode, $ex);
		}
		finally {
			// DB切断。
			$db = null;
		}
		return $assign;
	}
}
