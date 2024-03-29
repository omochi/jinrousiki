<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('info_functions');
InfoHTML::OutputHeader('開発履歴', 1, 'develop_history');
?>
<p>
Ver. 2.2.x<br>
<a href="#ver221">1</a>
</p>
<p>
Ver. 2.2.0<br>
<a href="#ver220rc1">RC1</a>
<a href="#ver220">Release</a>
</p>
<p>
<a href="#ver220a1">α1</a>
<a href="#ver220a2">α2</a>
<a href="#ver220a3">α3</a>
<a href="#ver220a4">α4</a>
<a href="#ver220a5">α5</a>
<a href="#ver220a6">α6</a>
<a href="#ver220a7">α7</a>
<a href="#ver220a8">α8</a>
<a href="#ver220b1">β1</a>
<a href="#ver220b2">β2</a>
</p>
<p>
<a href="history_1.3.php">～ 1.3</a>
<a href="history_1.4.php">1.4</a>
<a href="history_1.5.php">1.5</a>
<a href="history_2.0.php">2.0</a>
<a href="history_2.1.php">2.1</a>
</p>

<h2 id="ver221">Ver. 2.2.1 (Rev. 880) : 2013/07/15 (Mon) 03:00</h2>
<ul>
<li>データキャッシュ機能：リセット判定を再設計</li>
<li>憑依+恋人後追い処理の仕様変更</li>
</ul>

<h2 id="ver220">Ver. 2.2.0 (Rev. 861) : 2013/06/30 (Sun) 01:00</h2>
<ul>
<li>Twitter API 1.1 に対応</li>
</ul>

<h2 id="ver220rc1">Ver. 2.2.0 RC1 (Rev. 848) : 2013/06/23 (Sun) 17:25</h2>
<ul>
<li>データキャッシュ機能：ゲーム内：アイコンの表示設定に対応</li>
<li>データキャッシュ機能：リセット処理とデザインを調整</li>
<li>初期化処理の最適化</li>
</ul>

<h2 id="ver220b2">Ver. 2.2.0 β2 (Rev. 838) : 2013/06/09 (Sun) 22:06</h2>
<ul>
<li>データキャッシュ機能：観戦・ゲーム内(ゲーム開始前後)・霊界に対応</li>
<li>特徴と仕様：カテゴリ別に再構成</li>
<li>ゲームのルール：生存カウントの記載を追加</li>
</ul>

<h2 id="ver220b1">Ver. 2.2.0 β1 (Rev. 820) : 2013/05/27 (Mon) 00:37</h2>
<ul>
<li>データキャッシュ機能：過去ログ一覧・過去ログに対応</li>
<li>背景画像変更：TOPページ更新履歴</li>
<li>投票済み情報：未投票人数に変更</li>
</ul>

<h2 id="ver220a8">Ver. 2.2.0 α8 (Rev. 792) : 2013/05/07 (Tue) 01:19</h2>
<ul>
<li>データキャッシュ機能仮実装 (観戦モード)</li>
<li>入村制限：トリップ用ホワイトリスト実装</li>
<li>過去ログ：狼視点モード実装</li>
<li>固定配役追加モード：R(妖精村)追加</li>
<li>「益荒男」実装</li>
<li>投票済み情報のデザイン変更</li>
</ul>

<h2 id="ver220a7">Ver. 2.2.0 α7 (Rev. 769) : 2013/04/14 (Sun) 19:15</h2>
<ul>
<li>「蛇姫」の仕様変更</li>
<li>「紅天女」「鬼車鳥」実装</li>
<li>サブ役職「元紅天女」「吸毒者」実装</li>
<li>背景画像変更：特徴と仕様・ゲームオプション</li>
</ul>

<h2 id="ver220a6">Ver. 2.2.0 α6 (Rev. 759) : 2013/03/04 (Mon) 00:47</h2>
<ul>
<li>固定配役追加モード：Q(雛村)追加</li>
<li>「無鉄砲者」の仕様変更</li>
<li>「海御前」「煙々羅」実装</li>
<li>背景画像変更：詳細な仕様</li>
</ul>

<h2 id="ver220a5">Ver. 2.2.0 α5 (Rev. 752) : 2013/02/12 (Tue) 00:13</h2>
<ul>
<li>「牛頭鬼」「馬頭鬼」実装</li>
<li>オプション詳細情報のリンクを追加 (プレイ中限定)</li>
<li>背景画像変更：謝辞・素材</li>
</ul>

<h2 id="ver220a4">Ver. 2.2.0 α4 (Rev. 737) : 2013/01/27 (Sun) 22:01</h2>
<ul>
<li>「恋色迷彩村」オプション実装</li>
<li>「審神者」「山立」仕様変更</li>
<li>「文武王」実装</li>
<li>「恋色迷彩」の変換テーブルを管理者設定に変更</li>
<li>天候「霜柱」「地吹雪」「月虹」「春一番」「桜吹雪」実装</li>
<li>クイズ村：GM の表示情報を追加</li>
</ul>

<h2 id="ver220a3">Ver. 2.2.0 α3 (Rev. 713) : 2013/01/14 (Mon) 02:07</h2>
<ul>
<li>「足音村」オプション実装</li>
<li>固定配役追加モード：P(音鳴村)追加</li>
<li>「審神者」「山立」「響狼」「家鳴」「響狐」実装</li>
<li>役職システムメッセージ画像のフォントサイズを変更</li>
</ul>

<h2 id="ver220a2">Ver. 2.2.0 α2 (Rev. 699) : 2013/01/03 (Thu) 03:16</h2>
<ul>
<li>ログイン画面にトリップ入力欄を追加</li>
<li>基幹ライブラリの再構成</li>
</ul>

<h2 id="ver220a1">Ver. 2.2.0 α1 (Rev. 692) : 2012/12/24 (Mon) 03:01</h2>
<ul>
<li>PDO の導入</li>
</ul>
</body>
</html>
