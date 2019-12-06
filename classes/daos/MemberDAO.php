<?php
namespace SocymSlim\MVC\daos;

use PDO;
use SocymSlim\MVC\entities\Member;

class MemberDAO
{
	// PDOインスタンスを表すプロパティ。
	private $db;

	// コンストラクタ。
	public function __construct(PDO $db)
	{
		// 引数をプロパティに格納。
		$this->db = $db;
	}

	// 主キーによる検索メソッド。
	public function findByPK(int $id): ?Member
	{
		// データ取得SQL文字列を用意。
		$sqlSelect = "SELECT * FROM members WHERE id = :id";
		// プリペアドステートメントインスタンスを取得。
		$stmt = $this->db->prepare($sqlSelect);
		// 変数をバインド。
		$stmt->bindValue(":id", $id, PDO::PARAM_INT);
		// SQLの実行。
		$result = $stmt->execute();
		// データ取得。
		if($result && $row = $stmt->fetch()) {
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
		}
		// Memberエンティティインスタンスをリターン。
		return $member;
	}
}
