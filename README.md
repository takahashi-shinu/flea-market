# アプリケーション名
coachtech フリマ(flea-market app)

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:takahashi-shinu/flea-market.git`
2. cd flea-market
3. DockerDesktopアプリを立ち上げる
 mailhog の追加（メール確認用）
> *docker-compose.ymlファイルに下記の項目を追記してください。*
``` text
mailhog:
        image: mailhog/mailhog
        ports:
            - "8025:8025"
            - "1025:1025"
```

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*
``` text
mysql:
    platform: linux/x86_64
    image: mysql:8.0.26
    environment:
```
> *設定後、DockerDesktopアプリを立ち上げてください。*
```bash
docker-compose up -d --build
```

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 環境変数設定
「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
```bash
cp .env.example .env
```
.env を以下のように環境変数を設定する
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="flea-market"
```
4. アプリケーションキーの作成
``` bash
php artisan key:generate
```
5. fortifyの導入
``` bash
composer require laravel/fortify
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
```
> *メール認証有効化するため、config/fortify.php の features 配列変更*
```text
use Laravel\Fortify\Features;

'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
    ],
```
6. Stripeの導入
``` bash
composer require stripe/stripe-php
```
> *config/services.php の設定(追記)*
```text
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
],
```
> *.envファイルにStripeから取得したテスト用APIキーを追記*
```text
STRIPE_KEY=pk_test_xxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxx
```
 - Stripe Webhook 設定について(重要！)

 Stripe Webhookは外部サービス（Stripe）から直接POSTされるため、LaravelのCSRF保護を一部無効化する必要がある。
そのため、本アプリでは以下の設定を行っている。

  - CSRF 例外設定
`app/Http/Middleware/VerifyCsrfToken.php`

```php
protected $except = [
    'stripe/webhook',
];
```
Webhookの安全性については、CSRFの代わりにStripeが提供するStripe-Signatureを用いた署名検証により担保している。

7. マイグレーションの実行
``` bash
php artisan migrate
```
8. シーディングの実行
``` bash
php artisan db:seed
```
9. シンボリックリンク作成
``` bash
php artisan storage:link
```

## 使用技術(実行環境)
- PHP：8.1.33
- Laravel：8.83.27
- MySQL：8.0.26
- Docker/Docker Compose
- Mailhog
- Stripe API(stripe-php)
- PHPUnit

## URL
- 開発環境：http://localhost/
- ログイン：http://localhost/login
- phpMyAdmin：http://localhost:8080/
- Mailhog：http://localhost:8025/

 ## ER図
 本アプリは、ユーザー・商品・購入を中心としたフリマアプリ構成となっている
![alt text](src/ER_image.png)

## テーブル設計
### usersテーブル
ユーザー情報を管理するテーブル
購入者・出品者の両方を兼ねており、住所情報は購入時のデフォルト配送先として利用される
### itemsテーブル
出品された商品を管理するテーブル
user_id により出品者を紐づけ、status カラムで「SELLING / SOLD」を管理している
### purchasesテーブル
商品購入情報を管理するテーブル
購入時点の配送先住所を保持するため、users テーブルとは別に住所情報を保存している
Stripe 決済用に、stripe_session_id を保持している
決済離脱対策として、expires_at を保持している
### favoritesテーブル
ユーザーのお気に入り（いいね）情報を管理する中間テーブル
user_id と item_id の組み合わせで、お気に入り状態を管理している
### commentsテーブル
商品に対するコメントを管理するテーブル
購入前の質問や出品者とのコミュニケーションを目的としている
### categories / category_itemテーブル
商品カテゴリを管理するテーブル
1つの商品に複数カテゴリを紐づけられるよう、多対多の関係を中間テーブルで管理している

## 決済・購入フロー設計（重要）
本アプリでは「購入（注文）」と「支払い（決済）」を分離して設計している。
purchasesは「注文（Order）」の役割を持ち、Stripeは「決済（Payment）」のみを担当する責務分離設計としている。
### フロー
1. 購入画面で「購入する」を押す
2. purchases に status = pending（購入予約） を作成
3. Stripe Checkout に遷移
4. 決済完了
5. Stripe Webhook により
 - purchases.status：pending → paid
 - items.status：selling → sold

### 決済離脱対策
- Stripe 画面で戻るボタン等により決済未完了の場合
→ expires_at 経過後に pending → expired
- expired 状態の商品は再購入可能

> *Stripe Checkout の expires_at は 30分以上必須のため、DB 側も 30 分で統一しています。*

## アプリケーションシナリオ
### 購入者
1. ユーザー登録・メール認証
2. プロフィールで名前・住所を登録
3. 商品検索・詳細確認・いいね・コメント
4. 支払い方法選択（カード / コンビニ）
5. 配送先変更（プロフィール住所とは別の住所も可）
6. 決済完了後、購入確定

### 出品者
1. ユーザー登録・メール認証
2. プロフィール登録後に商品を出品
3. コメント機能で購入者とやり取り
4. 商品購入後は自動で SOLD 表示
5. 二重購入防止によりトラブルを防止

## サンプルアカウント（ログイン用）
本アプリには、あらかじめメール認証済みのログイン用ユーザーが５名登録され、開発時や動作確認にご利用ください
- ログインURL：http://localhost/login
- サンプルユーザー情報
 - User1 (商品１〜５を出品)
  - Email：sample1@example.com
  - Password：abc12345
 - User2 (商品６〜１０を出品)
  - Email：sample2@example.com
  - Password：abc12345
 - User3 (テスト商品１〜３を出品)
  - Email：sample3@example.com
  - Password：abc12345
 - User4 (テスト商品４〜６を出品)
  - Email：sample4@example.com
  - Password：abc12345
 - User5 (出品なし)
  - Email：sample5@example.com
  - Password：abc12345

## Stripe Webhook(手動テスト)について
-  ローカル環境でStripe Webhookを受信するため、事前にターミナルに下記のコマンドを実行し、Webhookリスナーを起動する(超重要！)
```bash
stripe listen --forward-to http://localhost/stripe/webhook
```
> *nginx経由で/stripe/webhookがLaravelにルーティングされている前提とする*

その後、上記のアプリケーションシナリオの流れに沿ってテストを行う
- カード決済の場合：決済完了後、即時に状態が更新される
(Stripeに入力する情報一例：メールアドレス(sample1@example.com)・カード情報（テスト用カードNo.4242 4242 4242 4242)・月/年 (10/29)・CVC (123）・カード名義(USER))
- コンビニ払いの場合：決済完了イベントが非同期であるため、状態更新までに多少の遅延が発生する（目安として２〜３分程度）
(Stripeに入力する情報一例：メールアドレス(sample1@example.com)・名前（ユーザー）・電話番号（09012345678）)

決済完了後、以下を確認する
- purchases.status が pending → paid に更新されていること
-  items.status が selling → sold に更新されていること

## PHPUnitテストについて
本アプリでは、Laravel標準のPHPUnitを用いて、Feature Testを中心としたテスト設計を行った

- テスト環境設定
``` bash
cp .env .env.testing
```
- .env.testingの設定
```text
APP_ENV=testing
APP_KEY=base64:（← .env と同じでOK）

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```
- phpUnit.xmlの設定
```text
<php>
    <server name="APP_ENV" value="testing"/>
    <server name="DB_CONNECTION" value="sqlite"/>
    <server name="DB_DATABASE" value=":memory:"/>
</php>
```
- テスト環境動作確認
> *PHPUnitテスト（`php artisan test`）を実行する前に、テスト環境であることを確認するため、まず下記のコマンドを実行して確認してください。*
```bash
php artisan test --filter=EnvCheckTest
```
```text
"testing"
"sqlite"
":memory:"

   PASS  Tests\Feature\EnvCheckTest
  ✓ env is testing
  ✓ ing env is loaded
  ✓ session driver is array
  ✓ csrf is disabled for tests
```
以上のようなテスト結果が出たら、`php artisan test`でPHPUnitテストを実行してください。

### テスト構成
- Feature Test
  - 商品一覧 / 詳細 / 購入 / 出品
  - コメント / いいね
  - プロフィール・住所管理
  - Stripe Webhook（冪等性含む）
- Unit Test
  - テスト環境設定確認

### テストとテーブル設計の関係

各テストは、ER図に基づくテーブル設計と対応している。

- items テーブル → ItemTest / SellTest
- purchases テーブル → PurchaseTest / StripeWebhookTest / AddressTest
- users テーブル → ProfileTest / ProfileCompletedTest / MypageTest
- favorites テーブル → FavoriteTest
- comments テーブル → CommentTest

これにより、テーブル単位ではなく「機能単位」での動作保証を行っている。

### 各テストの役割
- **AddressTest**
購入時の配送先住所の入力およびバリデーションを確認している。
購入時点の住所が purchases テーブルへ正しく保存されることを確認している。
- **CommentTest**
商品コメント機能を確認している。
コメントが正しく comments テーブルに保存され、商品と紐づいて表示されることを確認している。
- **FavoriteTest**
お気に入り機能を確認している。
favorites テーブルへの登録および解除処理が正しく動作することを確認している。
- **ItemTest**
商品一覧および商品詳細ページが正しく表示されることを確認している。
SOLD商品の表示状態や出品者情報の紐づけも確認している。
- **MypageTest**
プロフィール（マイページ）ページが正しく表示されることを確認している。
ログインユーザーで、プロフィール編集が完了され、マイページが正常に表示される（デフォルトは出品した商品タブで表示される）ことを確認している。
- **ProfileCompletedTest**
プロフィール未登録ユーザーに対する購入制御を確認している。
住所情報が未入力の場合に購入できないことを確認している。
- **ProfileTest**
ユーザープロフィールの更新機能を確認している。
名前・住所・画像情報が正しく保存されることを確認している。
- **PurchaseTest**
購入ページのアクセス制御および購入条件を確認している。
自分の出品商品やSOLD商品が購入できないことを確認している。
- **SellTest**
商品出品機能を確認している。
ログインユーザーのみ出品可能であること、正しく items テーブルに保存されることを確認している。
- **StripeWebhookTest**
Stripe決済完了後のWebhook処理を確認している。
purchases.status の更新および items.status の SOLD 化を確認し、二重更新が発生しないことを確認している。


## 今後の改善
- 商品のカテゴリでも検索可能(今回の機能要件では商品名で部分一致の検索になっている)
- 同時購入時のロック処理強化
- 決済離脱問題に更なる改善(Stripe決済画面でブラウザーの戻るボタンを押してしまった場合の対応)
- 複数商品同時購入
- 複数カード登録・支払い方法管理
- 購入キャンセル / 返金フローの実装
- 管理者画面の追加
