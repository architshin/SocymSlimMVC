<?php
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Flash\Messages;

$container = new Container();
$container->set("view",
	function() {
		$twig = Twig::create($_SERVER["DOCUMENT_ROOT"]."/../templates");
		return $twig;
	}
);
// PDOインスタンスを生成する処理。
$container->set("db",
	function() {
		// 環境変数DATABASE_URLを取得。
		$databaseUri = getenv("DATABASE_URL");
		// DATABASE_URLを解析。
		$parsedDatabaseUri = parse_url($databaseUri);
		// 解析済みDATABASE_URL配列からホスト部分を取得。
		$host = $parsedDatabaseUri["host"];
		// 解析済みDATABASE_URL配列からポート部分を取得。
		$port = $parsedDatabaseUri["port"];
		// 解析済みDATABASE_URL配列からユーザ部分を取得。
		$dbUsername = $parsedDatabaseUri["user"];
		// 解析済みDATABASE_URL配列からパスワード部分を取得。
		$dbPassword = $parsedDatabaseUri["pass"];
		// 解析済みDATABASE_URL配列からパス部分を取得した上で左の/を削除。
		$dbname = ltrim($parsedDatabaseUri["path"],"/");
		// それぞれ取得したデータからDNS文字列を生成。
		$dbDns = "pgsql:dbname=".$dbname.";host=".$host.";port=".$port;
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
