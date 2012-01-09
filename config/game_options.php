<?php
/*
 * このファイルでは村で使用可能なオプションを指定することが出来ます。
 * オプションを使用不可能にする場合は、対象のオプションをEnableからDisableに変更して下さい。
 */
global $INIT_CONF;
$INIT_CONF->LoadClass('ROOM_CONF', 'TIME_CONF');
global $ROOM_CONF, $TIME_CONF;

RoomOptions::Category('BUILD');
RoomOptions::Enable(RoomOptionItem::Text('room_name', '村の名前', '')
        ->Value('')
        ->Footer('村')
        );
RoomOptions::Enable(RoomOptionItem::Text('room_comment', '村についての説明', '')
        ->Value('')
        ->Footer('')
        );
RoomOptions::Enable(RoomOptionItem::Selector('max_user', '最大人数', '最大人数', '配役は<a href="info/rule.php">ルール</a>を確認して下さい')
        ->Items($ROOM_CONF->max_user_list)
        ->Value($ROOM_CONF->default_max_user)
        );

RoomOptions::Category('GENERAL');
RoomOptions::Enable(RoomOptionItem::Check('wish_role', '役割希望制', '希望の役割を指定できますが、なれるかは運です')
        );
RoomOptions::Enable(RoomOptionItem::RealTime('real_time', 'リアルタイム制', '制限時間が実時間で消費されます')
        ->ON()
        ->Day($TIME_CONF->default_day)
        ->Night($TIME_CONF->default_night)
        );
RoomOptions::Enable(RoomOptionItem::Check('wait_morning', '早朝待機制', '夜が明けてから一定時間の間発言ができません')
        );
RoomOptions::Enable(RoomOptionItem::Check('open_vote', '投票した票数を公表する', '「権力者」などのサブ役職が分かりやすくなります')
        ->ON()
        );
RoomOptions::Enable(RoomOptionItem::Check('seal_message', '天啓封印', '一部の個人通知メッセージが表示されなくなります')
        );
RoomOptions::Enable(RoomOptionItem::Check('open_day', 'オープニングあり', 'ゲームが1日目「昼」からスタートします')
        );

RoomOptions::Category('DUMMY');
RoomOptions::Enable(RoomOptionItem::Group('dummy_boy', '初日の夜は身代わり君')
        ->Item(RoomOptionItem::Radio('dummy_boy', '', '身代わり君なし')->Value(''))
        ->Item(RoomOptionItem::Radio('dummy_boy', '', '身代わり君あり (初日の夜、身代わり君が狼に食べられます)')->Value('on')->ON())
        ->Item(RoomOptionItem::Radio('dummy_boy', '', '仮想 GM が身代わり君としてログインします')->Value('gm_login'))
        );
RoomOptions::Enable(RoomOptionItem::Password('gm_password', 'GM ログインパスワード', '(仮想 GM モード・クイズ村モード時の GM のパスワードです)<br>※ ログインユーザ名は「dummy_boy」です。GM は入村直後に必ず名乗ってください。')
        ->Value('')
        );
RoomOptions::Enable(RoomOptionItem::Check('gerd', 'ゲルト君モード', '役職が村人固定になります [村人が出現している場合のみ有効]')
        );
RoomOptions::Enable(RoomOptionItem::Group('not_open_cast', '霊界で配役を公開しない')
        ->Item(RoomOptionItem::Radio('not_open_cast', '', '常時公開 (蘇生能力は無効です)')->Value(''))
        ->Item(RoomOptionItem::Radio('not_open_cast', '', '常時非公開 (誰がどの役職なのか公開されません。蘇生能力は有効です)')->Value('not'))
        ->Item(RoomOptionItem::Radio('not_open_cast', '', '自動公開 (蘇生能力者などが能力を持っている間だけ霊界が非公開になります)')->Value('auto')->ON())
        );

RoomOptions::Category('CASTING');
RoomOptions::Enable(RoomOptionItem::Check('poison', '埋毒者登場', '処刑されたり狼に食べられた場合、道連れにします [村人2→埋毒1・人狼1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('assassin', '暗殺者登場', '夜に村人一人を暗殺することができます [村人2→暗殺者1・人狼1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('wolf', '人狼追加', '人狼をもう一人追加します [村人1→人狼1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('boss_wolf', '白狼登場', '占い結果が「村人」・霊能結果が「白狼」と表示される狼です [人狼1→白狼1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('poison_wolf', '毒狼登場', '処刑時にランダムで村人一人を巻き添えにする狼です<br>　　　[人狼1→毒狼1 / 村人1→薬師1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('possessed_wolf', '憑狼登場', '襲撃した人に憑依して乗っ取ってしまう狼です [人狼1→憑狼1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('sirius_wolf', '天狼登場', '仲間が減ると特殊能力が発現する狼です [人狼1→天狼1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('fox', '妖狐追加', '妖狐をもう一人追加します [村人1→妖狐1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('child_fox', '子狐登場', '限定的な占い能力を持ち、占い結果が「村人」・霊能結果が「子狐」となる妖狐です <br>　　　[妖狐1→子狐1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('cupid', 'キューピッド登場', '初日夜に選んだ相手を恋人にします。恋人となった二人は勝利条件が変化します<br>　　　[村人1→キューピッド1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('medium', '巫女登場', '突然死した人の所属陣営が分かります [村人2→巫女1・女神1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('mania', '神話マニア登場', '初日夜に他の村人の役職をコピーします [村人1→神話マニア1]')
        );
RoomOptions::Enable(RoomOptionItem::Check('decide', '決定者登場', '投票が同数の時、決定者の投票先が優先されます [兼任]')
        ->ON()
        );
RoomOptions::Enable(RoomOptionItem::Check('authority', '権力者登場', '投票の票数が二票になります [兼任]')
        ->ON()
        );

RoomOptions::Category('SPECIAL');
RoomOptions::Enable(RoomOptionItem::Check('liar', '狼少年村', 'ランダムで「狼少年」がつきます')
        );
RoomOptions::Enable(RoomOptionItem::Check('gentleman', '紳士・淑女村', '全員に性別に応じた「紳士」「淑女」がつきます')
        );
RoomOptions::Enable(RoomOptionItem::Check('sudden_death', '虚弱体質村', '全員に投票でショック死するサブ役職のどれかがつきます')
        );
RoomOptions::Enable(RoomOptionItem::Check('perverseness', '天邪鬼村', '全員に「天邪鬼」がつきます。一部のサブ役職系オプションが強制オフになります')
        );
RoomOptions::Enable(RoomOptionItem::Check('deep_sleep', '静寂村', '全員に「爆睡者」がつきます')
        );
RoomOptions::Enable(RoomOptionItem::Check('mind_open', '白夜村', '全員に「公開者」がつきます')
        );
RoomOptions::Enable(RoomOptionItem::Check('blinder', '宵闇村', '全員に「目隠し」がつきます')
        );
RoomOptions::Enable(RoomOptionItem::Check('critical', '急所村', '全員に「会心」「痛恨」がつきます。')
        );
RoomOptions::Enable(RoomOptionItem::Check('joker', 'ババ抜き村', '誰か一人に「ジョーカー」がつきます')
        );
RoomOptions::Enable(RoomOptionItem::Check('death_note', 'デスノート村', '毎日、誰か一人に「デスノート」が与えられます')
        );
RoomOptions::Enable(RoomOptionItem::Check('detective', '探偵村', '「探偵」が登場し、初日の夜に全員に公表されます')
        );
RoomOptions::Enable(RoomOptionItem::Check('weather', '天候あり', '「天候」と呼ばれる特殊イベントが発生します')
        );
RoomOptions::Enable(RoomOptionItem::Check('festival', 'お祭り村', '管理人がカスタムする特殊設定です')
        );
RoomOptions::Enable(RoomOptionItem::Selector('replace_human', 'モード名', '村人置換村', '「村人」が全員特定の役職に入れ替わります')
        ->Value('')
        ->Item('', 'なし')
        ->Item('replace_human', '村人置換村')
        ->Item('full_mad', '狂人村')
        ->Item('full_cupid', 'キューピッド村')
        ->Item('full_quiz', '出題者村')
        ->Item('full_vampire', '吸血鬼村')
        ->Item('full_chiropetra', '蝙蝠村')
        ->Item('full_mania', '神話マニア村')
        ->Item('full_unknown_mania', '鵺村')
        );
RoomOptions::Enable(RoomOptionItem::Selector('change_common', 'モード名', '共有者置換村', '「共有者」が全員特定の役職に入れ替わります')
        ->Value('')
        ->Item('', 'なし')
        ->Item('change_common', '共有者置換村')
        ->Item('change_hermit_common', '隠者村')
        );
RoomOptions::Enable(RoomOptionItem::Selector('change_mad', 'モード名', '狂人置換村', '「狂人」が全員特定の役職に入れ替わります')
        ->Value('')
        ->Item('', 'なし')
        ->Item('change_mad', '狂人置換村')
        ->Item('change_fanatic_mad', '狂信者村')
        ->Item('change_whisper_mad', '囁き狂人村')
        ->Item('change_immolate_mad', '殉教者村')
        );
RoomOptions::Enable(RoomOptionItem::Selector('change_cupid', 'モード名', 'キューピッド置換村', '「キューピッド」が全員特定の役職に入れ替わります')
        ->Value('')
        ->Item('', 'なし')
        ->Item('change_cupid', 'キューピッド置換村')
        ->Item('change_mind_cupid', '女神村')
        ->Item('change_triangle_cupid', '小悪魔村')
        ->Item('change_angel', '天使村')
        );

RoomOptions::Category('CHAOS');
RoomOptions::Enable(RoomOptionItem::Selector('special_role', 'モード名', '特殊配役モード', '詳細は<a href="info/game_option.php">ゲームオプション</a>を参照してください')
        ->Value('')
        ->Item('', 'なし')
        ->Item('chaos', '闇鍋モード')
        ->Item('chaosfull', '真・闇鍋モード')
        ->Item('chaos_hyper', '超・闇鍋モード')
        ->Item('chaos_verso', '裏・闇鍋モード')
        ->Item('duel', '決闘村')
        ->Item('gray_random', 'グレラン村')
        ->Item('quiz', 'クイズ村')
        );

RoomOptions::Category('CHAOS_CASTING');
RoomOptions::Enable(RoomOptionItem::Selector('topping', 'モード名', '固定配役追加モード', '固定配役に追加する役職セットです')
        ->Value('')
        ->Item('', 'なし')
        ->Item('a', 'A：人形村')
        ->Item('b', 'B：出題村')
        ->Item('c', 'C：吸血村')
        ->Item('d', 'D：蘇生村')
        ->Item('e', 'E：憑依村')
        ->Item('f', 'F：鬼村')
        ->Item('g', 'G：嘘吐村')
        ->Item('h', 'H：村人村')
        ->Item('i', 'I：恋人村')
        ->Item('j', 'J：宿敵村')
        ->Item('k', 'K：覚醒村')
        ->Item('l', 'L：白銀村')
        );
RoomOptions::Enable(RoomOptionItem::Selector('boost_rate', 'モード名', '出現率変動モード', '役職の出現率に補正がかかります')
        ->Value('')
        ->Item('', 'なし')
        ->Item('a', 'A：新顔村')
        ->Item('b', 'B：平等村')
        ->Item('c', 'C：派生村')
        ->Item('d', 'D：封蘇村')
        ->Item('e', 'E：封憑村')
        ->Item('f', 'F：合戦村')
        );

RoomOptions::Enable(RoomOptionItem::Group('chaos_open_cast', '配役を通知する')
        ->Item(RoomOptionItem::Radio('chaos_open_cast', '', '通知無し')->Value('')->ON())
        ->Item(RoomOptionItem::Radio('chaos_open_cast', '', '陣営通知 (陣営毎の合計を通知)')->Value('camp'))
        ->Item(RoomOptionItem::Radio('chaos_open_cast', '', '役職通知 (役職の種類別に合計を通知)')->Value('role'))
        ->Item(RoomOptionItem::Radio('chaos_open_cast', '', '完全通知 (通常村相当)')->Value('full'))
        );
RoomOptions::Enable(RoomOptionItem::Group('sub_role_limit', 'サブ役職制限')
        ->Item(RoomOptionItem::Radio('sub_role_limit', '', 'サブ役職をつけない')->Value('no_sub_role')->ON())
        ->Item(RoomOptionItem::Radio('sub_role_limit', '', 'サブ役職制限：EASYモード')->Value('sub_role_limit_easy'))
        ->Item(RoomOptionItem::Radio('sub_role_limit', '', 'サブ役職制限：NORMALモード')->Value('sub_role_limit_normal'))
        ->Item(RoomOptionItem::Radio('sub_role_limit', '', 'サブ役職制限：HARDモード')->Value('sub_role_limit_hard'))
        ->Item(RoomOptionItem::Radio('sub_role_limit', '', 'サブ役職制限なし'))
        );
RoomOptions::Enable(RoomOptionItem::Check('secret_sub_role', 'サブ役職を表示しない', 'サブ役職が分からなくなります：闇鍋モード専用オプション')
        );

RoomOptions::End();
