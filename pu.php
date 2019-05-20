<?php
require_once "./Item.php" ;
require_once "./Feed.php" ;
require_once "./RSS2.php" ;
include './InvalidOperationException.php';
date_default_timezone_set("Asia/Tokyo");
use FeedWriter\RSS2;    // エイリアスの作成

require_once "vendor/autoload.php";

$baseUrl = 'https://www.w3.org/TR/?tag=css&status=rec';
$startPage = 1;
$maxPage = 3;

$posts = [];

// - ①
$dom = \phpQuery::newDocumentFile($baseUrl);

// - ②
foreach ($dom['li[data-status="rec"]'] as $row) {
    // - ③
    $title = pq($row)->find('h2')->text();
    $url = pq($row)->find('a')->attr('href');
    $status = pq($row)->find('.profile')->text();
    $datetext = pq($row)->find('.pubdetails')->text();
    $datebase = substr($datetext, 0, 10);
    $date = date("Y-n-j", strtotime($datebase));

    $posts[] = [
        'title' => $title,
        'url' => $url,
        'status' => $status,
        'date' => $date
    ];
}

var_dump($posts);


$feed = new RSS2;




// チャンネル情報の登録
$feed->setTitle( "" ) ;    // チャンネル名
$feed->setLink( "https://www.w3.org/TR/?tag=css&status=rec" ) ;    // URLアドレス
$feed->setDescription( "hoge" );    // チャンネル紹介テキスト
$feed->setDate(strtotime("2019-05-15 18:30")); // 更新日時
$feed->setChannelElement( "language" , "ja-JP" ) ;    // 言語
$feed->setChannelElement( "pubDate" , date( \DATE_RSS , time() ) ) ;    // フィードの変更時刻

foreach($posts as $key => $val){
    // インスタンスの作成
    $item = $feed->createNewItem() ;

    // アイテムの情報
    $item->setTitle( $val['title'] ) ;    // タイトル
    $item->setLink( $val['url'] ) ;    // リンク
    //$item->setDate( $val['date'] ) ;    // 更新日時
    $item->setDate( $val['date'] );

    // アイテムの追加
    $feed->addItem( $item ) ;
}

// コードの生成
$xml = $feed->generateFeed() ;

// ファイルの保存場所を設定
$file = "feed/pu.xml" ;

// ファイルの保存を実行
file_put_contents( $file , $xml ) ;
 ?>
