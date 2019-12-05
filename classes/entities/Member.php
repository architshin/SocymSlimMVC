<?php
namespace SocymSlim\MVC\entities;

class Member
{
	private $id;
	private $mbNameLast;
	private $mbNameFirst;
	private $mbBirth;
	private $mbType;

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