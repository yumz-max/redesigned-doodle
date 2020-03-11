<?php
//データベース接続
	$dsn = 'mysql:dbname="データベース名";host=localhost';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブルを作成
	$sql = "CREATE TABLE IF NOT EXISTS table51"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "password TEXT"
	.");";
	$stmt = $pdo->query($sql);

	
//編集番号入力
if(isset($_POST["hensyu"])){
//定義
$edit = $_POST["edit"];
$epass = $_POST["epassword"];
	//番号欄・パスワード欄の条件分岐
	if(empty($edit)){
		echo "編集対象番号が空です";
	}elseif(empty($epass)){
		echo "パスワードが空です";
	}else{
		$sql = 'SELECT * FROM table51';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){
			//$rowの中にはテーブルのカラム名が入る
			if($row['id'] == $edit){
			$okpass = $row['password'];
				if($okpass == $epass){
				$editname = $row['name'];
				$editcomment = $row['comment'];
				}
			}
		}
		}	
		}
		
?>
	
<!DOCTYPE html>
<html lang = "ja">
<head>
<meta charset = "utf-8">
</head>
<body>
<h1>最近あった出来事</h1>
<section>
<h3>新規投稿<h3>
<form action="mission_5-1.php" method="post">
	<input type="text" name ="name" size="30" placeholder="名前" autocomplete="off"
		value="<?php if(!empty($editname)) {echo $editname;}?>"><br>
	<input type="text" name ="comment" size="30" placeholder="コメント"autocomplete="off"
		value="<?php if(!empty($editcomment)) {echo $editcomment;}?>"><br>
	<input type="hidden" name ="emode" size="30" 
		value="<?php if(!empty($editname)) {echo $edit;}?>"><br>
	<input type="password" name ="ppassword" size="30" placeholder="パスワード"><br>
　　	<input type="submit" name ="sousin"value="送信"><br>
</section>
<section>
<h3>削除</h3>
	<input type="text" name ="delete" size="30" placeholder="削除対象番号"autocomplete="off"><br>
	<input type="password" name ="dpassword" size="30" placeholder="パスワード"><br>
　　	<input type="submit" name ="sakujo"value="削除"><br>
</section>
<section>
<h3>編集</h3>
	<input type="text" name ="edit" size="30" placeholder="編集対象番号"autocomplete="off"><br>
	<input type="password" name ="epassword" size="30" placeholder="パスワード"><br>
　　	<input type="submit" name ="hensyu"value="編集"><br>
</section>
</form>
</body>
</html>

<?php	
//投稿(送信ボタンが押された時)
if(isset($_POST["sousin"])){
	//定義
	$ppass = $_POST["ppassword"];
	$emode = $_POST["emode"];
	$name = $_POST["name"];
	$comment = $_POST["comment"];
	$date = date('Y/m/d H:i:s');
	
	if(empty($name)){
		echo "名前が空です". "<br>";
	}elseif(empty($comment)){
		echo "コメントが空です". "<br>";	
	}elseif(empty($ppass)){
		echo "パスワードが空です". "<br>";
	}elseif(empty($emode)){
		//通常の投稿。insertでテーブルにデータを入力
		$sql = $pdo -> prepare("INSERT INTO table51 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':date', $date, PDO::PARAM_STR);
		$sql -> bindParam(':password', $ppass, PDO::PARAM_STR);
		$sql -> execute();
	}else{
		$sql = 'SELECT * FROM table51';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){
			if($row['id'] == $emode){
			$okepass = $row['password'];
			}
		}
		
		if($ppass == $okepass){
			$id = $emode; //変更する投稿番号
			$sql = 'update table51 set name=:name,comment=:comment,date=:date,password=:password where id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
			//ここで:passwordの中に$okepassを入れないと新しく変なパスワードが入れられる
			$stmt->bindParam(':password', $okepass, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
		}else{
			echo "パスワードが違います". "<br>";
		}
	}
}
//削除機能
if(isset($_POST["sakujo"])){
	//定義
	$delete = $_POST["delete"];
	$dpass = $_POST["dpassword"];	
	if(empty($delete)){
		echo "削除対象番号が空です". "<br>";
	}elseif(empty($dpass)){
		echo "パスワードが空です". "<br>";
	}else{	
		$sql = 'SELECT * FROM table51';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){
			if($row['id'] == $delete){
				$okpass = $row['password'];
			}
		}
	if($dpass == $okpass){	
	$id = $delete;
	$sql = 'delete from table51 where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	}
	}
}	

//ブラウザ表示
	$sql = 'SELECT * FROM table51';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['password'].',';
		echo $row['date'].'<br>';
		echo "<hr>";
	}
?>