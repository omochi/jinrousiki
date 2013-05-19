<?php
//-- キャッシュ設定 --//
class CacheConfig {
  /* 全体設定 */
  const ENABLE = false; //有効設定 (true:有効にする / false:しない)
  const EXCEED = 172800; //キャッシュデータ保持時間 (秒)

  /* 観戦発言キャッシュ */
  const ENABLE_TALK_VIEW = false; //観戦発言キャッシュ設定
  const TALK_VIEW_EXPIRE = 60; //発言キャッシュのデータ更新間隔 (秒)

  /* デバッグ用設定 */
  const DEBUG_MODE = false; //デバッグ用メッセージ表示
}
