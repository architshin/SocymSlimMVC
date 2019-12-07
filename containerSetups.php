<?php
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Flash\Messages;

$container = new Container();
$container->set("view",
	function() {
		// $twig = Twig::create($_SERVER["DOCUMENT_ROOT"]."/../templates");
		$twig = new Twig($_SERVER["DOCUMENT_ROOT"]."/../templates");
		return $twig;
	}
);
// PDOインスタンスを生成する処理。
$container->set("db",
	function() {
		// DB接続情報を表す変数。
		$dbDns = "pgsql:dbname=socymslimdb;host=localhost;port=5432";
		$dbUsername = "socymslimdbusr";
		$dbPassword = "hogehoge";
		// PDOインスタンスを生成。DB接続。
		$db = new PDO($dbDns, $dbUsername, $dbPassword);
		// PDOのエラー表示モードを例外モードに設定。
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// プリペアドステートメントを有効に設定。
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		// フェッチモードをカラム名のみの結果セットに設定。
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		// PDOインスタンスをリターン。
		return $db;
	}
);
// フラッシュメッセージ用のMessageインスタンスを生成する処理。
$container->set("flash",
	function() {
		session_start();
		$flash = new Messages();
		return $flash;
	}
);
AppFactory::setContainer($container);
