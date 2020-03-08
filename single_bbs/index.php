<?php ob_start();?>
<?php
function redirect() {
    header('Location: http://shinyakato.php.xdomain.jp/single_bbs/');
    exit;
}
?>

<?php

$pdo = new PDO("mysql:host=mysql1.php.xdomain.ne.jp;dbname=shinyakato_lesson;charset=utf8", "shinyakato_root", "rootadmin");

if (isset($_POST["delete_id"])) {
    $delete_id = $_POST["delete_id"];
    $sql  = "DELETE FROM bbs WHERE id = :delete_id;";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue(":delete_id", $delete_id, PDO::PARAM_INT);
    $stmt -> execute();
}

if (isset($_POST["content"]) && isset($_POST["user_name"])) {

    $content   = trim($_POST["content"]);
    $user_name = trim($_POST["user_name"]);
    
    if ($_POST["user_name"] === "") {
        $user_name = "NoName";
    }

    if ($content !== "") {
        $sql  = "INSERT INTO bbs (content, user_name, updated_at) VALUES (:content, :user_name, NOW());";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue(":content", $content, PDO::PARAM_STR);
        $stmt -> bindValue(":user_name", $user_name, PDO::PARAM_STR);
        $stmt -> execute();
        redirect();
    }	else {
        $error_message[] = '※投稿内容を入力してください。';
    }

} ?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ひとこと掲示板</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
<header class="bg-secondary">
    <h1 class="container text-white p-3">ひとこと掲示板</h1>
</header>
	<div class="container">
		<div class="content">
			<h2 class="p-1 px-3 mt-5 mb-3 bg-info text-white">投稿フォーム</h2>
			<form class="form" action="index.php" method="post">
				<div class="form-group">
					<label class="control-label">投稿内容</label>
					<input class="form-control" type="text" name="content">
                    <?php foreach((array)$error_message as $value): ?>
                    <p class="text-danger"><?php echo $value; ?></p>
                     <?php endforeach; ?>
				</div>
				<div class="form-group">
					<label class="control-label">投稿者</label>
					<input class="form-control" type="text" name="user_name">
				</div>
				<button class="btn btn-primary" type="submit">送信</button>
			</form>
			<h2 class="p-1 px-3 mt-5 mb-3 bg-info text-white">投稿リスト</h2>
			<?php

			$sql = "SELECT * FROM bbs ORDER BY updated_at;";
			$stmt = $pdo->prepare($sql);
			$stmt -> execute();
			?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tr>
                        <th class="text-nowrap">id</th>
                        <th class="text-nowrap">日時</th>
                        <th class="text-nowrap">投稿内容</th>
                        <th class="text-nowrap">投稿者</th>
                        <th></th>
                    </tr>
                    <?php
 
                    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td class="text-nowrap"><?= $row["id"] ?></td>
                            <td><?= $row["updated_at"] ?></td>
                            <td class="text-nowrap"><?= $row["content"] ?></td>
                            <td class="text-nowrap"><?= $row["user_name"] ?></td>
                            <td>
                                <form action="index.php" method="post" class="text-nowrap">
                                    <input type="hidden" name="delete_id" value=<?= $row["id"] ?>>
                                    <button class="btn btn-danger" type="submit">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
		</div>
	</div>
</body>
</html>