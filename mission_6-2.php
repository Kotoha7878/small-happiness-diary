<?php

include 'db_connect.php';
include 'functions.php';

// フォームの初期値
$message = '';
$formDate = date('Y-m-d');
$formCategory = '';
$formTitle = '';
$formEvent = '';
$formFeeling = '';
$formLevel = '';
$editId = '';


// ----------------------------------
// 編集対象を読み込む
// ----------------------------------

if (isset($_POST['submit_edit_select'])) {

    $selectedId = isset($_POST['edit_select_id'])
        ? trim($_POST['edit_select_id'])
        : '';

    $editPassword = isset($_POST['edit_select_password'])
        ? $_POST['edit_select_password']
        : '';

    if (isValidId($selectedId) && $editPassword !== '') {

        $selectedId = (int)$selectedId;

        $sql = 'SELECT * FROM good_things WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $selectedId, PDO::PARAM_INT);
        $stmt->execute();

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record === false) {
            $message = '指定された記録は存在しません。';

        } elseif (
            password_verify(
                $editPassword,
                $record['password']
            )
        ) {
            $formDate = $record['event_date'];
            $formCategory = $record['category'];
            $formTitle = $record['title'];
            $formEvent = $record['event_text'];
            $formFeeling = $record['feeling'];
            $formLevel = $record['happiness_level'];
            $editId = $record['id'];

            $message = '記録番号'
                . $editId
                . 'を編集中です。';

        } else {
            $message = '編集パスワードが違います。';
        }

    } else {
        $message = '編集番号とパスワードを入力してください。';
    }


// ----------------------------------
// 新規登録または編集保存
// ----------------------------------

} elseif (isset($_POST['submit_record'])) {

    $eventDate = isset($_POST['event_date'])
        ? trim($_POST['event_date'])
        : '';

    $category = isset($_POST['category'])
        ? trim($_POST['category'])
        : '';

    $title = isset($_POST['title'])
        ? trim($_POST['title'])
        : '';

    $eventText = isset($_POST['event_text'])
        ? trim($_POST['event_text'])
        : '';

    $feeling = isset($_POST['feeling'])
        ? trim($_POST['feeling'])
        : '';

    $level = isset($_POST['happiness_level'])
        ? trim($_POST['happiness_level'])
        : '';

    $recordPassword = isset($_POST['record_password'])
        ? $_POST['record_password']
        : '';

    $submittedEditId = isset($_POST['edit_id'])
        ? trim($_POST['edit_id'])
        : '';

    // 入力値をフォームに残す
    $formDate = $eventDate;
    $formCategory = $category;
    $formTitle = $title;
    $formEvent = $eventText;
    $formFeeling = $feeling;
    $formLevel = $level;
    $editId = $submittedEditId;

    $levelNumber = (int)$level;

    // 入力チェック
    if (
        $eventDate === ''
        || !isValidCategory($category)
        || $title === ''
        || $eventText === ''
        || $feeling === ''
        || $levelNumber < 1
        || $levelNumber > 5
        || $recordPassword === ''
    ) {
        $message = 'すべての項目を正しく入力してください。';

    // 編集番号が空なら新規登録
    } elseif ($submittedEditId === '') {

        $passwordHash = password_hash(
            $recordPassword,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO good_things (
                    event_date,
                    category,
                    title,
                    event_text,
                    feeling,
                    happiness_level,
                    password
                ) VALUES (
                    :event_date,
                    :category,
                    :title,
                    :event_text,
                    :feeling,
                    :happiness_level,
                    :password
                )";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(
            ':event_date',
            $eventDate,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ':category',
            $category,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ':title',
            $title,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ':event_text',
            $eventText,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ':feeling',
            $feeling,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ':happiness_level',
            $levelNumber,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ':password',
            $passwordHash,
            PDO::PARAM_STR
        );

        $stmt->execute();

        $message = '今日の良い出来事を記録しました。';

        // 登録後はフォームを空に戻す
        $formDate = date('Y-m-d');
        $formCategory = '';
        $formTitle = '';
        $formEvent = '';
        $formFeeling = '';
        $formLevel = '';
        $editId = '';

    // 編集保存
    } elseif (isValidId($submittedEditId)) {

        $submittedEditId = (int)$submittedEditId;

        $sql = 'SELECT password
                FROM good_things
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(
            ':id',
            $submittedEditId,
            PDO::PARAM_INT
        );
        $stmt->execute();

        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($target === false) {
            $message = '編集対象の記録が存在しません。';

        } elseif (
            password_verify(
                $recordPassword,
                $target['password']
            )
        ) {
            $sql = "UPDATE good_things SET
                        event_date = :event_date,
                        category = :category,
                        title = :title,
                        event_text = :event_text,
                        feeling = :feeling,
                        happiness_level = :happiness_level
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(
                ':event_date',
                $eventDate,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ':category',
                $category,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ':title',
                $title,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ':event_text',
                $eventText,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ':feeling',
                $feeling,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ':happiness_level',
                $levelNumber,
                PDO::PARAM_INT
            );

            $stmt->bindParam(
                ':id',
                $submittedEditId,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $message = '記録番号'
                . $submittedEditId
                . 'を更新しました。';

            $formDate = date('Y-m-d');
            $formCategory = '';
            $formTitle = '';
            $formEvent = '';
            $formFeeling = '';
            $formLevel = '';
            $editId = '';

        } else {
            $message = '編集パスワードが違います。';
        }
    }


// ----------------------------------
// 削除
// ----------------------------------

} elseif (isset($_POST['submit_delete'])) {

    $deleteId = isset($_POST['delete_id'])
        ? trim($_POST['delete_id'])
        : '';

    $deletePassword = isset($_POST['delete_password'])
        ? $_POST['delete_password']
        : '';

    if (isValidId($deleteId) && $deletePassword !== '') {

        $deleteId = (int)$deleteId;

        $sql = 'SELECT password
                FROM good_things
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
        $stmt->execute();

        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($target === false) {
            $message = '指定された記録は存在しません。';

        } elseif (
            password_verify(
                $deletePassword,
                $target['password']
            )
        ) {
            $sql = 'DELETE FROM good_things WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
            $stmt->execute();

            $message = '記録番号'
                . $deleteId
                . 'を削除しました。';

        } else {
            $message = '削除パスワードが違います。';
        }

    } else {
        $message = '削除番号とパスワードを入力してください。';
    }
}


// ----------------------------------
// 記録一覧
// ----------------------------------

$sql = "SELECT
            id,
            event_date,
            category,
            title,
            event_text,
            feeling,
            happiness_level,
            created_at
        FROM good_things
        ORDER BY event_date DESC, id DESC";

$stmt = $pdo->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>きょうの小さなしあわせ</title>

    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>

<body>

<div class="container">

    <header>
        <h1>きょうの小さなしあわせ</h1>

        <p class="subtitle">
            今日あった良かったこと、癒されたこと、
            嬉しかったことを残そう。
        </p>
    </header>

    <?php if ($message !== '') { ?>
        <p class="message"><?php echo h($message); ?></p>
    <?php } ?>

    <!-- 新規登録・編集兼用フォーム -->
    <section class="form-box <?php
        if ($editId !== '') {
            echo 'editing';
        }
    ?>">

        <?php if ($editId !== '') { ?>
            <h2>
                記録番号<?php echo h($editId); ?>を編集中
            </h2>

            <p>
                内容を変更し、登録時のパスワードを
                もう一度入力してください。
            </p>
        <?php } else { ?>
            <h2>今日の良い出来事を記録する</h2>
        <?php } ?>

        <form method="POST" action="">

            <input
                type="hidden"
                name="edit_id"
                value="<?php echo h($editId); ?>"
            >

            <div class="form-item">
                <label for="event_date">日付</label>

                <input
                    type="date"
                    id="event_date"
                    name="event_date"
                    value="<?php echo h($formDate); ?>"
                    required
                >
            </div>

            <div class="form-item">
                <label for="category">カテゴリ</label>

                <select
                    id="category"
                    name="category"
                    required
                >
                    <option value="">選択してください</option>

                    <?php
                    $categories = [
                        '良かったこと',
                        '癒されたこと',
                        '嬉しかったこと'
                    ];

                    foreach ($categories as $category) {
                    ?>
                        <option
                            value="<?php echo h($category); ?>"
                            <?php
                            if ($formCategory === $category) {
                                echo 'selected';
                            }
                            ?>
                        >
                            <?php echo h($category); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-item">
                <label for="title">タイトル</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="100"
                    value="<?php echo h($formTitle); ?>"
                    required
                >
            </div>

            <div class="form-item">
                <label for="event_text">何があった？</label>

                <textarea
                    id="event_text"
                    name="event_text"
                    maxlength="1000"
                    data-counter="event-counter"
                    required
                ><?php echo h($formEvent); ?></textarea>

                <div
                    id="event-counter"
                    class="counter"
                ></div>
            </div>

            <div class="form-item">
                <label for="feeling">
                    どんな気持ちになった？
                </label>

                <textarea
                    id="feeling"
                    name="feeling"
                    maxlength="1000"
                    data-counter="feeling-counter"
                    required
                ><?php echo h($formFeeling); ?></textarea>

                <div
                    id="feeling-counter"
                    class="counter"
                ></div>
            </div>

            <div class="form-item">
                <label for="happiness_level">
                    しあわせ度
                </label>

                <select
                    id="happiness_level"
                    name="happiness_level"
                    required
                >
                    <option value="">選択してください</option>

                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <option
                            value="<?php echo $i; ?>"
                            <?php
                            if ((string)$formLevel === (string)$i) {
                                echo 'selected';
                            }
                            ?>
                        >
                            <?php echo $i; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-item">
                <label for="record_password">
                    編集・削除用パスワード
                </label>

                <input
                    type="password"
                    id="record_password"
                    name="record_password"
                    required
                >
            </div>

            <input
                type="submit"
                name="submit_record"
                value="<?php
                    echo $editId !== ''
                        ? '編集内容を保存'
                        : '記録する';
                ?>"
            >

        </form>
    </section>

    <!-- 編集番号指定 -->
    <section class="form-box">
        <h2>記録を編集する</h2>

        <form method="POST" action="" class="small-form">

            <div>
                <label for="edit_select_id">記録番号</label>
                <input
                    type="number"
                    id="edit_select_id"
                    name="edit_select_id"
                    min="1"
                    required
                >
            </div>

            <div>
                <label for="edit_select_password">
                    パスワード
                </label>
                <input
                    type="password"
                    id="edit_select_password"
                    name="edit_select_password"
                    required
                >
            </div>

            <input
                type="submit"
                name="submit_edit_select"
                value="編集"
            >
        </form>
    </section>

    <!-- 削除 -->
    <section class="form-box">
        <h2>記録を削除する</h2>

        <form
            method="POST"
            action=""
            class="small-form delete-form"
        >
            <div>
                <label for="delete_id">記録番号</label>
                <input
                    type="number"
                    id="delete_id"
                    name="delete_id"
                    min="1"
                    required
                >
            </div>

            <div>
                <label for="delete_password">
                    パスワード
                </label>
                <input
                    type="password"
                    id="delete_password"
                    name="delete_password"
                    required
                >
            </div>

            <input
                type="submit"
                name="submit_delete"
                value="削除"
                class="delete-button"
            >
        </form>
    </section>

    <!-- 記録一覧 -->
    <section>
        <h2>これまでの小さなしあわせ</h2>

        <?php if (count($records) === 0) { ?>
            <p>まだ記録はありません。</p>
        <?php } else { ?>

            <div class="records">

                <?php foreach ($records as $record) { ?>

                    <article class="record">

                        <div class="record-header">
                            <span class="category">
                                <?php echo h($record['category']); ?>
                            </span>

                            <h3>
                                <?php echo h($record['title']); ?>
                            </h3>
                        </div>

                        <p class="record-meta">
                            記録番号：
                            <?php echo h($record['id']); ?>

                            ／ 日付：
                            <?php echo h($record['event_date']); ?>

                            ／ しあわせ度：
                            <?php
                            echo str_repeat(
                                '★',
                                (int)$record['happiness_level']
                            );
                            ?>
                        </p>

                        <h4>何があった？</h4>

                        <p class="record-text">
                            <?php
                            echo nl2br(
                                h($record['event_text'])
                            );
                            ?>
                        </p>

                        <h4>どんな気持ちになった？</h4>

                        <p class="record-text">
                            <?php
                            echo nl2br(
                                h($record['feeling'])
                            );
                            ?>
                        </p>

                    </article>

                <?php } ?>

            </div>

        <?php } ?>
    </section>

</div>

</body>
</html>