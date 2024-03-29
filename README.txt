-----------------------------------------------------------------------------
  人狼式 ～汝は人狼なりや？式神エディション Version 2.2.1 README 2013/07/15
-----------------------------------------------------------------------------

ダウンロードありがとうございます。
人狼式 ～汝は人狼なりや？式神エディション (以下本スクリプト) は、
人狼スクリプト「汝は人狼なりや？の PHP+MySQL 移植版(from ふたば)」のコードを基に
改良・新機能を追加した、PHP で記述された人狼スクリプトです。

汝は人狼なりや？の PHP+MySQL 移植版(from ふたば)の開発元はこちらです。
http://f45.aaa.livedoor.jp/~netfilms/

----------------------------------------
  重要：旧バージョンからの更新について
----------------------------------------
◆ Ver. 2.x ～
Ver. 2.x 以降は、設定ファイル、データベースともに Ver 1.x から大幅に変更しています。
スクリプトのみをアップグレードしても互換性が全くありませんのでご注意ください。
・設定ファイル
カテゴリ毎に分割し、config/ 以下にサブディレクトリを切って移動しています。
詳細は個々のファイルを参照してください。

・データベース
DB の種類を MyISAM から InnoDB に変更しています。
また、ユーザアイコン (user_icon) 以外は全く内容に互換性がありません。

◆設定ファイル関連
・Ver. 1.5.0 α4 (パッケージバージョン) / Rev. 291 (SourceForgeリビジョン番号)
デバッグモードの設定変数が include/init.php 中の $DEBUG_MODE から
config/server_config.php 中のクラス ServerConfig 内の $debug_mode に変更されています。

・Ver. 1.4.0 β1 (パッケージバージョン) / Rev. 152 (SourceForgeリビジョン番号)
上記のバージョン以降から設定ファイルのパスが変更されています。
現行の設定ファイルの位置については下記の「設定ファイルについて」を確認してください。

◆データベース関連
以下の旧バージョンに該当するスクリプトを稼動させていた方は、
最新版の admin/setup.php を実行してください。

・Ver. 2.2.1 (パッケージバージョン) / Rev. 880 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、データキャッシュ機能用のテーブルが再生性されます
前のバージョンまでのキャッシュが一度完全にクリアされるので注意してください。

・Ver. 2.2.0 β1 (パッケージバージョン) / Rev. 820 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、データキャッシュ機能用のテーブルが作成されます。
α8 とはスキーマが違うので上手くいかない場合は document_cache を一度削除してください。

・Ver. 2.2.0 α8 (パッケージバージョン) / Rev. 792 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、データキャッシュ機能用のテーブルが作成されます。

・Ver. 2.0.0 α6 (パッケージバージョン) / Rev. 508 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、旧バージョンで使用されていたデータベースの
talk 関連のインデックスが再生成されます。

・Ver. 1.5.0 β8 (パッケージバージョン) / Rev. 368 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、旧バージョンで使用されていたデータベースに
アイコンの非表示設定フラグを追加します。

・Ver. 1.4.0 β5 (パッケージバージョン) / Rev. 171 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、旧バージョンで使用されていたデータベースに
発言の ID を追加します。

・Ver. 1.4.0 β3 (パッケージバージョン) / Rev. 164 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、旧バージョンで使用されていたデータベースに
アイコンの出展、カテゴリ、作者、登録日のフィールドを追加します。

・Ver. 1.4.0 α24 (パッケージバージョン) / Rev. 149 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、旧バージョンで使用されていたデータベースに
村の作成・開始・終了時刻記録用フィールドを追加します。

・Ver. 1.4.0 α19 (パッケージバージョン) / Rev. 123 (SourceForgeリビジョン番号)
上記のバージョン未満の場合は、旧バージョンで使用されていたデータベースに
IP記録用フィールドを追加します。

◆文字コード関連
・Ver. 1.4.0 β16 (パッケージバージョン) / Rev. 202 (SourceForgeリビジョン番号)
上記のバージョン以降、スクリプトの文字コードを EUC から UTF-8 に変更しています。
これに伴い、旧バージョンからアップデートする場合はデータベースの文字コードを
ujis_japanese_ci から utf8_unicode_ci に変更する必要があります。
過去のデータベースの内容も変換する必要がありますが、アップデート時に自動で
変換する機能を搭載する予定はありません。

------------------
  パッケージ内容
------------------
本スクリプトのディレクトリ構成は以下のとおりです。
<top>          : 本体コード
├─admin      : 管理用スクリプトファイル (常時使用する場所ではありません)
├─config     : 設定ファイル
├─css        : 表示用 CSS ファイル
├─dev        : 開発者用ファイル (テスト用なのでアップロード不要です)
├─img        : システム画像ファイル
├─include    : インクルードファイル
├─info       : 説明書などの情報ファイル
├─javascript : JavaScript ファイル
├─log        : HTML化ログ置き場 (変換していない場合は不要です)
├─module     : 外部モジュールファイル
├─swf        : 音声ファイル
└─user_icon  : アイコン画像ファイル

------------
  動作環境
------------
本スクリプトは、PHP5＋MySQL4.0 環境下での動作を想定しています。
MySQL 以外の RDBS 環境を用いる場合、MySQL 用クエリ関数を書き直す必要があります。

注：
PHP4 環境のサポートは Ver. 1.4.x までとなっています。
Ver. 1.5 以降は PHP5 環境が必要です。

------------
  設置方法
------------
1. MySQL サーバ内に専用データベースとアカウントを作成
   文字コード、照合順序は utf8_unicode_ci (注) を使用すること
2. config/server/database_config.php を編集し、データベースを設定
3. config/server/server_config.php を編集し、サーバを設定
4. パッケージをサーバにアップロード (コード無変換、またはバイナリモード)
5. サーバ上の user_icon/、rss/ ディレクトリに書き込み属性を付与
6. admin/ ディレクトリにアクセス制限を設定
7. 初期セットアップ作業 (自動) のために admin/setup.php にアクセス
8. index.php にアクセスし、動作を確認

注：
Ver. 1.4.0 β16 より、文字コードを ujis_japanese_ci から utf8_unicode_ci に変更しています

----------------------------
  管理用スクリプトについて
----------------------------
本スクリプトで管理用のスクリプトは以下の通りです。
全てadmin/ディレクトリ内に存在します。
1. setup.php
   初期設定を行うスクリプト。
   新しくサーバに設置した際に、初めに実行すること。

2. room_delete.php
   稼働中の村を削除するためのスクリプト。
   デバッグモード (後述) が ON の時のみ有効。

3. icon_delete.php
   指定したアイコンを削除するためのテスト用スクリプト。
   デバッグモード (後述) が ON の時のみ有効。
   該当アイコンがすでに使用されていると表示に影響が出るので要注意。

4. generate_html_log.php
   過去ログを HTML 化するためのテスト用スクリプト。
   スクリプト内の変数 $disable が false の時のみ有効。
   負荷が非常に高い上、試作段階なので使用は要注意。

5. role_test.php
   特殊村の配役テストをするためのスクリプト。

room_delete.php については直接呼び出すものではなく、稼働中の村一覧に表示される
削除用リンクから呼び出されるものです。
ただし、admin/ ディレクトリにアクセス制限をかけていない場合、
デバッグモード (後述) が ON の時には直接アドレス入力することで、
任意の村の削除が誰にでも可能となります。
そのため、通常稼動時にはデバッグモードを OFF に設定しておくようにしてください。
デフォルトでは OFF に設定されています。

------------------------
  設定ファイルについて
------------------------
本スクリプトの設定ファイルは以下の通りです。
全て config/ ディレクトリ内に存在します。
1. server/ : 主にサーバ側の設定ファイル置き場
2. game/   : 主にゲーム内の設定ファイル置き場
3. system/ : 主にシステム関連の設定ファイル置き場

主な設定ファイルは以下になります
1-1. server/database_config.php : データベース関連の設定ファイル
1-2. server/server_config.php   : 本スクリプトをサーバ上で動作させるための設定ファイル
1-3. server/room_config.php     : 村作成時の制限設定ファイル
2-1. game/game_config.php       : ゲーム内の設定ファイル
2-2. game/cast_config.php       : 配役設定ファイル
2-3. game/message.php           : ゲーム中で表示されるシステムメッセージの設定ファイル
3-1. system/version.php         : トップページで表示されるバージョン情報の設定ファイル

このうち、必ず設定が必要になるのは database_config, server_config.php のみです。
他のファイルについては必要に応じて変更してください。
詳細は各ファイルのコメントを参照してください。

--------------------------
  デバッグモードについて
--------------------------
ON にすることでいくつかの管理スクリプトや例外チェックがスキップされます。
config/server/server_config.php 中のクラス ServerConfig 内の DEBUG_MODE で設定し、
true にすると ON、false にすると OFF になります。
主に開発用のスイッチなので基本的には OFF に設定してください。
デフォルトでは OFF に設定されています。

----------------
  既知の不具合
----------------
現在、以下の不具合が発生する可能性があります。
これらの不具合は、サーバの状態や負荷の度合いによるものとみられています。

・投票完了したのに突然死する場合がある
　投票完了したにも関わらず、突然死処理が行われる場合が報告されております。
　原因は不明です。対策として、早め早めの投票を呼びかけてください。

・再投票時の投票結果表示が一部しか表示されない
　MySQL サーバのロック機構が何らかの原因で効いていない現象です。
　サーバ依存の問題のようです。頻繁に発生するようであれば、MySQL を更新するか、
　サーバを別のものに変更することを試みてください。

・投票時に大量突然死が発生する場合がある
　投票処理中に突然死処理が何らかの原因で呼び出されたことによる不具合です。
　現在のバージョンでは発生は確認されておりません。

-------------
  管理 TIPS
-------------
・過去ログが DB にたまってくると、サーバの動作が重くなる場合があります。
その場合、過去ログを HTML 形式で保存した上で DB から村データの削除を行ってください。
X 番地村のデータを消す場合は room, system_message, talk*, user_entry, player, vote result_*
各テーブル中の room_no = X に該当するレコードを削除してください。
また、レコード削除後にはデータベースの最適化 (OPTIMIZE) を行う必要があります。

・登録されたアイコンを何らかの理由で削除する場合は、
user_icon テーブル中の該当レコード、user_icon/ ディレクトリ内の該当アイコン画像を
削除してください。
ただし、該当アイコンが過去ログで使用されている場合は、データベースから削除せずに
何らかの別のアイコン画像で置き換える方法をとってください。
また、レコード削除後に icon_no の調整を行ってください。
Ver. 1.5.0 β8 より、アイコン一覧のページから入村時の表示/非表示を設定できます。

・パスワードハッシュ化のための salt 文字列は定期的に変更するようにしてください。
また、村が稼動している間、村終了後も会話しているプレイヤーがいる間は、salt 文字列を
変更しないようにしてください。途中で salt 文字列が変更されるとログインができなくなります。

・Ver. 1.5.0 β13 より、設定ファイルを編集することにより村の作成や自動廃村処理を
止めることができます。これを設定することでサーバの更新や停止作業を行う際の事故を
減らすことができます。

-------
  FAQ
-------
Q. 動いたけど文字化けしてる
A. MySQL サーバの設定、データベースの設定、ブラウザのエンコード、
   どれかが EUC-JP (ujis_japanese_ci) を使用していないない可能性が高いです。
   一度チェックしてみてください。
   サーバによっては EUC-JP が使えない事があります。
   その場合はファイル自体の文字コードをサーバにあわせる必要があります。
   また、Ver. 1.4.0 β16 より、文字コードを ujis_japanese_ci から
   utf8_unicode_ci に変更しています。

Q. 更新したら残り時間の表示が出なくなった
A. CSS や JavaScript の更新があった場合はブラウザのキャッシュが残っていると
   正常に動作しない場合があります。
   特にフレーム内部のページキャッシュが消えにくいようです。
   そういう場合はフレームを指定したリロードや、
   強制リロード (Ctrl + F5 など / ブラウザによって違います) してみてください。

Q. 村を立てようとしたり、アイコンを登録しようとすると「無効なアクセスです」と出る
A. config/server/server_config.php 内の class ServerConfig::SITE_ROOT の設定を確認してください。

Q. 発言欄の文字列が消えない
A. javascript/gaem_up.js に格納されている JavaScript 関数が機能していません。
   該当ファイルや、ブラウザの設定を確認してください。

Q. 画面が真っ白になる
A. PHP の文法エラーの可能性が高いです。設定ファイルを確認してみてください。
また、config/server/server_config.php 内の class ServerConfig::DISPLAY_ERROR を
一時的に true に設定する事で詳細なエラーメッセージが表示されるようになります。
(デバッグ用機能なので普段は false に設定してあります)

-------------------------
  公式配布 URL について
-------------------------
本スクリプトの公式配布 URL は以下です。
http://sourceforge.jp/projects/jinrousiki/

----------------------
  ライセンスについて
----------------------
本スクリプトのライセンスは GNU General Public License v2 (GPLv2) です。

----------------------
  使用素材について
----------------------
植物の背景画像、左上にある文字の入ったタイトル画像は天の欠片さんの素材を使用しています。
この画像をそのまま使う場合は config/system/copyright_config.php の素材情報設定の中にある、
天の欠片さんへのリンクを削除しないようにお願いします。
http://photozou.jp/photo/list/2066445/5445429

この画像の著作権は天の欠片さんの物なので、自分で撮影したとか自分で作ったとか言わないようにしてください。
Version 1.2.0 以降で追加した画像については、 あずきふぉんとさんのフォントを利用させていただいています。
この画像をそのまま使う場合は config/system/copyright_config.php の素材情報設定の中にある、
あずきふぉんとさんへのリンクを削除しないようにお願いします。
http://azukifont.mints.ne.jp/

module フォルダの中は mbstring エミュレータが入っています。
これは mbstring が使えない環境でも文字コード関連の処理をするためです。
著作権は mbstring の作者さんのものです。
config/system/copyright_config.php の素材情報設定の中にあるリンクを削除しないようにお願いします。
http://www.matsubarafamily.com/blog/mbemu.php

--------------------
  サポートについて
--------------------
本スクリプトに関するバグ報告・要望については以下のどこかにお願いいたします。
なお、現在 Sourceforge のページにフォーラムは用意されておりません。
Sourceforge の方に報告される場合には、チケットを通してお願いいたします。

式神研究同好会 (現行スレッド)
http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50

ウミガメ人狼専用掲示板 (上記スレッドが存在しない場合にはこちらから)
http://jbbs.livedoor.jp/netgame/2829/

人狼式 ～汝は人狼なりや？式神エディション (Sourceforgeプロジェクトページ)
http://sourceforge.jp/projects/jinrousiki/

--------------
  クレジット
--------------
◆式神研究同好会メンバー
enogu
埋めチル
お肉
赤いきつね
乗月
Kei
atari
火月

◆「異議あり」音源作成者
炎の紋章
シトウ

◆スキン用アイコン作成者
布

◆背景画像作成者
みこ
九鳥
雨栗
八木
春石
猩猩
きりしゅや/ここだけ流行性感冒
kinari
水無月飛燕

◆Special Thanks
多数のテストプレイヤー、協力していただいた方々

------------
  変更履歴
------------
Version 2.2.1:
・データキャッシュ機能：リセット判定を再設計
・憑依+恋人後追い処理の仕様変更

Version 2.2.0:
・PDO 導入
・基幹ライブラリ再構成
・役職システムメッセージ画像のフォントサイズ変更
・データキャッシュ機能実装
・役職を追加
・ゲームオプションを追加

Version 2.1.0:
・システムクラスを再設計
・村オプション変更機能 (GM 専用) 実装
・アイコン、ユーザ名表示オプション実装
・役職を追加
・ゲームオプションを追加

Version 2.0.0:
・データベース構造を再設計
・設定ファイルを再構成
・ユーザ登録情報変更機能を実装

Version 1.5.0:
・天候システム実装
・デスノート実装
・裏・闇鍋モード実装
・役職を大量に追加
・ゲームオプションを大量に追加

Version 1.4.1:
・PHPの浮動小数点数に関するバグに対応

Version 1.4.0:
・多人数対応 (クッキーの処理の改善)
・リアルタイム表示 JavaScript の自動補正処理 (時計合わせが不要になります)
・ユーザアイコン表示/登録の機能強化
・ユーザ入村画面の改訂
・再入村リンクの表示機能実装 (過去ログ)
・埋毒者を吊った / 噛んだ際の巻き込まれる対象を限定できる
・闇鍋モードの実装
・役職を大量に追加
・ゲームオプションを大量に追加
・複数キューピッドに対応

Version 1.3.0:
・複数の同一役職を村に登場させることが可能
　（デフォルト設定では登場しない。編成を管理者が変更した場合のみ登場）
・狐の念話・仲間表示機能を追加
・埋毒者を噛んで巻き込まれる狼の対象決定方法を任意で変更できる機能を追加
　（狼からランダム or 噛んだ狼固定）
・過去ログを逆順で表示できる機能実装
　(デフォルトは設定ファイルで変更可能)
・投票画面のデザイン変更
　（常に参加者全員が表示され、投票できる者だけラジオボタンを付ける方式に変更）
・遺言は昼/夜の切り替えの影響を受けずに保存できるよう変更
・身代わり君の昼／夜の発言をシステムメッセージとして表示できる機能を追加
　（管理者向け機能）
・発言に「」をつけるオプションを追加
　（設定ファイルでON/OFF可能）
・狼・共有者の仲間表示を改善
・引き分け判定処理の改善
・突然死処理の改善
・突然死などの制限時間表示のバグ修正
・Kick 処理が全ての村に影響するバグ修正
・配役テーブルの仕様変更
・トリップに仮対応 (# が含まれていたらエラーを返すだけ)
・ソースコードファイルの整理・分割・最適化
・その他細かい変更点・バグ修正

Version 1.2.2:
・HTML出力にCSSを使用するように変更
・アイコンサイズの最大値をサーバ側で制限する機能を追加
・ソースコードの最適化
・ディレクトリの整理

Version 1.2.1:
・身代わりが狼、狐、キューピッドになる可能性がある不具合を修正
・愛の矢のシステムメッセージで村人名ではなくユーザ名が表示される不具合を修正
・霊界役職非表示時にもユーザ名が霊話に表示される不具合を修正
・稼働中の村の霊界役職非表示アイコンが表示されない不具合を修正

Version 1.2.0:
・新役職追加（キューピッド・恋人）
・希望役職に埋毒者を追加
・霊界役職非表示オプションを追加
・過去ログに村の参加人数を表示するように修正
・4-7人村の配役追加、最低ゲーム開始可能人数を4人に設定
・その他細かい修正

--- ここから「汝は人狼なりや？のPHP+MySQL移植版(from ふたば) 」の変更履歴 ---
version1.1.5 Kickの処理でKickされて空いたユーザNoを切り詰める処理がうまくいってなかったのを修正
version1.1.4 setting.php以外のほぼ全て修正。Kickされた人の発言が消えるのを修正。遺言の表示順が固定されていたのをランダムに修正。
version1.1.3 game_play.php,game_functions.php,game_view.phpで現在の生存者の数を表示するように、また多少の表示修正
version1.1.2 old_log.phpで妖狐の勝利アイコンが表示されないのを修正
version1.1.1 game_view.phpで遺言が出力されないのを修正
version1.1.0 game_functions.phpのLastWordOutput()で global $day_nightが抜けていたのを修正
version1.0.0 配布開始
