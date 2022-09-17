<?php
  // 設定
  $api_key   = "";
  $engine_id = "";
  $url       = "https://www.googleapis.com/customsearch/v1?";
  // 開始位置
  $start     = 1;
  
  // 入力されたキーワード
  if (isset($_GET['keyword'])) {
    $query = htmlspecialchars($_GET['keyword'], ENT_QUOTES, 'UTF-8');
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Media List</title>
</head>
<body>
  <form action="/" method="get">
    <input type="text" name="keyword" id="" value="" placeholder="キーワード">
    <button type="submit" >検索</button>
  </form>
  <?php if (empty($query)) : ?>
    <p>検索キーワードを入力してください</p>
  <?php else : ?>
    <ol>
      <?php for($i = 1; $i <= 3; $i++) : ?> 
        <?php
          $param_arr = array(
            'key'          => $api_key,
            'cx'           => $engine_id,
            'q'            => $query,
            'alt'          => 'json',
            'start'        => $start,
            'excludeTerms' => 'pdf uploads'
          );
          $param = http_build_query($param_arr);
          $request_url = $url . $param;
          $contents_json = @file_get_contents($request_url, true);
          $contents_arr = json_decode($contents_json, true);
        ?>
        <?php if (empty($contents_arr['items'])) : ?>
          <p>検索結果は 0 件です。</p>
        <?php break; ?>       
        <?php else : ?>
        <?php foreach ($contents_arr['items'] as $index => $value) : ?>
          <?php $getUrl = @file_get_contents($value['link'], true); ?>
          <?php if (preg_match('/"Article"|"NewsArticle"/',$getUrl)) : ?>
            <li>
              <a href="<?= $value['link'];?>" target="_blank"><?= $value['title']; ?></a>
            </li>
          <?php else: ?>
          <?php endif; ?>
          <?php $start++; ?>
        <?php endforeach; ?>
        <?php 
          if (isset($contents_arr['queries']['nextPage'][0]['startIndex'])) {  
            $start = $contents_arr['queries']['nextPage'][0]['startIndex'];
          } else {
            break;
          }
        ?>
        <?php endif; ?> 
      <?php endfor; ?>  
    </ol>
  <?php endif; ?>
</body>
</html>