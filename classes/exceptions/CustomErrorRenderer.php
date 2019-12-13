<?php
namespace SocymSlim\MVC\exceptions;

use Throwable;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Views\Twig;
use Slim\Error\Renderers\HtmlErrorRenderer;
use Slim\Exception\HttpNotFoundException;
use SocymSlim\MVC\exceptions\DataAccessException;

class CustomErrorRenderer implements ErrorRendererInterface
{
	// コンテナインスタンスプロパティ。
	private $container;

	// コンストラクタ。コンテナインスタンスを受け取ってプロパティに格納する。
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function __invoke(Throwable $exception, bool $displayErrorDetails): string
	{
		// エラー詳細表示なら…
		if($displayErrorDetails) {
			// SlimのデフォルトHTMLエラーレンダラクラスインスタンスを生成。
			$htmlErrorRenderer = new HtmlErrorRenderer();
			// SlimのデフォルトHTMLエラーレンダラインスタンスを実行。
			$returnHtml = $htmlErrorRenderer($exception, $displayErrorDetails);
		}
		// エラー詳細表示させないなら…
		else {
			// Twigインスタンスをコンテナから取得。
			$twig = $this->container->get("view");
			// 発生した例外がHttpNotFoundExceptionなら…
			if($exception instanceof HttpNotFoundException) {
				// テンプレートとして404.htmlを利用。
				$returnHtml = $twig->fetch("404.html");
			}
			// 発生した例外がDataAccessExceptionなら…
			elseif($exception instanceof DataAccessException) {
				// 例外インスタンスからメッセージを取得。
				$errorMsg = $exception->getMessage();
				// メッセージを加工した上でテンプレート変数に格納。
				$errorMsg .= "もう一度初めから操作してください。";
				$assign["errorMsg"] = $errorMsg;
				// Twigによりテンプレートから生成されたHTML文字列を取得。
				$returnHtml = $twig->fetch("error.html", $assign);
			}
			else {
				// メッセージをテンプレート変数に格納。
				$assign["errorMsg"] = "もう一度初めから操作してください。";
				// Twigによりテンプレートから生成されたHTML文字列を取得。
				$returnHtml = $twig->fetch("error.html", $assign);
			}
		}
		// HTML文字列をリターン。
		return $returnHtml;
	}
}
