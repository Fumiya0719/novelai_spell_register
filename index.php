<?php
$home = './';
require_once($home . 'database/commonlib.php');

$user_id = isset($_SESSION['user_id']) ? h($_SESSION['user_id']) : ''; 

$presets = [];
// ログインしている場合、ログインユーザーの登録コマンド一覧を取得
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = dbConnect();
        $pdo->beginTransaction();

        
        if (!isset($_GET['order'])) {
            $st = $pdo->prepare('SELECT * FROM preset WHERE user_id = :user_id');
            $st->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $st->execute();
    
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            $pdo->commit();
            $presets = $rows;
        } else {
            $st = $pdo->prepare('SELECT * FROM preset WHERE user_id = :user_id');
            $st->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $st->execute();
    
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            $pdo->commit();
            $presets = $rows;
            // 更に、検索ボックスで検索された場合内容に沿ってコマンドを絞り込む
            // 年齢制限項目
        }
        
    } catch (PDOException $e) {
        echo 'データベース接続に失敗しました。';
        if (DEBUG) echo $e;
    }    

    
}

$title = 'NovelAI コマンド登録機';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= $home ?>top.css">
<title><?= $title ?></title>
</head>
<body>
<?php include($home . 'header.php') ?>
<main>
    <?php if (isset($_SESSION['user_id'])) { ?>
    <section class="spell-list">
        <h2><?= $_SESSION['user_id'] ?>の登録コマンド一覧</h2>
        <?php if (empty($presets)) { ?>
            <div class="no-presets">
                <p>まだコマンドが登録されていません。</p>
                <a href="<?= $home ?>commands.php">新規登録</a>
            </div>
        <?php } else { ?>
        <div class="search-area">
            <div class="open-searchbox">
                <p>検索ボックス</p>
                <button class="btn-common green" onclick="openMenu()" id="open-menu">▽開く</button>
                <button class="btn-common red" onclick="closeMenu()" id="close-menu" style="display:none;">△閉じる</button>
            </div>
            <?php include($home . 'search_form.php') ?>
        </div>
        <div class="description">
            <p><span>オレンジ色</span>の項目をクリックするとデータをコピーできます。</p>
            <span id="copy-alert"></span>
        </div>
        <table class="command-table">
            <thead>
                <tr>
                    <th id="description">説明</th>
                    <th id="commands">コマンド</th>
                    <th id="commands_ban">BANコマンド</th>
                    <th id="seed">シード値</th>
                    <th id="resolution">解像度</th>
                    <th id="others">備考</th>
                    <th id="edit">編集</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presets as $preset) { ?>
                    <tr>
                        <td id="description"><?= $preset['description'] ?></td>
                        <td id="commands" onclick="copyPreset('<?= $preset['commands'] ?>', '<?= $preset['description'] . 'のコマンド' ?>')"><?= $preset['commands'] ?></td>
                        <td id="commands_ban" onclick="copyPreset('<?= $preset['commands_ban'] ?>', '<?= $preset['description'] . 'のBANコマンド' ?>')"><?= $preset['commands_ban'] ?></td>
                        <td id="seed" onclick="copyPreset('<?= $preset['seed'] ?>', '<?= $preset['description'] . 'のシード値' ?>')"><?= $preset['seed'] ?></td>
                        <td id="resolution"><?= $preset['resolution'] ?></td>
                        <td id="others"><?= $preset['others'] ?></td>
                        <td id="edit"><a href="<?= $home ?>commands.php?preset_id=<?= $preset['preset_id'] ?>">編集</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </section>
    <?php } ?>
</main>
</body>
<script lang="js">
{
    function copyPreset(command, title) {
        navigator.clipboard.writeText(command);
        const copyAlert = document.getElementById('copy-alert');
        copyAlert.innerHTML = title + 'をコピーしました。'
    };

    const om = document.getElementById('open-menu');
    const cm = document.getElementById('close-menu');
    const sf = document.getElementById('search-form');
    function openMenu() {
        om.style.display = "none";
        cm.style.display = "inline-block";
        sf.style.display = "block";
    }

    function closeMenu() {
        om.style.display = "inline-block";
        cm.style.display = "none";
        sf.style.display = "none";
    }

    function checkAllBox() {
        const si = document.getElementsByName('search_item[]');
        if (si[0].checked) {
            for (let i = 1; i < si.length; i++) {
                si[i].disabled = true;
            }
        } else {
            for (let i = 1; i < si.length; i++) {
                si[i].disabled = false;
            }
        }
    }
}
</script>
</html>