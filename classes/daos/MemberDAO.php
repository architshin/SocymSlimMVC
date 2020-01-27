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

	// 全件検索メソッド。
	public function findAll(): array
	{
		// 会員情報リストを格納する連想配列の用意。
		$memberList = [];
		// データ取得SQL文字列を用意。
		$sqlSelect = "SELECT * FROM members ORDER BY id";
		// プリペアドステートメントインスタンスを取得。
		$stmt = $this->db->prepare($sqlSelect);
		// SQLの実行。
		$result = $stmt->execute();
		// SQL実行が成功した場合。
		if($result) {
			// フェッチループ。
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
		// 全件が格納された連想配列をリターン。
		return $memberList;
	}

	// 全件を連想配列で得るメソッド。
	public function findAll2Array(): array
	{
		// データ取得SQL文字列を用意。
		$sqlSelect = "SELECT * FROM members";
		// プリペアドステートメントインスタンスを取得。
		$stmt = $this->db->prepare($sqlSelect);
		// SQLの実行。
		$result = $stmt->execute();
		// SQLの結果表の全データを連想配列形式で取得。
		$allList = $stmt->fetchAll();
		// 全データが格納された連想配列をリターン。
		return $allList;
	}

	// データ登録メソッド。
	public function insert(Member $member): int
	{
		//登録用SQL文字列を用意。
		$sqlInsert = "INSERT INTO members (mb_name_last, mb_name_first, mb_birth, mb_type) VALUES (:mb_name_last, :mb_name_first, :mb_birth, :mb_type)";
		// プリペアドステートメントインスタンスを取得。
		$stmt = $this->db->prepare($sqlInsert);
		// 変数をバインド。
		$stmt->bindValue(":mb_name_last", $member->getMbNameLast(), PDO::PARAM_STR);
		$stmt->bindValue(":mb_name_first", $member->getMbNameFirst(), PDO::PARAM_STR);
		if(empty($member->getMbBirth())) {
			$stmt->bindValue(":mb_birth", null, PDO::PARAM_NULL);
		}
		else {
			$stmt->bindValue(":mb_birth", $member->getMbBirth(), PDO::PARAM_STR);
		}
		$stmt->bindValue(":mb_type", $member->getMbType(), PDO::PARAM_INT);
		// SQLの実行。
		$result = $stmt->execute();
		// 戻り値となる連番主キー値を初期値-1で用意。
		$mbId = -1;
		// SQL実行が成功した場合。
		if($result) {
			// 連番主キーを取得。
			$mbId = $this->db->lastInsertId();
		}
		return $mbId;
	}
}
