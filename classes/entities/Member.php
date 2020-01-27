<?php
namespace SocymSlim\MVC\entities;

class Member
{
	private $id;
	private $mbNameLast;
	private $mbNameFirst;
	private $mbBirth;
	private $mbType;

	// Y年n月j日形式で加工された誕生日プロパティのゲッタ。
	public function getMbBirthStr(): string
	{
		// 初期値として空文字列を用意。
		$mbBirthStr = "";
		// プロパティの$mbBirthがemptyでないならば…
		if(!empty($this->mbBirth)) {
			// $mbBirthを加工。
			$mbBirthStr = date("Y年n月j日", strtotime($this->mbBirth));
		}
		// 結果をリターン。
		return $mbBirthStr;
	}

	// 会員種類の整数値を文字列に変換した値のゲッタ。
	public function getMbTypeStr(): string
	{
		// 初期値として空文字列を用意。
		$mbTypeStr = "";
		// プロパティ$mbTypeの値で処理を分岐。この場合switchが便利。
		switch($this->mbType) {
			case 1:
				$mbTypeStr = "一般会員";
				break;
			case 2:
				$mbTypeStr = "優良会員";
				break;
			case 3:
				$mbTypeStr = "特別会員";
				break;
		}
		// 結果をリターン。
		return $mbTypeStr;
	}

	// 会員氏名を半角スペースで結合した文字列のゲッタ。
	public function getMbNameFull(): string
	{
		return $this->mbNameLast." ".$this->mbNameFirst;
	}

	public function getId(): ?int
	{
		return $this->id;
	}
	public function setId(?int $id): void
	{
		$this->id = $id;
	}
	public function getMbNameLast(): ?string
	{
		return $this->mbNameLast;
	}
	public function setMbNameLast(?string $mbNameLast): void
	{
		$this->mbNameLast = $mbNameLast;
	}
	public function getMbNameFirst(): ?string
	{
		return $this->mbNameFirst;
	}
	public function setMbNameFirst(?string $mbNameFirst): void
	{
		$this->mbNameFirst = $mbNameFirst;
	}
	public function getMbBirth(): ?string
	{
		return $this->mbBirth;
	}
	public function setMbBirth(?string $mbBirth): void
	{
		$this->mbBirth = $mbBirth;
	}
	public function getMbType(): ?int
	{
		return $this->mbType;
	}
	public function setMbType(?int $mbType): void
	{
		$this->mbType = $mbType;
	}
}