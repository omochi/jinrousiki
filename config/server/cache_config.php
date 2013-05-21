<?php
//-- キャッシュ設定 --//
class CacheConfig {
  /* 全体設定 */
  const ENABLE = false; //有効設定 (true:有効にする / false:しない)
  const EXCEED = 172800; //キャッシュデータ保持時間 (秒)

  /* 観戦発言 */
  const ENABLE_TALK_VIEW = false; //有効設定
  const TALK_VIEW_EXPIRE = 60; //データ更新間隔 (秒)

  /* 過去ログ */
  const ENABLE_OLD_LOG = false; //有効設定
  const OLD_LOG_EXPIRE = 60; //データ更新間隔 (秒)

  /* 過去ログ一覧 */
  const ENABLE_OLD_LOG_LIST = false; //有効設定
  const OLD_LOG_LIST_EXPIRE = 60; //データ更新間隔 (秒)

  /* デバッグ用設定 */
  const DEBUG_MODE = false; //デバッグ用メッセージ表示
}
